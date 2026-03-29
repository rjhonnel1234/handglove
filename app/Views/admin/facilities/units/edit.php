<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $page_title ?></h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="<?= base_url('admin/facilities/details/' . $client_id) ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Details</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form id="unitForm" action="<?= base_url('admin/facilities/units/update/' . $unit['id']) ?>" method="POST">
            <input type="hidden" name="client_id" value="<?= $client_id ?>">
            <div class="row">
                <div class="col-md-7">
                    <div class="card card-outline card-primary">
                        <div class="card-header"><h3 class="card-title">Edit Unit Information</h3></div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name">Unit Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" value="<?= $unit['name'] ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="description">Description (Optional)</label>
                                <textarea name="description" id="description" class="form-control" rows="4"><?= $unit['description'] ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card card-outline card-info">
                        <div class="card-header"><h3 class="card-title">Additional Details</h3></div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="census">Census (e.g. 1:10)</label>
                                <input type="text" name="census" id="census" class="form-control" value="<?= $unit['census'] ?>">
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Unit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
$(document).ready(function() {
    $('#unitForm').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    Toast.fire({ icon: 'success', title: res.message });
                    setTimeout(() => window.location.href = res.redirect, 1000);
                } else {
                    Toast.fire({ icon: 'error', title: res.message || 'Error occurred.' });
                    $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Unit');
                }
            },
            error: function() {
                Toast.fire({ icon: 'error', title: 'Network or server error.' });
                $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Unit');
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
