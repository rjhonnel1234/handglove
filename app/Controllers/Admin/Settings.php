<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;

class Settings extends BaseController
{
    public function index()
    {
        $model = new SettingsModel();
        
        $data['billing_rate'] = $model->getSetting('billing_rate', '1.00');
        $data['page_title'] = "General Settings";
        $data['session'] = session();
        
        return view('admin/settings/index', $data);
    }

    public function store()
    {
        $model = new SettingsModel();
        
        $billingRate = $this->request->getPost('billing_rate');
        
        if (!is_numeric($billingRate)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Billing rate must be a numeric value.'
            ]);
        }

        $model->updateSetting('billing_rate', $billingRate);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message_header' => 'Settings',
            'message' => 'Settings updated successfully.',
            'redirect' => base_url('admin/settings')
        ]);
    }
}
