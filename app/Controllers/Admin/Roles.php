<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RolesModel;
use App\Models\AccessMatrixModel;

class Roles extends BaseController
{
    public function index()
    {
        $model = new RolesModel();
        $data['roles'] = $model->where('isDeleted', 0)->orderBy('roleId', 'DESC')->findAll();
        $data['page_title'] = "Roles";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
        ];

        return view('admin/roles/index', $data);
    }

    public function create()
    {
        $data['page_title'] = "Add Role";
        $data['session'] = session();
        return view('admin/roles/create', $data);
    }

    public function store()
    {
        $model = new RolesModel();

        $rules = [
            'role_name' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'role' => $this->request->getPost('role_name'),
            'status' => 1,
            'isDeleted' => 0,
            'createdBy' => session()->get('userId') ?? 1,
        ];

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Role',
            'message' => 'Role added successfully.',
            'redirect' => base_url('admin/roles')
        ]);
    }

    public function edit($id)
    {
        $model = new RolesModel();
        $accessModel = new AccessMatrixModel();
        $data['role'] = $model->find($id);

        if (!$data['role']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $accessData = $accessModel->where('roleId', $id)->first();
        $data['access'] = $accessData ? json_decode($accessData['access'], true) : [];
        $data['adminMenu'] = config('AdminMenu');
        $data['page_title'] = "Edit Role";
        $data['session'] = session();
        return view('admin/roles/edit', $data);
    }

    public function update($id)
    {
        $model = new RolesModel();
        $accessModel = new AccessMatrixModel();

        $rules = [
            'role_name' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'role' => $this->request->getPost('role_name'),
            'updatedBy' => session()->get('userId') ?? 1,
        ];

        $model->update($id, $data);

        // Update Access Matrix
        $menus = $this->request->getPost('menus') ?? [];
        $accessJson = json_encode($menus);

        $existingAccess = $accessModel->where('roleId', $id)->first();
        $accessData = [
            'roleId' => $id,
            'access' => $accessJson,
            'updatedBy' => session()->get('userId') ?? 1,
        ];

        if ($existingAccess) {
            $accessModel->update($existingAccess['id'], $accessData);
        } else {
            $accessData['createdBy'] = session()->get('userId') ?? 1;
            $accessModel->insert($accessData);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Role',
            'message' => 'Role updated successfully.',
            'redirect' => base_url('admin/roles')
        ]);
    }

    public function delete($id)
    {
        $model = new RolesModel();
        $model->update($id, ['isDeleted' => 1, 'updatedBy' => session()->get('userId') ?? 1]);

        return redirect()->to('admin/roles')->with('message', 'Role deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new RolesModel();
        $roles = $model->where('isDeleted', 0)->orderBy('roleId', 'DESC')->findAll();
        $formattedData = [];

        foreach ($roles as $role) {
            $formattedData[] = [
                $role['role'],
                $role['status'] == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>',
                sprintf(
                    '<div class="text-center">
                        <a href="'.base_url('admin/roles/edit/' . $role['roleId']).'" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a> '
                        .($role['roleId'] != 1 ? '<a href="'.base_url('admin/roles/delete/' . $role['roleId']).'" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this role?\')"><i class="fas fa-trash"></i></a>' : '') . '</div>'
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $formattedData
        ]);
    }
}
