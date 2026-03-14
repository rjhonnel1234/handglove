<?php
namespace App\Controllers\Clinician;
use App\Controllers\BaseController;

use App\Models\CliniciansModel;
use App\Models\FacilityModel;
use App\Models\ShiftsModel;
use App\Models\ShiftTypesModel;
use App\Models\ShiftRequestsModel;
use App\Models\ShiftCliniciansModel;
use App\Models\FacilityUnitsModel;
use App\Models\ShiftsTimekeepingModel;
use App\Models\ClientRatingsModel;
use App\Models\InvoicesModel;
use App\Models\UserModel;
use App\Models\ShiftClinicianUpdatesModel;
use \Datetime;
use CodeIgniter\Files\File;

class Shifts extends BaseController
{
    public function get_offers()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $session = session();
        $clinModel = new CliniciansModel();
        $profileData = $clinModel->where('email', $session->get('email'))->first();

        if (empty($profileData)) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Profile not found.']);
        }

        $shiftRequestsModel = new ShiftRequestsModel();
        $offers = $shiftRequestsModel
            ->select('tbl_client_shift_requests.*, tbl_shifts.start_date, tbl_shifts.shift_start_time, tbl_shifts.shift_end_time, tbl_shifts.rate, tbl_clients.company_name, tbl_clients.company_logo, tbl_clients.company_address as address,tbl_clients.zip_code as zip, tbl_client_units.name as unit_name, tbl_shift_clinicians.status as shift_clinician_status')
            ->join('tbl_shifts', 'tbl_shifts.id = tbl_client_shift_requests.shift_id', 'inner')
            ->join('tbl_clients', 'tbl_clients.id = tbl_client_shift_requests.client_id', 'inner')
            ->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'inner')
            ->join('tbl_shift_clinicians', 'tbl_shift_clinicians.shift_id = tbl_shifts.id AND tbl_shift_clinicians.clinician_id = tbl_client_shift_requests.clinician_id', 'left')
            ->where('tbl_client_shift_requests.clinician_id', $profileData['id'])
            ->whereIn('tbl_client_shift_requests.status', [10, 20])
            ->where('tbl_shift_clinicians.status', 0)
            ->where('tbl_client_shift_requests.from_callout', 1)
            ->findAll();

        foreach ($offers as &$offer) {
            $offer['shift_start_time_formatted'] = date("h:i A", strtotime($offer['shift_start_time']));
            $offer['shift_end_time_formatted'] = date("h:i A", strtotime($offer['shift_end_time']));
            $offer['start_date_formatted'] = date("D, M d", strtotime($offer['start_date']));
            $offer['distance'] = "5.8 miles"; // Placeholder matching the image
        }

        return $this->response->setJSON([
            'success' => 1,
            'offers' => $offers
        ]);
    }

    public function respond_to_offer()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $requestId = $this->request->getPost('request_id');
        $action = $this->request->getPost('action'); // 'accept' or 'decline'

        $shiftRequestsModel = new ShiftRequestsModel();
        $request = $shiftRequestsModel->find($requestId);

        if (!$request) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Request not found.']);
        }

        if ($action == 'accept') {
            // Update request status
            $shiftRequestsModel->update($requestId, ['status' => 20]);

            // Assign clinician to the shift slot
            $shiftCliniciansModel = new ShiftCliniciansModel();
            //if accepted, set the field from_callout to 1, might need to add from_callout on shiftCliniciansModel
            if ($request['replacing_clinician_id']) {
                $shiftCliniciansModel->update($request['replacing_clinician_id'], [
                    'clinician_id' => $request['clinician_id'],
                    'personnel_id' => 0,
                    'from_callout' => 1,
                    'status' => 0, // Pending
                    'shift_status' => 0 // Not started
                ]);
            }

            return $this->response->setJSON(['success' => 1, 'message' => 'Offer accepted.']);
        } else {
            // Decline
            $shiftRequestsModel->update($requestId, ['status' => 100]);
            return $this->response->setJSON(['success' => 1, 'message' => 'Offer declined.']);
        }
    }

    public function send_update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $shiftClinicianId = $this->request->getPost('shift_clinician_id');
        $statusText = $this->request->getPost('status_text');
        $locationText = $this->request->getPost('location_text');
        $minutesAway = $this->request->getPost('minutes_away');
        $isArrival = $this->request->getPost('is_arrival');

        $updateModel = new ShiftClinicianUpdatesModel();
        $updateModel->insert([
            'shift_clinician_id' => $shiftClinicianId,
            'status_text' => $statusText,
            'location_text' => $locationText,
            'minutes_away' => $minutesAway,
            'is_arrival' => $isArrival ? 1 : 0
        ]);

        if ($isArrival) {
            $shiftCliniciansModel = new ShiftCliniciansModel();
            $shiftCliniciansModel->update($shiftClinicianId, [
                'status' => 10 // Active/Confirmed arrival
            ]);
        }

        return $this->response->setJSON(['success' => 1, 'message' => 'Update sent successfully.']);
    }

    public function index()
    {
        $session = session();
        $data['session'] = $session;
        if( is_null($session->get('isLoggedIn')) || $session->get('isLoggedIn') != 1){
            return redirect()->to('/');
        }else{
            $clinModel = new CliniciansModel;

            $data['profileData'] = $clinModel
                                        ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name')
                                        ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'INNER')
                                        ->where('tbl_clinicians.email', session()->get('email'))
                                        ->first();

            $objShiftClinician = new ShiftCliniciansModel;
            $data['profileData']['total_shifts'] = $objShiftClinician->where('clinician_id', $data['profileData']['id'])->where('status', 10)->countAllResults();

            $objTimekeeping = new ShiftsTimekeepingModel;
            $stats = $objTimekeeping->getStats($data['profileData']['id']);
            $data['profileData']['attendance_percentage'] = $stats['attendance'];
            $data['profileData']['lateness_percentage'] = $stats['lateness'];

            // PAGE HEAD PROCESSING
            return view('components/header', array(
                'title' => 'Handglove',
                'description' => '',
                'url' => BASE_URL,
                'keywords' => '',
                'meta' => array(
                    'title' => 'Handglove',
                    'description' => '',
                    'image' => IMG_URL . ''
                ),
                'styles' => array(
                    'plugins/font_awesome',
                    'plugins/datatables',
                    COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                    COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                    COMPILED_ASSETS_PATH . 'css/components/owl',
                    COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                    COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
                    COMPILED_ASSETS_PATH . 'css/components/bootstrap-datepicker',
                    COMPILED_ASSETS_PATH . 'css/components/global',
                    COMPILED_ASSETS_PATH . 'css/components/animations',
                    COMPILED_ASSETS_PATH . 'css/components/buttons',
                    COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                    COMPILED_ASSETS_PATH . 'css/components/footer',
                    COMPILED_ASSETS_PATH . 'css/pages/profile'
                ),
                'session' => $data['session']
            ))
            .view('profile/shifts', $data)
            .view('components/scripts_render', array(
                'scripts' => array(
                    'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                        'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                        'crossorigin' => 'anonymous'
                    ),
                    'https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js',
                    'https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js',
                    ASSETS_URL . 'js/plugins/popper.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                    ASSETS_URL . 'js/components/global.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-datepicker.js',
                    ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                    ASSETS_URL . 'js/components/navigation_bar.min.js',
                    ASSETS_URL . 'js/pages/profile_shifts.min.js',
                )
            ))
            .view('components/footer');
        }
    }


    public function list(){

        $data = [
            'success' => 0, 
            'message' => 'Invalid requests.'
        ];

        $session = session();
        if( $session->get('isLoggedIn') == 1){
            if($this->request->isAJAX()){

                $clinModel = new CliniciansModel;
                $profileData = $clinModel
                                        ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name')
                                        ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'INNER')
                                        ->where('tbl_clinicians.email', session()->get('email'))
                                        ->first();

                if(!empty($profileData)){
                    $objShiftClinician = new ShiftCliniciansModel;
                    $shifts = $objShiftClinician
                                    ->select('tbl_clients.company_name,tbl_clients.company_logo, tbl_shifts.*, tbl_client_units.name as unit_name')
                                    ->join('tbl_clients', 'tbl_clients.id = tbl_shift_clinicians.client_id', 'INNER')
                                    ->join('tbl_shifts', 'tbl_shifts.id = tbl_shift_clinicians.shift_id', 'INNER')
                                    ->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'INNER')
                                    ->where('tbl_shifts.start_date', $this->request->getPost('date'))
                                    ->where('tbl_shift_clinicians.clinician_id', $profileData['id'])
                                    ->where('tbl_shift_clinicians.shift_status', 0)
                                    ->where('tbl_shift_clinicians.status', 10)
                                    ->findAll();

                    $shiftsArr = [];

                    $objTimekeeping = new ShiftsTimekeepingModel;

                    foreach($shifts as $shift){
                        $shift['shift_end_time_formatted'] = date("h:i A", strtotime($shift['shift_end_time']));
                        $shift['shift_start_time_formatted'] = date("h:i A", strtotime($shift['shift_start_time']));

                        $timeKeeping['punchIn'] = $objTimekeeping
                                                    ->where('shift_id', $shift['id'])
                                                    ->where('clinician_id', $profileData['id'])
                                                    ->where('punch_type', 10)
                                                    ->orderBy('punch_datetime', 'asc')
                                                    ->first();

                        if(!empty($timeKeeping['punchIn'])){
                            $timeKeeping['punchIn']['punch_datetime'] = date("M d, Y h:i A", strtotime($timeKeeping['punchIn']['punch_datetime']));
                        }

                        $timeKeeping['punchOut'] = $objTimekeeping
                                                    ->where('shift_id', $shift['id'])
                                                    ->where('clinician_id', $profileData['id'])
                                                    ->where('punch_type', 20)
                                                    ->orderBy('punch_datetime', 'desc')
                                                    ->first();
                        if(!empty($timeKeeping['punchOut'])){
                            $timeKeeping['punchOut']['punch_datetime'] = date("M d, Y h:i A", strtotime($timeKeeping['punchOut']['punch_datetime']));
                        }

                        $shift['timekeeping'] = $timeKeeping;

                        $shiftsArr[] = $shift;
                    }

                    $data['data'] = $shiftsArr;

                }

            }

        }


        echo json_encode($data);
        exit();
    }

    public function clockIn(){

        $data = [
            'success' => 0, 
            'message' => 'Invalid requests.'
        ];

        $session = session();
        if( $session->get('isLoggedIn') == 1){
            if($this->request->isAJAX()){

                $clinModel = new CliniciansModel;
                $profileData = $clinModel
                                        ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name')
                                        ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'INNER')
                                        ->where('tbl_clinicians.email', session()->get('email'))
                                        ->first();

                if(!empty($profileData)){
                    $objTimekeeping = new ShiftsTimekeepingModel;


                    $punchInDateTime = date("Y-m-d H:i:s");

                    $item = [
                        'shift_id' => $this->request->getPost('shiftID'),
                        'clinician_id' => $profileData['id'],
                        'punch_datetime' => $punchInDateTime,
                        'punch_type' => 10,
                        'ip_address' => $this->request->getPost('ip')
                    ];
                    $id = $objTimekeeping->save($item);
                    if($id){
                        $data['success'] = 1;
                        $data['message'] = 'Punch In successful.';

                        $data['data']['time'] = $punchInDateTime;
                    }

                }

            }
        }


        echo json_encode($data);
        exit();
    }

    public function clockOut(){

        $data = [
            'success' => 0, 
            'message' => 'Invalid requests.'
        ];

        $session = session();
        if( $session->get('isLoggedIn') == 1){
            if($this->request->isAJAX()){

                $clinModel = new CliniciansModel;
                $profileData = $clinModel
                                        ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name')
                                        ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'INNER')
                                        ->where('tbl_clinicians.email', session()->get('email'))
                                        ->first();

                if(!empty($profileData)){
                    $objTimekeeping = new ShiftsTimekeepingModel;


                    $punchOutDateTime = date("Y-m-d H:i:s");

                    $shiftID = $this->request->getPost('shiftID');
                    $item = [
                        'shift_id' => $shiftID,
                        'clinician_id' => $profileData['id'],
                        'punch_datetime' => $punchOutDateTime,
                        'punch_type' => 20,
                        'reference' =>  $this->request->getPost('reference'),
                        'ip_address' => $this->request->getPost('ip')
                    ];
                    $id = $objTimekeeping->save($item);
                    if($id){
                        $data['success'] = 1;
                        $data['message'] = 'Punch Out successful.';

                        $data['data']['time'] = $punchOutDateTime;

                        //generate invoice
                        $punchIn = $objTimekeeping
                                            ->where('shift_id', $shiftID)
                                            ->where('clinician_id', $profileData['id'])
                                            ->where('punch_type', 10)
                                            ->orderBy('punch_datetime', 'asc')
                                            ->first();

                        if(!empty($punchIn)){
                            $objShifts = new ShiftsModel();
                            $shiftDetails = $objShifts->find($shiftID);

                            if(!empty($shiftDetails)){
                                $start = new DateTime($punchIn['punch_datetime']);
                                $end = new DateTime($punchOutDateTime);
                                $interval = $start->diff($end);
                                
                                $totalHours = $interval->h + ($interval->days * 24) + ($interval->i / 60);
                                $rate = $shiftDetails['rate'];
                                $totalAmount = $totalHours * $rate;

                                $objInvoices = new InvoicesModel();
                                $objSettings = new \App\Models\SettingsModel();
                                $billingRate = $objSettings->getSetting('billing_rate', 1.00);

                                $invoiceItem = [
                                    'shift_id'     => $shiftID,
                                    'clinician_id' => $profileData['id'],
                                    'client_id'    => $shiftDetails['client_id'],
                                    'total_hours'  => $totalHours,
                                    'rate'         => $rate,
                                    'billing_rate' => $billingRate,
                                    'total_amount' => $totalHours * $rate * $billingRate,
                                    'status'       => 10 // Pending
                                ];
                                $objInvoices->save($invoiceItem);
                            }
                        }
                    }

                }

            }
        }


        echo json_encode($data);
        exit();
    }

    public function submitFeedback()
    {
        $data = [
            'success' => 0,
            'message' => 'Invalid requests.'
        ];

        $session = session();
        if ($session->get('isLoggedIn') == 1) {
            if ($this->request->isAJAX()) {
                $clinModel = new CliniciansModel();
                $profileData = $clinModel
                    ->where('email', $session->get('email'))
                    ->first();

                if (!empty($profileData)) {
                    $shiftID = $this->request->getPost('shiftID');
                    $objShifts = new ShiftsModel();
                    $shift = $objShifts->find($shiftID);

                    if ($shift) {
                        $objRatings = new ClientRatingsModel();
                        $cleanliness = $this->request->getPost('cleanliness');
                        $workEnvironment = $this->request->getPost('work_environment');
                        $toolsNeeded = $this->request->getPost('tools_needed');
                        $average = ($cleanliness + $workEnvironment + $toolsNeeded) / 3;

                        $item = [
                            'shift_id' => $shiftID,
                            'clinician_id' => $profileData['id'],
                            'client_id' => $shift['client_id'],
                            'cleanliness' => $cleanliness,
                            'work_environment' => $workEnvironment,
                            'tools_needed' => $toolsNeeded,
                            'average' => $average,
                            'comment' => $this->request->getPost('comment'),
                            'datetime_added' => date("Y-m-d H:i:s")
                        ];
                        
                        if ($objRatings->save($item)) {
                            $data['success'] = 1;
                            $data['message'] = 'Thank you for your feedback!';
                        }
                    }
                }
            }
        }

        echo json_encode($data);
        exit();
    }
}
