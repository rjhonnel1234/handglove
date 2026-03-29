<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstitutionsModel;
use App\Models\StatesModel;
use App\Models\CountriesModel;
use App\Models\ProviderInstitutionTypeModel;
use App\Models\ProviderInstitutionOwnershipTypeModel;

class Institutions extends BaseController
{
    public function index()
    {
        $data['page_title'] = "Institutions";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
            ASSETS_URL . 'js/admin/institutions.js',
        ];

        return view('admin/institutions/index', $data);
    }

    public function create()
    {
        $statesModel = new StatesModel();
        $countriesModel = new CountriesModel();
        $typeModel = new ProviderInstitutionTypeModel();
        $ownerModel = new ProviderInstitutionOwnershipTypeModel();

        $data['page_title'] = "Add Institution";
        $data['session'] = session();
        $data['states'] = $statesModel->orderBy('name', 'ASC')->findAll();
        $data['countries'] = $countriesModel->orderBy('name', 'ASC')->findAll();
        $data['provider_types'] = $typeModel->orderBy('name', 'ASC')->findAll();
        $data['ownership_types'] = $ownerModel->orderBy('name', 'ASC')->findAll();

        $data['styles'] = [
            COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
        ];
        $data['scripts'] = [
            ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
        ];

        return view('admin/institutions/create', $data);
    }

    public function store()
    {
        $model = new InstitutionsModel();

        $rules = [
            'name' => 'required',
            'ccn'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'ccn'            => $this->request->getPost('ccn'),
            'name'           => $this->request->getPost('name'),
            'address'        => $this->request->getPost('address'),
            'city'           => $this->request->getPost('city'),
            'state'          => $this->request->getPost('state'),
            'zip'            => $this->request->getPost('zip'),
            'contact_number' => $this->request->getPost('contact_number'),
            'county'         => $this->request->getPost('county'),
            'ownership_type' => $this->request->getPost('ownership_type'),
            'provider_type'  => $this->request->getPost('provider_type'),
            'country'        => $this->request->getPost('country'),
        ];

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Institution',
            'message' => 'Institution added successfully.',
            'redirect' => base_url('admin/institutions')
        ]);
    }

    public function edit($id)
    {
        $model = new InstitutionsModel();
        $statesModel = new StatesModel();
        $countriesModel = new CountriesModel();
        $typeModel = new ProviderInstitutionTypeModel();
        $ownerModel = new ProviderInstitutionOwnershipTypeModel();
        
        $data['institution'] = $model->find($id);
        if (!$data['institution']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['page_title'] = "Edit Institution";
        $data['session'] = session();
        $data['states'] = $statesModel->orderBy('name', 'ASC')->findAll();
        $data['countries'] = $countriesModel->orderBy('name', 'ASC')->findAll();
        $data['provider_types'] = $typeModel->orderBy('name', 'ASC')->findAll();
        $data['ownership_types'] = $ownerModel->orderBy('name', 'ASC')->findAll();

        $data['styles'] = [
            COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
        ];
        $data['scripts'] = [
            ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
        ];

        return view('admin/institutions/edit', $data);
    }

    public function update($id)
    {
        $model = new InstitutionsModel();

        $rules = [
            'name' => 'required',
            'ccn'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'ccn'            => $this->request->getPost('ccn'),
            'name'           => $this->request->getPost('name'),
            'address'        => $this->request->getPost('address'),
            'city'           => $this->request->getPost('city'),
            'state'          => $this->request->getPost('state'),
            'zip'            => $this->request->getPost('zip'),
            'contact_number' => $this->request->getPost('contact_number'),
            'county'         => $this->request->getPost('county'),
            'ownership_type' => $this->request->getPost('ownership_type'),
            'provider_type'  => $this->request->getPost('provider_type'),
            'country'        => $this->request->getPost('country'),
        ];

        $model->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Institution',
            'message' => 'Institution updated successfully.',
            'redirect' => base_url('admin/institutions')
        ]);
    }

    public function delete($id)
    {
        $model = new InstitutionsModel();
        $model->delete($id);

        return redirect()->to('admin/institutions')->with('message', 'Institution deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new InstitutionsModel();
        $institutions = $model->select('tbl_provider_institution.*, states.name as state_name')
                             ->join('states', 'states.id = tbl_provider_institution.state', 'left')
                             ->orderBy('tbl_provider_institution.id', 'DESC')
                             ->findAll();

        $formattedData = [];
        foreach ($institutions as $inst) {
            $formattedData[] = [
                
                '<div class="text-left">'.$inst['ccn'].'</div>',
                '<div><strong>'.$inst['name'].'</strong></div><div><div class="d-flex align-items-start"><small class="mr-2"><i class="fa fa-map-marker-alt"></i></small> <div class="small">'.$inst['address'].'<br>'.$inst['city'].'<br>'.$inst['state_name'].'</div></div><div><small><i class="fa fa-phone"></i> '.$inst['contact_number'].'</small></div>',
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                    </div>',
                    base_url('admin/institutions/edit/' . $inst['id'])
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $formattedData
        ]);
    }
}
