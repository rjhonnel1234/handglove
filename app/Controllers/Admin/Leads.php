<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LeadsModel;
use App\Models\InstitutionsModel;
use App\Models\StatesModel;
use App\Models\CountriesModel;
use App\Models\AgenciesModel;
use App\Models\UserTypesModel;
use App\Models\LeadsManagementModel;
use App\Models\FacilityModel;
use App\Models\ClientPersonnelModel;
use App\Models\UserModel;

class Leads extends BaseController
{
    public function index()
    {
        $data['page_title'] = "Leads";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
            ASSETS_URL . 'js/admin/leads.js',
        ];

        return view('admin/leads/index', $data);
    }

    public function create()
    {
        $data['page_title'] = "Add Lead";
        $data['session'] = session();

        $institutionsModel = new InstitutionsModel();
        $data['institutions'] = $institutionsModel->orderBy('name', 'ASC')->findAll();

        $statesModel = new StatesModel();
        $countriesModel = new CountriesModel();
        $data['states'] = $statesModel->where('country_id', 233)->orderBy('name', 'ASC')->findAll();
        $data['countries'] = $countriesModel->where('id', 233)->orderBy('name', 'ASC')->findAll();

        $agenciesModel = new AgenciesModel();
        $data['agencies'] = $agenciesModel->where('status', 1)->orderBy('name', 'ASC')->findAll();

        $userTypesModel = new UserTypesModel();
        $data['user_types'] = $userTypesModel->where('is_management', 1)->orderBy('name', 'ASC')->findAll();

        $data['status_mapping'] = (new LeadsModel())->status_mapping;
        $data['styles'] = [
            COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
        ];
        $data['scripts'] = [
            ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
        ];

        return view('admin/leads/create', $data);
    }

    public function store()
    {
        $model = new LeadsModel();

        $rules = [
            'institution_id' => 'required',
            'address'        => 'required',
            'zip_code'       => 'required',
            'email'          => 'required|valid_email',
            'contact_number' => 'required',
            'date'           => 'required',
            'time'           => 'required',
        ];

        // Conditional rules for 'other' company
        if ($this->request->getPost('institution_id') === 'other') {
            $rules['company_name'] = 'required';
            $rules['country']      = 'required';
            $rules['state']        = 'required';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Validate at least one management contact
        $mgmt_names = $this->request->getPost('mgmt_name');
        $has_mgmt = false;
        if ($mgmt_names) {
            foreach ($mgmt_names as $name) {
                if (!empty(trim($name))) {
                    $has_mgmt = true;
                    break;
                }
            }
        }

        if (!$has_mgmt) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['management' => 'At least one management contact name is required.']
            ]);
        }

        $institution_id = $this->request->getPost('institution_id');
        $company_name = $this->request->getPost('company_name');
        
        $status = $this->request->getPost('status');
        $data = [
            'address'        => $this->request->getPost('address'),
            'zip_code'       => $this->request->getPost('zip_code'),
            'email'          => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('contact_number'),
            'date'           => $this->request->getPost('date'),
            'time'           => $this->request->getPost('time'),
            'status'         => $status,
            'origin'         => $this->request->getPost('origin') ?: 1,
            'notes'          => $this->request->getPost('notes'),
            'created_datetime' => date('Y-m-d H:i:s'),
        ];

        // Status timestamps
        if ($status == 10) $data['presentation_datetime'] = date('Y-m-d H:i:s');
        if ($status == 20) $data['awaiting_contract_datetime'] = date('Y-m-d H:i:s');
        if ($status == 50) $data['on_contract_datetime'] = date('Y-m-d H:i:s');
        if ($status == 100) $data['cancelled_datetime'] = date('Y-m-d H:i:s');

        $agencies = $this->request->getPost('agencies');
        $data['agencies'] = $agencies ? implode(',', $agencies) : '';

        if ($institution_id && $institution_id !== 'other') {
            $institutionsModel = new InstitutionsModel();
            $institution = $institutionsModel->find($institution_id);
            if ($institution) {
                $data['provider_id'] = $institution['id'];
                $data['company_name'] = $institution['name'];
                $data['country'] = $institution['country'];
                $data['state'] = $institution['state'];
                
                // If address/zip are empty, maybe use institution's?
                if (empty($data['address'])) $data['address'] = $institution['address'];
                if (empty($data['zip_code'])) $data['zip_code'] = $institution['zip'];
            } else {
                $data['company_name'] = $company_name;
            }
        } else {
            $data['company_name'] = $company_name;
            $data['provider_id'] = 0;
            $data['country'] = $this->request->getPost('country');
            $data['state'] = $this->request->getPost('state');
        }

        if (empty($data['company_name'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['company_name' => 'Company Name is required.']
            ]);
        }

        $id = $model->insert($data);

        // Save Leads Management
        $mgmtModel = new LeadsManagementModel();
        
        $mgmt_types = $this->request->getPost('mgmt_user_type');
        $mgmt_names = $this->request->getPost('mgmt_name');
        $mgmt_phones = $this->request->getPost('mgmt_contact_number');
        $mgmt_emails = $this->request->getPost('mgmt_email');
        $mgmt_approvers = $this->request->getPost('mgmt_is_approver');

        if ($mgmt_types) {
            foreach ($mgmt_types as $index => $user_type) {
                if (!empty($user_type) || !empty($mgmt_names[$index])) {
                    $mgmtModel->insert([
                        'leads_id'       => $id,
                        'user_type'      => $user_type,
                        'name'           => $mgmt_names[$index],
                        'contact_number' => $mgmt_phones[$index],
                        'email'          => $mgmt_emails[$index],
                        'is_approver'    => (isset($mgmt_approvers) && $mgmt_approvers == $index) ? 1 : 0,
                    ]);
                }
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Lead',
            'message' => 'Lead added successfully.',
            'redirect' => base_url('admin/leads')
        ]);
    }

    public function edit($id)
    {
        $model = new LeadsModel();
        
        $data['lead'] = $model->find($id);
        if (!$data['lead']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($data['lead']['status'] == 50) {
            return redirect()->to('admin/leads')->with('error', 'Leads that are On Contract cannot be edited.');
        }

        $institutionsModel = new InstitutionsModel();
        $data['institutions'] = $institutionsModel->orderBy('name', 'ASC')->findAll();
        $data['origin_mapping'] = (new LeadsModel())->origin_mapping;

        $statesModel = new StatesModel();
        $countriesModel = new CountriesModel();
        $data['states'] = $statesModel->where('country_id', 233)->orderBy('name', 'ASC')->findAll();
        $data['countries'] = $countriesModel->where('id', 233)->orderBy('name', 'ASC')->findAll();

        $agenciesModel = new AgenciesModel();
        $data['agencies'] = $agenciesModel->where('status', 1)->orderBy('name', 'ASC')->findAll();

        $userTypesModel = new UserTypesModel();
        $data['user_types'] = $userTypesModel->where('is_management', 1)->orderBy('name', 'ASC')->findAll();

        $mgmtModel = new LeadsManagementModel();
        $data['management'] = $mgmtModel->where('leads_id', $id)->findAll();

        $data['page_title'] = "Edit Lead";
        $data['session'] = session();

        $data['status_mapping'] = (new LeadsModel())->status_mapping;
        $data['styles'] = [
            COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
        ];
        $data['scripts'] = [
            ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
        ];

        return view('admin/leads/edit', $data);
    }

    public function update($id)
    {
        $model = new LeadsModel();

        // Check current status before updating
        $currentLead = $model->find($id);
        if ($currentLead && $currentLead['status'] == 50) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Leads that are On Contract cannot be updated.'
            ]);
        }

        $rules = [
            'institution_id' => 'required',
            'address'        => 'required',
            'zip_code'       => 'required',
            'email'          => 'required|valid_email',
            'contact_number' => 'required',
            'date'           => 'required',
            'time'           => 'required',
        ];

        // Conditional rules for 'other' company
        if ($this->request->getPost('institution_id') === 'other') {
            $rules['company_name'] = 'required';
            $rules['country']      = 'required';
            $rules['state']        = 'required';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Validate at least one management contact
        $mgmt_names = $this->request->getPost('mgmt_name');
        $has_mgmt = false;
        if ($mgmt_names) {
            foreach ($mgmt_names as $name) {
                if (!empty(trim($name))) {
                    $has_mgmt = true;
                    break;
                }
            }
        }

        if (!$has_mgmt) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['management' => 'At least one management contact name is required.']
            ]);
        }

        $institution_id = $this->request->getPost('institution_id');
        $company_name = $this->request->getPost('company_name');

        $status = $this->request->getPost('status');
        $data = [
            'address'        => $this->request->getPost('address'),
            'zip_code'       => $this->request->getPost('zip_code'),
            'email'          => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('contact_number'),
            'date'           => $this->request->getPost('date'),
            'time'           => $this->request->getPost('time'),
            'status'         => $status,
            'notes'          => $this->request->getPost('notes'),
        ];

        // Fetch current lead to check if status is changing
        $currentLead = $model->find($id);
        if ($currentLead && $currentLead['status'] != $status) {
            if ($status == 10) $data['presentation_datetime'] = date('Y-m-d H:i:s');
            if ($status == 20) $data['awaiting_contract_datetime'] = date('Y-m-d H:i:s');
            if ($status == 50) {
                $data['on_contract_datetime'] = date('Y-m-d H:i:s');
                // Trigger conversion to facility
                $this->convertToFacility($id);
            }
            if ($status == 100) $data['cancelled_datetime'] = date('Y-m-d H:i:s');
        }

        $agencies = $this->request->getPost('agencies');
        $data['agencies'] = $agencies ? implode(',', $agencies) : '';

        if ($institution_id && $institution_id !== 'other') {
            $institutionsModel = new InstitutionsModel();
            $institution = $institutionsModel->find($institution_id);
            if ($institution) {
                $data['provider_id'] = $institution['id'];
                $data['company_name'] = $institution['name'];
                $data['country'] = $institution['country'];
                $data['state'] = $institution['state'];
            } else {
                $data['company_name'] = $company_name;
            }
        } else {
            $data['company_name'] = $company_name;
            $data['provider_id'] = 0;
            $data['country'] = $this->request->getPost('country');
            $data['state'] = $this->request->getPost('state');
        }

        if (empty($data['company_name'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['company_name' => 'Company Name is required.']
            ]);
        }

        $model->update($id, $data);

        // Update Leads Management
        $mgmtModel = new LeadsManagementModel();
        $mgmtModel->where('leads_id', $id)->delete(); // Clear existing management entries

        $mgmt_types = $this->request->getPost('mgmt_user_type');
        $mgmt_names = $this->request->getPost('mgmt_name');
        $mgmt_phones = $this->request->getPost('mgmt_contact_number');
        $mgmt_emails = $this->request->getPost('mgmt_email');
        $mgmt_approvers = $this->request->getPost('mgmt_is_approver');

        if ($mgmt_types) {
            foreach ($mgmt_types as $index => $user_type) {
                if (!empty($user_type) || !empty($mgmt_names[$index])) {
                    $mgmtModel->insert([
                        'leads_id'       => $id,
                        'user_type'      => $user_type,
                        'name'           => $mgmt_names[$index],
                        'contact_number' => $mgmt_phones[$index],
                        'email'          => $mgmt_emails[$index],
                        'is_approver'    => (isset($mgmt_approvers) && $mgmt_approvers == $index) ? 1 : 0,
                    ]);
                }
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Lead',
            'message' => 'Lead updated successfully.',
            'redirect' => base_url('admin/leads')
        ]);
    }

    public function delete($id)
    {
        $model = new LeadsModel();
        $model->delete($id);

        // Also delete associated management entries
        $mgmtModel = new LeadsManagementModel();
        $mgmtModel->where('leads_id', $id)->delete();

        return redirect()->to('admin/leads')->with('message', 'Lead deleted successfully.');
    }

    public function view($id)
    {
        $model = new LeadsModel();
        $data['lead'] = $model->find($id);
        
        if (!$data['lead']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mgmtModel = new LeadsManagementModel();
        $data['management'] = $mgmtModel->where('leads_id', $id)->findAll();
        
        $userTypesModel = new UserTypesModel();
        $data['user_types'] = array_column($userTypesModel->findAll(), 'name', 'id');

        $data['page_title'] = "View Lead";
        $data['session'] = session();
        $data['status_mapping'] = $model->status_mapping;
        $data['status_badge_color'] = $model->status_badge_color;

        return view('admin/leads/view', $data);
    }

    private function convertToFacility($id)
    {
        $leadModel = new LeadsModel();
        $facilityModel = new FacilityModel();
        
        $lead = $leadModel->find($id);
        if (!$lead) return;

        // Check if already converted
        $existing = $facilityModel->where('leads_id', $id)->first();
        if ($existing) return;

        // 1. Create Facility
        $facilityData = [
            'leads_id'        => $lead['id'],
            'provider_id'     => $lead['provider_id'],
            'company_name'    => $lead['company_name'],
            'company_address' => $lead['address'],
            'zip_code'        => $lead['zip_code'],
            'company_email'   => $lead['email'],
            'company_number'  => $lead['contact_number'],
            'agencies'        => $lead['agencies'],
            'country_id'      => $lead['country'],
            'state_id'        => $lead['state'],
            'status'          => 1, // Active by default
        ];
        $facilityId = $facilityModel->insert($facilityData);

        // 2. Convert Management to Personnel & Users
        $mgmtModel = new LeadsManagementModel();
        $userModel = new UserModel();
        $personnelModel = new ClientPersonnelModel();
        
        $managements = $mgmtModel->where('leads_id', $id)->findAll();
        
        foreach ($managements as $mgmt) {
            if (empty($mgmt['email'])) continue;

            // Split Name
            $nameParts = explode(' ', $mgmt['name'], 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            // Check if user already exists
            $user = $userModel->where('email', $mgmt['email'])->first();
            if (!$user) {
                $userData = [
                    'facility_id'    => $facilityId,
                    'type'           => $mgmt['user_type'],
                    'first_name'     => $firstName,
                    'last_name'      => $lastName,
                    'email'          => $mgmt['email'],
                    'contact_number' => $mgmt['contact_number'],
                    'password'       => password_hash('Welcome123!', PASSWORD_DEFAULT),
                    'status'         => 1, // Active
                ];
                $userId = $userModel->insert($userData);
            } else {
                $userId = $user['id'];
                // Update user to link to this facility if they didn't have one? 
                if (empty($user['facility_id'])) {
                    $userModel->update($userId, ['facility_id' => $facilityId]);
                }
            }

            // Create Personnel
            $personnelData = [
                'client_id'      => $facilityId,
                'user_id'        => $userId,
                'type'           => $mgmt['user_type'],
                'first_name'     => $firstName,
                'last_name'      => $lastName,
                'email'          => $mgmt['email'],
                'contact_number' => $mgmt['contact_number'],
                'status'         => 1, // Active
            ];
            $personnelModel->insert($personnelData);
        }
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new LeadsModel();
        $leads = $model->orderBy('date, status ASC')->findAll();

        $formattedData = [];
        $statusMapping = $model->status_mapping;
        $statusBadgeColor = $model->status_badge_color;

        $mgmtModel = new LeadsManagementModel();
        
        foreach ($leads as $lead) {

            $approver_details = '';
            $statusBadge = '<div class="text-center"><span class="badge '.$statusBadgeColor[$lead['status']].' text-white">' . $statusMapping[$lead['status']] . '</span></div>';

            $management = $mgmtModel->where('leads_id', $lead['id'])->where('is_approver', 1)->first();
            if(!empty($management)){
                $approver_details .= '<div><small><strong>'.$management['name'].'</strong><br>'.$management['email'].'<br>'.$management['contact_number'].'</small></div>';
            }

            $statusOptionsHtml = '';
            foreach ($statusMapping as $statusId => $statusName) {
                $statusOptionsHtml .= sprintf(
                    '<li><a class="dropdown-item change-status" href="javascript:void(0)" data-id="%s" data-status="%s">%s</a></li>',
                    $lead['id'],
                    $statusId,
                    $statusName
                );
            }

            $editButton = '';
            if ($lead['status'] != 50) {
                $editButton = sprintf('<li class="p-1"><a href="%s" class="text-secondary small"><i class="fas fa-edit"></i> Edit Lead</a></li><li class="p-1"><a href="%s" class="text-secondary small"><i class="fas fa-eye"></i> View Lead</a></li>', base_url('admin/leads/edit/' . $lead['id']), base_url('admin/leads/view/' . $lead['id']));
            }else{
                $editButton = sprintf('<li class="p-1"><a href="%s" class="text-secondary small"><i class="fas fa-eye"></i> View Lead</a></li>', base_url('admin/leads/view/' . $lead['id']));
            }

            $formattedData[] = [
                '<div><strong>'.$lead['company_name'].'</strong></div><div><small>'.$lead['email'].'<br>'.$lead['contact_number'].'</small></div>',
                $approver_details,
                '<small>'.(!empty($lead['date']) ? date('d M Y', strtotime($lead['date'])).' '.date('h:i A', strtotime($lead['time'])) : '') .'</small>',
                $statusBadge,
                sprintf(
                    '<div class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm thm-btn dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false" title="Options">
                                <i class="fas fa-cogs"></i>
                            </button>
                            <div class="dropdown-menu pl-3 pr-3">
                                <ul class="list-unstyled m-0">
                                   %s
                                </ul>
                            </div>
                        </div>

                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false" title="Change Status">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <div class="dropdown-menu pl-3 pr-3">
                                <strong><small>Set Lead Status</small></strong><br>
                                <ul class="list-unstyled small m-0">
                                %s
                                </ul>
                            </div>
                        </div>
                    </div>',
                    $editButton,
                    $statusOptionsHtml,
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $formattedData
        ]);
    }

    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if (!$id || !isset($status)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid parameters'
            ]);
        }

        $model = new LeadsModel();
        
        $updateData = ['status' => $status];
        
        // Update datetime columns based on status (Exclude Cold=0)
        if ($status == 10) {
            $updateData['presentation_datetime'] = date('Y-m-d H:i:s');
        } elseif ($status == 20) {
            $updateData['awaiting_contract_datetime'] = date('Y-m-d H:i:s');
        } elseif ($status == 50) {
            $updateData['on_contract_datetime'] = date('Y-m-d H:i:s');
            // Trigger conversion to facility
            $this->convertToFacility($id);
        } elseif ($status == 100) {
            $updateData['cancelled_datetime'] = date('Y-m-d H:i:s');
        }

        $model->update($id, $updateData);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Status updated successfully'
        ]);
    }
}
