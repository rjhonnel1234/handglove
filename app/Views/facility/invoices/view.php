<div class="content-wrapper">
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php 
                $totalToPay = 0;
                $unpaidIds = [];
                foreach ($invoices as $inv): 
                    if ($inv['status'] != 20) {
                        $totalToPay += $inv['total_amount'];
                        $unpaidIds[] = $inv['id'];
                    }
                ?>
                <div class="card border-0 shadow-sm overflow-hidden mb-4 invoice-card" data-id="<?php echo $inv['id']; ?>">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <img src="<?php echo !empty($inv['profile_pic_url']) ? $inv['profile_pic_url'] : base_url('assets/img/blank-img.png'); ?>" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                </div>
                                <div>
                                    <div class="font-weight-bold mb-0"><?php echo htmlspecialchars($inv['clinician_name']); ?></div>
                                    <div class="text-muted small">Invoice #<?php echo str_pad($inv['id'], 6, '0', STR_PAD_LEFT); ?></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <?php if ($inv['status'] == 20): ?>
                                    <span class="badge badge-success px-3 py-2">PAID</span>
                                <?php else: ?>
                                    <span class="badge badge-warning px-3 py-2">UNPAID</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-sm-4">
                                <span class="text-uppercase text-muted small font-weight-bold mr-2">Date:</span>
                                <span><?php echo date('M d, Y', strtotime($inv['created_at'])); ?></span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-uppercase text-muted small font-weight-bold mr-2">Due:</span>
                                <span><?php echo date('M d, Y', strtotime($inv['created_at'] . ' + 7 days')); ?></span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-uppercase text-muted small font-weight-bold mr-2">Shift:</span>
                                <span>#<?php echo $inv['shift_id']; ?></span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-2 pl-3">Description</th>
                                        <th class="py-2 text-center">Hours</th>
                                        <th class="py-2 text-right">Rate</th>
                                        <th class="py-2 text-right pr-3">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="py-3 pl-3">
                                            <div class="font-weight-bold">Clinical Shift Rendering</div>
                                            <div class="text-muted small">Professional services for Shift #<?php echo $inv['shift_id']; ?></div>
                                        </td>
                                        <td class="py-3 text-center align-middle"><?php echo number_format($inv['total_hours'], 2); ?></td>
                                        <td class="py-3 text-right align-middle">$ <?php echo number_format($inv['rate'], 2); ?></td>
                                        <td class="py-3 text-right align-middle font-weight-bold pr-3">$ <?php echo number_format($inv['total_amount'], 2); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if (!empty($unpaidIds)): ?>
                <div class="card border-0 shadow-sm bg-primary text-white p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 font-weight-bold">Grand Total for Completion</h5>
                            <p class="mb-0 small opacity-75">You have <?php echo count($unpaidIds); ?> unpaid invoice(s) selected.</p>
                        </div>
                        <div class="text-right">
                            <div class="h3 font-weight-bold mb-2">$ <?php echo number_format($totalToPay, 2); ?></div>
                            <button id="btnPayBatch" class="btn btn-light font-weight-bold px-4" data-ids="<?php echo implode(':', $unpaidIds); ?>">
                                <i class="fas fa-money-bill-wave mr-2"></i> PAY ALL NOW
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-4 text-center no-print">
                    <a href="<?php echo base_url('facility/manage/profile'); ?>" class="text-muted"><i class="fas fa-arrow-left mr-2"></i> Back to Profile</a>
                    <button class="btn btn-outline-secondary ml-3" onclick="window.print();"><i class="fas fa-print mr-2"></i> Print All</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .content-wrapper { padding: 0 !important; }
        .no-print, .btn, .mt-4, .card-footer { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; page-break-after: always; }
        .bg-primary { background-color: #007bff !important; color: #fff !important; }
    }
    .opacity-75 { opacity: 0.75; }
</style>

<script>
$(document).ready(function() {
    $("#btnPayBatch").click(function() {
        var ids = $(this).data('ids').toString().split(':');
        
        Swal.fire({
            title: 'Stripe Payment',
            text: "You will be redirected to Stripe to securely complete your payment.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Proceed to Checkout'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo base_url('facility/manage/invoices/checkout'); ?>',
                    data: { invoiceIds: ids },
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success == 1) {
                            window.location.href = response.url;
                        } else {
                            toastr.error(response.message, 'Checkout Error');
                        }
                    },
                    error: function() {
                        toastr.error('An unexpected error occurred. Please try again.', 'Error');
                    }
                });
            }
        });
    });
});
</script>

