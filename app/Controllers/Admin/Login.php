<?php

namespace App\Controllers\Admin;
 
use App\Controllers\BaseController;
use App\Models\AdminUsersModel;
 
class Login extends BaseController
{
    protected $request;
    protected $admin;

    public function __construct(){
        $this->request = \Config\Services::request();
        $this->admin = new AdminUsersModel;
    }
    
    public function index(){
        $data = [];
        $data['page_title'] = "Admin Login";
        $data['data'] = $this->request;
        $session = session();
        
        if( strtolower($this->request->getMethod()) == 'post'){
            $admin = $this->admin->where('email', $this->request->getPost('email'))->first();
            if($admin){
                if($admin['isDeleted'] != 1){
                    $verify_password  = password_verify($this->request->getPost('password'), $admin['password']);
                    if($verify_password){
                        foreach($admin as $k => $v){
                            $session->set('admin_' . $k, $v);
                        }
                        if($admin['isAdmin'] == 1){
                            $session->set('isAdmin', 1);
                        }
                        $session->set('isAdminLoggedIn', 1);
                        return redirect()->to('/admin/dashboard');
                    }else{
                        $session->setFlashdata('error', 'Incorrect Password');
                    }
                }else{
                    $session->setFlashdata('error', 'Admin account was suspended.');
                }
            }else{
                $session->setFlashdata('error', 'Incorrect Email or Password');
            }
        }

        $data['session'] = $session;
        return view('components/header_no_nav', array(
            'title' => 'Admin Login',
            'description' => '',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => array(
                'title' => 'Admin Login',
                'description' => '',
                'image' => IMG_URL . ''
            ),
            'styles' => array(
                'plugins/font_awesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/animations',
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/pages/login'
            )
        ))
        .view('admin/login', $data)
        .view('components/scripts_render', array(
            'scripts' => array(
                'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                    'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                    'crossorigin' => 'anonymous'
                ),
                ASSETS_URL . 'js/plugins/popper.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                ASSETS_URL . 'js/components/global.min.js',
            )
        ));
    }

    public function logout(){
        $session = session();
        $session->destroy();
        return redirect()->to('/admin/login');
    }
}
