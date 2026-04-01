<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FaqModel;

class Faqs extends BaseController
{
    public function index()
    {
        $data['page_title'] = "Frequently Asked Questions";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
            ASSETS_URL . 'js/admin/faqs.min.js',
        ];

        return view('admin/faqs/index', $data);
    }

    public function create()
    {
        $data['page_title'] = "Add FAQ";
        $data['session'] = session();
        $data['scripts'] = [
            ASSETS_URL . 'js/admin/faqs.min.js',
        ];
        return view('admin/faqs/create', $data);
    }

    public function store()
    {
        $model = new FaqModel();

        $rules = [
            'question' => 'required|min_length[5]',
            'answer'   => 'required|min_length[10]',
            'status'   => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'question' => $this->request->getPost('question'),
            'answer'   => $this->request->getPost('answer'),
            'status'   => $this->request->getPost('status'),
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
            'message_header' => 'FAQ',
            'message' => 'FAQ added successfully.',
            'redirect' => base_url('admin/faqs')
        ]);
    }

    public function edit($id)
    {
        $model = new FaqModel();
        $data['faq'] = $model->find($id);

        if (!$data['faq']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['page_title'] = "Edit FAQ";
        $data['session'] = session();
        $data['scripts'] = [
            ASSETS_URL . 'js/admin/faqs.min.js',
        ];
        return view('admin/faqs/edit', $data);
    }

    public function update($id)
    {
        $model = new FaqModel();

        $rules = [
            'question' => 'required|min_length[5]',
            'answer'   => 'required|min_length[10]',
            'status'   => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'question' => $this->request->getPost('question'),
            'answer'   => $this->request->getPost('answer'),
            'status'   => $this->request->getPost('status'),
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
            'message_header' => 'FAQ',
            'message' => 'FAQ updated successfully.',
            'redirect' => base_url('admin/faqs')
        ]);
    }

    public function delete($id)
    {
        $model = new FaqModel();
        $model->delete($id);

        return redirect()->to('admin/faqs')->with('message', 'FAQ deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new FaqModel();
        $faqs = $model->orderBy('id', 'DESC')->findAll();
        $formattedData = [];

        foreach ($faqs as $faq) {
            $formattedData[] = [
                $faq['question'],
                mb_strimwidth($faq['answer'], 0, 100, "..."),
                $faq['status'] == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>',
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                        <a href="%s" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this FAQ?\')"><i class="fas fa-trash"></i></a>
                    </div>',
                    base_url('admin/faqs/edit/' . $faq['id']),
                    base_url('admin/faqs/delete/' . $faq['id'])
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $formattedData
        ]);
    }
}
