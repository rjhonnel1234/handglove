<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ClinicianTypesModel;

class ClinicianTypes extends BaseController
{
    public function index()
    {
        $data['page_title'] = "Clinician Types";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
            ASSETS_URL . 'js/admin/clinician_types.min.js',
        ];

        return view('admin/clinician_types/index', $data);
    }

    public function create()
    {
        $data['page_title'] = "Add Clinician Type";
        $data['session'] = session();

        $data['styles'] = [
            COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
        ];
        $data['scripts'] = [
            ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
        ];

        return view('admin/clinician_types/create', $data);
    }

    public function store()
    {
        $model = new ClinicianTypesModel();

        $rules = [
            'name'     => 'required',
            'grouping' => 'required',
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
            'grouping'    => $this->request->getPost('grouping'),
        ];

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Clinician Type',
            'message' => 'Clinician Type added successfully.',
            'redirect' => base_url('admin/clinician-types')
        ]);
    }

    public function edit($id)
    {
        $model = new ClinicianTypesModel();
        
        $data['clinician_type'] = $model->find($id);
        if (!$data['clinician_type']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['page_title'] = "Edit Clinician Type";
        $data['session'] = session();

        $data['styles'] = [
            COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
        ];
        $data['scripts'] = [
            ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
        ];

        return view('admin/clinician_types/edit', $data);
    }

    public function update($id)
    {
        $model = new ClinicianTypesModel();

        $rules = [
            'name'     => 'required',
            'grouping' => 'required',
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
            'grouping'    => $this->request->getPost('grouping'),
        ];

        $model->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Clinician Type',
            'message' => 'Clinician Type updated successfully.',
            'redirect' => base_url('admin/clinician-types')
        ]);
    }

    public function delete($id)
    {
        $model = new ClinicianTypesModel();
        $model->delete($id);

        return redirect()->to('admin/clinician-types')->with('message', 'Clinician Type deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new ClinicianTypesModel();
        $types = $model->orderBy('id', 'DESC')->findAll();

        $formattedData = [];
        foreach ($types as $type) {
            $statusBadge = $type['status'] == 1 ? '<span class="badge bg-success text-white">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            $formattedData[] = [
                $type['name'],
                $type['description'],
                $type['grouping'] ?: '-',
                $statusBadge,
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                        <a href="%s" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')"><i class="fas fa-trash"></i></a>
                    </div>',
                    base_url('admin/clinician-types/edit/' . $type['id']),
                    base_url('admin/clinician-types/delete/' . $type['id'])
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $formattedData
        ]);
    }
}
