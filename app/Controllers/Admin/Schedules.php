<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ShiftsModel;
use App\Models\FacilityModel;

class Schedules extends BaseController
{
    protected $shiftsModel;
    protected $facilityModel;

    public function __construct()
    {
        $this->shiftsModel = new ShiftsModel();
        $this->facilityModel = new FacilityModel();
    }

    public function index()
    {
        if (!session()->get('isAdminLoggedIn')) {
            return redirect()->to('/admin/login');
        }

        $data = [];
        $data['page_title'] = "Schedules Management";

        // Date range logic: last week (Monday) to this week (Sunday)
        $startOfLastWeek = date('Y-m-d', strtotime('monday last week'));
        $endOfThisWeek = date('Y-m-d', strtotime('sunday this week'));

        // Generate the 14-day range
        $dates = [];
        $current = $startOfLastWeek;
        while ($current <= $endOfThisWeek) {
            $dates[] = $current;
            $current = date('Y-m-d', strtotime($current . ' +1 day'));
        }
        $data['dates'] = $dates;

        // Paginated facilities (3 per page)
        $data['facilities'] = $this->facilityModel->orderBy('company_name', 'ASC')->paginate(3);
        $data['pager'] = $this->facilityModel->pager;

        // Shift counts for the facilities on the current page
        $facilityIds = array_column($data['facilities'], 'id');
        $shiftCounts = [];
        if (!empty($facilityIds)) {
            $db = \Config\Database::connect();
            $results = $db->table('tbl_shifts')
                ->select('client_id, start_date, COUNT(id) as shift_count, SUM(posted) as posted_count')
                ->whereIn('client_id', $facilityIds)
                ->where('start_date >=', $startOfLastWeek)
                ->where('start_date <=', $endOfThisWeek)
                ->groupBy('client_id, start_date')
                ->get()->getResultArray();

            foreach ($results as $row) {
                $shiftCounts[$row['client_id']][$row['start_date']] = [
                    'count' => $row['shift_count'],
                    'posted' => $row['posted_count']
                ];
            }
        }
        $data['shift_counts'] = $shiftCounts;

        return view('admin/schedules/index', $data);
    }

    public function list()
    {
        if (!session()->get('isAdminLoggedIn')) {
            return $this->response->setJSON([]);
        }

        $facilityId = $this->request->getPost('facility_id');

        $db = \Config\Database::connect();
        $builder = $db->table('tbl_shifts');
        $builder->select('tbl_shifts.start_date, COUNT(tbl_shifts.id) as total_shifts');
        $builder->select('(SELECT COUNT(id) FROM tbl_shift_clinicians WHERE shift_id IN (SELECT id FROM tbl_shifts as s2 WHERE s2.start_date = tbl_shifts.start_date ' . (!empty($facilityId) ? ' AND s2.client_id = ' . $db->escape($facilityId) : '') . ') AND from_callout = 1) as replacement_count');

        $builder->join('tbl_clients', 'tbl_clients.id = tbl_shifts.client_id', 'inner');

        if (!empty($facilityId)) {
            $builder->where('tbl_shifts.client_id', $facilityId);
        }

        $builder->groupBy('tbl_shifts.start_date');
        $results = $builder->get()->getResultArray();

        $events = [];
        foreach ($results as $row) {
            $date = $row['start_date'];
            $totalShifts = $row['total_shifts'];
            $hasReplacement = $row['replacement_count'] > 0;

            // Background Highlight
            $events[] = [
                'start' => $date,
                'display' => 'background',
                'backgroundColor' => $hasReplacement ? '#9ae6b4' : '#faf089', // Green or Yellow
                'allDay' => true,
                'zIndex' => 1
            ];

            // Content Event (HTML for shift count)
            $events[] = [
                'start' => $date,
                'allDay' => true,
                'extendedProps' => [
                    'html' => '<div class="fc-event-main text-center font-weight-bold" style="color:#2d3748; padding: 10px 0;">' . $totalShifts . ' Shift' . ($totalShifts > 1 ? 's' : '') . '</div>',
                    'date' => $date
                ],
                'display' => 'block',
                'backgroundColor' => 'transparent',
                'borderColor' => 'transparent',
                'zIndex' => 5
            ];
        }

        return $this->response->setJSON($events);
    }

    public function details()
    {
        if (!session()->get('isAdminLoggedIn')) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized']);
        }

        $date = $this->request->getPost('date');
        $facilityId = $this->request->getPost('facility_id');

        if (empty($date)) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Missing date']);
        }

        $builder = $this->shiftsModel->select('tbl_shifts.*, tbl_clients.company_name, tbl_client_units.name as unit_name, tbl_client_units.unit_manager_id, tbl_shift_types.name as shift_type_name');
        $builder->join('tbl_clients', 'tbl_clients.id = tbl_shifts.client_id', 'inner');
        $builder->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'left');
        $builder->join('tbl_shift_types', 'tbl_shift_types.id = tbl_shifts.shift_type', 'left');
        $builder->where('tbl_shifts.start_date', $date);

        if (!empty($facilityId)) {
            $builder->where('tbl_shifts.client_id', $facilityId);
        }

        $shifts = $builder->findAll();
        $db = \Config\Database::connect();

        // Fetch facility-wide information
        $schedulers = [];
        if (!empty($facilityId)) {
            $schedulers = $db->table('tbl_client_personnel')
                ->where('client_id', $facilityId)
                ->where('type', 5) // Scheduler
                ->where('status', 1)
                ->get()->getResultArray();
        }

        $supervisors = []; // Unit Managers
        $processedManagers = [];

        foreach ($shifts as $shift) {
            // Get unit manager (Supervisor) if not already added
            if (!empty($shift['unit_manager_id']) && !in_array($shift['unit_manager_id'], $processedManagers)) {
                $manager = $db->table('tbl_client_personnel')
                    ->where('id', $shift['unit_manager_id'])
                    ->get()->getRowArray();

                if ($manager) {
                    // Extract shift time from shift_time_range if available or use shift_start_time
                    // For the UI we need something like (7-3)
                    $timeLabel = '';
                    if (!empty($shift['shift_start_time'])) {
                        $start = date('g', strtotime($shift['shift_start_time']));
                        $end = date('g', strtotime($shift['shift_end_time']));
                        $timeLabel = "($start-$end)";
                    }

                    $supervisors[] = [
                        'name' => $manager['first_name'] . ' ' . $manager['last_name'],
                        'phone' => $manager['contact_number'],
                        'email' => $manager['email'],
                        'time' => $timeLabel
                    ];
                    $processedManagers[] = $shift['unit_manager_id'];
                }
            }

            // Get clinicians assigned to this shift
            $clinicians = $db->table('tbl_shift_clinicians')
                ->select('tbl_shift_clinicians.*, tbl_clinicians.name as clinician_name, tbl_client_personnel.first_name, tbl_client_personnel.last_name')
                ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_shift_clinicians.clinician_id', 'left')
                ->join('tbl_client_personnel', 'tbl_client_personnel.id = tbl_shift_clinicians.personnel_id', 'left')
                ->where('shift_id', $shift['id'])
                ->get()->getResultArray();

            foreach ($clinicians as &$clinician) {
                if ($clinician['clinician_id'] > 0) {
                    $clinician['display_name'] = $clinician['clinician_name'];
                    $clinician['is_external'] = true;
                } else {
                    $clinician['display_name'] = $clinician['first_name'] . ' ' . $clinician['last_name'];
                    $clinician['is_external'] = false;
                }
            }

            $shift['clinicians'] = $clinicians;
            $enrichedShifts[] = $shift;
        }

        return $this->response->setJSON([
            'success' => 1,
            'date' => date('F j, Y', strtotime($date)),
            'schedulers' => $schedulers,
            'supervisors' => $supervisors,
            'shifts' => $enrichedShifts
        ]);
    }

    public function get_available_clinicians()
    {
        if (!session()->get('isAdminLoggedIn')) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized']);
        }

        $shiftId = $this->request->getPost('shift_id');
        $shift = $this->shiftsModel->find($shiftId);

        if (!$shift) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Shift not found']);
        }

        $clinicianModel = new \App\Models\CliniciansModel();

        // Find clinicians of the same type
        $clinicians = $clinicianModel->where('type', $shift['shift_type'])
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        // Filter out already assigned clinicians for this shift
        $db = \Config\Database::connect();
        $assigned = $db->table('tbl_shift_clinicians')
            ->where('shift_id', $shiftId)
            ->get()->getResultArray();

        $assignedIds = array_column($assigned, 'clinician_id');

        $available = [];
        foreach ($clinicians as $clinician) {
            if (!in_array($clinician['id'], $assignedIds)) {
                $available[] = $clinician;
            }
        }

        return $this->response->setJSON([
            'success' => 1,
            'clinicians' => $available
        ]);
    }

    public function assign_clinician()
    {
        if (!session()->get('isAdminLoggedIn')) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized']);
        }

        $shiftId = $this->request->getPost('shift_id');
        $clinicianId = $this->request->getPost('clinician_id');

        if (empty($shiftId) || empty($clinicianId)) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Missing data']);
        }

        $shift = $this->shiftsModel->find($shiftId);
        if (!$shift) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Shift not found']);
        }

        $db = \Config\Database::connect();

        // Check if slots are available (summing confirmed and pending requests)
        $assignedCount = $db->table('tbl_shift_clinicians')->where('shift_id', $shiftId)->countAllResults();
        $requestCount = $db->table('tbl_client_shift_requests')->where('shift_id', $shiftId)->where('status !=', 100)->countAllResults();

        if (($assignedCount + $requestCount) >= $shift['slots']) {
            return $this->response->setJSON(['success' => 0, 'message' => 'No available slots for this shift.']);
        }

        // Assign
        $db->table('tbl_shift_clinicians')->insert([
            'client_id' => $shift['client_id'],
            'shift_id' => $shiftId,
            'clinician_id' => $clinicianId,
            'personnel_id' => 0,
            'status' => 10, // Accepted
            'shift_status' => 0,
            'pcc_status' => 10
        ]);

        return $this->response->setJSON(['success' => 1, 'message' => 'Clinician assigned successfully']);
    }
}
