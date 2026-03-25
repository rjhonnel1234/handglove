<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AgenciesModel;

class Agencies extends BaseController
{
    public function index()
    {
        $data['page_title'] = "Agencies";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
            'js/pages/admin/agencies.js',
        ];

        return view('admin/agencies/index', $data);
    }

    public function create()
    {
        $data['page_title'] = "Add Agency";
        $data['session'] = session();
        return view('admin/agencies/create', $data);
    }

    public function store()
    {
        $model = new AgenciesModel();

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
            'message_header' => 'Agency',
            'message' => 'Agency added successfully.',
            'redirect' => base_url('admin/agencies')
        ]);
    }

    public function edit($id)
    {
        $model = new AgenciesModel();
        
        $data['agency'] = $model->find($id);
        if (!$data['agency']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['page_title'] = "Edit Agency";
        $data['session'] = session();
        return view('admin/agencies/edit', $data);
    }

    public function update($id)
    {
        $model = new AgenciesModel();

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
            'message_header' => 'Agency',
            'message' => 'Agency updated successfully.',
            'redirect' => base_url('admin/agencies')
        ]);
    }

    public function delete($id)
    {
        $model = new AgenciesModel();
        $model->delete($id);

        return redirect()->to('admin/agencies')->with('message', 'Agency deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new AgenciesModel();
        $agencies = $model->orderBy('id', 'DESC')->findAll();

        $formattedData = [];
        foreach ($agencies as $agency) {
            $statusBadge = $agency['status'] == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            $formattedData[] = [
                $agency['name'],
                $agency['description'],
                $statusBadge,
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                        <a href="%s" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')"><i class="fas fa-trash"></i></a>
                    </div>',
                    base_url('admin/agencies/edit/' . $agency['id']),
                    base_url('admin/agencies/delete/' . $agency['id'])
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $formattedData
        ]);
    }
}
