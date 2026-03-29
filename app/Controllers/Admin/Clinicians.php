<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CliniciansModel;
use App\Models\ClinicianTypesModel;
use App\Models\ProvidersModel;
use App\Models\AgenciesModel;
use App\Models\FacilityModel;
use App\Models\ClinicianPccModel;
use App\Models\UserModel;

class Clinicians extends BaseController
{
    public function index()
    {
        $data['page_title'] = "Clinicians";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
        ];

        return view('admin/clinicians/index', $data);
    }

    public function create()
    {
        $clinicianModel = new CliniciansModel();
        $typesModel = new ClinicianTypesModel();
        $agenciesModel = new AgenciesModel();
        $facilityModel = new FacilityModel();

        $data['types'] = $typesModel->where('status', 1)->findAll();
        $data['agencies'] = $agenciesModel->findAll();
        $data['facilities'] = $facilityModel->findAll();
        
        $data['tiers'] = $clinicianModel->tier_mapping;
        $data['page_title'] = "Add Clinician";
        $data['session'] = session();

        $data['styles'] = [
            COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
            COMPILED_ASSETS_PATH . 'css/components/dropzone'
        ];
        $data['scripts'] = [
            ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js',
            ASSETS_URL . 'js/admin/clinicians.js',
        ];

        return view('admin/clinicians/create', $data);
    }

    public function store()
    {
        $model = new CliniciansModel();
        //add validation for name, email, address, zip_code, profile_pic, contact_number, type, tier
        $rules = [
            'name'     => 'required',
            'email'    => 'required|valid_email|is_unique[tbl_clinicians.email]',
            'contact_number' => 'required',
            'type' => 'required',
            'tier' => 'required',
            'profile_pic_path' => 'required',
            'address' => 'required',
            'zip_code' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $agencies = "";
        if(!empty($this->request->getPost('agencies'))){
            $agencies = implode(",",$this->request->getPost('agencies'));
        }
        $data = [
            'name'           => $this->request->getPost('name'),
            'email'          => $this->request->getPost('email'),
            'address'        => $this->request->getPost('address'),
            'zip_code'       => $this->request->getPost('zip_code'),
            'contact_number' => $this->request->getPost('contact_number'),
            'status'         => $this->request->getPost('status'),
            'tier'           => $this->request->getPost('tier'),
            'type'           => $this->request->getPost('type'),
            'rate'           => $this->request->getPost('rate'),
            'agencies'      => $agencies,
            'company_worked' => addslashes(serialize($this->request->getPost('company_work') ?: [])),
            'handglove_username' => $this->request->getPost('email'),
            'handglove_password' => $this->request->getPost('handglove_password'),
        ];

        // Handle Profile Picture
        $profilePicPath = $this->request->getPost('profile_pic_path');
        if (!empty($profilePicPath)) {
            $data['profile_pic_url'] = base_url($profilePicPath);
        } else {
            // Fallback to traditional upload
            $file = $this->request->getFile('profile_pic_url');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/clinicians', $newName);
                $data['profile_pic_url'] = base_url('uploads/clinicians/' . $newName);
            }
        }

        $clinician_id = $model->insert($data);

        // Create Handglove User Access if requested
        if ($this->request->getPost('create_clinician_access')) {
            $userModel = new UserModel();
            $userData = [
                'email'    => $data['email'],
                'password' => password_hash($data['handglove_password'], PASSWORD_DEFAULT),
                'first_name' => $data['name'], // Or split name if needed, but 'name' is used in clinicians
                'status'   => 1,
                'type'     => 10, // Staff/Clinician type based on InitialDataSeeder (Staff is 7)
            ];
            
            // Check if user already exists
            $existingUser = $userModel->where('email', $data['email'])->first();
            if ($existingUser) {
                //show error message
                return $this->response->setJSON([
                    'status' => 'error',
                    'message_header' => 'Clinician',
                    'message' => 'Unable to create access for clinician, user with this email already exists. Clinician details have been saved.',
                    'redirect' => base_url('admin/clinicians')
                ]);
            } else {
                $user_id = $userModel->insert($userData);
            }

            // Link user_id to clinician
            $model->update($clinician_id, ['user_id' => $user_id]);
        }

        // Save Facility-specific PCC Credentials
        $pcc_data = $this->request->getPost('pcc');
        if ($pcc_data && is_array($pcc_data)) {
            $pccModel = new ClinicianPccModel();
            foreach ($pcc_data as $facility_id => $creds) {
                if (!empty($creds['username']) || !empty($creds['password'])) {
                    $pccModel->insert([
                        'clinician_id' => $clinician_id,
                        'facility_id'  => $facility_id,
                        'username'     => $creds['username'],
                        'password'     => $creds['password']
                    ]);
                }
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Clinician',
            'message' => 'Clinician added successfully.',
            'redirect' => base_url('admin/clinicians')
        ]);
    }

    public function edit($id)
    {
        $model = new CliniciansModel();
        $typesModel = new ClinicianTypesModel();
        $agenciesModel = new AgenciesModel();
        $facilityModel = new FacilityModel();

        $data['clinician'] = $model->find($id);
        if (!$data['clinician']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['types'] = $typesModel->where('status', 1)->findAll();
        $data['agencies'] = $agenciesModel->findAll();
        $data['facilities'] = $facilityModel->findAll();
        $data['tiers'] = $model->tier_mapping;

        $data['clinician']['agencies'] = explode(',', $data['clinician']['agencies']);

        $pccModel = new ClinicianPccModel();
        $pcc_creds = $pccModel->where('clinician_id', $id)->findAll();
        $data['pcc_credentials'] = [];
        
        foreach ($pcc_creds as $cred) {
            $data['pcc_credentials'][$cred['facility_id']] = $cred;
        }

        $data['company_worked'] = unserialize(stripslashes($data['clinician']['company_worked'] ?? ''));
        if (!$data['company_worked']) {
            $data['company_worked'] = [];
        }

        // Fetch associated user record
        $userModel = new UserModel();
        $data['user_record'] = $userModel->where('email', $data['clinician']['email'])->first();


        $data['styles'] = [
            COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
            COMPILED_ASSETS_PATH . 'css/components/dropzone'
        ];
        $data['scripts'] = [
            ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js',
            ASSETS_URL . 'js/admin/clinicians.js',
        ];

        $data['page_title'] = "Edit Clinician";
        $data['session'] = session();
        return view('admin/clinicians/edit', $data);
    }

    public function update($id)
    {
        $model = new CliniciansModel();

        $rules = [
            'name'     => 'required',
            'email'    => "required|valid_email|is_unique[tbl_clinicians.email,id,$id]",
            'contact_number' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        $agencies = "";
        if(!empty($this->request->getPost('agencies'))){
            $agencies = implode(",",$this->request->getPost('agencies'));
        }
        $data = [
            'name'           => $this->request->getPost('name'),
            'email'          => $this->request->getPost('email'),
            'address'        => $this->request->getPost('address'),
            'zip_code'       => $this->request->getPost('zip_code'),
            'contact_number' => $this->request->getPost('contact_number'),
            'status'         => $this->request->getPost('status'),
            'tier'           => $this->request->getPost('tier'),
            'type'           => $this->request->getPost('type'),
            'rate'           => $this->request->getPost('rate'),
            'agencies'      => $agencies,
            'company_worked' => serialize($this->request->getPost('company_work') ?: []),
            'handglove_username' => $this->request->getPost('email'),
            'handglove_password' => $this->request->getPost('handglove_password'),
        ];

        // Handle Profile Picture
        $profilePicPath = $this->request->getPost('profile_pic_path');
        if (!empty($profilePicPath)) {
            $data['profile_pic_url'] = base_url($profilePicPath);
        } else {
            // Fallback to traditional upload
            $file = $this->request->getFile('profile_pic');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/clinicians', $newName);
                $data['profile_pic_url'] = base_url('uploads/clinicians/' . $newName);
            }
        }

        $old_email = $model->find($id)['email'];
        $model->update($id, $data);

        // Sync Email/Username in tbl_users if email changed
        if ($old_email != $data['email']) {
            $userModel = new UserModel();
            $userModel->where('email', $old_email)->set(['email' => $data['email']])->update();
        }

        // Update Facility-specific PCC Credentials
        $pccModel = new ClinicianPccModel();
        if ($id) {
            $pccModel->where('clinician_id', $id)->delete();
        }
        $pcc_data = $this->request->getPost('pcc');
        if ($pcc_data && is_array($pcc_data)) {
            foreach ($pcc_data as $facility_id => $creds) {
                if (!empty($creds['username']) || !empty($creds['password'])) {
                    $pccModel->insert([
                        'clinician_id' => $id,
                        'facility_id'  => $facility_id,
                        'username'     => $creds['username'],
                        'password'     => $creds['password']
                    ]);
                }
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Clinician',
            'message' => 'Clinician updated successfully.',
            'redirect' => base_url('admin/clinicians')
        ]);
    }

    public function toggle_user_status()
    {
        $user_id = $this->request->getPost('user_id');
        $status = $this->request->getPost('status');

        if (!$user_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing User ID']);
        }

        $userModel = new UserModel();
        $userModel->update($user_id, ['status' => $status]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'User status updated successfully.'
        ]);
    }

    public function delete($id)
    {
        $model = new CliniciansModel();
        // Check if there's an isDeleted column, otherwise just delete
        $model->delete($id);

        return redirect()->to('admin/clinicians')->with('message', 'Clinician deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new CliniciansModel();
        $clinicians = $model->select('tbl_clinicians.*, tbl_clinician_types.name as type_name')
                           ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'left')
                           ->orderBy('tbl_clinicians.id', 'DESC')
                           ->findAll();

        $formattedData = [];
        foreach ($clinicians as $clinician) {
            $statusBadge = $clinician['status'] == 1 ? '<span class="badge bg-success text-white">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            $formattedData[] = [
                sprintf('<img src="%s" class="rounded-circle" width="40" height="40" onerror="this.src=\'%s\'">', $clinician['profile_pic_url'] ?: base_url('assets/img/blank-img.png'), base_url('assets/img/blank-img.png')),
                sprintf('<div><strong>%s</strong></div>', $clinician['name'] ?: 'N/A').
                sprintf('<div><small><i class="fas fa-envelope"></i> %s</small></div>', $clinician['email'] ?: 'N/A').
                sprintf('<div><small><i class="fas fa-phone"></i> %s</small></div>', $clinician['contact_number'] ?: 'N/A'),
                sprintf('<div>%s</div>', $clinician['type_name'] ?? 'N/A'),
                $statusBadge,
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-info text-white" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-dark text-white upload-credentials" data-id="%s" data-name="%s" title="Upload Credentials"><i class="fas fa-upload"></i></a>
                        <a href="%s" class="btn btn-sm btn-warning text-white" title="Send Reset Password" onclick="return confirm(\'Send password reset email to this clinician?\')"><i class="fas fa-key"></i></a>
                    </div>',
                    base_url('admin/clinicians/edit/' . $clinician['id']),
                    $clinician['id'],
                    addslashes($clinician['name']),
                    base_url('admin/clinicians/send-reset-password/' . $clinician['id']),
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $formattedData
        ]);
    }

    public function get_pcc_credentials()
    {
        $clinician_id = $this->request->getGet('clinician_id');
        $facility_id = $this->request->getGet('facility_id');

        if (!$clinician_id || !$facility_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing IDs']);
        }

        $pccModel = new ClinicianPccModel();
        $creds = $pccModel->where([
            'clinician_id' => $clinician_id,
            'facility_id'  => $facility_id
        ])->first();

        if ($creds) {
            return $this->response->setJSON(['status' => 'success', 'data' => $creds]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No credentials found']);
        }
    }

    public function sendResetPassword($id)
    {
        $clinicianModel = new CliniciansModel();
        $userModel = new UserModel();

        $clinician = $clinicianModel->find($id);
        if (!$clinician) {
            return redirect()->to('admin/clinicians')->with('error', 'Clinician not found.');
        }

        $user = $userModel->where('email', $clinician['email'])->first();
        if (!$user) {
            return redirect()->to('admin/clinicians')->with('error', 'Associated user account not found.');
        }

        $token = generate_token() . '-' . $user['id'];
        $url   = generate_url("login", "reset-password", $token);
        $link  = "<a href='" .  $url . "'>Click here to reset your password</a>";

        $data = ['token' => $token, 'token_active' => 1, 'token_datetime' => getServerTimestamp()];
        $userModel->where('id', $user['id'])->set($data)->update();

        $email = \Config\Services::email();
        $item = $user;
        $item['link'] = $link;

        $template = view("login/email_forgot_password", ['session' => session(), 'item' => $item]);

        $email->setTo($user['email']);
        $email->setSubject('Password Reset');
        $email->setMessage($template);

        if ($email->send()) {
            return redirect()->to('admin/clinicians')->with('message', 'Password reset email sent successfully.');
        } else {
            //show email error if there are any
            pe($email->printDebugger());
            return redirect()->to('admin/clinicians')->with('error', 'Failed to send password reset email.');
        }
    }

    public function get_credentials()
    {
        $clinician_id = $this->request->getGet('clinician_id');
        if (!$clinician_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing Clinician ID']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('tbl_credential_types t');
        $builder->select('t.id as type_id, t.name as type_name, c.filename, c.file_path, c.id as credential_record_id');
        $builder->join('tbl_clinician_credentials c', "c.credential_id = t.id AND c.clinician_id = $clinician_id", 'left');
        $builder->where('t.status', 1);
        $result = $builder->get()->getResultArray();

        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }

    public function upload_credential()
    {
        $clinician_id = $this->request->getPost('clinician_id');
        $credential_id = $this->request->getPost('credential_id');
        $file = $this->request->getFile('file');

        if (!$clinician_id || !$credential_id || !$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request or file.']);
        }

        $newName = $file->getRandomName();
        $targetPath = 'uploads/credentials';
        
        if (!is_dir(FCPATH . $targetPath)) {
            mkdir(FCPATH . $targetPath, 0777, true);
        }

        $file->move(FCPATH . $targetPath, $newName);
        $filePath = $targetPath . '/' . $newName;

        $model = new \App\Models\ClinicianCredentialsModel();
        $existing = $model->where(['clinician_id' => $clinician_id, 'credential_id' => $credential_id])->first();

        $data = [
            'clinician_id' => $clinician_id,
            'credential_id' => $credential_id,
            'filename' => $file->getClientName(),
            'file_path' => $filePath
        ];

        if ($existing) {
            $model->update($existing['id'], $data);
        } else {
            $model->insert($data);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Credential uploaded successfully.',
            'file_path' => base_url($filePath)
        ]);
    }

    public function upload()
    {
        $file = $this->request->getFile('file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $targetPath = 'uploads/clinicians';
            
            if (!is_dir(FCPATH . $targetPath)) {
                mkdir(FCPATH . $targetPath, 0777, true);
            }

            $file->move(FCPATH . $targetPath, $newName);

            return $this->response->setJSON([
                'status' => 'success',
                'path' => $targetPath . '/' . $newName,
                'url' => base_url($targetPath . '/' . $newName)
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Upload failed.']);
    }
}
