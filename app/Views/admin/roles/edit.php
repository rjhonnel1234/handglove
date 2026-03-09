<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="heading mb-4">
            <h2>Roles</h2>
            <div class="kicker-bottom">Edit Role</div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="role-form" action="<?= base_url('admin/roles/update/' . $role['roleId']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" name="role_name" class="form-control" value="<?= esc($role['role']) ?>" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label d-block mb-3">Menu Access Permissions</label>
                        <div class="row">
                            <?php foreach ($adminMenu->menus as $section => $links): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card bg-light border-0">
                                        <div class="card-header bg-secondary text-white py-2">
                                            <h6 class="mb-0"><?= esc($section) ?></h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <?php foreach ($links as $index => $link): ?>
                                                <div class="form-check mb-2">
                                                    <?php 
                                                        $checked = false;
                                                        if (isset($access[$section])) {
                                                            foreach ($access[$section] as $savedLink) {
                                                                if (isset($savedLink['url']) && $savedLink['url'] == $link['url']) {
                                                                    $checked = true;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="menus[<?= esc($section) ?>][<?= $index ?>][url]" 
                                                           value="<?= esc($link['url']) ?>" 
                                                           id="menu_<?= md5($section . $link['url']) ?>"
                                                           <?= $checked ? 'checked' : '' ?>>
                                                    <input type="hidden" name="menus[<?= esc($section) ?>][<?= $index ?>][title]" value="<?= esc($link['title']) ?>">
                                                    <input type="hidden" name="menus[<?= esc($section) ?>][<?= $index ?>][icon]" value="<?= esc($link['icon']) ?>">
                                                    <label class="form-check-label" for="menu_<?= md5($section . $link['url']) ?>">
                                                        <i class="<?= esc($link['icon']) ?> mr-2 text-muted" style="width: 20px;"></i>
                                                        <?= esc($link['title']) ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn thm-btn">Update Role</button>
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
                        toastr.error(response.message || 'Validation failed');
                        $btn.prop('disabled', false).text('Update Role');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                    $btn.prop('disabled', false).text('Update Role');
                }
            });
        });
    });
</script>
<?php echo $this->endSection() ?>
