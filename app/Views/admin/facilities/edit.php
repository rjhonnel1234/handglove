<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="heading mb-4">
        <h2>Facilities</h2>
        <div class="kicker-bottom">Edit Facility Details</div>
    </div>

    <form id="facility-form" action="<?= base_url('admin/facilities/update/' . $facility['id']) ?>" method="post"
        enctype="multipart/form-data" data-upload-url="<?= base_url('admin/facilities/upload') ?>"
        data-csrf-token="<?= csrf_token() ?>" data-csrf-hash="<?= csrf_hash() ?>" data-base-url="<?= base_url() ?>">
        <?= csrf_field() ?>

        <input type="hidden" name="provider_id"
            value="<?= $facility['provider_id'] == 0 ? 'others' : $facility['provider_id'] ?>">

        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <input type="hidden" name="company_logo_path" id="company_logo_path"
                            value="<?= $facility['company_logo'] ?>">

                        <div class="row mb-4">
                            <div class="col-md-12 text-center">
                                <label class="form-label fw-bold d-block text-left">Company Logo</label>
                                <div id="logo-dropzone"
                                    class="dropzone d-flex align-items-center justify-content-center"
                                    data-existing-logo="<?= !empty($facility['company_logo']) ? $facility['company_logo'] : '' ?>">
                                    <div class="dz-message" data-dz-message>
                                        <span>Drop logo here or click to upload</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($facility['provider_id'] != 0): ?>
                            <!-- Display Only for Linked Facilities -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Company Name</label>
                                <div class="p-2 bg-light rounded text-muted"><?= esc($facility['company_name']) ?></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Company Address</label>
                                <div class="p-2 bg-light rounded text-muted"><?= nl2br(esc($facility['company_address'])) ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">State</label>
                                    <div class="p-2 bg-light rounded text-muted"><?= esc($facility['state_name']) ?></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Country</label>
                                    <div class="p-2 bg-light rounded text-muted"><?= esc($facility['country_name']) ?></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Zip Code</label>
                                    <div class="p-2 bg-light rounded text-muted"><?= esc($facility['zip_code']) ?></div>
                                </div>
                            </div>

                        <?php else: ?>
                            <!-- Editable for Others -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Company Name</label>
                                <input type="text" name="company_name" class="form-control" placeholder="Facility Name"
                                    value="<?= esc($facility['company_name']) ?>">
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Company Address</label>
                                <textarea name="company_address" class="form-control" rows="2"
                                    placeholder="Full address"><?= esc($facility['company_address']) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">State</label>
                                        <select name="state_id" class="form-select form-control selectpicker"
                                            data-live-search="true">
                                            <option value="">Select State</option>
                                            <?php foreach ($states as $state): ?>
                                                <option value="<?= $state['id'] ?>" <?= $facility['state_id'] == $state['id'] ? 'selected' : '' ?>><?= $state['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Country</label>
                                        <select name="country_id" class="form-select form-control selectpicker"
                                            data-live-search="true">
                                            <option value="">Select Country</option>
                                            <?php foreach ($countries as $country): ?>
                                                <option value="<?= $country['id'] ?>" <?= $facility['country_id'] == $country['id'] ? 'selected' : '' ?>><?= $country['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Zip Code</label>
                                        <input type="text" name="zip_code" class="form-control" placeholder="Zip code"
                                            value="<?= esc($facility['zip_code']) ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Agency Provider</label>
                                    <select name="agencies[]" class="form-select form-control selectpicker" multiple
                                        data-title="Choose Agencies">
                                        <?php foreach ($agencies as $agency): ?>
                                            <option value="<?= $agency['id'] ?>" <?= in_array($agency['id'], $facility['agencies']) ? 'selected' : '' ?>><?= $agency['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Company Email</label>
                                    <input type="email" name="company_email" class="form-control"
                                        placeholder="Email contact" value="<?= esc($facility['company_email']) ?>"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Company Number</label>
                                    <input type="text" name="company_number" class="form-control"
                                        placeholder="Phone contact" value="<?= esc($facility['company_number']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <select name="status" class="form-select form-control selectpicker">
                                        <option value="10" <?= $facility['status'] == 10 ? 'selected' : '' ?>>Active
                                        </option>
                                        <option value="0" <?= $facility['status'] == 0 ? 'selected' : '' ?>>Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body d-flex justify-content-end gap-2">
                        <button type="submit" class="btn thm-btn px-4 mr-2">Update Facility</button>
                        <a href="<?= base_url('admin/facilities') ?>" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('customJS') ?>
<script type="text/javascript">
    Dropzone.autoDiscover = false;
    $(document).ready(function () {
        // Dropzone initialization for Logo
        if ($('#logo-dropzone').length) {

            var logoDropzone = new Dropzone("#logo-dropzone", {
                url: $('#facility-form').data('upload-url'),
                maxFiles: 1,
                acceptedFiles: 'image/*',
                addRemoveLinks: true,
                headers: {
                    'X-CSRF-TOKEN': $('#facility-form').data('csrf-hash')
                },
                init: function () {
                    var dz = this;

                    // Show existing logo if editing
                    var existingLogo = $('#logo-dropzone').data('existing-logo');
                    if (existingLogo) {
                        var mockFile = { name: "Existing Logo", size: 12345 };
                        dz.emit("addedfile", mockFile);
                        dz.emit("thumbnail", mockFile, existingLogo);
                        dz.emit("complete", mockFile);
                        dz.options.maxFiles = 0; // Prevent adding more files if one exists
                    }

                    this.on("success", function (file, response) {
                        if (response.status === 'success') {
                            $('#company_logo_path').val(response.path);
                            dz.options.maxFiles = 0;
                        } else {
                            toastr.error(response.message || 'Upload failed');
                            this.removeFile(file);
                        }
                    });

                    this.on("removedfile", function (file) {
                        $('#company_logo_path').val('');
                        dz.options.maxFiles = 1;
                    });

                    this.on("error", function (file, message) {
                        toastr.error(message);
                        this.removeFile(file);
                    });
                }
            });
        }

        // Form Submission
        $(document).on('submit', '#facility-form', function (e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var formData = new FormData(this);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                beforeSend: function () {
                    form.find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Saving...');
                },
                success: function (response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        if (response.redirect) {
                            setTimeout(function () {
                                window.location.href = response.redirect;
                            }, 1000);
                        }
                    } else if (response.status === 'error') {
                        if (response.errors) {
                            var errorMsg = '';
                            $.each(response.errors, function (key, value) {
                                errorMsg += value + '<br>';
                            });
                            toastr.error(errorMsg);
                        } else {
                            toastr.error(response.message || 'An error occurred.');
                        }
                    }
                },
                error: function () {
                    toastr.error('A system error occurred.');
                },
                complete: function () {
                    form.find('button[type="submit"]').prop('disabled', false).html(form.find('button[type="submit"]').data('original-text') || 'Update Facility');
                }
            });
        });

        // Store original text of submit button
        $('#facility-form button[type="submit"]').each(function () {
            $(this).data('original-text', $(this).text());
        });
    });
</script>
<?= $this->endSection() ?>