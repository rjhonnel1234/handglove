<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="heading d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Invoice Details</h2>
                <div class="kicker-bottom">Viewing Invoice #<?php echo str_pad($invoice['id'], 6, '0', STR_PAD_LEFT); ?></div>
            </div>
            <div class="no-print">
                <button class="btn btn-outline-secondary me-2" onclick="window.print();"><i class="fas fa-print me-1"></i> Print</button>
                <a href="<?= base_url('admin/invoices') ?>" class="btn btn-warning">Back to List</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="mb-3">
                        <div class="text-uppercase text-muted small font-weight-bold mb-1">Billing To:</div>
                        <div class="h6 font-weight-bold mb-1"><?php echo htmlspecialchars($invoice['client_name']); ?></div>
                        <div class="text-muted small"><?php echo nl2br(htmlspecialchars($invoice['client_address'] ?? '')); ?></div>
                    </div>

                    <div class="text-end">
                        <?php if ($invoice['status'] == 20): ?>
                            <span class="badge bg-success px-3 py-2">PAID</span>
                        <?php else: ?>
                            <span class="badge bg-warning px-3 py-2 text-dark">UNPAID</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-5">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <div class="text-uppercase text-muted small font-weight-bold mb-1">Invoice Details:</div>
                        <div><span class="text-muted small">Date:</span> <?php echo date('M d, Y', strtotime($invoice['created_at'])); ?></div>
                        <div><span class="text-muted small">Due:</span> <?php echo date('M d, Y', strtotime($invoice['created_at'] . ' + 7 days')); ?></div>
                        <div><span class="text-muted small">Shift:</span> #<?php echo $invoice['shift_id']; ?></div>
                    </div>
                    <div class="col-sm-6 text-right">
                        <div class="text-uppercase text-muted small font-weight-bold mb-1">Invoice Amount:</div>
                        <div class="h3 font-weight-bold mb-0 text-primary">$ <?php echo number_format($invoice['total_amount'], 2); ?></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2 px-3 border-bottom">Description</th>
                                <th class="py-2 text-center border-bottom text-center" width="100">Hours</th>
                                <th class="py-2 text-end border-bottom text-center" width="120">Pay Rate</th>
                                <th class="py-2 text-end border-bottom text-center" width="100">Bill Rate</th>
                                <th class="py-2 text-end px-3 border-bottom text-center" width="180">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-4 px-3">
                                    <div class="h6 font-weight-bold mb-1">Clinical Shift Rendering</div>
                                    <div class="text-muted small">Professional services payment for <?php echo htmlspecialchars($invoice['clinician_name']); ?><br>Shift ID #<?php echo $invoice['shift_id']; ?></div>
                                </td>
                                <td class="py-4 text-center align-middle h6 text-right"><?php echo number_format($invoice['total_hours'], 2); ?></td>
                                <td class="py-4 text-end align-middle h6 text-right">$ <?php echo number_format($invoice['rate'], 2); ?></td>
                                <td class="py-4 text-end align-middle h6 text-right"><?php echo number_format($invoice['billing_rate'], 2); ?></td>
                                <td class="py-4 text-end align-middle h5 font-weight-bold px-3 text-dark text-right">$ <?php echo number_format($invoice['total_amount'], 2); ?></td>
                            </tr>
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-end py-3 h6 mb-0">Subtotal:</td>
                                <td class="text-end py-3 px-3 h6 mb-0 text-right">$ <?php echo number_format($invoice['total_amount'], 2); ?></td>
                            </tr>
                            <tr class="table-active">
                                <td colspan="3"></td>
                                <td class="text-end py-3 h5 font-weight-bold text-dark">Total:</td>
                                <td class="text-end py-3 px-3 h5 font-weight-bold text-primary text-right">$ <?php echo number_format($invoice['total_amount'], 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-3 px-4 text-center text-muted small">
                Handglove Professional Services Invoice. Generated automatically on <?php echo date('F d, Y H:i'); ?>.
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .header, .sidebar, .no-print, .btn { display: none !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
        .bg-light { background-color: #f8f9fa !important; }
    }
</style>

<?php echo $this->endSection() ?>
