<?php

namespace App\Controllers\Facility;

use App\Controllers\BaseController;
use App\Models\FacilityModel;
use App\Models\ShiftsModel;
use App\Models\CliniciansModel;
use App\Models\ShiftCliniciansModel;
use App\Models\ShiftTypesModel;
use App\Models\ShiftRequestsModel;
use App\Models\FacilityUnitsModel;
use App\Models\NotificationsModel;
use App\Models\UserModel;
use \Datetime;
use CodeIgniter\Files\File;

class Jobs extends BaseController
{
    protected $facilityModel;
    protected $shiftsModel;
    protected $clinicianModel;
    protected $shiftCliniciansModel;
    protected $shiftTypesModel;
    protected $shiftRequestsModel;
    protected $facilityUnitsModel;
    protected $notificationsModel;
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->facilityModel = new FacilityModel();
        $this->shiftsModel = new ShiftsModel();
        $this->clinicianModel = new CliniciansModel();
        $this->shiftCliniciansModel = new ShiftCliniciansModel();
        $this->shiftTypesModel = new ShiftTypesModel();
        $this->shiftRequestsModel = new ShiftRequestsModel();
        $this->facilityUnitsModel = new FacilityUnitsModel();
        $this->notificationsModel = new NotificationsModel();
        $this->userModel = new UserModel();
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
            'shiftTypes' => $this->shiftTypesModel->where('status', 1)->findAll(),
            'shiftTimes' => $this->shiftsModel->shift_time_range,
            'units' => $this->facilityUnitsModel->where('client_id', $this->session->get('facility_id'))->findAll(),
            'page' => 'jobs'
        ];

        return view('components/header', [
            'title' => 'Jobs Management - Handglove',
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
        . view('facility/manage', $data)
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
                ASSETS_URL . 'js/pages/facility_jobs.min.js',
            ]
        ])
        . view('components/footer');
    }

    public function update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $shiftId = $this->request->getPost('shiftID');
        $shift = $this->shiftsModel->find($shiftId);

        if (empty($shift)) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Shift not found.']);
        }

        $validation = \Config\Services::validation();
        $rules = [];
        $item = [];

        if ($this->request->getPost('shift_type')) {
            $rules['shift_type'] = 'required';
            $item['shift_type'] = $this->request->getPost('shift_type');
        }

        if ($this->request->getPost('rate')) {
            $rules['rate'] = 'required';
            $item['rate'] = $this->request->getPost('rate');
        }

        if ($this->request->getPost('unit_id')) {
            $rules['unit_id'] = 'required';
            $item['unit_id'] = $this->request->getPost('unit_id');
        }

        if ($this->request->getPost('slots')) {
            $rules['slots'] = 'required';
            $item['slots'] = $this->request->getPost('slots');
        }

        $status = $this->request->getPost('status') ?: 1;

        if (!empty($rules)) {
            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => 0,
                    'message_header' => 'Shifts',
                    'message' => $validation->getErrors()
                ]);
            }
        }

        $item['status'] = $status;
        $this->shiftsModel->update($shiftId, $item);

        return $this->response->setJSON([
            'success' => 1,
            'message_header' => 'Shifts',
            'message' => $status == 100 ? 'Shift has been cancelled.' : 'Shift has been updated successfully.'
        ]);
    }

    public function insert()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $rules = [
            'shift_type' => [
                'label' => 'Shift Type',
                'rules' => 'required'
            ],
            'rate' => [
                'label' => 'Rate',
                'rules' => 'required'
            ],
            'unit_id' => [
                'label' => 'Unit',
                'rules' => 'required'
            ],
            'date' => [
                'label' => 'Date',
                'rules' => 'required'
            ],
            'time' => [
                'label' => 'Time',
                'rules' => 'required'
            ],
            'slots' => [
                'label' => 'Slots',
                'rules' => 'required'
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => 0,
                'message_header' => 'Shifts',
                'message' => $this->validator->getErrors()
            ]);
        }

        $start_date = $this->request->getPost('date');
        $end_date = $start_date;
        $shiftTime = explode(" - ", $this->request->getPost('time'));
        $startTime = date("H:i:s", strtotime($shiftTime[0]));
        $endTime = date("H:i:s", strtotime($shiftTime[1]));
        
        if (strtotime($endTime) < strtotime($startTime)) {
            $end_date = date('Y-m-d', strtotime($start_date . ' + 1 day'));
        }

        $item = [
            'client_id' => $facilityId,
            'shift_type' => $this->request->getPost('shift_type'),
            'rate' => $this->request->getPost('rate'),
            'slots' => $this->request->getPost('slots'),
            'unit_id' => $this->request->getPost('unit_id'),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => 1,
            'shift_start_time' => $startTime,
            'shift_end_time' => $endTime
        ];

        if ($this->shiftsModel->save($item)) {
            return $this->response->setJSON([
                'success' => 1,
                'message_header' => 'Shifts',
                'message' => 'Successfully added.'
            ]);
        }

        return $this->response->setJSON([
            'success' => 0,
            'message_header' => 'Shifts',
            'message' => 'Error adding shift.'
        ]);
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $builder = $this->shiftsModel
            ->select('tbl_shifts.*, tbl_shift_types.name as type_name, tbl_client_units.name as unit_name, 
                (SELECT count(tbl_client_shift_requests.id) FROM tbl_client_shift_requests WHERE tbl_client_shift_requests.client_id = tbl_shifts.client_id AND tbl_client_shift_requests.shift_id = tbl_shifts.id) as applicants, 
                (SELECT count(tbl_client_shift_requests.id) FROM tbl_client_shift_requests WHERE tbl_client_shift_requests.client_id = tbl_shifts.client_id AND tbl_client_shift_requests.shift_id = tbl_shifts.id AND status = 20) as accepted')
            ->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'inner')
            ->join('tbl_shift_types', 'tbl_shift_types.id = tbl_shifts.shift_type', 'inner')
            ->where('tbl_shifts.client_id', $facilityId)
            ->where('tbl_shifts.start_date', $this->request->getPost('date'));

        $timeFilter = $this->request->getPost('time');
        if ($timeFilter == 'am') {
            $builder->where('tbl_shifts.shift_start_time', '07:00:00');
        } elseif ($timeFilter == 'pm') {
            $builder->groupStart()
                ->where('tbl_shifts.shift_start_time', '15:00:00')
                ->orWhere('tbl_shifts.shift_start_time', '21:00:00')
                ->groupEnd();
        } elseif ($timeFilter == 'eve') {
            $builder->where('tbl_shifts.shift_start_time', '23:00:00');
        }

        $shifts = $builder->findAll();
        $formattedData = [];

        foreach ($shifts as $shift) {
            $shift['requests'] = $this->shiftRequestsModel
                ->select('tbl_clinicians.name as clinician_name')
                ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_client_shift_requests.clinician_id', 'INNER')
                ->whereIn('tbl_client_shift_requests.status', [10, 15])
                ->where('tbl_client_shift_requests.shift_id', $shift['id'])
                ->findAll();

            $shift['accepted_arr'] = $this->shiftRequestsModel
                ->select('tbl_clinicians.name as clinician_name')
                ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_client_shift_requests.clinician_id', 'INNER')
                ->where('tbl_client_shift_requests.status', 20)
                ->where('tbl_client_shift_requests.shift_id', $shift['id'])
                ->findAll();

            $item = [
                'name' => view('facility/_shift_view', $shift),
                'action' => sprintf(
                    '<div class="text-center"><a href="javascript:;" data-id="%s" data-type="%s" class="request-clinician-table btn btn-blue pl-2 pr-2 pt-1 pb-1" title="Request for Clinicians"><i class="fa fa-users"></i> Request Clinicians</a></div>',
                    $shift['id'],
                    $shift['shift_type']
                )
            ];

            if ($shift['status'] == 100) {
                $item['action'] = '<div class="alert alert-danger text-center" role="alert">Cancelled</div>';
            }

            $formattedData[] = $item;
        }

        return $this->response->setJSON([
            'success' => 1,
            'message' => '',
            'data' => $formattedData
        ]);
    }

    public function requests_list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $shiftId = $this->request->getVar('shift');
        $requests = $this->shiftRequestsModel
            ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name, tbl_client_shift_requests.*')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_client_shift_requests.clinician_id', 'INNER')
            ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'inner')
            ->where('tbl_client_shift_requests.shift_id', $shiftId)
            ->findAll();

        $formattedData = [];
        foreach ($requests as $request) {
            $item = [
                'details' => sprintf('<div><strong>%s</strong><br>%s<br>%s</div>', $request['name'], $request['email'], $request['contact_number']),
                'type' => $request['type_name'],
                'level' => $this->clinicianModel->tier_mapping[$request['tier']] ?? 'N/A',
            ];

            if ($request['status'] == 15) {
                $item['action'] = sprintf(
                    '<div class="text-center"><a href="javascript:;" data-shift-id="%s" data-id="%s" data-value="20" class="respond-request btn thm-btn pl-2 pr-2 pt-1 pb-1" title=""><i class="fa fa-plus"></i> Accept</a> <a href="javascript:;" data-shift-id="%s" data-id="%s" data-value="100" class="respond-request btn btn-danger pl-2 pr-2 pt-1 pb-1" title=""><i class="fa fa-times"></i> Decline</a></div>',
                    $shiftId,
                    $request['clinician_id'],
                    $shiftId,
                    $request['clinician_id']
                );
            } else {
                $item['action'] = sprintf('<div class="text-center">%s</div>', $this->shiftRequestsModel->status_mapping_facility_pov[$request['status']] ?? 'Unknown');
            }
            $formattedData[] = $item;
        }

        return $this->response->setJSON([
            'success' => 1,
            'message' => '',
            'data' => $formattedData
        ]);
    }

    public function request()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $shiftId = $this->request->getPost('shiftID');
        $clinicianId = $this->request->getPost('clinicianID');
        $shift = $this->shiftsModel->find($shiftId);

        if (!$shift) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Shift not found.']);
        }

        $applied = $this->shiftRequestsModel
            ->where('client_id', $facilityId)
            ->where('shift_id', $shiftId)
            ->where('status', 20)
            ->get()->getNumRows();

        if ($applied < $shift['slots']) {
            $item = [
                'client_id' => $facilityId,
                'shift_id' => $shiftId,
                'clinician_id' => $clinicianId,
                'status' => 10
            ];

            $id = $this->shiftRequestsModel->save($item);
            $this->notificationsModel->createNotification($facilityId, $clinicianId, 'shift_request', $id);

            return $this->response->setJSON(['success' => 1, 'message_header' => 'Shifts', 'message' => 'Successfully sent a request to clinician.']);
        }

        return $this->response->setJSON(['success' => 0, 'message_header' => 'Shifts', 'message' => 'You reached the maximum slots allowed for this shift.']);
    }

    public function respond_to_application()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $shiftId = $this->request->getPost('shiftID');
        $clinicianId = $this->request->getPost('clinicianID');
        $status = $this->request->getPost('status');
        $shift = $this->shiftsModel->find($shiftId);

        if (empty($shift)) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Shift not found.']);
        }

        $applied = $this->shiftRequestsModel
            ->where('client_id', $facilityId)
            ->where('shift_id', $shiftId)
            ->where('status', 20)
            ->countAllResults();

        if ($status == 20 && $applied >= $shift['slots']) {
            return $this->response->setJSON(['success' => 0, 'message' => 'You reached the maximum slots allowed for this shift.']);
        }

        $this->shiftRequestsModel
            ->set(['status' => $status])
            ->where('shift_id', $shiftId)
            ->where('clinician_id', $clinicianId)
            ->update();

        if ($status == 20) {
            $this->notificationsModel->createNotification($facilityId, $clinicianId, 'approval', $shiftId);
            $this->shiftCliniciansModel->save([
                'client_id' => $facilityId,
                'shift_id' => $shiftId,
                'clinician_id' => $clinicianId,
                'status' => 10,
                'pcc_status' => 10
            ]);
            $message = 'Application accepted successfully.';
        } else {
            $message = 'Application declined successfully.';
        }

        return $this->response->setJSON([
            'success' => 1,
            'message_header' => 'Shifts',
            'message' => $message
        ]);
    }

    public function view($id)
    {
        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return redirect()->to('/profile');
        }

        $shift = $this->shiftsModel
            ->select('tbl_shifts.*, tbl_shift_types.name as type_name, tbl_client_units.name as unit_name, 
                (SELECT count(tbl_client_shift_requests.id) FROM tbl_client_shift_requests WHERE tbl_client_shift_requests.client_id = tbl_shifts.client_id AND tbl_client_shift_requests.shift_id = tbl_shifts.id) as applicants_count, 
                (SELECT count(tbl_client_shift_requests.id) FROM tbl_client_shift_requests WHERE tbl_client_shift_requests.client_id = tbl_shifts.client_id AND tbl_client_shift_requests.shift_id = tbl_shifts.id AND status = 20) as accepted_count')
            ->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'inner')
            ->join('tbl_shift_types', 'tbl_shift_types.id = tbl_shifts.shift_type', 'inner')
            ->where('tbl_shifts.id', $id)
            ->where('tbl_shifts.client_id', $facilityId)
            ->first();

        if (empty($shift)) {
            return redirect()->to('/facility/manage/jobs');
        }

        $shift['applicants'] = $this->shiftRequestsModel
            ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name, tbl_client_shift_requests.*')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_client_shift_requests.clinician_id', 'INNER')
            ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'inner')
            ->whereIn('tbl_client_shift_requests.status', [10, 15])
            ->where('tbl_client_shift_requests.shift_id', $id)
            ->findAll();

        $shift['accepted_clinicians'] = $this->shiftRequestsModel
            ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name, tbl_client_shift_requests.*')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_client_shift_requests.clinician_id', 'INNER')
            ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'inner')
            ->where('tbl_client_shift_requests.status', 20)
            ->where('tbl_client_shift_requests.shift_id', $id)
            ->findAll();

        $data = [
            'shift' => $shift,
            'session' => $this->session,
            'facility' => $this->facilityModel->find($facilityId),
            'page' => 'job_view'
        ];

        return view('components/header', [
            'title' => 'Shift Details - Handglove',
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
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/animations',
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/pages/facility_profile'
            ],
            'session' => $this->session
        ])
        . view('facility/manage', $data)
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
                ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                ASSETS_URL . 'js/pages/facility_jobs_view.min.js',
            ]
        ])
        . view('components/footer');
    }
}
