<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminUsersModel;
use App\Models\RolesModel;

class Users extends BaseController
{
    public function index()
    {
        $model = new AdminUsersModel();
        $data['page_title'] = "Personnel";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
        ];

        return view('admin/users/index', $data);
    }

    public function create()
    {
        $rolesModel = new RolesModel();
        $data['roles'] = $rolesModel->where('isDeleted', 0)->where('roleId !=', 1)->findAll();
        $data['page_title'] = "Add Personnel";
        $data['session'] = session();
        return view('admin/users/create', $data);
    }

    public function store()
    {
        $model = new AdminUsersModel();

        $rules = [
            'name'     => 'required',
            'email'    => 'required|valid_email|is_unique[tbl_admin_users.email]',
            'password' => 'required|min_length[6]',
            'roleId'   => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'mobile'    => $this->request->getPost('mobile'),
            'roleId'    => $this->request->getPost('roleId'),
            'designation' => $this->request->getPost('designation'),
            'isAdmin'   => $this->request->getPost('roleId') == 1 ? 1 : 0,
            'isDeleted' => 0,
            'createdBy' => session()->get('userId') ?? 1,
        ];

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Personnel',
            'message' => 'Personnel added successfully.',
            'redirect' => base_url('admin/users')
        ]);
    }

    public function edit($id)
    {
        $model = new AdminUsersModel();
        $rolesModel = new RolesModel();
        
        $data['user'] = $model->find($id);
        if (!$data['user']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['roles'] = $rolesModel->where('isDeleted', 0)->where('roleId !=', 1)->findAll();
        $data['page_title'] = "Edit Personnel";
        $data['session'] = session();
        return view('admin/users/edit', $data);
    }

    public function update($id)
    {
        $model = new AdminUsersModel();

        $rules = [
            'name'   => 'required',
            'email'  => "required|valid_email|is_unique[tbl_admin_users.email,userId,$id]",
            'roleId' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'mobile'    => $this->request->getPost('mobile'),
            'roleId'    => $this->request->getPost('roleId'),
            'isAdmin'   => $this->request->getPost('roleId') == 1 ? 1 : 0,
            'designation' => $this->request->getPost('designation'),
            'updatedBy' => session()->get('userId') ?? 1,
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $model->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Personnel',
            'message' => 'Personnel updated successfully.',
            'redirect' => base_url('admin/users')
        ]);
    }

    public function delete($id)
    {
        if ($id == 1) {
            return redirect()->to('admin/users')->with('error', 'Cannot delete system administrator.');
        }

        $model = new AdminUsersModel();
        $model->update($id, ['isDeleted' => 1, 'updatedBy' => session()->get('userId') ?? 1]);

        return redirect()->to('admin/users')->with('message', 'Personnel deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new AdminUsersModel();
        $users = $model->select('tbl_admin_users.*, tbl_roles.role as role_name')
                       ->join('tbl_roles', 'tbl_roles.roleId = tbl_admin_users.roleId', 'left')
                       ->where('tbl_admin_users.isDeleted', 0)
                       ->where('tbl_admin_users.userId !=', 1)
                       ->orderBy('tbl_admin_users.userId', 'DESC')
                       ->findAll();

        $formattedData = [];
        foreach ($users as $user) {
            $formattedData[] = [
                $user['name'],
                $user['email'],
                $user['mobile'],
                $user['role_name'] ?? 'N/A',
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                        <a href="%s" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')"><i class="fas fa-trash"></i></a>
                    </div>',
                    base_url('admin/users/edit/' . $user['userId']),
                    base_url('admin/users/delete/' . $user['userId'])
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $formattedData
        ]);
    }
}
