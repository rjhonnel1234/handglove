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
use \Datetime;
use CodeIgniter\Files\File;

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

        $date = $this->request->getPost('date') ?: '2026-03-02';
        // $date = $this->request->getPost('date') ?: date("Y-m-d");
        $unitId = $this->request->getPost('unitID');

        $shifts = $this->shiftsModel
            ->select('tbl_shifts.*, tbl_shift_types.name as type_name, tbl_client_units.name as unit_name, 
                (SELECT count(tbl_client_shift_requests.id) FROM tbl_client_shift_requests WHERE tbl_client_shift_requests.client_id = tbl_shifts.client_id AND tbl_client_shift_requests.shift_id = tbl_shifts.id) as applicants, 
                (SELECT count(tbl_client_shift_requests.id) FROM tbl_client_shift_requests WHERE tbl_client_shift_requests.client_id = tbl_shifts.client_id AND tbl_client_shift_requests.shift_id = tbl_shifts.id AND status = 20) as accepted')
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
                ->where('tbl_shift_clinicians.status', 10)
                ->where('tbl_shift_clinicians.shift_status', 0)
                ->where('tbl_shift_clinicians.shift_id', $shift['id'])
                ->findAll();

            //get personnel/staff from shifts
            foreach ($shift['clinicians'] as &$clinician) {
                // Resolve Name
                if (!empty($clinician['clinician_id']) && $clinician['clinician_id'] > 0) {
                    $displayName = $clinician['clinician_name'];
                    $displayType = $clinician['clinician_type_name'];
                    $clockInIdField = 'clinician_id';
                    $clockInId = $clinician['clinician_id'];
                } else {
                    $displayName = $clinician['staff_first'] . ' ' . $clinician['staff_last'];
                    $displayType = $clinician['staff_type_name'];
                    $clockInIdField = 'personnel_id';
                    $clockInId = $clinician['personnel_id'];
                    $clinician['profile_pic_url'] =  base_url('assets/img/blank-img.png');

                }
                $clinician['display_name'] = $displayName;
                $clinician['display_type'] = $displayType;
                $clinician['type'] = $clinician['clinician_id'] > 0 ? 'clinician' : 'staff';
                $punchIn = '';
                if($clockInIdField == 'clinician_id'){
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
        }

        return $this->response->setJSON([
            'success' => 1,
            'message' => '',
            'data' => $shifts
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

        return $this->response->setJSON(['success' => 0, 'message' => 'Failed to transfer clinician.']);
    }
}
