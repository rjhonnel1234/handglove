<?php

namespace App\Controllers\Facility;

use App\Controllers\BaseController;
use App\Models\FacilityModel;
use App\Models\InvoicesModel;
use App\Models\CliniciansModel;
use \Datetime;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class Invoices extends BaseController
{
    public function __construct()
    {
        // Add any middleware or construct logic here if needed
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $session = session();
        $clientId = $session->get('facility_id');
        $statusFilter = $this->request->getPost('status');

        $objInvoices = new InvoicesModel();
        
        $builder = $objInvoices->select('tbl_invoices.*, tbl_clinicians.name as clinician_name, tbl_clinicians.profile_pic_url')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_invoices.clinician_id', 'inner')
            ->where('tbl_invoices.client_id', $clientId);

        if (!empty($statusFilter)) {
            $builder->where('tbl_invoices.status', $statusFilter);
        }

        $invoices = $builder->orderBy('tbl_invoices.created_at', 'DESC')
            ->findAll();

        $data = [];
        foreach ($invoices as $invoice) {
            $profilePic = !empty($invoice['profile_pic_url']) ? $invoice['profile_pic_url'] : base_url('assets/img/blank-img.png');
            
            $clinicianHtml = '
                <div class="d-flex align-items-center">
                    <div class="avatar-wrapper mr-3">
                        <img src="' . $profilePic . '" alt="" class="rounded-circle avatar-sm">
                    </div>
                    <div>
                        <div class="font-weight-bold">' . htmlspecialchars($invoice['clinician_name']) . '</div>
                        <div class="text-muted small">ID# ' . str_pad($invoice['clinician_id'], 5, '0', STR_PAD_LEFT) . '</div>
                    </div>
                </div>';

            $statusHtml = '';
            $checkboxHtml = '';
            if ($invoice['status'] == 20) {
                $statusHtml = '<span class="badge badge-success">Paid</span>';
                $checkboxHtml = '';
            } else {
                $statusHtml = '<span class="badge badge-warning">Unpaid</span>';
                $checkboxHtml = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-checkbox" id="inv_' . $invoice['id'] . '" value="' . $invoice['id'] . '"><label class="custom-control-label" for="inv_' . $invoice['id'] . '"></label></div>';
            }

            $data[] = [
                'checkbox'  => $checkboxHtml,
                'clinician' => $clinicianHtml,
                'hours'     => number_format($invoice['total_hours'], 2) . ' <span class="text-muted small">hrs</span>',
                'rate'      => '$ ' . number_format($invoice['rate'], 2),
                'total'     => '$ ' . number_format($invoice['total_amount'], 2),
                'status'    => $statusHtml,
                'action'    => '<a href="' . base_url('facility/manage/invoices/view/' . $invoice['id']) . '" class="text-muted"><i class="fas fa-chevron-right"></i></a>'
            ];
        }

        return $this->response->setJSON([
            'draw'            => intval($this->request->getPost('draw')),
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
            'data'            => $data
        ]);
    }

    public function pay()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $invoiceIds = $this->request->getPost('invoiceIds');
        if (empty($invoiceIds) || !is_array($invoiceIds)) {
            return $this->response->setJSON(['success' => 0, 'message' => 'No invoices selected.']);
        }

        $objInvoices = new InvoicesModel();
        
        $session = session();
        $clientId = $session->get('facility_id');

        // Update status to 20 (Paid) for selected invoices belonging to this client
        $updated = $objInvoices->whereIn('id', $invoiceIds)
            ->where('client_id', $clientId)
            ->where('status', 10) // Only update unpaid ones
            ->set(['status' => 20])
            ->update();

        if ($updated) {
            return $this->response->setJSON([
                'success' => 1, 
                'message' => 'Selected invoices have been marked as Paid.',
                'message_header' => 'Success'
            ]);
        }

        return $this->response->setJSON(['success' => 0, 'message' => 'Failed to update invoices.']);
    }

    public function view($ids)
    {
        $session = session();
        $clientId = $session->get('facility_id');

        $idArray = explode(':', $ids);
        if (empty($idArray)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $objInvoices = new InvoicesModel();
        $invoices = $objInvoices->select('tbl_invoices.*, tbl_clinicians.name as clinician_name, tbl_clinicians.profile_pic_url')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_invoices.clinician_id', 'inner')
            ->whereIn('tbl_invoices.id', $idArray)
            ->where('tbl_invoices.client_id', $clientId)
            ->findAll();

        if (empty($invoices)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $facilityModel = new FacilityModel();
        $facility = $facilityModel->find($clientId);

        $data = [
            'invoices'   => $invoices,
            'facility'   => $facility,
            'page_title' => count($invoices) > 1 ? 'Batch Invoices' : 'Invoice #' . str_pad($invoices[0]['id'], 6, '0', STR_PAD_LEFT),
            'session'    => $session
        ];

        return view('components/header', array(
                'title' => 'Handglove',
                'description' => '',
                'url' => BASE_URL,
                'keywords' => '',
                'meta' => array(
                    'title' => 'Handglove',
                    'description' => '',
                    'image' => IMG_URL . ''
                ),
                'styles' => array(
                    'plugins/font_awesome',
                    COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                    COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                    COMPILED_ASSETS_PATH . 'css/components/owl',
                    COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                    COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
                    COMPILED_ASSETS_PATH . 'css/components/global',
                    COMPILED_ASSETS_PATH . 'css/components/animations',
                    COMPILED_ASSETS_PATH . 'css/components/buttons',
                    COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                    COMPILED_ASSETS_PATH . 'css/components/footer',
                    COMPILED_ASSETS_PATH . 'css/pages/facility_profile'
                ),
                'session' => $data['session']
            ))
            .view('facility/invoices/view', $data)
            .view('components/scripts_render', array(
                'scripts' => array(
                    'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                        'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                        'crossorigin' => 'anonymous'
                    ),
                    ASSETS_URL . 'js/plugins/popper.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                    ASSETS_URL . 'js/components/global.min.js',
                    ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                    ASSETS_URL . 'js/components/navigation_bar.min.js',
                    ASSETS_URL . 'js/pages/facility_profile.min.js',
                    'https://cdn.jsdelivr.net/npm/sweetalert2@10'
                )
            ))
            .view('components/footer');
    }

    public function checkout()
    {
        $invoiceIds = $this->request->getPost('invoiceIds');
        if (empty($invoiceIds) || !is_array($invoiceIds)) {
            return $this->response->setJSON(['success' => 0, 'message' => 'No invoices selected.']);
        }

        $session = session();
        $clientId = $session->get('facility_id');

        $objInvoices = new InvoicesModel();
        $invoices = $objInvoices->select('tbl_invoices.*, tbl_clinicians.name as clinician_name')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_invoices.clinician_id', 'inner')
            ->whereIn('tbl_invoices.id', $invoiceIds)
            ->where('tbl_invoices.client_id', $clientId)
            ->where('tbl_invoices.status', 10)
            ->findAll();

        if (empty($invoices)) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Valid unpaid invoices not found.']);
        }

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $lineItems = [];
        foreach ($invoices as $inv) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Invoice #' . str_pad($inv['id'], 6, '0', STR_PAD_LEFT) . ' - ' . $inv['clinician_name'],
                    ],
                    'unit_amount' => (int)($inv['total_amount'] * 100),
                ],
                'quantity' => 1,
            ];
        }

        try {
            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => base_url('facility/manage/invoices/success') . '?sess_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => base_url('facility/manage/invoices/cancel'),
                'metadata' => [
                    'invoice_ids' => implode(',', $invoiceIds),
                    'client_id' => $clientId
                ],
            ]);

            return $this->response->setJSON([
                'success' => 1,
                'url' => $checkoutSession->url
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function success()
    {
        return view('components/header', [
            'title' => 'Payment Success',
            'session' => session(),
            'description' => '',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => array(
                'title' => 'Handglove',
                'description' => '',
                'image' => IMG_URL . ''
            ),
            'styles' => [
                'plugins/font_awesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                COMPILED_ASSETS_PATH . 'css/components/owl',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/animations',
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
            ]
        ]) . view('facility/invoices/success') . view('components/footer');
    }

    public function cancel()
    {
        return view('components/header', [
            'title' => 'Payment Cancelled',
            'session' => session(),
            'description' => '',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => array(
                'title' => 'Handglove',
                'description' => '',
                'image' => IMG_URL . ''
            ),
            'styles' => [
                'plugins/font_awesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                COMPILED_ASSETS_PATH . 'css/components/owl',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/animations',
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
            ]
        ]) . view('facility/invoices/cancel') . view('components/footer');
    }

    public function webhook()
    {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            return $this->response->setStatusCode(400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return $this->response->setStatusCode(400);
        }

        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
            $invoiceIdsStr = $session->metadata->invoice_ids ?? '';
            $clientId = $session->metadata->client_id ?? null;

            if ($invoiceIdsStr && $clientId) {
                $invoiceIds = explode(',', $invoiceIdsStr);
                $objInvoices = new InvoicesModel();
                $objInvoices->whereIn('id', $invoiceIds)
                    ->where('client_id', $clientId)
                    ->set(['status' => 20])
                    ->update();
            }
        }

        return $this->response->setStatusCode(200);
    }
}
