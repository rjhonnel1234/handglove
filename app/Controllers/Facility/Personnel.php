<?php

namespace App\Controllers\Facility;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\FacilityModel;
use App\Models\UserTypesModel;
use App\Models\ClientPersonnelModel;
use App\Models\ClinicianTypesModel;

class Personnel extends BaseController
{
    protected $userModel;
    protected $facilityModel;
    protected $userTypesModel;
    protected $clientPersonnelModel;
    protected $clinicianTypesModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->facilityModel = new FacilityModel();
        $this->userTypesModel = new UserTypesModel();
        $this->clientPersonnelModel = new ClientPersonnelModel();
        $this->clinicianTypesModel = new ClinicianTypesModel();
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
            'userTypes' => $this->userTypesModel->findAll(),
            'clinicianTypes' => $this->clinicianTypesModel->where('status', 1)->findAll(),
            'page' => 'personnel'
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
                'https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js',
                'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
                ASSETS_URL . 'js/plugins/popper.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                ASSETS_URL . 'js/components/global.min.js',
                ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                ASSETS_URL . 'js/plugins/toastr.min.js',
                ASSETS_URL . 'js/components/notifications.min.js',
                ASSETS_URL . 'js/pages/facility_personnel.min.js',
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

        $personnel = $this->clientPersonnelModel
            ->select('tbl_client_personnel.*, tbl_user_types.name as type_name')
            ->join('tbl_user_types', 'tbl_user_types.id = tbl_client_personnel.type', 'inner')
            ->where('client_id', $facilityId)
            ->findAll();

        $formattedData = [];
        foreach ($personnel as $item) {
            $formattedData[] = [
                'name'    => sprintf('<div><strong>%s %s</strong></div>', $item['first_name'], $item['last_name']),
                'email'   => $item['email'],
                'contact' => $item['contact_number'],
                'type'    => $item['type_name'],
                'status'  => $item['status'] == 1 ? 'Active' : 'Inactive',
                'action'  => sprintf('<div class="text-center"><a href="javascript:;" data-id="%s" class="view-personnel btn btn-yellow pl-2 pr-2 pt-1 pb-1" title="View details"><i class="fa fa-search"></i></a></div>', $item['id'])
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'message' => '',
            'data'    => $formattedData
        ]);
    }

    public function get()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $personnelId = $this->request->getPost('personnelID');
        if (!$personnelId) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Missing ID.']);
        }

        $personnel = $this->clientPersonnelModel->find($personnelId);
        if ($personnel && $personnel['client_id'] == $this->session->get('facility_id')) {
            // If linked to a user record, fetch signature and password (hashed) if needed
            if ($personnel['user_id'] > 0) {
                $user = $this->userModel->find($personnel['user_id']);
                // if ($user) {
                //     $personnel['signature'] = $user['signature'];
                // }
            }
            return $this->response->setJSON(['success' => 1, 'personnel' => $personnel]);
        }

        return $this->response->setJSON(['success' => 0, 'message' => 'Personnel not found or unauthorized.']);
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
            'first_name' => [
                'label' => 'First Name',
                'rules' => 'required'
            ],
            'last_name' => [
                'label' => 'Last Name',
                'rules' => 'required'
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|is_unique[tbl_client_personnel.email]'
            ],
            'contact_number' => [
                'label' => 'Contact Number',
                'rules' => 'required'
            ],
            'signature' => [
                'label' => 'Signature',
                'rules' => 'required'
            ],
            'type' => [
                'label' => 'Type',
                'rules' => 'required'
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[5]'
            ]
        ];


        //if type is 7, remove password and signature rules
        if ($this->request->getPost('type') == 7 || $this->request->getPost('type') == '') {
            unset($rules['password']);
            unset($rules['signature']);
        }
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => 0,
                'message_header' => 'Personnel',
                'message' => $this->validator->getErrors()
            ]);
        }


        // Initialize item for ClientPersonnelModel
        $clientPersonnelItem = [
            'client_id'      => $facilityId,
            'first_name'     => $this->request->getPost('first_name'),
            'last_name'      => $this->request->getPost('last_name'),
            'email'          => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('contact_number'),
            'type'           => $this->request->getPost('type'),
            'clinician_type' => $this->request->getPost('clinician_type'),
            'status'         => $this->request->getPost('status')
        ];

        if ($this->request->getPost('type') != 7) {
            $clientPersonnelItem['signature'] = $this->request->getPost('signature');
        }
        // Save to ClientPersonnelModel first
        $this->clientPersonnelModel->save($clientPersonnelItem);
        $clientPersonnelId = $this->clientPersonnelModel->getInsertID();

        // If type is not 7, also add to Users model
        if ($this->request->getPost('type') != 7) {
            $userItem = [
                'facility_id'    => $facilityId,
                'first_name'     => $this->request->getPost('first_name'),
                'last_name'      => $this->request->getPost('last_name'),
                'email'          => $this->request->getPost('email'),
                'contact_number' => $this->request->getPost('contact_number'),
                'type'           => $this->request->getPost('type'),
                'status'         => $this->request->getPost('status'),
                'password'       => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'token'          => '',
                'token_active'   => 0
            ];

            $this->userModel->save($userItem);
            $newUserId = $this->userModel->getInsertID();

            // Link the user_id back to client_personnel
            $this->clientPersonnelModel->update($clientPersonnelId, ['user_id' => $newUserId]);
        }

        return $this->response->setJSON([
            'success'        => 1,
            'message_header' => 'Personnel',
            'message'        => 'Personnel successfully added.'
        ]);
    }

    public function update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        $personnelId = $this->request->getPost('personnelID');

        if ($facilityId == 0 || !$personnelId) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request or missing ID.']);
        }

        $validation = \Config\Services::validation();
        $rules = [
            'first_name' => [
                'label' => 'First Name',
                'rules' => 'required'
            ],
            'last_name' => [
                'label' => 'Last Name',
                'rules' => 'required'
            ],
            'email' => [
                'label' => 'Email',
                'rules' => "required|valid_email|is_unique[tbl_client_personnel.email,id,{$personnelId}]"
            ],
            'contact_number' => [
                'label' => 'Contact Number',
                'rules' => 'required'
            ],
            'signature' => [
                'label' => 'Signature',
                'rules' => 'required'
            ],
            'type' => [
                'label' => 'Type',
                'rules' => 'required'
            ]
        ];

        //if type is 7, remove password and signature rules
        if ($this->request->getPost('type') == 7 || $this->request->getPost('type') == '') {
            unset($rules['password']);
            unset($rules['signature']);
        }

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[5]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => 0,
                'message_header' => 'Personnel',
                'message' => $validation->getErrors()
            ]);
        }

        // Fetch existing record to check for user_id
        $personnel = $this->clientPersonnelModel->find($personnelId);
        if (!$personnel) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Personnel not found.']);
        }

        $clientPersonnelItem = [
            'first_name'     => $this->request->getPost('first_name'),
            'last_name'      => $this->request->getPost('last_name'),
            'email'          => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('contact_number'),
            'type'           => $this->request->getPost('type'),
            'signature'      => $this->request->getPost('signature'),
            'clinician_type' => $this->request->getPost('clinician_type'),
            'status'         => $this->request->getPost('status')
        ];

        $this->clientPersonnelModel->update($personnelId, $clientPersonnelItem);

        // Handle User record update/creation
        if ($this->request->getPost('type') != 7) {
            $userItem = [
                'first_name'     => $this->request->getPost('first_name'),
                'last_name'      => $this->request->getPost('last_name'),
                'email'          => $this->request->getPost('email'),
                'contact_number' => $this->request->getPost('contact_number'),
                'type'           => $this->request->getPost('type'),
                'status'         => $this->request->getPost('status')
            ];

            // Conditionally include signature and password
            // if ($this->request->getPost('signature')) {
            //     $userItem['signature'] = $this->request->getPost('signature');
            // }
            if ($this->request->getPost('password')) {
                $userItem['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }

            if ($personnel['user_id'] > 0) {
                // Update existing user
                $this->userModel->update($personnel['user_id'], $userItem);
            } else {
                // Create new user (type changed from 7)
                $userItem['facility_id'] = $facilityId;
                $userItem['token'] = '';
                $userItem['token_active'] = 0;
                
                $this->userModel->save($userItem);
                $newUserId = $this->userModel->getInsertID();

                // Link back to client_personnel
                $this->clientPersonnelModel->update($personnelId, ['user_id' => $newUserId]);
            }
        }

        return $this->response->setJSON([
            'success'        => 1,
            'message_header' => 'Personnel',
            'message'        => 'Personnel successfully updated.'
        ]);
    }
}
