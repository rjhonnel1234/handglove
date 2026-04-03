<?php

namespace App\Controllers;
 
use App\Controllers\BaseController;
use App\Models\UserModel;
 
class Login extends BaseController
{
    protected $request;
    protected $user;

    public function __construct(){
        $this->request = \Config\Services::request();
        $this->user = new UserModel;

    }
    
    public function index(){

        // if( session()->get('isLoggedIn') == 1 ){
        //     return redirect()->to('profile');
        // }

        $data = [];
        $data['page_title'] = "Login";
        $data['data'] = $this->request;
        $session = session();
        if( strtolower($this->request->getMethod()) == 'post'){
            $user = $this->user->where('email', $this->request->getPost('email'))->first();
            if($user){
                if($user['status'] == 1){
                    $verify_password  = password_verify($this->request->getPost('password'),$user['password']);
                    if($verify_password){
                        foreach($user as $k => $v){
                            $session->set($k, $v);
                        }
                        $session->set('isLoggedIn', 1);
                        if($user['facility_id'] != 0){
                            return redirect()->to('/facility/manage');
                        }else{
                            return redirect()->to('/profile');
                        }
                    }else{
                        $session->setFlashdata('error','Incorrect Password');
                    }
                }else{
                    $session->setFlashdata('error','User account was suspended.');
                }
            }else{
                $session->setFlashdata('error','Incorrect Email or Password');
            }
        }

        $data['session'] = $session;
        return view('components/header_no_nav', array(
            'title' => 'Login',
            'description' => '',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => array(
                'title' => 'Login',
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
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/pages/login'
            )
        ))
        .view('login/index', $data)
        .view('components/scripts_render', array(
            'scripts' => array(
                'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                    'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                    'crossorigin' => 'anonymous'
                ),
                ASSETS_URL . 'js/plugins/popper.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                ASSETS_URL . 'js/components/global.min.js',
                ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                // ASSETS_URL . 'js/pages/home.min.js',
            )
        ));
        // .view('components/footer');
    }

    public function reset_password($token = ''){
        if(empty($token)){
            return redirect()->to('/');
        }

        $data = [];
        $data['page_title'] = "Reset Password";
        $data['data'] = $this->request;
        $session = session();
        $data['session'] = $session;
        

        $user = $this->user->where('token', $token)->first();
        $data['isValid'] = 0;
        $data['token'] = $token;
        if($user){
            $item = ['token_date' => date("Y-m-d", strtotime($user['token_datetime'])), 'token' => $token, 'token_active' => $user['token_active']];
            $isValid = isValidToken($item, $token);
            if($isValid){
                $data['isValid'] = 1;
            }
        }

        return view('components/header_no_nav', array(
            'title' => 'Reset Password',
            'description' => '',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => array(
                'title' => 'Reset Password',
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
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/pages/login'
            )
        ))
        .view('login/reset_password', $data)
        .view('components/scripts_render', array(
            'scripts' => array(
                'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                    'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                    'crossorigin' => 'anonymous'
                ),
                ASSETS_URL . 'js/plugins/popper.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                ASSETS_URL . 'js/components/global.min.js',
                ASSETS_URL . 'js/plugins/jquery.validate.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                ASSETS_URL . 'js/pages/reset_password.min.js',
            )
        ));

    }

    public function change_password(){
        $data = [
            'success' => false,
            'message' => 'Invalid request. Please try again later',
        ];

        //if ($this->request->isAJAX()) {
            $token = $this->request->getPost('token');
            $user = $this->user->where('token', $token)->first();
            if($user){
                $item = ['token_date' => date("Y-m-d", strtotime($user['token_datetime'])), 'token' => $token, 'token_active' => $user['token_active']];
                $isValid = isValidToken($item, $token);
                if($isValid == 1){
                    $password = $this->request->getPost('password');
                    $tokens = explode("-", $token);
                    
                    $this->user->where('id', $tokens[1])->set(['password' => password_hash($password, PASSWORD_DEFAULT), 'token_active' => 0])->update();
    
                    $data['success'] = 1;
                    $data['message'] = '<div id="reg-form"><div class="fields"><h3>Change password successful</h3><p class="text-center">Your password has been reset.<br>You can now sign-in <a href="'.base_url('login').'">here.</a></p></div></div>';
                }else{
                    $data['message'] = 'The reset password link is either invalid or already expired.';
                }
            }else{
                $data['message'] = 'The reset password link is either invalid or already expired.';
            }
        //}

        echo json_encode($data);
        exit();
    }

    public function forgot_password(){
        $data = [];
        $data['page_title'] = "Forgot Password";
        $data['data'] = $this->request;
        $session = session();
        $data['session'] = $session;

        if( strtolower($this->request->getMethod()) == 'post'){
            $user = $this->user->where('email', $this->request->getPost('email'))->first();
            if($user){
                $token = generate_token() . '-' . $user['id'];
                $url   = generate_url("login", "reset-password", $token);
                $link  = "<a href='" .  $url . "'>Click here to reset your password</a>";

                $data = ['token' => $token, 'token_active' => 1, 'token_datetime' => getServerTimestamp()];
                $this->user->where('id', $user['id'])->set($data)->update();

                $email = \Config\Services::email();
                // $email->initialize([
                //     'SMTPHost' => 'smtp.sendgrid.net',
                //     'SMTPPort' => 587,
                //     'SMTPUser' => 'apikey',
                //     'SMTPPass' => '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',
                //     'protocol' => 'smtp',
                //     'mailType' => 'html',
                //     'fromEmail' => 'noreply@gtindustries.ph',
                //     'fromName' => 'Villamor Air Base Golf Course',
                // ]);
                $item = $user;
                $item['link'] = $link;

                $template = view("login/email_forgot_password", ['session' => $session, 'item' => $item]);

                $email->setTo($user['email']);
                $email->setSubject('Password Reset');
                $email->setMessage($template);

                $email->send();
                $session->setFlashdata('email_sent', 1);
                return redirect()->to('/login/forgot-password');

            }else{
                $session->setFlashdata('error','The email address you entered is not associated to an account.');
            }
        }

        return view('components/header_no_nav', array(
            'title' => 'Forgot Password',
            'description' => '',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => array(
                'title' => 'Forgot Password',
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
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/pages/login'
            )
        )).view('login/forgot_password', $data)
        .view('components/scripts_render', array(
            'scripts' => array(
                'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                    'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                    'crossorigin' => 'anonymous'
                ),
                ASSETS_URL . 'js/plugins/popper.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                ASSETS_URL . 'js/components/global.min.js',
                ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                // ASSETS_URL . 'js/pages/home.min.js',
            )
        ));

    }

    public function logout(){
        $session = session();
        if ($session->get('isLoggedIn')) {
            $userModel = new UserModel();
            $userModel->update($session->get('id'), ['online_status' => 0]);
        }
        $session->destroy();
        return redirect()->to('/');
    }

}