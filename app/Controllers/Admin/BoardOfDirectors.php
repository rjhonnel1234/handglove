<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BoardOfDirectorsModel;

class BoardOfDirectors extends BaseController
{
    public function index()
    {
        $model = new BoardOfDirectorsModel();
        $data['board_members'] = $model->orderBy('id', 'DESC')->findAll();
        $data['page_title'] = "Board of Directors";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
            COMPILED_ASSETS_PATH . 'css/components/dropzone'
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
            ASSETS_URL . 'js/admin/board_of_directors.min.js',
        ];

        return view('admin/board_of_directors/index', $data);
    }

    public function create()
    {
        $data['page_title'] = "Add Board Member";
        $data['session'] = session();
        $data['scripts'] = [
            'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js',
            ASSETS_URL . 'js/admin/board_of_directors.min.js',
        ];
        return view('admin/board_of_directors/create', $data);
    }

    public function store()
    {
        $model = new BoardOfDirectorsModel();

        $rules = [
            'company_name' => 'required',
            'ceo_name' => 'required',
            'position' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'company_name' => $this->request->getPost('company_name'),
            'ceo_name' => $this->request->getPost('ceo_name'),
            'position' => $this->request->getPost('position'),
            'title' => $this->request->getPost('title'),
            'company_website' => $this->request->getPost('company_website'),
            'created_datetime' => date('Y-m-d H:i:s'),
            'picture' => $this->request->getPost('picture_path'),
            'company_logo' => $this->request->getPost('logo_path'),
        ];

        // Fallback to traditional upload if Dropzone wasn't used
        if (empty($data['picture'])) {
            $picture = $this->request->getFile('picture');
            if ($picture && $picture->isValid() && !$picture->hasMoved()) {
                $newName = $picture->getRandomName();
                $picture->move(FCPATH . 'uploads/board/pictures', $newName);
                $data['picture'] = 'uploads/board/pictures/' . $newName;
            }
        }

        if (empty($data['company_logo'])) {
            $logo = $this->request->getFile('company_logo');
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $newName = $logo->getRandomName();
                $logo->move(FCPATH . 'uploads/board/logos', $newName);
                $data['company_logo'] = 'uploads/board/logos/' . $newName;
            }
        }

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Board Member',
            'message' => 'Board member added successfully.',
            'redirect' => base_url('admin/board-of-directors')
        ]);
    }

    public function edit($id)
    {
        $model = new BoardOfDirectorsModel();
        $data['member'] = $model->find($id);

        if (!$data['member']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['page_title'] = "Edit Board Member";
        $data['session'] = session();
        $data['scripts'] = [
            'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js',
            ASSETS_URL . 'js/admin/board_of_directors.min.js',
        ];
        return view('admin/board_of_directors/edit', $data);
    }

    public function update($id)
    {
        $model = new BoardOfDirectorsModel();

        $rules = [
            'company_name' => 'required',
            'ceo_name' => 'required',
            'position' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'company_name' => $this->request->getPost('company_name'),
            'ceo_name' => $this->request->getPost('ceo_name'),
            'position' => $this->request->getPost('position'),
            'title' => $this->request->getPost('title'),
            'company_website' => $this->request->getPost('company_website'),
            'picture' => $this->request->getPost('picture_path'),
            'company_logo' => $this->request->getPost('logo_path'),
        ];

        // Only update image fields if new paths are provided
        if (empty($data['picture'])) {
            unset($data['picture']);
            $picture = $this->request->getFile('picture');
            if ($picture && $picture->isValid() && !$picture->hasMoved()) {
                $newName = $picture->getRandomName();
                $picture->move(FCPATH . 'uploads/board/pictures', $newName);
                $data['picture'] = 'uploads/board/pictures/' . $newName;
            }
        }

        if (empty($data['company_logo'])) {
            unset($data['company_logo']);
            $logo = $this->request->getFile('company_logo');
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $newName = $logo->getRandomName();
                $logo->move(FCPATH . 'uploads/board/logos', $newName);
                $data['company_logo'] = 'uploads/board/logos/' . $newName;
            }
        }

        $model->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Board Member',
            'message' => 'Board member updated successfully.',
            'redirect' => base_url('admin/board-of-directors')
        ]);
    }

    public function delete($id)
    {
        $model = new BoardOfDirectorsModel();
        $model->delete($id);

        return redirect()->to('admin/board-of-directors')->with('message', 'Board member deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new BoardOfDirectorsModel();
        $board_members = $model->orderBy('id', 'DESC')->findAll();
        $formattedData = [];

        foreach ($board_members as $member) {
            $formattedData[] = [
                $member['picture'] ? sprintf('<img src="%s" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%%;">', base_url($member['picture'])) : sprintf('<img src="%s" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%%;">', base_url('assets/img/blank-img.png')),
                $member['company_name'],
                $member['ceo_name'],
                $member['position'],
                $member['title'],
                sprintf(
                    '<div class="text-right">
                        <a href="%s" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                        <a href="%s" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this board member?\')"><i class="fas fa-trash"></i></a>
                    </div>',
                    base_url('admin/board-of-directors/edit/' . $member['id']),
                    base_url('admin/board-of-directors/delete/' . $member['id'])
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
        $type = $this->request->getPost('type'); // 'picture' or 'logo'

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $targetPath = ($type == 'logo') ? 'uploads/board/logos' : 'uploads/board/pictures';
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
