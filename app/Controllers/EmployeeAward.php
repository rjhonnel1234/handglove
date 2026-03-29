<?php

namespace App\Controllers;

use App\Models\LeadsModel;
use App\Models\ProvidersModel;
use App\Models\LeadsManagementModel;
use App\Models\CliniciansModel;

class EmployeeAward extends BaseController
{
    public function index()
    {

        $session = session();
        $data['session'] = $session;
        if( is_null($session->get('isLoggedIn')) || $session->get('isLoggedIn') != 1){
            return redirect()->to('/login');
        }
        $providerModel = new ProvidersModel;
        $data['providers'] = $providerModel->findAll();
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
        .view('pages/employee_award', $data)
        .view('components/scripts_render', array(
            'scripts' => array(
                'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                    'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                    'crossorigin' => 'anonymous'
                ),
                ASSETS_URL . 'js/plugins/popper.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                ASSETS_URL . 'js/components/global.min.js',
                ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                ASSETS_URL . 'js/plugins/jquery.validate.js',
                ASSETS_URL . 'js/pages/award.min.js',
            )
        ))
        .view('components/footer');
    }

    public function submit(){
         $data = [
            'success' => 0, 
            'message' => 'Invalid requests.'
        ];

        $session = session();
        if( $session->get('isLoggedIn') == 1){
            if($this->request->isAJAX()){

                $company_name = $this->request->getPost('company_name');
                $leadsModel = new LeadsModel;
                $profileData = $leadsModel->where('company_name', $company_name)->first();
                $clinModel = new CliniciansModel;
                $profileData = $clinModel
                                            ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name')
                                            ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'INNER')
                                            ->where('tbl_clinicians.email', session()->get('email'))
                                            ->first();

                                            
                // if(empty($profileData)){

                $address = $this->request->getPost('company_address');
                $zip_code = $this->request->getPost('zip_code');

                $item = [
                    'company_name' => $company_name,
                    'address' => $address,
                    'origin' => 3,
                    'zip_code' => $zip_code,
                    'ref_clinician_id' => $profileData['id']
                ];
                $leadsModel->insert($item);
                $leads_id = $leadsModel->getInsertID();

                $supervisor_name = $this->request->getPost('supervisor_name');
                $supervisor_email = $this->request->getPost('supervisor_email');
                $supervisor_contact_number = $this->request->getPost('supervisor_contact_number');
                $leadsManagementModel = new LeadsManagementModel;

                $item = [
                    'leads_id' => $leads_id,
                    'user_type' => 1,
                    'name' => $supervisor_name,
                    'email' => $supervisor_email,
                    'contact_number' => $supervisor_contact_number,
                    'is_approver'=> 0
                ];
                $leadsManagementModel->insert($item);
                $data['message'] = '<div class="form-confirmation"><h2>Thank you!.</h2><p><strong>Thank you for submitting a referral!</strong></p><p>We truly appreciate you taking the time to share our services and help us grow through your recommendation.</p></div>';
                $data['success'] = 1;

                // }else{
                //     $data['message'] = '<div class="form-confirmation"><h2>Thank you!.</h2><p><strong>Thank you for submitting a referral!</strong></p><p>We truly appreciate you taking the time to share our services and help us grow through your recommendation.</p></div>';
                //     $data['success'] = 1;
                // }
            }
        }

        echo json_encode($data);
        exit();

    }
}
