<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="heading mb-4">
        <h2>Agencies</h2>
        <div class="kicker-bottom">Add New Agency</div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="agency-form" action="<?= base_url('admin/agencies/store') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Agency Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter agency name" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Optional description"></textarea>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn thm-btn px-4">Create Agency</button>
                            <a href="<?= base_url('admin/agencies') ?>" class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('customJS') ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#agency-form').on('submit', function(e) {
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
                        if (response.errors) {
                            let errorMsg = Object.values(response.errors).join('<br>');
                            toastr.error(errorMsg);
                        } else {
                            toastr.error(response.message || 'Validation failed');
                        }
                        $btn.prop('disabled', false).text('Create Agency');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                    $btn.prop('disabled', false).text('Create Agency');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
