<?php

namespace App\Controllers\Facility;

use App\Controllers\BaseController;
use App\Models\FacilityModel;
use App\Models\ShiftsModel;
use App\Models\ShiftTypesModel;
use App\Models\ShiftRequestsModel;
use App\Models\ShiftCliniciansModel;
use App\Models\FacilityUnitsModel;
use App\Models\ShiftsTimekeepingModel;
use App\Models\UserModel;
use App\Models\ShiftClinicianUpdatesModel;
use CodeIgniter\Files\File;
use \Datetime;

class Shifts extends BaseController
{
    protected $facilityModel;
    protected $shiftsModel;
    protected $shiftTypesModel;
    protected $shiftRequestsModel;
    protected $shiftCliniciansModel;
    protected $facilityUnitsModel;
    protected $shiftsTimekeepingModel;
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->facilityModel = new FacilityModel();
        $this->shiftsModel = new ShiftsModel();
        $this->shiftTypesModel = new ShiftTypesModel();
        $this->shiftRequestsModel = new ShiftRequestsModel();
        $this->shiftCliniciansModel = new ShiftCliniciansModel();
        $this->facilityUnitsModel = new FacilityUnitsModel();
        $this->shiftsTimekeepingModel = new ShiftsTimekeepingModel();
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
            'units' => $this->facilityUnitsModel->where('client_id', $this->session->get('facility_id'))->findAll(),
            'page' => 'shifts'
        ];

        return view('components/header', [
            'title' => 'Handglove',
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
                    ASSETS_URL . 'js/pages/facility_shifts.min.js',
                ]
            ])
            . view('components/footer');
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

        $facility = $this->facilityModel->find($facilityId);
        if (!$facility) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Facility not found.']);
        }
        $date = $this->request->getPost('date') ?: date("Y-m-d");
        $unitId = $this->request->getPost('unitID');

        $shifts = $this->shiftsModel
            ->select('tbl_shifts.*, tbl_shift_types.name as type_name, tbl_client_units.name as unit_name, 
                (SELECT count(tbl_client_shift_requests.id) FROM tbl_client_shift_requests WHERE tbl_client_shift_requests.client_id = tbl_shifts.client_id AND tbl_client_shift_requests.shift_id = tbl_shifts.id) as applicants, 
                (SELECT count(tbl_client_shift_requests.id) FROM tbl_client_shift_requests WHERE tbl_client_shift_requests.client_id = tbl_shifts.client_id AND tbl_client_shift_requests.shift_id = tbl_shifts.id AND status = 20) as accepted,
                (SELECT count(tbl_client_shift_requests.id) FROM tbl_client_shift_requests WHERE tbl_client_shift_requests.shift_id = tbl_shifts.id AND status = 10 AND from_callout = 1) as pending_callout_requests')
            ->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'inner')
            ->join('tbl_shift_types', 'tbl_shift_types.id = tbl_shifts.shift_type', 'inner')
            ->where('tbl_shifts.client_id', $facilityId)
            ->where('tbl_shifts.unit_id', $unitId)
            ->where('tbl_shifts.start_date', $date)
            ->where("CONCAT(IFNULL(tbl_shifts.end_date, tbl_shifts.start_date), ' ', tbl_shifts.shift_end_time) > '" . '2026-02-19 00:00:00' . "'")
            ->findAll();

        foreach ($shifts as &$shift) {
            $shift['shift_end_time_formatted'] = date("h:i A", strtotime($shift['shift_end_time']));
            $shift['shift_start_time_formatted'] = date("h:i A", strtotime($shift['shift_start_time']));

            //if shift is ongoing remove request clinician button
            if (strtotime($shift['start_date'] . ' ' . $shift['shift_start_time']) > time()) {
                $shift['action'] = sprintf(
                    '<div class="mt-3"><a href="javascript:;" data-id="%s" data-type="%s" class="request-clinician-table btn thm-btn pl-2 pr-2 pt-1 pb-1" title="Request for Clinicians"><i class="fa fa-plus"></i> Request Clinicians</a></div>',
                    $shift['id'],
                    $shift['shift_type']
                );
            } else {
                $shift['action'] = '';
            }

            $shift['clinicians'] = $this->shiftCliniciansModel
                ->select('tbl_clinicians.name as clinician_name, tbl_clinicians.profile_pic_url, tbl_client_personnel.first_name as staff_first, tbl_client_personnel.last_name as staff_last, tbl_shift_clinicians.*, tbl_clinicians.type as clinician_type_id, tbl_client_personnel.type as staff_type_id, tbl_clinician_types.name as clinician_type_name, staff_type.name as staff_type_name')
                ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_shift_clinicians.clinician_id', 'LEFT')
                ->join('tbl_client_personnel', 'tbl_client_personnel.id = tbl_shift_clinicians.personnel_id', 'LEFT')
                ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'LEFT')
                ->join('tbl_clinician_types as staff_type', 'staff_type.id = tbl_client_personnel.clinician_type', 'LEFT')
                ->whereIn('tbl_shift_clinicians.status', [10, 0])
                ->where('tbl_shift_clinicians.shift_status', 0)
                ->where('tbl_shift_clinicians.shift_id', $shift['id'])
                ->findAll();

            $pccModel = new \App\Models\ClinicianPccModel();
            //get personnel/staff from shifts
            foreach ($shift['clinicians'] as &$clinician) {
                // Resolve Name
                if (!empty($clinician['clinician_id']) && $clinician['clinician_id'] > 0) {
                    $displayName = $clinician['clinician_name'];
                    $displayType = $clinician['clinician_type_name'];
                    $clockInIdField = 'clinician_id';
                    $clockInId = $clinician['clinician_id'];

                    // Check for PCC Credentials
                    $pcc = $pccModel->where('clinician_id', $clinician['clinician_id'])
                        ->where('facility_id', $facilityId)
                        ->first();
                    if ($pcc) {
                        $clinician['has_pcc'] = true;
                        $clinician['pcc_username'] = $pcc['username'];
                        $clinician['pcc_password'] = $pcc['password'];
                        $clinician['pcc_status'] = 20; // Unlocked
                    } else {
                        $clinician['has_pcc'] = false;
                    }
                } else {
                    $displayName = $clinician['staff_first'] . ' ' . $clinician['staff_last'];
                    $displayType = $clinician['staff_type_name'];
                    $clockInIdField = 'personnel_id';
                    $clockInId = $clinician['personnel_id'];
                    $clinician['profile_pic_url'] = base_url('assets/img/blank-img.png');
                    $clinician['has_pcc'] = false;
                }
                $clinician['display_name'] = $displayName;
                $clinician['display_type'] = $displayType;
                $clinician['type'] = $clinician['clinician_id'] > 0 ? 'clinician' : 'staff';
                $punchIn = '';
                if ($clockInIdField == 'clinician_id') {
                    $punchIn = $this->shiftsTimekeepingModel
                        ->where('shift_id', $shift['id'])
                        ->where($clockInIdField, $clockInId)
                        ->where('punch_type', 10)
                        ->orderBy('punch_datetime', 'asc')
                        ->first();
                }

                $clinician['is_clocked_in'] = (!empty($punchIn));
            }

            // check if clinician is already logged in on the current shift, if yes, don't show alternative units
            $shift['alternative_units'] = $this->shiftsModel
                ->select('tbl_shifts.id as shift_id, tbl_client_units.name as unit_name')
                ->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'inner')
                ->where('tbl_shifts.client_id', $facilityId)
                ->where('tbl_shifts.start_date', $shift['start_date'])
                ->where('tbl_shifts.shift_type', $shift['shift_type'])
                ->where('tbl_shifts.shift_start_time', $shift['shift_start_time'])
                ->where('tbl_shifts.shift_end_time', $shift['shift_end_time'])
                ->where('tbl_shifts.unit_id !=', $shift['unit_id'])
                ->where('tbl_shifts.status', 1)
                ->findAll();

            // Get pending replacement requests
            $pendingReplacements = $this->shiftRequestsModel
                ->where('shift_id', $shift['id'])
                ->where('status', 10)
                ->where('from_callout', 1)
                ->findAll();

            $shift['pending_replacements'] = [];
            foreach ($pendingReplacements as $pr) {
                $shift['pending_replacements'][$pr['replacing_clinician_id']] = $pr['id'];
            }
            $shift['clinicians_html'] = '';
            foreach ($shift['clinicians'] as $clinician) {
                $shift['clinicians_html'] .= view('facility/manage/_shift_clinician_item', [
                    'clinician' => $clinician,
                    'shift' => $shift
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => 1,
            'message' => '',
            'data' => $shifts
        ]);
    }

    public function get_clinician_details($id)
    {
        $clinModel = new \App\Models\CliniciansModel();
        $clinician = $clinModel->find($id);
        if (!$clinician) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Clinician not found']);
        }

        $objShiftClinician = new \App\Models\ShiftCliniciansModel();
        $total_shifts = $objShiftClinician->where('clinician_id', $id)->where('status', 10)->countAllResults();

        $objTimekeeping = new \App\Models\ShiftsTimekeepingModel();
        $stats = $objTimekeeping->getStats($id);

        return $this->response->setJSON([
            'success' => 1,
            'data' => [
                'id' => $clinician['id'],
                'name' => $clinician['name'],
                'address' => $clinician['address'],
                'contact_number' => $clinician['contact_number'],
                'profile_pic_url' => $clinician['profile_pic_url'] ?: base_url('assets/img/blank-img.png'),
                'total_shifts' => $total_shifts,
                'attendance_percentage' => $stats['attendance'],
                'lateness_percentage' => $stats['lateness'],
            ]
        ]);
    }

    public function get_clinician_timeline($shiftClinicianId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $updateModel = new ShiftClinicianUpdatesModel();
        $updates = $updateModel
            ->where('shift_clinician_id', $shiftClinicianId)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        foreach ($updates as &$update) {
            $update['created_at_formatted'] = date("D, M d, h:i A", strtotime($update['created_at']));
        }

        return $this->response->setJSON([
            'success' => 1,
            'updates' => $updates
        ]);
    }

    public function transfer()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $shiftClinicianID = $this->request->getPost('shift_clinician_id');
        $newShiftID = $this->request->getPost('new_shift_id');

        $shiftClinician = $this->shiftCliniciansModel->find($shiftClinicianID);
        if (!$shiftClinician) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Clinician record not found.']);
        }

        $oldShift = $this->shiftsModel->find($shiftClinician['shift_id']);
        $newShift = $this->shiftsModel->find($newShiftID);

        if (!$newShift || $newShift['client_id'] != $facilityId) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Target shift not found or unauthorized.']);
        }

        if ($this->shiftCliniciansModel->update($shiftClinicianID, ['shift_id' => $newShiftID])) {
            return $this->response->setJSON([
                'success' => 1,
                'message' => 'Clinician transferred successfully.',
                'message_header' => 'Transfer Success'
            ]);
        }

    }

    public function pcc_request()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        $clinicianId = $this->request->getPost('clinician_id');
        $shiftId = $this->request->getPost('shift_id');

        $facility = $this->facilityModel->find($facilityId);
        $clinician = (new \App\Models\CliniciansModel())->find($clinicianId);

        if (!$facility || !$clinician) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Record not found.']);
        }

        $supervisorName = $this->session->get('first_name') . ' ' . $this->session->get('last_name');
        
        $email = \Config\Services::email();
        $email->setTo('pjsangat@gmail.com');
        $email->setSubject('PCC Credential Request');
        
        $message = "Supervisor <strong>$supervisorName</strong> is requesting PCC credentials for clinician <strong>{$clinician['name']}</strong> at facility <strong>{$facility['company_name']}</strong>.";
        
        $email->setMessage($message);

        if ($email->send()) {
            // Update pcc_status to 5 (Requested)
            $this->shiftCliniciansModel->where('shift_id', $shiftId)
                ->where('clinician_id', $clinicianId)
                ->set(['pcc_status' => 5])
                ->update();

            return $this->response->setJSON(['success' => 1, 'message' => 'PCC request sent successfully to Handglove Admin.']);
        } else {
            return $this->response->setJSON(['success' => 0, 'message' => 'Failed to send PCC request. Please try again or contact support.']);
        }
    }
}
