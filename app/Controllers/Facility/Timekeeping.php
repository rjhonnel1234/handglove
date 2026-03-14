<?php

namespace App\Controllers\Facility;

use App\Controllers\BaseController;
use App\Models\FacilityModel;
use App\Models\ShiftsTimekeepingModel;
use App\Models\CliniciansModel;
use App\Models\ShiftsModel;

class Timekeeping extends BaseController
{
    protected $facilityModel;
    protected $shiftsTimekeepingModel;
    protected $clinicianModel;
    protected $shiftsModel;
    protected $session;

    public function __construct()
    {
        $this->facilityModel = new FacilityModel();
        $this->shiftsTimekeepingModel = new ShiftsTimekeepingModel();
        $this->clinicianModel = new CliniciansModel();
        $this->shiftsModel = new ShiftsModel();
        $this->session = session();
    }

    public function index()
    {
        // Redirect if not a facility user
        if ($this->session->get('facility_id') == 0) {
            return redirect()->to('/profile');
        }

        $facility = $this->facilityModel->find($this->session->get('facility_id'));
        if (!$facility) {
            return redirect()->to('/profile');
        }

        $data = [
            'session' => $this->session,
            'facility' => $facility,
            'page' => 'timekeeping'
        ];

        return view('components/header', [
            'title' => 'Time Logs - Handglove',
            'description' => '',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => [
                'title' => 'Handglove',
                'description' => '',
                'image' => IMG_URL . ''
            ],
            'styles' => [
                'plugins/font_awesome',
                'plugins/datatables',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                COMPILED_ASSETS_PATH . 'css/components/owl',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-datepicker',
                COMPILED_ASSETS_PATH . 'css/components/toastr',
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/animations',
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/pages/facility_profile'
            ],
            'session' => $this->session
        ])
        . view('facility/manage/timekeeping', $data)
        . view('components/scripts_render', [
            'scripts' => [
                'https://code.jquery.com/jquery-3.5.1.min.js' => [
                    'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                    'crossorigin' => 'anonymous'
                ],
                'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
                ASSETS_URL . 'js/plugins/popper.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                ASSETS_URL . 'js/components/global.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-datepicker.js',
                ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                ASSETS_URL . 'js/components/notifications.min.js',
                ASSETS_URL . 'js/plugins/toastr.min.js',
                ASSETS_URL . 'js/pages/facility_timekeeping.min.js',
            ]
        ])
        . view('components/footer');
    }

    public function list()
    {
        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['data' => []]);
        }

        $logs = $this->shiftsTimekeepingModel
            ->select('
            tbl_shift_timekeeping.*, 
            tbl_clinicians.name as clinician_name, 
            tbl_shift_types.name as shift_type_name, 
            tbl_client_units.name as unit_name, 
            tbl_shifts.start_date as shift_start_date, 
            tbl_shifts.shift_start_time, 
            tbl_shifts.shift_end_time')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_shift_timekeeping.clinician_id', 'inner')
            ->join('tbl_shifts', 'tbl_shifts.id = tbl_shift_timekeeping.shift_id', 'inner')
            ->join('tbl_shift_types', 'tbl_shift_types.id = tbl_shifts.shift_type', 'left')
            ->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'inner')
            ->where('tbl_shifts.client_id', $facilityId)
            ->orderBy('tbl_shift_timekeeping.punch_datetime', 'DESC')
            ->findAll();

        foreach ($logs as &$log) {
            $log['punch_datetime'] = date('M d, Y h:i A', strtotime($log['punch_datetime']));
            $log['punch_type_label'] = ($log['punch_type'] == 10) ? '<span class="badge badge-success">Clock In</span>' : '<span class="badge badge-danger">Clock Out</span>';

            if (empty($log['reference']) && $log['punch_type'] == 20) {
                $log['punch_type_label'] = '<span class="badge badge-warning">Missed Punch Out</span>';
            }

            $log['shift_info'] = sprintf(
                '<div>Shift #%s</div><div>%s %s - %s</div><div>%s - %s</div>',
                $log['shift_id'],
                date("M d, Y", strtotime($log['shift_start_date'])),
                date("h:i A", strtotime($log['shift_start_time'])),
                date("h:i A", strtotime($log['shift_end_time'])),
                $log['shift_type_name'],
                $log['unit_name']
            );

            $log['action'] = sprintf(
                '<a href="%s" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>',
                base_url('facility/manage/timekeeping/view/' . $log['id'])
            );
        }

        return $this->response->setJSON(['data' => $logs]);
    }

    public function view($id)
    {
        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return redirect()->to('/profile');
        }

        $punch = $this->shiftsTimekeepingModel
            ->select('tbl_shift_timekeeping.*, tbl_clinicians.name as clinician_name, tbl_clinicians.profile_pic_url, tbl_shift_types.name as shift_type_name, tbl_client_units.name as unit_name, tbl_shifts.start_date as shift_start_date, tbl_shifts.shift_start_time, tbl_shifts.shift_end_time, tbl_shifts.rate')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_shift_timekeeping.clinician_id', 'inner')
            ->join('tbl_shifts', 'tbl_shifts.id = tbl_shift_timekeeping.shift_id', 'inner')
            ->join('tbl_shift_types', 'tbl_shift_types.id = tbl_shifts.shift_type', 'inner')
            ->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'inner')
            ->where('tbl_shift_timekeeping.id', $id)
            ->first();

        if (!$punch) {
            return redirect()->to('facility/manage/timekeeping');
        }

        $clockIn = $this->shiftsTimekeepingModel->where('shift_id', $punch['shift_id'])
            ->where('clinician_id', $punch['clinician_id'])
            ->where('punch_type', 10)
            ->first();

        $clockOut = $this->shiftsTimekeepingModel->where('shift_id', $punch['shift_id'])
            ->where('clinician_id', $punch['clinician_id'])
            ->where('punch_type', 20)
            ->first();

        $data = [
            'punch' => $punch,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'page' => 'timekeeping',
            'session' => $this->session,
            'status_label' => $clockOut ? '<span class="badge badge-primary px-3 py-2">Completed</span>' : '<span class="badge badge-success px-3 py-2">Ongoing</span>'
        ];

        // Calculation
        $totalShiftTime = "0:00";
        if ($clockIn && $clockOut) {
            $start = new \DateTime($clockIn['punch_datetime']);
            $end = new \DateTime($clockOut['punch_datetime']);
            $interval = $start->diff($end);

            $totalMinutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
            $totalShiftTime = sprintf('%d:%02d', floor($totalMinutes / 60), $totalMinutes % 60);
        }

        $data['total_shift_time'] = $totalShiftTime;
        $data['payable_time'] = $totalShiftTime; // Assuming no breaks for now

        return view('components/header', [
            'title' => 'Timesheet #' . $punch['shift_id'],
            'description' => '',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => [
                'title' => 'Handglove',
                'description' => '',
                'image' => IMG_URL . ''
            ],
            'styles' => [
                'plugins/font_awesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/pages/facility_profile'
            ],
            'session' => $this->session
        ])
        . view('facility/manage/timekeeping_view', $data)
        . view('components/scripts_render', [
            'scripts' => [
                'https://code.jquery.com/jquery-3.5.1.min.js',
                ASSETS_URL . 'js/plugins/popper.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                ASSETS_URL . 'js/components/global.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
            ]
        ])
        . view('components/footer');
    }
}
