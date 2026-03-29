<?php

namespace App\Controllers;

use App\Models\ProvidersModel;
use App\Models\CountriesModel;
use App\Models\StatesModel;
use App\Models\LeadsModel;
use App\Models\UserTypesModel;
use App\Models\LeadsManagementModel;
use App\Models\UserModel;

class Demo extends BaseController
{
    public function index()
    {
        $providerModel = new ProvidersModel;
        $statesModel = new StatesModel;
        $countriesModel = new CountriesModel;

        $userTypesModel = new UserTypesModel();
        $data['user_types'] = $userTypesModel->where('is_management', 1)->orderBy('name', 'ASC')->findAll();

        $data['providers'] = $providerModel->findAll();
        $data['countries'] = $countriesModel->where('id', 233)->findAll();
        $data['states'] = $statesModel->where('country_id', 233)->findAll();

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
                COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                COMPILED_ASSETS_PATH . 'css/components/owl',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/animations',
                COMPILED_ASSETS_PATH . 'css/components/jquery-steps',
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/pages/pages'
            )
        ))
        .view('pages/demo', $data)
        .view('components/scripts_render', array(
            'scripts' => array(
                'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                    'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                    'crossorigin' => 'anonymous'
                ),
                ASSETS_URL . 'js/plugins/popper.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                ASSETS_URL . 'js/plugins/jquery.steps.min.js',
                ASSETS_URL . 'js/plugins/jquery.validate.js',
                ASSETS_URL . 'js/components/global.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                ASSETS_URL . 'js/pages/demo.min.js',
            )
        ))
        .view('components/footer');
    }

    public function generateOTP(){
        $data = [
            'success' => false,
            'message' => 'Invalid request. Please try again later',
        ];

        if ($this->request->isAJAX()) {
            $email_address = $this->request->getPost('email');

            $userModel = new UserModel;
            $checkEmail = $userModel->where('email', $email_address)->countAllResults();
            
            if($checkEmail > 0){
                $data['message'] = 'Email already exists.';
            }else{
                $generator = "1357902468"; 
                $result = ""; 
                
                for ($i = 1; $i <= 6; $i++) { 
                    $result .= substr($generator, (rand()%(strlen($generator))), 1); 
                } 
    
                $item = [
                    'email_address' => $email_address,
                    'code' => $result,
                    'status' => 1,
                    'datetime_generated' => date("Y-m-d H:i:s")
                ];
    
                $otpModel = new \App\Models\EmailOTPModel();
                $otpModel->where('email_address', $email_address)->where('status', 1)->delete();
                $id = $otpModel->insert($item);
    
    
                if($id){
                    $email = \Config\Services::email();
                    $email->setSubject('Demo OTP');
                    $email->setTo($email_address);
                    $body = '
                        Hi there,<br><br>
                        You are requesting a demo to Handglove website.<br><br>
                        Please enter the OTP to verify the request before you can proceed with the registration.<br><br>
                        <strong>Your One-Time Password (OTP) is</strong><br>
                        <h3>'.$result.'</h3> 
                        <br><br><br>
                        Best,<br>
                        Handglove team
                    ';
                    $email->setMessage($body);
                    $response = $email->send();
                    // $response = true;
                    if($response){
                        $data['success'] = true;
                        $data['message'] = 'OTP Sent';
                    }else{
                        pe(["error" => $email->printDebugger()]);
                        $data['message'] = 'An unexpected error occurred. Please try again after few minutes.';
                    }
                }else{
                    $data['message'] = 'An unexpected error occurred. Please try again after few minutes.';
                }

            }
        }

        echo json_encode($data);
        exit();
    }

    public function verifyOTP(){
        $data = [
            'success' => false,
            'message' => 'Invalid request. Please try again later',
        ];

        if ($this->request->isAJAX()) {
            $otpModel = new \App\Models\EmailOTPModel();
            $email_address = $this->request->getPost('email');
            $otp = $this->request->getPost('otp');

            $result = $otpModel->where('status', 1)->where('code', $otp)->where('email_address', $email_address)->orderBy('created_at', 'desc')->limit(1)->first();

            if(!empty($result)){
                $data['message'] = 'Valid OTP';
                $data['success'] = 1;
                $otpModel->update($result['id'], ['status'=>2]);
            }else{
                $data['message'] = 'Invalid One-Time Password';
            }
        }

        echo json_encode($data);
        exit();
    }


    public function generateSMSOTP(){
        $data = [
            'success' => false,
            'message' => 'Invalid request. Please try again later',
        ];

        if ($this->request->isAJAX()) {
            $contact_number = $this->request->getPost('contact_number');

            $userModel = new UserModel;
            $checkEmail = $userModel->where('contact_number', $contact_number)->countAllResults();
            
            if($checkEmail > 0){
                $data['message'] = 'Contact number already exists.';
            }else{
                $generator = "1357902468"; 
                $result = ""; 
                
                for ($i = 1; $i <= 6; $i++) { 
                    $result .= substr($generator, (rand()%(strlen($generator))), 1); 
                } 
    
                $item = [
                    'contact_number' => $contact_number,
                    'code' => $result,
                    'status' => 1,
                    'datetime_generated' => date("Y-m-d H:i:s")
                ];
    
                $otpModel = new \App\Models\SMSOTPModel();
                $otpModel->where('contact_number', $contact_number)->where('status', 1)->delete();
                $id = $otpModel->insert($item);
    
    
                if($id){
                    // $email = service('email');
                    // $email->setSubject('Demo OTP');
                    // $email->setTo($email_address);
                    // $body = '
                    //     Hi there,<br><br>
                    //     You are requesting a demo to Handglove website.<br><br>
                    //     Please enter the OTP to verify the request before you can proceed with the registration.<br><br>
                    //     <strong>Your One-Time Password (OTP) is</strong><br>
                    //     <h3>'.$result.'</h3> 
                    //     <br><br><br>
                    //     Best,<br>
                    //     Handglove team
                    // ';
                    // $email->setMessage($body);
                    // $response = $email->send();
                    sleep(5);
                    $response = true;
                    if($response){
                        $data['success'] = true;
                        $data['message'] = 'OTP Sent';
                    }else{
                        pe(["error" => $email->printDebugger()]);
                        $data['message'] = 'An unexpected error occurred. Please try again after few minutes.';
                    }
                }else{
                    $data['message'] = 'An unexpected error occurred. Please try again after few minutes.';
                }

            }
        }

        echo json_encode($data);
        exit();
    }

    public function verifySMSOTP(){
        $data = [
            'success' => false,
            'message' => 'Invalid request. Please try again later',
        ];

        if ($this->request->isAJAX()) {
            $otpModel = new \App\Models\EmailOTPModel();
            $email_address = $this->request->getPost('email');
            $otp = $this->request->getPost('otp');

            $result = $otpModel->where('status', 1)->where('code', $otp)->where('email_address', $email_address)->orderBy('created_at', 'desc')->limit(1)->first();

            // if(!empty($result)){
                $data['message'] = 'Valid OTP';
                $data['success'] = 1;
                // $otpModel->update($result['id'], ['status'=>2]);
            // }else{
            //     $data['message'] = 'Invalid One-Time Password';
            // }
        }

        echo json_encode($data);
        exit();
    }


    public function submit(){

        $data['success'] = 0;
        $data['message'] = 'Invalid request';
        
        if ($this->request->isAJAX()) {
            $leadsObj = new LeadsModel;
            $company_name = $this->request->getPost('company_name');
            $address = $this->request->getPost('address');
            $provider_id = 0;
            if($company_name == 'Others'){
                $company_name = $this->request->getPost('other_company_name');
            }else{
                $provider_id = $company_name;
                $providerModel = new ProvidersModel;
                $provider = $providerModel->find($company_name);
                $company_name = $provider['name'];
                $address = $provider['address'];
            }
            $item = [
                'company_name' => $company_name,
                'address' => $address,
                'contact_number' => $this->request->getPost('contact_number'),
                'email' => $this->request->getPost('email_address'),
                'census' => $this->request->getPost('census'),
                'features' => json_encode($this->request->getPost('features')),
                'zip_code' => $this->request->getPost('zip'),
                'origin' => 4,
                'status' => 0,
                'provider_id' => $provider_id,
                'country' => $this->request->getPost('country'),
                'state' => $this->request->getPost('state'),
            ];
            $leadsObj->save($item);
            $leadID = $leadsObj->getInsertID();
            if($leadID){
                $leadsManagementObj = new LeadsManagementModel;
                $management = [
                    'leads_id' => $leadID,
                    'name' => $this->request->getPost('first_name') . ' ' . $this->request->getPost('last_name'),
                    'user_type' => $this->request->getPost('position'),
                    'email' => $this->request->getPost('email_address'),
                    'contact_number' => $this->request->getPost('contact_number')
                ];
                $leadsManagementObj->save($management);

                $data['success'] = 1;
                $data['message'] = 'Thank you for your interest. One of our team will contact you shortly.';
            }
        }

        echo json_encode($data);
        exit();
    }
}
