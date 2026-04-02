<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PerDiemNetworksModel;

class PerDiemNetworks extends BaseController
{
    public function index()
    {
        $data['page_title'] = "Per Diem Networks";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
            COMPILED_ASSETS_PATH . 'css/components/dropzone'
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
            ASSETS_URL . 'js/admin/per_diem_networks.min.js',
        ];

        return view('admin/per_diem_networks/index', $data);
    }

    public function create()
    {
        $data['page_title'] = "Add Per Diem Network";
        $data['session'] = session();
        $data['scripts'] = [
            'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js',
            ASSETS_URL . 'js/admin/per_diem_networks.min.js',
        ];
        return view('admin/per_diem_networks/create', $data);
    }

    public function store()
    {
        $model = new PerDiemNetworksModel();

        $rules = [
            'name' => 'required',
            'status' => 'required|in_list[0,1]',
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
            'logo'        => $this->request->getPost('logo_path'),
        ];

        // Fallback to traditional upload if Dropzone wasn't used
        if (empty($data['logo'])) {
            $logo = $this->request->getFile('logo');
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $newName = $logo->getRandomName();
                $logo->move(FCPATH . 'uploads/per-diem-networks/logos', $newName);
                $data['logo'] = 'uploads/per-diem-networks/logos/' . $newName;
            }
        }

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Per Diem Network',
            'message' => 'Network added successfully.',
            'redirect' => base_url('admin/per-diem-networks')
        ]);
    }

    public function edit($id)
    {
        $model = new PerDiemNetworksModel();
        $data['network'] = $model->find($id);

        if (!$data['network']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['page_title'] = "Edit Per Diem Network";
        $data['session'] = session();
        $data['scripts'] = [
            'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js',
            ASSETS_URL . 'js/admin/per_diem_networks.min.js',
        ];
        return view('admin/per_diem_networks/edit', $data);
    }

    public function update($id)
    {
        $model = new PerDiemNetworksModel();

        $rules = [
            'name' => 'required',
            'status' => 'required|in_list[0,1]',
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
            'logo'        => $this->request->getPost('logo_path'),
        ];

        // Only update image field if new path is provided
        if (empty($data['logo'])) {
            unset($data['logo']);
            $logo = $this->request->getFile('logo');
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $newName = $logo->getRandomName();
                $logo->move(FCPATH . 'uploads/per-diem-networks/logos', $newName);
                $data['logo'] = 'uploads/per-diem-networks/logos/' . $newName;
            }
        }

        $model->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Per Diem Network',
            'message' => 'Network updated successfully.',
            'redirect' => base_url('admin/per-diem-networks')
        ]);
    }

    public function delete($id)
    {
        $model = new PerDiemNetworksModel();
        $model->delete($id);

        return redirect()->to('admin/per-diem-networks')->with('message', 'Network deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new PerDiemNetworksModel();
        $networks = $model->orderBy('id', 'DESC')->findAll();
        $formattedData = [];

        foreach ($networks as $network) {
            $statusBadge = ($network['status'] == 1) ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
            $formattedData[] = [
                $network['logo'] ? sprintf('<img src="%s" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">', base_url($network['logo'])) : sprintf('<img src="%s" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">', base_url('assets/img/blank-img.png')),
                $network['name'],
                $network['description'] ?: 'N/A',
                $statusBadge,
                sprintf(
                    '<div class="text-right">
                        <a href="%s" class="btn btn-sm btn-info shadow-sm"><i class="fas fa-edit"></i></a>
                        <a href="%s" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm(\'Are you sure you want to delete this network?\')"><i class="fas fa-trash"></i></a>
                    </div>',
                    base_url('admin/per-diem-networks/edit/' . $network['id']),
                    base_url('admin/per-diem-networks/delete/' . $network['id'])
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

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $targetPath = 'uploads/per-diem-networks/logos';
            
            // Create directory if it doesn't exist
            if (!is_dir(FCPATH . $targetPath)) {
                mkdir(FCPATH . $targetPath, 0755, true);
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
