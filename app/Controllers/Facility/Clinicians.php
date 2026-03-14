<?php

namespace App\Controllers\Facility;

use App\Controllers\BaseController;
use App\Models\CliniciansModel;
use App\Models\ClinicianTypesModel;
use App\Models\FacilityModel;
use App\Models\ShiftRequestsModel;
use App\Models\FacilityOnboardingSettingsModel;
use CodeIgniter\Files\File;

class Clinicians extends BaseController
{
    protected $clinicianModel;
    protected $clinicianTypesModel;
    protected $facilityModel;
    protected $session;

    public function __construct()
    {
        $this->clinicianModel = new CliniciansModel();
        $this->clinicianTypesModel = new ClinicianTypesModel();
        $this->facilityModel = new FacilityModel();
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
            'clinicianTypes' => $this->clinicianTypesModel->where('status', 1)->findAll(),
            'page' => 'clinicians'
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
                ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                ASSETS_URL . 'js/plugins/toastr.min.js',
                ASSETS_URL . 'js/pages/facility_clinicians.min.js',
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

        $clinicianQuery = $this->clinicianModel
            ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name')
            ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'inner');
            // ->where('tbl_clinicians.client_id', $facilityId);

        $type = $this->request->getVar('type');
        if ($type === 'request') {
            // $shiftType = $this->request->getVar('shiftType');
            $shiftId = $this->request->getVar('shift');
            
            // $clinicianQuery->where('tbl_clinicians.type', $shiftType);
            $clinicianQuery->where("tbl_clinicians.id NOT IN (SELECT clinician_id FROM tbl_client_shift_requests WHERE client_id = $facilityId AND shift_id = $shiftId)");
        }

        $clinicians = $clinicianQuery->findAll();
        $formattedData = [];

        foreach ($clinicians as $clinician) {
            $item = [
                'details' => sprintf('<div><strong>%s</strong><br>%s<br>%s</div>', $clinician['name'], $clinician['email'], $clinician['contact_number']),
                'type' => $clinician['type_name'],
                'level' => $this->clinicianModel->tier_mapping[$clinician['tier']],
                'status' => $clinician['status'] == 1 ? 'Active' : 'Inactive',
                'action' => sprintf(
                    '<div class="text-center"><a href="javascript:;" data-id="%s" class="view-clinician btn btn-yellow pl-2 pr-2 pt-1 pb-1" title="View Details"><i class="fa fa-search"></i></a> <a href="javascript:;" class="edit-clinician btn btn-danger pl-2 pr-2 pt-1 pb-1" title="Remove"><i class="fa fa-trash"></i></a></div>',
                    $clinician['id']
                )
            ];

            if ($type === 'request') {
                $shiftId = $this->request->getVar('shift');
                $item['action'] = sprintf(
                    '<div class="text-center"><a href="javascript:;" data-shift-id="%s" data-id="%s" class="request-clinician btn thm-btn pl-2 pr-2 pt-1 pb-1" title=""><i class="fa fa-plust"></i> Request</a></div>',
                    $shiftId,
                    $clinician['id']
                );
            }
            $formattedData[] = $item;
        }

        return $this->response->setJSON([
            'success' => 1,
            'message' => '',
            'data' => $formattedData
        ]);
    }

    public function get()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $clinicianId = $this->request->getPost('clinicianID');
        if (!$clinicianId) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Missing clinician ID.']);
        }

        $clinician = $this->clinicianModel
            ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name')
            ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'inner')
            ->find($clinicianId);

        if (!$clinician) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Clinician not found.']);
        }

        $clinician['status_label'] = $clinician['status'] == 1 ? 'Active' : 'Inactive';
        $clinician['level'] = $this->clinicianModel->tier_mapping[$clinician['tier']];

        return $this->response->setJSON([
            'success' => 1,
            'message' => '',
            'clinician' => $clinician
        ]);
    }

    public function online_list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $session = session();
        $userModel = new \App\Models\UserModel();
        $me = $userModel->find($session->get('id'));
        
        $lat1 = ($me && !empty($me['latitude'])) ? $me['latitude'] : 34.0522; // Fallback to CA
        $lng1 = ($me && !empty($me['longitude'])) ? $me['longitude'] : -118.2437;

        $facilityId = $this->session->get('facility_id');
        $shiftId = $this->request->getGet('shift_id');
        $onlineUsers = $userModel
            ->select('tbl_users.id as user_id, tbl_users.latitude, tbl_users.longitude, tbl_clinicians.name, tbl_clinicians.address, tbl_clinicians.profile_pic_url, tbl_clinicians.company_worked, tbl_clinician_types.name as type_name, tbl_clinicians.id as clinician_id')
            ->join('tbl_clinicians', 'tbl_clinicians.email = tbl_users.email', 'inner')
            ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'inner')
            ->where('tbl_users.online_status', 1)
            ->where("tbl_clinicians.id NOT IN (SELECT clinician_id FROM tbl_client_shift_requests WHERE client_id = $facilityId AND shift_id = $shiftId)")
            ->findAll();
        


        $data = [];
        foreach ($onlineUsers as $user) {
            $distance = $this->calculateDistance($lat1, $lng1, $user['latitude'] ?? 0, $user['longitude'] ?? 0);
            $time = round($distance * 2); // Simple estimate: 2 mins per mile

            $data[] = [
                'name' => $user['name'],
                'type' => $user['type_name'],
                'company' => $user['company_worked'] ?: 'Independent',
                'profile_pic' => $user['profile_pic_url'] ?: base_url('assets/img/blank-img.png'),
                'distance' => round($distance, 1),
                'time_away' => $time > 0 ? $time : 1,
                'clinician_id' => $user['clinician_id']
            ];
        }

        // Sort by distance
        usort($data, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $shiftId = $this->request->getGet('shift_id');
        $replacingClinicianId = $this->request->getGet('replacing_clinician_id');
        $pendingRequests = [];
        
        if ($shiftId && $replacingClinicianId) {
            $shiftRequestsModel = new ShiftRequestsModel();
            $pendingRequests = $shiftRequestsModel
                ->select('tbl_client_shift_requests.*, tbl_clinicians.name as clinician_name')
                ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_client_shift_requests.clinician_id', 'inner')
                ->join('tbl_shift_clinicians', 'tbl_shift_clinicians.id = tbl_client_shift_requests.replacing_clinician_id', 'left')
                ->join('tbl_client_personnel as replacing_staff', 'replacing_staff.id = tbl_shift_clinicians.personnel_id', 'left')
                ->where([
                    'tbl_client_shift_requests.shift_id' => $shiftId,
                    'tbl_client_shift_requests.replacing_clinician_id' => $replacingClinicianId,
                    'tbl_client_shift_requests.status' => 10,
                    'tbl_client_shift_requests.from_callout' => 1
                ])->findAll();
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $data,
            'pending_requests' => $pendingRequests
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 3958.8; // miles
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }

    public function request_shift()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $shiftId = $this->request->getPost('shift_id');
        $clinicianId = $this->request->getPost('clinician_id');
        $replacingClinicianId = $this->request->getPost('replacing_clinician_id');
        $fromCallout = $this->request->getPost('from_callout') ?? 0;

        if (!$shiftId || !$clinicianId) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Missing required parameters.']);
        }

        $onboardingModel = new FacilityOnboardingSettingsModel();
        $onboarding = $onboardingModel->where('client_id', $facilityId)->first();
        $bonus = ($onboarding && !empty($onboarding['bonus'])) ? $onboarding['bonus'] : 0;

        $shiftRequestsModel = new ShiftRequestsModel();
        
        // Check if request already exists for this candidate on this shift
        $existing = $shiftRequestsModel->where([
            'shift_id' => $shiftId,
            'clinician_id' => $clinicianId
        ])->first();

        if ($existing) {
            return $this->response->setJSON(['success' => 0, 'message' => 'A request has already been sent to this clinician for this shift.']);
        }

        // Enforce one pending request per clinician on the shift list for callouts
        if ($fromCallout && $replacingClinicianId) {
            $pendingForReplaced = $shiftRequestsModel->where([
                'shift_id' => $shiftId,
                'replacing_clinician_id' => $replacingClinicianId,
                'status' => 10
            ])->first();

            if ($pendingForReplaced) {
                return $this->response->setJSON(['success' => 0, 'message' => 'There is already a pending request to replace this clinician. Please cancel it first if you wish to request someone else.']);
            }
        }

        $data = [
            'client_id' => $facilityId,
            'shift_id' => $shiftId,
            'clinician_id' => $clinicianId,
            'replacing_clinician_id' => $replacingClinicianId,
            'status' => 10, // Awaiting response
            'from_callout' => $fromCallout,
            'bonus' => $bonus
        ];

        if ($shiftRequestsModel->insert($data)) {
            return $this->response->setJSON(['success' => 1, 'message' => 'Shift request sent successfully.']);
        }

        return $this->response->setJSON(['success' => 0, 'message' => 'Failed to send shift request.']);
    }

    public function cancel_request()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $requestId = $this->request->getPost('request_id');
        if (!$requestId) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Missing request ID.']);
        }

        $shiftRequestsModel = new ShiftRequestsModel();
        if ($shiftRequestsModel->delete($requestId)) {
            return $this->response->setJSON(['success' => 1, 'message' => 'Request cancelled successfully.']);
        }

        return $this->response->setJSON(['success' => 0, 'message' => 'Failed to cancel request.']);
    }
}
