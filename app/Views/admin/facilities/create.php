<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="heading mb-4">
        <h2>Facilities</h2>
        <div class="kicker-bottom">Add New Facility</div>
    </div>

    <form id="facility-form" action="<?= base_url('admin/facilities/store') ?>" method="post" enctype="multipart/form-data"
        data-upload-url="<?= base_url('admin/facilities/upload') ?>"
        data-csrf-token="<?= csrf_token() ?>"
        data-csrf-hash="<?= csrf_hash() ?>"
        data-base-url="<?= base_url() ?>">
        <?= csrf_field() ?>
        
        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <input type="hidden" name="company_logo_path" id="company_logo_path">

                        <div class="row mb-4">
                            <div class="col-md-12 text-center">
                                <label class="form-label fw-bold d-block text-left">Company Logo</label>
                                <div id="logo-dropzone" class="dropzone d-flex align-items-center justify-content-center">
                                    <div class="dz-message" data-dz-message>
                                        <span>Drop logo here or click to upload</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Provider / Institution</label>
                                    <select name="provider_id" id="provider_id" class="form-select form-control selectpicker" data-live-search="true">
                                        <option value="">Select Provider</option>
                                        <?php foreach($providers as $provider): ?>
                                            <option value="<?= $provider['id'] ?>"><?= $provider['name'] ?></option>
                                        <?php endforeach; ?>
                                        <option value="others">Others</option>
                                    </select>
                                </div>
                            </div>
                            <div id="company-name-wrapper" class="col-md-6" style="display: none;">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Company Name</label>
                                    <input type="text" name="company_name" class="form-control" placeholder="Facility Name">
                                </div>
                            </div>
                        </div>

                        <div id="geography-wrapper" style="display: none;">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Company Address</label>
                                <textarea name="company_address" class="form-control" rows="2" placeholder="Full address"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">State</label>
                                        <select name="state_id" class="form-select form-control selectpicker" data-live-search="true">
                                            <option value="">Select State</option>
                                            <?php foreach($states as $state): ?>
                                                <option value="<?= $state['id'] ?>"><?= $state['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Country</label>
                                        <select name="country_id" class="form-select form-control selectpicker" data-live-search="true">
                                            <option value="">Select Country</option>
                                            <?php foreach($countries as $country): ?>
                                                <option value="<?= $country['id'] ?>" <?= $country['id'] == 231 ? 'selected' : '' ?>><?= $country['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Zip Code</label>
                                        <input type="text" name="zip_code" class="form-control" placeholder="Zip code">
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                    <select name="agencies[]" class="form-select form-control selectpicker" multiple data-title="Choose Agencies">
                                        <?php foreach($agencies as $agency): ?>
                                            <option value="<?= $agency['id'] ?>"><?= $agency['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Company Email</label>
                                    <input type="email" name="company_email" class="form-control" placeholder="Email contact" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Company Number</label>
                                    <input type="text" name="company_number" class="form-control" placeholder="Phone contact">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <select name="status" class="form-select form-control selectpicker">
                                        <option value="10">Active</option>
                                        <option value="0">Inactive</option>
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
                        <button type="submit" class="btn thm-btn px-4 mr-2">Create Facility</button>
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
                init: function() {
                    var dz = this;
                    this.on("success", function(file, response) {
                        if (response.status === 'success') {
                            $('#company_logo_path').val(response.path);
                            dz.options.maxFiles = 0;
                        } else {
                            toastr.error(response.message || 'Upload failed');
                            this.removeFile(file);
                        }
                    });

                    this.on("removedfile", function(file) {
                        $('#company_logo_path').val('');
                        dz.options.maxFiles = 1;
                    });

                    this.on("error", function(file, message) {
                        toastr.error(message);
                        this.removeFile(file);
                    });
                }
            });
        }

        // Toggle Company Name and Geography based on Provider
        function toggleCompanyName() {
            if ($('#provider_id').val() === 'others') {
                $('#company-name-wrapper').show();
                $('#company-name-wrapper input').attr('required', true);
                $('#geography-wrapper').show();
            } else {
                $('#company-name-wrapper').hide();
                $('#company-name-wrapper input').attr('required', false);
                $('#geography-wrapper').hide();
            }
        }

        $(document).on('change', '#provider_id', function() {
            toggleCompanyName();
            if ($(this).val() !== 'others') {
                $('#company-name-wrapper input').val('');
            }
        });

        // Run on load
        if ($('#provider_id').length) {
            toggleCompanyName();
        }

        // Form Submission
        $(document).on('submit', '#facility-form', function(e) {
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
                beforeSend: function() {
                    form.find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Saving...');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        if (response.redirect) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1000);
                        }
                    } else if (response.status === 'error') {
                        if (response.errors) {
                            var errorMsg = '';
                            $.each(response.errors, function(key, value) {
                                errorMsg += value + '<br>';
                            });
                            toastr.error(errorMsg);
                        } else {
                            toastr.error(response.message || 'An error occurred.');
                        }
                    }
                },
                error: function() {
                    toastr.error('A system error occurred.');
                },
                complete: function() {
                    form.find('button[type="submit"]').prop('disabled', false).html(form.find('button[type="submit"]').data('original-text') || 'Create Facility');
                }
            });
        });

        // Store original text of submit button
        $('#facility-form button[type="submit"]').each(function() {
            $(this).data('original-text', $(this).text());
        });
    });
</script>
<?= $this->endSection() ?>
