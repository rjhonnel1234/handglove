<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="heading mb-4">
        <h2>Clinicians</h2>
        <div class="kicker-bottom">Add New Clinician</div>
    </div>

    <form id="clinician-form" action="<?= base_url('admin/clinicians/store') ?>" method="post" enctype="multipart/form-data"
        data-upload-url="<?= base_url('admin/clinicians/upload') ?>"
        data-csrf-token="<?= csrf_token() ?>"
        data-csrf-hash="<?= csrf_hash() ?>"
        data-base-url="<?= base_url() ?>">
        <?= csrf_field() ?>
        
        <div class="row">
            <!-- Left Column: Profile & Basic Info -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <input type="hidden" name="profile_pic_path" id="profile_pic_path">

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Profile Picture</label>
                                <div id="profile-pic-dropzone" class="dropzone">
                                    <div class="dz-message" data-dz-message>
                                        <span>Drop profile picture here or click to upload</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Clinician Level</label>
                                    <select name="tier" class="form-select form-control selectpicker">
                                        <?php foreach($tiers as $value => $label): ?>
                                            <option value="<?= $value ?>"><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Clinician Name" required>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Address</label>
                                    <textarea name="address" class="form-control" rows="2" placeholder="Full Address"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Zip code</label>
                                    <input type="text" name="zip_code" class="form-control" placeholder="Zip code">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="Email address" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Contact Number</label>
                                    <input type="text" name="contact_number" class="form-control" placeholder="Contact number" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <select name="status" class="form-select form-control selectpicker">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Type</label>
                                    <select name="type" class="form-select form-control selectpicker">
                                        <?php foreach($types as $type): ?>
                                            <option value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Company Work</h5>
                        <button type="button" id="add-company-work" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus"></i></button>
                    </div>
                    <div class="card-body">
                        <div id="company-work-wrapper">
                            <div class="row mb-2 pb-2 border-bottom company-work-row">
                                <div class="col-md-4 ">
                                    <input type="text" name="company_work[0][company_name]" class="form-control form-control-sm" placeholder="Company Name">
                                </div>
                                <div class="col-md-3 pl-0">
                                    <input type="text" name="company_work[0][supervisor_name]" class="form-control form-control-sm" placeholder="Supervisor">
                                </div>
                                <div class="col-md-4 pl-0">
                                    <input type="text" name="company_work[0][supervisor_contact]" class="form-control form-control-sm" placeholder="Contact">
                                </div>
                                <div class="col-md-1  pl-0 d-flex align-items-center">
                                    <button type="button" class="btn btn-sm btn-link text-danger remove-company-work" style="display: none;"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

            <!-- Right Column: Rates, Credentials, etc. -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Rate</label>
                            <input type="number" step="0.01" name="rate" class="form-control" placeholder="0.00">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Agency Provider</label>
                            <select name="agencies[]" class="form-select form-control selectpicker" multiple>
                                <option value="">Select Agency</option>
                                <?php foreach($agencies as $agency): ?>
                                    <option value="<?= $agency['id'] ?>"><?= $agency['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">Handglove User Credentials</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-12 border-end mb-4">
                                <div class="mb-2">
                                    <label class="small text-muted">Username</label>
                                    <input type="text" name="handglove_username" class="form-control form-control-sm">
                                </div>
                                <div>
                                    <label class="small text-muted">Password</label>
                                    <input type="password" name="handglove_password" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">PCC User Credentials</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Facility</label>
                                    <select name="client_ids[]" id="client_ids" class="form-select form-control selectpicker" data-title="Select Facility">
                                        <?php foreach($facilities as $facility): ?>
                                            <option value="<?= $facility['id'] ?>"><?= $facility['company_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 pcc-credentials-container" style="display: none;">
                                <div id="pcc-fields-wrapper">
                                    <!-- Dynamic fields will be injected here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary px-4">Create Clinician</button>
                <a href="<?= base_url('admin/clinicians') ?>" class="btn btn-outline-secondary px-4">Reset</a>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('customJS') ?>
<!-- Handled by js/pages/admin/clinicians_dropzone.js -->
<?= $this->endSection() ?>
