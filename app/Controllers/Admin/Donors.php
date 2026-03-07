<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DonorsModel;

class Donors extends BaseController
{
    public function index()
    {
        $model = new DonorsModel();
        $data['donors'] = $model->orderBy('id', 'DESC')->findAll();
        $data['page_title'] = "Donors";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
            COMPILED_ASSETS_PATH . 'css/components/dropzone'
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
        ];

        return view('admin/donors/index', $data);
    }

    public function create()
    {
        $data['page_title'] = "Add Donor";
        $data['session'] = session();
        $data['scripts'] = [
            'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js',
            ASSETS_URL . 'js/admin/donors.min.js',
        ];
        return view('admin/donors/create', $data);
    }

    public function store()
    {
        $model = new DonorsModel();

        $rules = [
            'donor_name' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'name' => $this->request->getPost('donor_name'),
            'url'    => $this->request->getPost('website'),
            'logo'       => $this->request->getPost('logo_path'),
        ];

        // Fallback to traditional upload if Dropzone wasn't used
        if (empty($data['logo'])) {
            $logo = $this->request->getFile('logo');
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $newName = $logo->getRandomName();
                $logo->move(FCPATH . 'uploads/donors/logos', $newName);
                $data['logo'] = 'uploads/donors/logos/' . $newName;
            }
        }

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Donor',
            'message' => 'Donor added successfully.',
            'redirect' => base_url('admin/donors')
        ]);
    }

    public function edit($id)
    {
        $model = new DonorsModel();
        $data['donor'] = $model->find($id);

        if (!$data['donor']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['page_title'] = "Edit Donor";
        $data['session'] = session();
        $data['scripts'] = [
            'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js',
            ASSETS_URL . 'js/admin/donors.min.js',
        ];
        return view('admin/donors/edit', $data);
    }

    public function update($id)
    {
        $model = new DonorsModel();

        $rules = [
            'donor_name' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'name' => $this->request->getPost('donor_name'),
            'url'    => $this->request->getPost('website'),
            'logo'       => $this->request->getPost('logo_path'),
        ];

        // Only update image fields if new paths are provided
        if (empty($data['logo'])) {
            unset($data['logo']);
            $logo = $this->request->getFile('logo');
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $newName = $logo->getRandomName();
                $logo->move(FCPATH . 'uploads/donors/logos', $newName);
                $data['logo'] = 'uploads/donors/logos/' . $newName;
            }
        }

        $model->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Donor',
            'message' => 'Donor updated successfully.',
            'redirect' => base_url('admin/donors')
        ]);
    }

    public function delete($id)
    {
        $model = new DonorsModel();
        $model->delete($id);

        return redirect()->to('admin/donors')->with('message', 'Donor deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new DonorsModel();
        $donors = $model->orderBy('id', 'DESC')->findAll();
        $formattedData = [];

        foreach ($donors as $donor) {
            $formattedData[] = [
                $donor['logo'] ? sprintf('<img src="%s" alt="Logo" class="img-thumbnail" style="max-height: 50px;">', base_url($donor['logo'])) : '-',
                $donor['name'],
                $donor['url'] ? sprintf('<a href="%s" target="_blank">%s</a>', $donor['url'], $donor['url']) : '-',
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                        <a href="%s" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this donor?\')"><i class="fas fa-trash"></i></a>
                    </div>',
                    base_url('admin/donors/edit/' . $donor['id']),
                    base_url('admin/donors/delete/' . $donor['id'])
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $formattedData
        ]);
    }

    public function upload()
    {
        $file = $this->request->getFile('file');
        $type = $this->request->getPost('type') ?? 'logo'; // Default to 'logo'

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $targetPath = 'uploads/donors/logos';
            
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
