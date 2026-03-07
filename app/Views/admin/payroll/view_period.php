<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="heading mb-4 d-flex justify-content-between align-items-end">
            <div>
                <a href="<?= base_url('admin/payroll/setup') ?>" class="text-muted mb-2 d-inline-block"><i class="fa fa-arrow-left"></i> Back to Setup</a>
                <h2>Pay Period: <?= date('M d', strtotime($period['start_date'])) ?> - <?= date('M d, Y', strtotime($period['end_date'])) ?></h2>
                <div class="kicker-bottom">Pay Date: <?= date('M d, Y', strtotime($period['pay_date'])) ?></div>
            </div>
            <div>
                <button id="generate-stubs-btn" class="btn thm-btn">Generate Pay Stubs</button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">Generated Pay Stubs</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Clinician</th>
                                        <th>Total Hours</th>
                                        <th>Details</th>
                                        <th>Net Pay</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($stubs)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No stubs generated for this period yet.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $totalNetPay = 0; ?>
                                        <?php foreach ($stubs as $stub): ?>

                                            <?php $totalNetPay += $stub['net_pay']; ?>
                                            <tr>
                                                <td><strong><?= esc($stub['clinician_name']) ?></strong></td>
                                                <td><?= number_format($stub['total_hours'], 2) ?> hrs</td>
                                                <td><strong>Gross Pay:</strong> $<?= number_format($stub['gross_pay'], 2) ?> <br> <strong>Deductions:</strong> $<?= number_format($stub['total_deductions'], 2) ?></td>
                                                <td>$<?= number_format($stub['net_pay'], 2) ?></td>
                                                <td>
                                                    <?php if($stub['status'] == 10): ?>
                                                        <span class="badge badge-secondary">Pending</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success">Paid</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('admin/payroll/view-stub/' . $stub['id']) ?>" class="btn btn-info  pt-1 pb-1 pl-2 pr-2" target="_blank"><i class="fa fa-eye"></i> View</a>
                                                    <?php if ($stub['status'] != 20): ?>
                                                        <button data-id="<?= $stub['id'] ?>" class="btn btn-success pt-1 pb-1 pl-2 pr-2 mark-paid-btn">Mark as Paid</button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td><strong>Total</strong></td>
                                            <td></td>
                                            <td></td>
                                            <td><strong>$<?= number_format($totalNetPay, 2) ?></strong></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-5">
                    <div class="accordion" id="shiftsAccordion">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3" id="headingShifts" data-toggle="collapse" data-target="#collapseShifts" style="cursor: pointer;">
                            <h5 class="mb-0 d-flex justify-content-between align-items-center">
                                Completed Shifts in this Period
                                <i class="fa fa-chevron-down small text-muted"></i>
                            </h5>
                        </div>
                        <div id="collapseShifts" class="collapse" aria-labelledby="headingShifts" data-parent="#shiftsAccordion">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead class="bg-light text-muted small uppercase">
                                            <tr>
                                                <th>Date</th>
                                                <th>Clinician</th>
                                                <th>Hours</th>
                                                <th>Rate</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            <?php if (empty($completedShifts)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center py-3">No completed shifts found for this date range.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($completedShifts as $s): ?>
                                                    <tr>
                                                        <td><?= date('M d, Y', strtotime($s['start_date'])) ?></td>
                                                        <td><?= esc($s['clinician_name']) ?></td>
                                                        <td><?= $s['total_hours'] ?> hrs</td>
                                                        <td>$<?= number_format($s['rate'], 2) ?></td>
                                                        <td><strong>$<?= number_format($s['total_amount'], 2) ?></strong></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endSection() ?>

<?php echo $this->section('customJS') ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#generate-stubs-btn').on('click', function() {
            const btn = $(this);
            btn.prop('disabled', true).text('Generating...');
            
            $.ajax({
                url: '<?= base_url('admin/payroll/generate-stubs/' . $period['id']) ?>',
                type: 'POST',
                dataType: 'json',
                data: { <?= csrf_token() ?>: "<?= csrf_hash() ?>" },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        btn.prop('disabled', false).text('Generate Pay Stubs');
                        toastr.error(response.message || 'Error occurred');
                    }
                }
            });
        });

        $('.mark-paid-btn').on('click', function(e) {
            e.preventDefault();
            const btn = $(this);
            const id = btn.data('id');
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
            
            $.ajax({
                url: '<?= base_url('admin/payroll/mark-as-paid/') ?>' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        btn.prop('disabled', false).text('Mark as Paid');
                        toastr.error(response.message || 'Error occurred');
                    }
                }
            });
        });
    });
</script>
<?php echo $this->endSection() ?>
