<?php

namespace App\Controllers\Facility;

use App\Controllers\BaseController;
use App\Models\CliniciansModel;
use App\Models\CredentialTypesModel;
use App\Models\ClinicianCredentialsModel;
use App\Models\UserModel;
use App\Models\FacilityModel;
use App\Models\FacilityUnitsModel;
use \Datetime;
use CodeIgniter\Files\File;

class Units extends BaseController
{
    protected $clinicianModel;
    protected $credentialTypesModel;
    protected $clinicianCredentialsModel;
    protected $userModel;
    protected $facilityModel;
    protected $facilityUnitsModel;
    protected $clientPersonnelModel;
    protected $session;

    public function __construct()
    {
        $this->clinicianModel = new CliniciansModel();
        $this->credentialTypesModel = new CredentialTypesModel();
        $this->clinicianCredentialsModel = new ClinicianCredentialsModel();
        $this->userModel = new UserModel();
        $this->facilityModel = new FacilityModel();
        $this->facilityUnitsModel = new FacilityUnitsModel();
        $this->clientPersonnelModel = new \App\Models\ClientPersonnelModel();
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
            'personnel' => $this->clientPersonnelModel->where('client_id', $this->session->get('facility_id'))->where('status', 1)->where('type', 6)->findAll(),
            'page' => 'units'
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
                ASSETS_URL . 'js/components/notifications.min.js',
                ASSETS_URL . 'js/plugins/toastr.min.js',
                ASSETS_URL . 'js/pages/facility_units.min.js',
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

        $units = $this->facilityUnitsModel->where('client_id', $facilityId)->findAll();
        $formattedData = [];

        foreach ($units as $unit) {
            $formattedData[] = [
                'name' => '<div><strong>' . $unit['name'] . '</strong></div>',
                'census' => '<div class="text-center">' . $unit['census'] . '</div>',
                'description' => $unit['description'],
                'action' => sprintf(
                    '<div class="text-center"><a href="javascript:;" data-id="%s" class="view-unit btn btn-yellow pl-2 pr-2 pt-1 pb-1" title="View Details"><i class="fa fa-search"></i></a></div>',
                    $unit['id']
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'message' => '',
            'data' => $formattedData
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

        $validation = \Config\Services::validation();
        $rules = [
            'name' => [
                'rules' => 'required',
                'label' => 'Name'
            ],
            'census.*' => [
                'rules' => 'required',
                'label' => 'Census'
            ],
            'unit_manager_id' => [
                'rules' => 'required',
                'label' => 'Unit Manager'
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => 0,
                'message_header' => 'Units',
                'message' => implode('<br>', $validation->getErrors())
            ]);
        }

        $censusInput = $this->request->getPost('census');
        $census = implode(':', $censusInput);

        $item = [
            'client_id' => $facilityId,
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'unit_manager_id' => $this->request->getPost('unit_manager_id'),
            'census' => $census,
        ];

        $this->facilityUnitsModel->save($item);

        return $this->response->setJSON([
            'success' => 1,
            'message_header' => 'Units',
            'message' => 'Successfully added.'
        ]);
    }

    public function update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message_header' => 'Units', 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        $unitId = $this->request->getPost('unitID');

        if ($facilityId == 0 || !$unitId) {
            return $this->response->setJSON(['success' => 0, 'message_header' => 'Units', 'message' => 'Invalid parameters.']);
        }

        $validation = \Config\Services::validation();
        $rules = [
            'name' => [
                'rules' => 'required',
                'label' => 'Name'
            ],
            'census.*' => [
                'rules' => 'required',
                'label' => 'Census'
            ],
            'unit_manager_id' => [
                'rules' => 'required',
                'label' => 'Unit Manager'
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => 0,
                'message_header' => 'Units',
                'message' => implode('<br>', $validation->getErrors())
            ]);
        }

        $censusInput = $this->request->getPost('census');
        $census = implode(':', $censusInput);

        $item = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'unit_manager_id' => $this->request->getPost('unit_manager_id'),
            'census' => $census,
        ];

        $this->facilityUnitsModel->update($unitId, $item);

        return $this->response->setJSON([
            'success' => 1,
            'message_header' => 'Units',
            'message' => 'Successfully updated.'
        ]);
    }

    public function get()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $unitId = $this->request->getPost('unitID');
        if (!$unitId) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Missing unit ID.']);
        }

        $unit = $this->facilityUnitsModel->find($unitId);
        if (!$unit) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unit not found.']);
        }

        $census = explode(':', $unit['census']);
        $unit['census_1'] = $census[0] ?? 0;
        $unit['census_2'] = $census[1] ?? 0;

        return $this->response->setJSON([
            'success' => 1,
            'message' => '',
            'unit' => $unit
        ]);
    }
}
