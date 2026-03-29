<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="heading mb-4">
        <h2>Institutions</h2>
        <div class="kicker-bottom">Edit Institution</div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="institution-form" action="<?= base_url('admin/institutions/update/' . $institution['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Institution Name</label>
                                    <input type="text" name="name" class="form-control" value="<?= esc($institution['name']) ?>" placeholder="Enter institution name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">CCN (CMS Certification Number)</label>
                                    <input type="text" name="ccn" class="form-control" value="<?= esc($institution['ccn']) ?>" placeholder="Enter CCN" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Address</label>
                                    <input type="text" name="address" class="form-control" value="<?= esc($institution['address']) ?>" placeholder="Enter street address">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">City</label>
                                    <input type="text" name="city" class="form-control" value="<?= esc($institution['city']) ?>" placeholder="Enter city">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">State</label>
                                    <select name="state" class="form-select form-control selectpicker" data-live-search="true">
                                        <option value="">Select State</option>
                                        <?php foreach($states as $state): ?>
                                            <option value="<?= $state['id'] ?>" <?= $institution['state'] == $state['id'] ? 'selected' : '' ?>><?= $state['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">ZIP Code</label>
                                    <input type="text" name="zip" class="form-control" value="<?= esc($institution['zip']) ?>" placeholder="Enter ZIP">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Contact Number</label>
                                    <input type="text" name="contact_number" class="form-control" value="<?= esc($institution['contact_number']) ?>" placeholder="Enter phone number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">County</label>
                                    <input type="text" name="county" class="form-control" value="<?= esc($institution['county']) ?>" placeholder="Enter county">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Ownership Type</label>
                                    <select name="ownership_type_id" class="form-select form-control selectpicker" data-live-search="true">
                                        <option value="">Select Ownership Type</option>
                                        <?php foreach($ownership_types as $owner): ?>
                                            <option value="<?= $owner['id'] ?>" <?= $institution['ownership_type_id'] == $owner['id'] ? 'selected' : '' ?>><?= $owner['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Provider Type</label>
                                    <select name="provider_type_id" class="form-select form-control selectpicker" data-live-search="true">
                                        <option value="">Select Provider Type</option>
                                        <?php foreach($provider_types as $type): ?>
                                            <option value="<?= $type['id'] ?>" <?= $institution['provider_type_id'] == $type['id'] ? 'selected' : '' ?>><?= $type['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">Country</label>
                                    <select name="country" class="form-select form-control selectpicker" data-live-search="true">
                                        <option value="">Select Country</option>
                                        <?php foreach($countries as $country): ?>
                                            <option value="<?= $country['id'] ?>" <?= $institution['country'] == $country['id'] ? 'selected' : '' ?>><?= $country['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn thm-btn px-4 mr-2">Update Institution</button>
                            <a href="<?= base_url('admin/institutions') ?>" class="btn btn-outline-secondary px-4">Cancel</a>
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
        $('#institution-form').on('submit', function(e) {
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
                        $btn.prop('disabled', false).text('Update Institution');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                    $btn.prop('disabled', false).text('Update Institution');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
