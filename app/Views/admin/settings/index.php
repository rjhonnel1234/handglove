<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="heading mb-4">
            <h2>General Settings</h2>
            <div class="kicker-bottom">Configure global application settings</div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="settings-form" 
                    action="<?= base_url('admin/settings/store') ?>" 
                    method="post">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Global Billing Rate (Multiplier)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">x</span>
                                    </div>
                                    <input type="number" step="0.01" name="billing_rate" class="form-control" value="<?= esc($billing_rate) ?>" required>
                                </div>
                                <small class="text-muted">Invoices total amount will be: (Hours * Rate * Billing Rate)</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" id="submit-btn" class="btn thm-btn">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endSection() ?>

<?php echo $this->section('customJS') ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#settings-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $('#submit-btn');
            
            $btn.prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' || response.success == 1) {
                        toastr.success(response.message || 'Settings updated successfully');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        $btn.prop('disabled', false).text('Save Settings');
                        toastr.error(response.message || 'An error occurred');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Save Settings');
                    toastr.error('Server error occurred. Please try again.');
                }
            });
        });
    });
</script>
<?php echo $this->endSection() ?>
