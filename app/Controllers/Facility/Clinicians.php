<?php

namespace App\Controllers\Facility;

use App\Controllers\BaseController;
use App\Models\CliniciansModel;
use App\Models\ClinicianTypesModel;
use App\Models\FacilityModel;
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
}
