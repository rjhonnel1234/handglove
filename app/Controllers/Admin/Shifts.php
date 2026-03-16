<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ShiftsModel;
use App\Models\ShiftsTimekeepingModel;
use App\Models\CliniciansModel;
use App\Models\FacilityModel;

class Shifts extends BaseController
{
    protected $shiftsModel;
    protected $timekeepingModel;
    protected $clinicianModel;
    protected $facilityModel;

    public function __construct()
    {
        $this->shiftsModel = new ShiftsModel();
        $this->timekeepingModel = new ShiftsTimekeepingModel();
        $this->clinicianModel = new CliniciansModel();
        $this->facilityModel = new FacilityModel();
    }

    public function index()
    {
        if (!session()->get('isAdminLoggedIn')) {
            return redirect()->to('/admin/login');
        }

        $data = [
            'page_title' => "All Shifts",
            'clinicians' => $this->clinicianModel->orderBy('name', 'ASC')->findAll(),
            'facilities' => $this->facilityModel->orderBy('company_name', 'ASC')->findAll()
        ];

        return view('admin/shifts/index', $data);
    }

    public function list()
    {
        if (!session()->get('isAdminLoggedIn')) {
            return $this->response->setJSON([]);
        }

        $db = \Config\Database::connect();
        
        // Fetch all shifts with clinician and unit info
        $builder = $db->table('tbl_shifts');
        $builder->select('tbl_shifts.*, tbl_clients.company_name, tbl_client_units.name as unit_name, tbl_shift_types.name as shift_type_name');
        $builder->join('tbl_clients', 'tbl_clients.id = tbl_shifts.client_id', 'inner');
        $builder->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'inner');
        $builder->join('tbl_shift_types', 'tbl_shift_types.id = tbl_shifts.shift_type', 'left');
        
        $shifts = $builder->get()->getResultArray();

        $events = [];
        foreach ($shifts as $shift) {
            // Get clinicians assigned to this shift
            $assignedClinicians = $db->table('tbl_shift_clinicians')
                ->select('tbl_shift_clinicians.clinician_id, tbl_clinicians.name as clinician_name')
                ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_shift_clinicians.clinician_id', 'inner')
                ->where('shift_id', $shift['id'])
                ->get()->getResultArray();

            foreach ($assignedClinicians as $clinician) {
                // Determine status and color for each clinician in the shift
                $statusData = $this->getShiftStatus($shift, $clinician['clinician_id']);
                
                $events[] = [
                    'id' => $shift['id'] . '_' . $clinician['clinician_id'],
                    'resourceId' => $clinician['clinician_id'],
                    'title' => '(' . $shift['shift_type_name'] . ') ' . $shift['company_name'],
                    'start' => $shift['start_date'] . 'T' . $shift['shift_start_time'],
                    'end' => $shift['start_date'] . 'T' . $shift['shift_end_time'],
                    'backgroundColor' => $statusData['color'],
                    'borderColor' => $statusData['color'],
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'shift_id' => $shift['id'],
                        'clinician_id' => $clinician['clinician_id'],
                        'clinician_name' => $clinician['clinician_name'],
                        'facility_name' => $shift['company_name'],
                        'unit_name' => $shift['unit_name'],
                        'shift_type' => $shift['shift_type_name'],
                        'status_label' => $statusData['label'],
                        'clock_in' => $statusData['clock_in'],
                        'clock_out' => $statusData['clock_out']
                    ]
                ];
            }
        }

        return $this->response->setJSON($events);
    }

    public function resources()
    {
        if (!session()->get('isAdminLoggedIn')) {
            return $this->response->setJSON([]);
        }

        $clinicians = $this->clinicianModel->orderBy('name', 'ASC')->findAll();
        
        $resources = [];
        foreach ($clinicians as $clinician) {
            $resources[] = [
                'id' => $clinician['id'],
                'title' => $clinician['name']
            ];
        }

        return $this->response->setJSON($resources);
    }

    private function getShiftStatus($shift, $clinicianId)
    {
        $clockIn = $this->timekeepingModel->where('shift_id', $shift['id'])
            ->where('clinician_id', $clinicianId)
            ->where('punch_type', 10)
            ->first();

        $clockOut = $this->timekeepingModel->where('shift_id', $shift['id'])
            ->where('clinician_id', $clinicianId)
            ->where('punch_type', 20)
            ->first();

        $scheduledStart = strtotime($shift['start_date'] . ' ' . $shift['shift_start_time']);
        $scheduledEnd = strtotime($shift['start_date'] . ' ' . $shift['shift_end_time']);
        
        // Handle overnight shifts
        if ($scheduledEnd < $scheduledStart) {
            $scheduledEnd = strtotime($shift['start_date'] . ' ' . $shift['shift_end_time'] . ' +1 day');
        }

        $color = '#a0aec0'; // Gray (No logs)
        $label = 'No Clock In';
        $ci_time = null;
        $co_time = null;

        if ($clockIn) {
            $ci_time = date('h:i A', strtotime($clockIn['punch_datetime']));
            $actualStart = strtotime($clockIn['punch_datetime']);
            
            if ($clockOut) {
                $co_time = date('h:i A', strtotime($clockOut['punch_datetime']));
                $actualEnd = strtotime($clockOut['punch_datetime']);
                
                // Color Logic
                if ($actualEnd > $scheduledEnd) {
                    $color = '#4299e1'; // Blue (Overtime)
                    $label = 'Overtime';
                } elseif ($actualStart > $scheduledStart) {
                    $color = '#f56565'; // Red (Late)
                    $label = 'Late';
                } else {
                    $color = '#48bb78'; // Green (Completed)
                    $label = 'Completed';
                }
            } else {
                $color = '#ecc94b'; // Yellow (Clock In)
                $label = 'Clocked In';
            }
        }

        return [
            'color' => $color,
            'label' => $label,
            'clock_in' => $ci_time,
            'clock_out' => $co_time
        ];
    }
}
