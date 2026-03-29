<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CredentialTypesModel;

class CredentialTypes extends BaseController
{
    public function index()
    {
        $data['page_title'] = "Credential Types";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
            ASSETS_URL . 'js/admin/credential_types.js',
        ];

        return view('admin/credential_types/index', $data);
    }

    public function create()
    {
        $data['page_title'] = "Add Credential Type";
        $data['session'] = session();

        $data['styles'] = [
            COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
        ];
        $data['scripts'] = [
            ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
        ];

        return view('admin/credential_types/create', $data);
    }

    public function store()
    {
        $model = new CredentialTypesModel();

        $rules = [
            'name' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status'),
        ];

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Credential Type',
            'message' => 'Credential Type added successfully.',
            'redirect' => base_url('admin/credential-types')
        ]);
    }

    public function edit($id)
    {
        $model = new CredentialTypesModel();
        
        $data['credential_type'] = $model->find($id);
        if (!$data['credential_type']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['page_title'] = "Edit Credential Type";
        $data['session'] = session();

        $data['styles'] = [
            COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
        ];
        $data['scripts'] = [
            ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
        ];

        return view('admin/credential_types/edit', $data);
    }

    public function update($id)
    {
        $model = new CredentialTypesModel();

        $rules = [
            'name' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status'),
        ];

        $model->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Credential Type',
            'message' => 'Credential Type updated successfully.',
            'redirect' => base_url('admin/credential-types')
        ]);
    }

    public function delete($id)
    {
        $model = new CredentialTypesModel();
        $model->delete($id);

        return redirect()->to('admin/credential-types')->with('message', 'Credential Type deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new CredentialTypesModel();
        $types = $model->orderBy('id', 'DESC')->findAll();

        $formattedData = [];
        foreach ($types as $type) {
            $statusBadge = $type->status == 1 ? '<span class="badge bg-success text-white">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            $formattedData[] = [
                $type->name,
                $type->description,
                $statusBadge,
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                    </div>',
                    base_url('admin/credential-types/edit/' . $type->id),
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $formattedData
        ]);
    }
}
