<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InvoicesModel;
use App\Models\CliniciansModel; // I need to verify if this exists
use App\Models\ClientsModel;    // I need to verify if this exists

class Invoices extends BaseController
{
    public function index()
    {
        $model = new InvoicesModel();
        
        // I'll use a join to get clinician and client names
        // $data['invoices'] = $model->select('tbl_invoices.*, tbl_clinicians.name as clinician_name, tbl_clients.name as client_name')
        //     ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_invoices.clinician_id', 'left')
        //     ->join('tbl_clients', 'tbl_clients.id = tbl_invoices.client_id', 'left')
        //     ->orderBy('tbl_invoices.id', 'DESC')
        //     ->findAll();

        $data['page_title'] = "Invoices";
        $data['session'] = session();
        $data['styles'] = [
            'plugins/datatables',
        ];
        $data['scripts'] = [
            'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
        ];

        return view('admin/invoices/index', $data);
    }

    public function view($id)
    {
        $model = new InvoicesModel();
        $invoice = $model->select('tbl_invoices.*, tbl_clinicians.name as clinician_name, tbl_clinicians.profile_pic_url, tbl_clients.company_name as client_name, tbl_clients.company_address as client_address')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_invoices.clinician_id', 'left')
            ->join('tbl_clients', 'tbl_clients.id = tbl_invoices.client_id', 'left')
            ->find($id);

        if (!$invoice) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'invoice'    => $invoice,
            'page_title' => 'Invoice #' . str_pad($invoice['id'], 6, '0', STR_PAD_LEFT),
            'session'    => session()
        ];

        return view('admin/invoices/view', $data);
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $model = new InvoicesModel();
        $builder = $model->select('
        tbl_invoices.*, tbl_clinicians.name as clinician_name, tbl_clients.company_name as client_name')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_invoices.clinician_id', 'left')
            ->join('tbl_clients', 'tbl_clients.id = tbl_invoices.client_id', 'left');

        $status = $this->request->getPost('status');
        if ($status !== null && $status !== '') {
            $builder->where('tbl_invoices.status', $status);
        }

        $invoices = $builder->orderBy('tbl_invoices.id', 'DESC')
            ->findAll();

        $formattedData = [];
        foreach ($invoices as $invoice) {
            $statusBadge = ($invoice['status'] == 20) 
                ? '<span class="badge badge-success">Paid</span>' 
                : '<span class="badge badge-warning">Unpaid</span>';

            $formattedData[] = [
                'ID-' . str_pad($invoice['id'], 5, '0', STR_PAD_LEFT),
                $invoice['client_name'] ?? 'Unknown',
                $invoice['clinician_name'] ?? 'Unknown',
                '$ ' . number_format($invoice['total_amount'], 2),
                $statusBadge,
                date('M d, Y', strtotime($invoice['created_at'])),
                sprintf(
                    '<div class="text-center">
                        <a href="%s" class="btn btn-sm btn-light border"><i class="fas fa-eye"></i> View</a>
                    </div>',
                    base_url('admin/invoices/view/' . $invoice['id'])
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'data' => $formattedData
        ]);
    }
}
