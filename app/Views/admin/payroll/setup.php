<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="heading mb-4">
            <h2>Payroll Setup</h2>
            <div class="kicker-bottom">Configure payroll cycles and manage pay periods</div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">Cycle Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="payroll-settings-form" action="<?= base_url('admin/payroll/save-settings') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="form-group mb-3">
                                <label class="form-label">Frequency</label>
                                <select name="frequency" class="form-control">
                                    <option value="weekly" <?= $frequency == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                                    <option value="bi-weekly" <?= $frequency == 'bi-weekly' ? 'selected' : '' ?>>Bi-Weekly</option>
                                    <option value="monthly" <?= $frequency == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Cycle Starts On</label>
                                <select name="start_day" class="form-control">
                                    <?php 
                                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                    foreach ($days as $day): ?>
                                        <option value="<?= $day ?>" <?= $start_day == $day ? 'selected' : '' ?>><?= $day ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Pay Date (Days after period ends)</label>
                                <div class="input-group">
                                    <input type="number" name="pay_date_offset" class="form-control" value="<?= esc($pay_date_offset) ?>" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">days</span>
                                    </div>
                                </div>
                                <small class="text-muted">How many days after the pay period ends is the actual payment date.</small>
                            </div>
                            <button type="submit" class="btn thm-btn btn-block mt-4">Save Cycle Settings</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pay Periods</h5>
                        <button id="generate-period-btn" class="btn btn-sm btn-primary">Generate Next Period</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date Range</th>
                                        <th>Pay Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($periods)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No periods defined yet.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($periods as $period): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= date('M d, Y', strtotime($period['start_date'])) ?></strong> - 
                                                    <strong><?= date('M d, Y', strtotime($period['end_date'])) ?></strong>
                                                </td>
                                                <td><?= date('M d, Y', strtotime($period['pay_date'])) ?></td>
                                                <td>
                                                    <?php if($period['status'] == 10): ?>
                                                        <span class="badge badge-success">Open</span>
                                                    <?php elseif($period['status'] == 20): ?>
                                                        <span class="badge badge-warning">Processing</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Paid</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('admin/payroll/view-period/' . $period['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                        Manage
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger delete-period" data-id="<?= $period['id'] ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
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

<?php echo $this->endSection() ?>

<?php echo $this->section('customJS') ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#payroll-settings-form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message || 'Error occurred');
                    }
                }
            });
        });

        $('#generate-period-btn').on('click', function() {
            const btn = $(this);
            btn.prop('disabled', true).text('Generating...');
            $.ajax({
                url: '<?= base_url('admin/payroll/generate-period') ?>',
                type: 'POST',
                dataType: 'json',
                data: { <?= csrf_token() ?>: "<?= csrf_hash() ?>" },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        btn.prop('disabled', false).text('Generate Next Period');
                        toastr.error(response.message || 'Error occurred');
                    }
                }
            });
        });

        $('.delete-period').on('click', function() {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this period?')) {
                $.ajax({
                    url: '<?= base_url('admin/payroll/delete-period/') ?>' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => window.location.reload(), 500);
                        } else {
                            toastr.error(response.message || 'Error occurred');
                        }
                    }
                });
            }
        });
    });
</script>
<?php echo $this->endSection() ?>
