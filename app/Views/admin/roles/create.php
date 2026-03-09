<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-6">
        <div class="heading mb-4">
            <h2>Roles</h2>
            <div class="kicker-bottom">Add New Role</div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="role-form" action="<?= base_url('admin/roles/store') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" name="role_name" class="form-control" placeholder="e.g. Administrator" required>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn thm-btn">Save Role</button>
                        <a href="<?= base_url('admin/roles') ?>" class="btn btn-secondary">Cancel</a>
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
        $('#role-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Validation failed');
                        $btn.prop('disabled', false).text('Save Role');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                    $btn.prop('disabled', false).text('Save Role');
                }
            });
        });
    });
</script>
<?php echo $this->endSection() ?>
