<?php

namespace App\Controllers;

use App\Models\ShiftsModel;
use App\Models\FacilityUnitsModel;
use App\Models\CliniciansModel;
use App\Models\ShiftRequestsModel;
use App\Models\ClinicianTypesModel;
use App\Models\ClinicianTempShiftModel;
use App\Models\ClinicianCredentialsModel;


class Jobs extends BaseController
{
    public function index()
    {


        $session = session();
        if ($session->get('isLoggedIn') == 1) {
            $clinModel = new CliniciansModel;

            $profileData = $clinModel
                ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name')
                ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'INNER')
                ->where('tbl_clinicians.email', session()->get('email'))
                ->first();
            $data['profileData'] = $profileData;
        }
        $shiftModel = new ShiftsModel;
        $builder = $shiftModel
            ->select('tbl_shifts.*, tbl_shift_types.name as type_name, 
                            (
                                (SELECT count(id) FROM tbl_shift_clinicians WHERE tbl_shift_clinicians.shift_id = tbl_shifts.id AND tbl_shift_clinicians.status != 100) + 
                                (SELECT count(id) FROM tbl_client_shift_requests WHERE tbl_client_shift_requests.shift_id = tbl_shifts.id AND tbl_client_shift_requests.status != 100)
                            ) as slots_taken, 
                            tbl_client_units.name as unit_name, 
                            tbl_clients.company_name'
            )
            ->join('tbl_clients', 'tbl_clients.id = tbl_shifts.client_id', 'inner')
            ->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'inner')
            ->join('tbl_shift_types', 'tbl_shift_types.id = tbl_shifts.shift_type', 'inner')
            ->where('tbl_shifts.start_date >=', date("Y-m-d"))
            ->where('tbl_shifts.status !=', 100)
            ->where('tbl_shifts.posted', 1)
            ->having('slots_taken < tbl_shifts.slots');


        if (isset($profileData)) {
            $db = \Config\Database::connect();
            $subQuery = $db->table('tbl_client_shift_requests')
                ->select('tbl_client_shift_requests.shift_id')
                ->where('tbl_client_shift_requests.clinician_id', $profileData['id'])
                ->where('tbl_client_shift_requests.status !=', 100)->getCompiledSelect();

            $builder->where('tbl_shifts.id NOT IN (' . $subQuery . ')');
            $builder->where('tbl_shifts.shift_type', $profileData['type']);
        }

        $objClinTypes = new ClinicianTypesModel;

        $data['clinician_types'] = $objClinTypes->where('status', 1)->findAll();
        $data['shifts'] = $builder->findAll();

        // pe($data['shifts']);
        // PAGE HEAD PROCESSING
        return view('components/header', array(
            'title' => 'Handglove',
            'description' => 'Water for Every Filipino. 50 years in the pipe manufacturing industry and more than 30 years experience in bulk water supply, water distribution system and wastewater management.',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => array(
                'title' => 'Handglove',
                'description' => 'Water for Every Filipino. 50 years in the pipe manufacturing industry and more than 30 years experience in bulk water supply, water distribution system and wastewater management.',
                'image' => IMG_URL . ''
            ),
            'styles' => array(
                'plugins/font_awesome',
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
                COMPILED_ASSETS_PATH . 'css/pages/pages'
            )
        ))
            . view('pages/jobs', $data)
            . view('components/scripts_render', array(
                'scripts' => array(
                    'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                        'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                        'crossorigin' => 'anonymous'
                    ),
                    ASSETS_URL . 'js/plugins/popper.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                    ASSETS_URL . 'js/components/global.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                    ASSETS_URL . 'js/components/navigation_bar.min.js',
                    ASSETS_URL . 'js/pages/jobs.min.js',
                )
            ))
            . view('components/footer');
    }

    public function apply()
    {
        $data = [
            'success' => 0,
            'message' => 'Invalid requests.'
        ];

        $session = session();
        if ($session->get('isLoggedIn') == 1) {
            if ($this->request->isAJAX()) {

                $clinModel = new CliniciansModel;
                $profileData = $clinModel
                    ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name')
                    ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'INNER')
                    ->where('tbl_clinicians.email', session()->get('email'))
                    ->first();

                if (!empty($profileData)) {
                    $objShiftRequest = new ShiftRequestsModel;
                    $objShift = new ShiftsModel;
                    $shift = $objShift->find($this->request->getPost('shiftID'));

                    if (!empty($shift)) {
                        $item = [
                            'shift_id' => $shift['id'],
                            'client_id' => $shift['client_id'],
                            'clinician_id' => $profileData['id'],
                            'status' => 15
                        ];
                        $objShiftRequest->save($item);
                        $data['success'] = 1;
                        $data['message'] = 'Successfully applied to this shift.';
                    }
                }
            }
        }

        echo json_encode($data);
        exit();
    }

    public function apply_register_clinician()
    {

        $data = [
            'success' => 0,
            'message' => 'Invalid requests.'
        ];

        if ($this->request->isAJAX()) {
            if ($this->request->getPost()) {

                $objShift = new ShiftsModel;
                $shift = $objShift->find($this->request->getPost('shift_id'));

                if (!empty($shift)) {
                    $validation = \Config\Services::validation();
                    $rules = [
                        'first_name' => [
                            'label' => 'First Name',
                            'rules' => 'required',
                        ],
                        'last_name' => [
                            'label' => 'Last Name',
                            'rules' => 'required',
                        ],
                        'email' => [
                            'label' => 'Email Address',
                            'rules' => 'required',
                        ],
                        'contact_number' => [
                            'label' => 'Contact Number',
                            'rules' => 'required',
                        ],
                        'type' => [
                            'label' => 'Certification or License',
                            'rules' => 'required',
                        ],
                        'cv' => [
                            'label' => 'Resume',
                            'rules' => [
                                'mime_in[cv,image/jpg,image/jpeg,image/png,application/pdf]',
                                'max_size[cv, ' . (1024 * 5) . ']',
                            ]
                        ]
                    ];
                    if ($this->validate($rules)) {
                        $objClinicians = new CliniciansModel;
                        $clinicians = $objClinicians->where('email', $this->request->getPost('email'))->findAll();

                        if (empty($clinicians)) {
                            if ($this->request->getPost('type') != $shift['shift_type']) {
                                $data['message'] = ['Unable to proceed. Shift and Certification does not match.'];
                            } else {
                                $item = [
                                    'name' => $this->request->getPost('first_name') . ' ' . $this->request->getPost('last_name'),
                                    'email' => $this->request->getPost('email'),
                                    'contact_number' => $this->request->getPost('contact_number'),
                                    'type' => $this->request->getPost('type'),
                                    'tier' => 10,
                                    'company_worked' => 'a:3:{s:4:\"name\";a:1:{i:0;s:0:\"\";}s:10:\"supervisor\";a:1:{i:0;s:0:\"\";}s:10:\"contact_no\";a:1:{i:0;s:0:\"\";}}',
                                    'status' => 0,
                                ];
                                $objClinicians->save($item);
                                $clinID = $objClinicians->getInsertID();
                                if ($clinID) {
                                    $objTemp = new ClinicianTempShiftModel;
                                    $item = [
                                        'shift_id' => $this->request->getPost('shift_id'),
                                        'clinician_id' => $clinID
                                    ];
                                    $objTemp->save($item);
                                    if ($_FILES['cv']['error'] == 0) {
                                        $orig_filename = $_FILES['cv']['name'];
                                        $img = $this->request->getFile('cv');
                                        if (!$img->hasMoved()) {
                                            $credsModel = new ClinicianCredentialsModel;
                                            $filepath = WRITEPATH . 'uploads/' . $img->store();

                                            $item = [
                                                'credential_id' => 14,
                                                'clinician_id' => $clinID,
                                                'filename' => $orig_filename,
                                                'file_path' => $filepath
                                            ];
                                            $credsModel->save($item);

                                            $data['success'] = 1;
                                            $data['message'] = 'Thank you for your interest on this shift. Your application has been received. One of our team will contact you soon.';
                                        }
                                    }

                                }
                            }
                        } else {
                            $data['message'] = ['Email already exists.'];
                        }
                    } else {
                        $data['message'] = $validation->getErrors();
                    }
                }
            }
        }


        echo json_encode($data);
        exit();

    }

}
