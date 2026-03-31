<?= $this->extend('admin/includes/layout'); ?>

<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $page_title ?></h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="<?= base_url('admin/facilities/details/' . $client_id) ?>" class="btn btn-secondary"><i
                        class="fas fa-arrow-left"></i> Back to Details</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form id="personnelForm" action="<?= base_url('admin/facilities/personnel/store') ?>" method="POST">
            <input type="hidden" name="client_id" value="<?= $client_id ?>">
            <div class="row">
                <!-- Main Info -->
                <div class="col-md-7">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Basic Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" id="first_name" class="form-control"
                                            placeholder="First Name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" id="last_name" class="form-control"
                                            placeholder="Last Name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="Email Address" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="contact_number">Contact Number</label>
                                <input type="text" name="contact_number" id="contact_number" class="form-control"
                                    placeholder="Contact Number">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Info -->
                <div class="col-md-5">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Role & Status</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="type">User Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="">Select User Type</option>
                                    <?php foreach ($user_types as $type): ?>
                                        <option value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="clinician_type">Clinician Type (Optional)</label>
                                <select name="clinician_type" id="clinician_type" class="form-control">
                                    <option value="0">None</option>
                                    <?php foreach ($clinician_types as $ct): ?>
                                        <option value="<?= $ct['id'] ?>"><?= $ct['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save
                                Personnel</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    $(document).ready(function () {
        $('#personnelForm').on('submit', function (e) {
            e.preventDefault();
            const $btn = $(this).find('button[type="submit"]');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    if (res.status === 'success') {
                        Toast.fire({ icon: 'success', title: res.message });
                        setTimeout(() => window.location.href = res.redirect, 1000);
                    } else {
                        Toast.fire({ icon: 'error', title: res.message || 'Error occurred.' });
                        $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Personnel');
                    }
                },
                error: function () {
                    Toast.fire({ icon: 'error', title: 'Network or server error.' });
                    $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Personnel');
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>