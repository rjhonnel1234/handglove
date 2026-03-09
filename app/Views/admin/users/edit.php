<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-8">
        <div class="heading mb-4">
            <h2>Personnel</h2>
            <div class="kicker-bottom">Edit Personnel</div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="user-form" action="<?= base_url('admin/users/update/' . $user['userId']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?= esc($user['name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control">
                                <small class="text-muted">Leave blank to keep current password. Minimum 6 characters.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Mobile Number</label>
                                <input type="text" name="mobile" class="form-control" value="<?= esc($user['mobile']) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Role</label>
                                <select name="roleId" class="form-control" required>
                                    <option value="">Select Role</option>
                                    <?php foreach($roles as $role): ?>
                                        <option value="<?= $role['roleId'] ?>" <?= $user['roleId'] == $role['roleId'] ? 'selected' : '' ?>><?= esc($role['role']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Designation</label>
                                <input type="text" name="designation" class="form-control" value="<?= esc($user['designation']) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn thm-btn">Update Personnel</button>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Cancel</a>
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
        $('#user-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            
            $btn.prop('disabled', true).text('Updating...');

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
                        if (response.errors) {
                            let errorMsg = Object.values(response.errors).join('<br>');
                            toastr.error(errorMsg);
                        } else {
                            toastr.error(response.message || 'Validation failed');
                        }
                        $btn.prop('disabled', false).text('Update Personnel');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                    $btn.prop('disabled', false).text('Update Personnel');
                }
            });
        });
    });
</script>
<?php echo $this->endSection() ?>
