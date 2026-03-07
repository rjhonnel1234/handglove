<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TaxTypeModel;

class TaxTypes extends BaseController
{
    public function index()
    {
        $model = new TaxTypeModel();
        $data['tax_types'] = $model->orderBy('id', 'DESC')->findAll();
        $data['page_title'] = "Tax Types";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
            ASSETS_URL . 'js/admin/tax_types.min.js',
        ];

        return view('admin/tax_types/index', $data);
    }

    public function create()
    {
        $data['page_title'] = "Add Tax Type";
        $data['session'] = session();
        $data['scripts'] = [
            ASSETS_URL . 'js/admin/tax_types.min.js',
        ];
        return view('admin/tax_types/create', $data);
    }

    public function store()
    {
        $model = new TaxTypeModel();

        $rules = [
            'name'       => 'required',
            'percentage' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'percentage'  => $this->request->getPost('percentage'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status') ?? 1,
        ];

        try {
            $model->insert($data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Tax Type',
            'message' => 'Tax Type added successfully.',
            'redirect' => base_url('admin/tax-types')
        ]);
    }

    public function edit($id)
    {
        $model = new TaxTypeModel();
        $data['tax_type'] = $model->find($id);

        if (!$data['tax_type']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['page_title'] = "Edit Tax Type";
        $data['session'] = session();
        $data['scripts'] = [
            ASSETS_URL . 'js/admin/tax_types.min.js',
        ];
        return view('admin/tax_types/edit', $data);
    }

    public function update($id)
    {
        $model = new TaxTypeModel();

        $rules = [
            'name'       => 'required',
            'percentage' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'percentage'  => $this->request->getPost('percentage'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status') ?? 1,
        ];

        try {
            $model->update($id, $data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Tax Type',
            'message' => 'Tax Type updated successfully.',
            'redirect' => base_url('admin/tax-types')
        ]);
    }

    public function delete($id)
    {
        $model = new TaxTypeModel();
        $model->delete($id);

        return redirect()->to('admin/tax-types')->with('message', 'Tax Type deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new TaxTypeModel();
        $tax_types = $model->orderBy('id', 'DESC')->findAll();
        $formattedData = [];

        foreach ($tax_types as $tax_type) {
            $formattedData[] = [
                $tax_type['name'],
                $tax_type['percentage'] . '%',
                $tax_type['description'] ?: '-',
                $tax_type['status'] == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>',
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                        <a href="%s" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this tax type?\')"><i class="fas fa-trash"></i></a>
                    </div>',
                    base_url('admin/tax-types/edit/' . $tax_type['id']),
                    base_url('admin/tax-types/delete/' . $tax_type['id'])
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $formattedData
        ]);
    }
}
