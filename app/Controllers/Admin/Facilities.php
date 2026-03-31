<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FacilityModel;
use App\Models\StatesModel;
use App\Models\CountriesModel;
use App\Models\AgenciesModel;
use App\Models\InstitutionsModel;
use App\Models\FacilityUnitsModel;
use App\Models\ClientPersonnelModel;
use App\Models\ShiftsModel;
use App\Models\InvoicesModel;
use App\Models\UserTypesModel;
use App\Models\ClinicianTypesModel;

class Facilities extends BaseController
{
    public function index()
    {
        $data['page_title'] = "Facilities";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
        ];

        return view('admin/facilities/index', $data);
    }

    public function create()
    {
        $statesModel = new StatesModel();
        $countriesModel = new CountriesModel();
        $agenciesModel = new AgenciesModel();
        $institutionsModel = new InstitutionsModel();

        $data['page_title'] = "Add Facility";
        $data['session'] = session();
        $data['states'] = $statesModel->where('country_id', 233)->orderBy('name', 'ASC')->findAll();
        $data['countries'] = $countriesModel->where('id', 233)->orderBy('name', 'ASC')->findAll();
        $data['agencies'] = $agenciesModel->findAll();
        $data['providers'] = $institutionsModel->orderBy('name', 'ASC')->findAll();

        $data['styles'] = [
            COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
            COMPILED_ASSETS_PATH . 'css/components/dropzone'
        ];
        $data['scripts'] = [
            'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js',
            ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
        ];

        return view('admin/facilities/create', $data);
    }

    public function store()
    {
        $model = new FacilityModel();
        $institutionsModel = new InstitutionsModel();

        $rules = [
            'provider_id' => 'required',
            'company_email' => 'required|valid_email',
        ];

        if ($this->request->getPost('provider_id') === 'others') {
            $rules['company_name'] = 'required';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $agencies = "";
        if (!empty($this->request->getPost('agencies'))) {
            $agencies = implode(",", $this->request->getPost('agencies'));
        }

        $logoPath = $this->request->getPost('company_logo_path');
        $provider_id = $this->request->getPost('provider_id');
        $company_name = $this->request->getPost('company_name');
        $company_address = $this->request->getPost('company_address');
        $zip_code = $this->request->getPost('zip_code');
        $state_id = $this->request->getPost('state_id');
        $country_id = $this->request->getPost('country_id');

        if ($provider_id !== 'others') {
            $provider = $institutionsModel->find($provider_id);
            if ($provider) {
                $company_name = $provider['name'];
                $company_address = $provider['address'];
                $zip_code = $provider['zip'];
                $state_id = $provider['state'];
                $country_id = $provider['country'];
            }
        } else {
            $provider_id = 0;
        }

        $data = [
            'provider_id' => $provider_id,
            'company_name' => $company_name,
            'company_email' => $this->request->getPost('company_email'),
            'company_number' => $this->request->getPost('company_number'),
            'company_address' => $company_address,
            'zip_code' => $zip_code,
            'state_id' => $state_id,
            'country_id' => $country_id,
            'agencies' => $agencies,
            'status' => $this->request->getPost('status'),
            'company_logo' => $logoPath ? base_url($logoPath) : '',
        ];

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Facility',
            'message' => 'Facility added successfully.',
            'redirect' => base_url('admin/facilities')
        ]);
    }

    public function edit($id)
    {
        $model = new FacilityModel();
        $statesModel = new StatesModel();
        $countriesModel = new CountriesModel();
        $agenciesModel = new AgenciesModel();
        $data['facility'] = $model->select('tbl_clients.*, states.name as state_name, countries.name as country_name')
            ->join('states', 'states.id = tbl_clients.state_id', 'left')
            ->join('countries', 'countries.id = tbl_clients.country_id', 'left')
            ->find($id);

        if (!$data['facility']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['page_title'] = "Edit Facility";
        $data['session'] = session();
        $data['states'] = $statesModel->where('country_id', 233)->orderBy('name', 'ASC')->findAll();
        $data['countries'] = $countriesModel->where('id', 233)->orderBy('name', 'ASC')->findAll();
        $data['agencies'] = $agenciesModel->findAll();
        $data['facility']['agencies'] = explode(',', $data['facility']['agencies'] ?? '');

        $data['styles'] = [
            COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
            COMPILED_ASSETS_PATH . 'css/components/dropzone'
        ];
        $data['scripts'] = [
            ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js',
        ];

        return view('admin/facilities/edit', $data);
    }

    public function update($id)
    {
        $model = new FacilityModel();
        $institutionsModel = new InstitutionsModel();

        $rules = [
            'provider_id' => 'required',
            'company_email' => 'required|valid_email',
        ];

        if ($this->request->getPost('provider_id') === 'others') {
            $rules['company_name'] = 'required';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $agencies = "";
        if (!empty($this->request->getPost('agencies'))) {
            $agencies = implode(",", $this->request->getPost('agencies'));
        }

        $logoPath = $this->request->getPost('company_logo_path');
        $provider_id = $this->request->getPost('provider_id');
        $company_name = $this->request->getPost('company_name');
        $company_address = $this->request->getPost('company_address');
        $zip_code = $this->request->getPost('zip_code');
        $state_id = $this->request->getPost('state_id');
        $country_id = $this->request->getPost('country_id');

        if ($provider_id !== 'others') {
            $provider = $institutionsModel->find($provider_id);
            if ($provider) {
                $company_name = $provider['name'];
                $company_address = $provider['address'];
                $zip_code = $provider['zip'];
                $state_id = $provider['state'];
                $country_id = $provider['country'];
            }
        } else {
            $provider_id = 0;
        }

        $data = [
            'provider_id' => $provider_id,
            'company_name' => $company_name,
            'company_email' => $this->request->getPost('company_email'),
            'company_number' => $this->request->getPost('company_number'),
            'company_address' => $company_address,
            'zip_code' => $zip_code,
            'state_id' => $state_id,
            'country_id' => $country_id,
            'agencies' => $agencies,
            'status' => $this->request->getPost('status'),
        ];

        if (!empty($logoPath)) {
            $data['company_logo'] = base_url($logoPath);
        }

        $model->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Facility',
            'message' => 'Facility updated successfully.',
            'redirect' => base_url('admin/facilities')
        ]);
    }

    public function delete($id)
    {
        $model = new FacilityModel();
        $model->delete($id);

        return redirect()->to('admin/facilities')->with('message', 'Facility deleted successfully.');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new FacilityModel();
        $statusMapping = $model->status_mapping;
        $statusBadgeColor = $model->status_badge_color;
        $facilities = $model->select('tbl_clients.*, states.name as state_name')
            ->join('states', 'states.id = tbl_clients.state_id', 'left')
            ->orderBy('tbl_clients.id', 'DESC')
            ->findAll();

        $formattedData = [];
        foreach ($facilities as $facility) {
            $statusBadge = '<div class="text-center"><span class="badge ' . $statusBadgeColor[$facility['status']] . ' text-white">' . $statusMapping[$facility['status']] . '</span></div>';

            $formattedData[] = [
                $facility['company_logo'] ? sprintf('<img src="%s" class="img-thumbnail" width="50" height="50">', $facility['company_logo']) : '-',
                sprintf('<div><strong>%s</strong></div><div><small>%s</small></div>', $facility['company_name'], $facility['company_email']),
                sprintf('<div><small><i class="fas fa-map-marker-alt"></i> %s, %s</small></div><div><small><i class="fas fa-phone"></i> %s</small></div>', $facility['company_address'], $facility['state_name'] ?? 'N/A', $facility['company_number']),
                $statusBadge,
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-outline-primary" title="Details"><i class="fas fa-info-circle"></i></a>
                        <a href="%s" class="btn btn-sm btn-info text-white" title="Edit"><i class="fas fa-edit"></i></a>
                    </div>',
                    base_url('admin/facilities/details/' . $facility['id']),
                    base_url('admin/facilities/edit/' . $facility['id']),
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
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $targetPath = 'uploads/facilities';

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

    /* -------------------------------------------------------------------------- */
    /*                               Client Details                               */
    /* -------------------------------------------------------------------------- */

    public function details($id)
    {
        $model = new FacilityModel();
        $facility = $model->select('tbl_clients.*, states.name as state_name, countries.name as country_name')
            ->join('states', 'states.id = tbl_clients.state_id', 'left')
            ->join('countries', 'countries.id = tbl_clients.country_id', 'left')
            ->find($id);

        if (!$facility) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['page_title'] = "Facility Details: " . $facility['company_name'];
        $data['facility'] = $facility;
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
        ];

        return view('admin/facilities/details', $data);
    }

    /* -------------------------------------------------------------------------- */
    /*                                    Units                                   */
    /* -------------------------------------------------------------------------- */

    public function units_list($client_id)
    {
        $model = new FacilityUnitsModel();
        $units = $model->where('client_id', $client_id)->orderBy('id', 'DESC')->findAll();

        $formattedData = [];
        foreach ($units as $unit) {
            $formattedData[] = [
                $unit['name'],
                $unit['description'] ?: '-',
                $unit['census'] ?: '-',
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-info text-white" title="Edit"><i class="fas fa-edit"></i></a>
                        <button type="button" class="btn btn-sm btn-danger delete-item" data-url="%s" title="Delete"><i class="fas fa-trash"></i></button>
                    </div>',
                    base_url('admin/facilities/units/edit/' . $unit['id']),
                    base_url('admin/facilities/units/delete/' . $unit['id'])
                )
            ];
        }

        return $this->response->setJSON(['success' => 1, 'data' => $formattedData]);
    }

    public function units_create($client_id)
    {
        $data['page_title'] = "Add Unit";
        $data['client_id'] = $client_id;
        $data['user_types'] = (new UserTypesModel())->findAll();

        return view('admin/facilities/units/create', $data);
    }

    public function units_edit($id)
    {
        $model = new FacilityUnitsModel();
        $data['unit'] = $model->find($id);
        if (!$data['unit'])
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $data['page_title'] = "Edit Unit";
        $data['client_id'] = $data['unit']['client_id'];
        $data['user_types'] = (new UserTypesModel())->findAll();

        return view('admin/facilities/units/edit', $data);
    }

    public function units_store()
    {
        $model = new FacilityUnitsModel();
        $data = [
            'client_id' => $this->request->getPost('client_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'census' => $this->request->getPost('census'),
        ];

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Unit saved successfully.',
            'redirect' => base_url('admin/facilities/details/' . $data['client_id'])
        ]);
    }

    public function units_update($id)
    {
        $model = new FacilityUnitsModel();
        $data = [
            'client_id' => $this->request->getPost('client_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'census' => $this->request->getPost('census'),
        ];

        $model->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Unit updated successfully.',
            'redirect' => base_url('admin/facilities/details/' . $data['client_id'])
        ]);
    }

    public function units_delete($id)
    {
        $model = new FacilityUnitsModel();
        $model->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Unit deleted.']);
    }

    /* -------------------------------------------------------------------------- */
    /*                                  Personnel                                 */
    /* -------------------------------------------------------------------------- */

    public function personnel_list($client_id)
    {
        $model = new ClientPersonnelModel();
        $personnel = $model->select('tbl_client_personnel.*, tbl_user_types.name as type_name')
            ->join('tbl_user_types', 'tbl_user_types.id = tbl_client_personnel.type', 'left')
            ->where('client_id', $client_id)
            ->orderBy('id', 'DESC')
            ->findAll();

        $formattedData = [];
        foreach ($personnel as $p) {
            $formattedData[] = [
                $p['first_name'] . ' ' . $p['last_name'],
                $p['email'],
                $p['contact_number'],
                $p['type_name'] ?: 'N/A',
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-info text-white" title="Edit"><i class="fas fa-edit"></i></a>
                        <button type="button" class="btn btn-sm btn-danger delete-item" data-url="%s" title="Delete"><i class="fas fa-trash"></i></button>
                    </div>',
                    base_url('admin/facilities/personnel/edit/' . $p['id']),
                    base_url('admin/facilities/personnel/delete/' . $p['id'])
                )
            ];
        }

        return $this->response->setJSON(['success' => 1, 'data' => $formattedData]);
    }

    public function personnel_create($client_id)
    {
        $data['page_title'] = "Add Personnel";
        $data['client_id'] = $client_id;
        $data['user_types'] = (new UserTypesModel())->findAll();
        $data['clinician_types'] = (new ClinicianTypesModel())->findAll();

        return view('admin/facilities/personnel/create', $data);
    }

    public function personnel_edit($id)
    {
        $model = new ClientPersonnelModel();
        $data['personnel'] = $model->find($id);
        if (!$data['personnel'])
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $data['page_title'] = "Edit Personnel";
        $data['client_id'] = $data['personnel']['client_id'];
        $data['user_types'] = (new UserTypesModel())->findAll();
        $data['clinician_types'] = (new ClinicianTypesModel())->findAll();

        return view('admin/facilities/personnel/edit', $data);
    }

    public function personnel_store()
    {
        $model = new ClientPersonnelModel();
        $data = [
            'client_id' => $this->request->getPost('client_id'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('contact_number'),
            'type' => $this->request->getPost('type'),
            'clinician_type' => $this->request->getPost('clinician_type'),
            'status' => 1,
        ];

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Personnel saved successfully.',
            'redirect' => base_url('admin/facilities/details/' . $data['client_id'])
        ]);
    }

    public function personnel_update($id)
    {
        $model = new ClientPersonnelModel();
        $data = [
            'client_id' => $this->request->getPost('client_id'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('contact_number'),
            'type' => $this->request->getPost('type'),
            'clinician_type' => $this->request->getPost('clinician_type'),
            'status' => 1,
        ];

        $model->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Personnel updated successfully.',
            'redirect' => base_url('admin/facilities/details/' . $data['client_id'])
        ]);
    }

    public function personnel_delete($id)
    {
        $model = new ClientPersonnelModel();
        $model->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Personnel deleted.']);
    }

    /* -------------------------------------------------------------------------- */
    /*                                   Shifts                                   */
    /* -------------------------------------------------------------------------- */

    public function shifts_list($client_id)
    {
        $model = new ShiftsModel();
        $shifts = $model->select('tbl_shifts.*, tbl_client_units.name as unit_name')
            ->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'left')
            ->where('tbl_shifts.client_id', $client_id)
            ->orderBy('tbl_shifts.date', 'DESC')
            ->findAll();

        $formattedData = [];
        foreach ($shifts as $s) {
            $formattedData[] = [
                $s['date'],
                $s['start_time'] . ' - ' . $s['end_time'],
                $s['unit_name'] ?: 'N/A',
                $s['total_hours'],
                $s['status']
            ];
        }

        return $this->response->setJSON(['success' => 1, 'data' => $formattedData]);
    }

    /* -------------------------------------------------------------------------- */
    /*                                  Invoices                                  */
    /* -------------------------------------------------------------------------- */

    public function invoices_list($client_id)
    {
        $model = new InvoicesModel();
        $invoices = $model->select('tbl_invoices.*, tbl_clinicians.name')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_invoices.clinician_id', 'left')
            ->where('tbl_invoices.client_id', $client_id)
            ->orderBy('tbl_invoices.created_at', 'DESC')
            ->findAll();

        $formattedData = [];
        foreach ($invoices as $inv) {
            $formattedData[] = [
                '#' . $inv['id'],
                $inv['created_at'],
                $inv['name'],
                $inv['total_hours'],
                number_format($inv['total_amount'], 2),
                $inv['status']
            ];
        }

        return $this->response->setJSON(['success' => 1, 'data' => $formattedData]);
    }
}
