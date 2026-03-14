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
use App\Models\FacilityOnboardingModel;
use App\Models\FacilityOnboardingSettingsModel;
use App\Models\UserModel;
use \Datetime;
use CodeIgniter\Files\File;

class Onboarding extends BaseController
{
    protected $facilityModel;
    protected $shiftsModel;
    protected $clinicianModel;
    protected $shiftCliniciansModel;
    protected $shiftTypesModel;
    protected $shiftRequestsModel;
    protected $facilityUnitsModel;
    protected $facilityOnboardingModel;
    protected $facilityOnboardingSettingsModel;
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
        $this->facilityOnboardingModel = new FacilityOnboardingModel();
        $this->facilityOnboardingSettingsModel = new FacilityOnboardingSettingsModel();
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

        $onboardingSettings = $this->facilityOnboardingSettingsModel->where('client_id', $this->session->get('facility_id'))->first();

        // Standardize Decoding
        $decodeFields = ['clock_in', 'access', 'clock_out_approval', 'task_delay', 'back_up_approval', 'phone', 'accepted_per_diem_network'];
        foreach ($decodeFields as $field) {
            $onboardingSettings[$field] = json_decode($onboardingSettings[$field] ?? '[]', true);
        }

        if (isset($onboardingSettings['average_census'])) {
            $census = explode(':', $onboardingSettings['average_census']);
            $onboardingSettings['average_census_1'] = $census[0] ?? '';
            $onboardingSettings['average_census_2'] = $census[1] ?? '';
        } else {
            $onboardingSettings['average_census_1'] = '';
            $onboardingSettings['average_census_2'] = '';
        }

        $data = [
            'session' => $this->session,
            'facility' => $facility,
            'onboardingSettings' => $onboardingSettings,
            'page' => 'onboarding'
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
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-switch-toggle',
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
                'https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-datepicker.js',
                ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                ASSETS_URL . 'js/components/notifications.min.js',
                ASSETS_URL . 'js/plugins/toastr.min.js',
                ASSETS_URL . 'js/pages/facility_onboarding.min.js',
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

        $postData = $this->request->getPost();
        $file = $this->request->getFile('pdf');

        $rules = [
            'name' => [
                'label' => 'Name',
                'rules' => 'required'
            ],
            'youtube_link' => [
                'label' => 'Youtube Link',
                'rules' => 'permit_empty|valid_url_strict'
            ]
        ];

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $rules['pdf'] = 'uploaded[pdf]|mime_in[pdf,application/pdf]|max_size[pdf,5120]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => 0,
                'message_header' => 'Onboarding',
                'message' => implode('<br>', $this->validator->getErrors())
            ]);
        }

        $item = [
            'name' => $postData['name'],
            'description' => $postData['description'],
            'command' => $postData['command'],
            'youtube_link' => $postData['youtube_link'],
        ];

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $onboarding = $this->facilityOnboardingModel->find($postData['onboardingID']);
            if (!empty($onboarding) && !empty($onboarding['filename'])) {
                @unlink($onboarding['filename']);
            }
            $item['filename'] = WRITEPATH . 'uploads/' . $file->store();
        }

        $this->facilityOnboardingModel->update($postData['onboardingID'], $item);

        return $this->response->setJSON([
            'success' => 1,
            'message_header' => 'Onboarding',
            'message' => 'File updated successfully.'
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

        $postData = $this->request->getPost();
        $file = $this->request->getFile('pdf');

        $rules = [
            'name' => [
                'label' => 'Name',
                'rules' => 'required'
            ],
            'youtube_link' => [
                'label' => 'Youtube Link',
                'rules' => 'permit_empty|valid_url_strict'
            ]
        ];

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $rules['pdf'] = [
                'label' => 'PDF',
                'rules' => 'uploaded[pdf]|mime_in[pdf,application/pdf]|max_size[pdf,5120]'
            ];
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => 0,
                'message_header' => 'Onboarding',
                'message' => implode('<br>', $this->validator->getErrors())
            ]);
        }

        $item = [
            'name' => $postData['name'],
            'description' => $postData['description'],
            'client_id' => $facilityId,
            'command' => $postData['command'],
            'youtube_link' => $postData['youtube_link'],
        ];

        $this->facilityOnboardingModel->save($item);
        $onboardingID = $this->facilityOnboardingModel->getInsertID();

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $filepath = WRITEPATH . 'uploads/' . $file->store();
            $this->facilityOnboardingModel->update($onboardingID, ['filename' => $filepath]);
        }

        return $this->response->setJSON([
            'success' => 1,
            'message_header' => 'Onboarding',
            'message' => 'File added successfully.'
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

        $onboardingFiles = $this->facilityOnboardingModel->where('client_id', $facilityId)->findAll();
        $formattedData = [];

        foreach ($onboardingFiles as $file) {
            $formattedData[] = [
                'name' => $file['name'],
                'pdf' => !empty($file['filename']) ? sprintf('<div class="text-center"><a href="%s" target="_blank" style="font-size: 25px;color: red;"><i class="fas fa-file-pdf"></i></a></div>', base_url('facility/profile/' . $file['client_id'] . '/onboarding/' . $file['id'] . '/pdf')) : '',
                'youtube' => !empty($file['youtube_link']) ? sprintf('<div class="text-center"><a href="%s" target="_blank" style="font-size: 25px;color: red;"><i class="fab fa-youtube"></i></a></div>', $file['youtube_link']) : '',
                'action' => sprintf(
                    '<div class="text-center"><a href="javascript:;" data-id="%s" class="update-onboarding btn btn-blue pl-2 pr-2 pt-1 pb-1" title="Update"><i class="fa fa-edit"></i></a> <a href="%s" target="_blank" class="btn pl-2 pr-2 pt-1 pb-1" title="View"><i class="fa fa-eye"></i></a></div>',
                    $file['id'],
                    base_url('facility/profile/' . $file['client_id'] . '/onboarding/' . $file['id'])
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'message' => '',
            'data' => $formattedData
        ]);
    }

    public function update_settings()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $postData = $this->request->getPost();
        
        $rules = [
            'average_census.*' => [
                'label' => 'Average Census',
                'rules' => 'permit_empty|numeric'
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => 0,
                'message_header' => 'Onboarding',
                'message' => implode('<br>', $this->validator->getErrors())
            ]);
        }

        $fields = ['clock_in', 'access', 'clock_out_approval', 'task_delay', 'back_up_approval', 'phone', 'accepted_per_diem_network'];
        
        $censusInput = $this->request->getPost('average_census');
        $averageCensus = ($censusInput && count($censusInput) == 2) ? implode(':', $censusInput) : null;

        $item = [
            'timezone' => $postData['timezone'],
            'client_id' => $facilityId,
            'allow_overtime' => $postData['allow_overtime'] ?? 0,
            'total_beds' => $postData['total_beds'] ?? null,
            'average_census' => $averageCensus,
            'average_rate' => $postData['average_rate'] ?? null,
            'bonus' => $postData['bonus'] ?? null,
        ];
        
        foreach ($fields as $field) {
            $item[$field] = isset($postData[$field]) ? json_encode($postData[$field]) : json_encode([]);
        }

        $onboardingSettings = $this->facilityOnboardingSettingsModel->where('client_id', $facilityId)->first();
        if (empty($onboardingSettings)) {
            $this->facilityOnboardingSettingsModel->save($item);
        } else {
            $item['updated_when'] = date("Y-m-d H:i:s");
            $this->facilityOnboardingSettingsModel->update($onboardingSettings['id'], $item);
        }

        return $this->response->setJSON([
            'success' => 1,
            'message_header' => 'Onboarding',
            'message' => 'Settings updated successfully.'
        ]);
    }

    public function edit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $onboardingId = $this->request->getPost('onboardingID');
        $onboarding = $this->facilityOnboardingModel->find($onboardingId);

        if (!empty($onboarding) && $onboarding['client_id'] == $this->session->get('facility_id')) {
            return $this->response->setJSON([
                'success' => 1,
                'onboarding' => $onboarding
            ]);
        }

        return $this->response->setJSON(['success' => 0, 'message' => 'Onboarding record not found.']);
    }
}
