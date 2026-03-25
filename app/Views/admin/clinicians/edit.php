<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="heading mb-4">
        <h2>Clinicians</h2>
        <div class="kicker-bottom">Edit Clinician</div>
    </div>

    <form id="clinician-form" 
        action="<?= base_url('admin/clinicians/update/' . $clinician['id']) ?>" 
        method="post"
        data-upload-url="<?= base_url('admin/clinicians/upload') ?>"
        data-csrf-token="<?= csrf_token() ?>"
        data-csrf-hash="<?= csrf_hash() ?>"
        data-base-url="<?= base_url() ?>"
        data-existing-image="<?= $clinician['profile_pic_url'] ?>"
        data-clinician-id="<?= $clinician['id'] ?>"
        enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="profile_pic_path" id="profile_pic_path">
        
        <div class="row">
            <!-- Left Column: Profile & Basic Info -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
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
                                            <option value="<?= $value ?>" <?= $clinician['tier'] == $value ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" name="name" class="form-control" value="<?= esc($clinician['name']) ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Address</label>
                                    <textarea name="address" class="form-control" rows="2"><?= esc($clinician['address']) ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Zip code</label>
                                    <input type="text" name="zip_code" class="form-control" value="<?= esc($clinician['zip_code']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= esc($clinician['email']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Contact Number</label>
                                    <input type="text" name="contact_number" class="form-control" value="<?= esc($clinician['contact_number']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <select name="status" class="form-select form-control selectpicker">
                                        <option value="1" <?= $clinician['status'] == 1 ? 'selected' : '' ?>>Active</option>
                                        <option value="0" <?= $clinician['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Type</label>
                                    <select name="type" class="form-select form-control selectpicker">
                                        <?php foreach($types as $type): ?>
                                            <option value="<?= $type['id'] ?>" <?= $clinician['type'] == $type['id'] ? 'selected' : '' ?>><?= $type['name'] ?></option>
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
                        <button type="button" id="add-company-work" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus"></i> Add Row</button>
                    </div>
                    <div class="card-body">
                        <div id="company-work-wrapper">
                            <?php 
                            $company_worked_list = is_array($company_worked) ? (isset($company_worked['company_name']) ? [$company_worked] : $company_worked) : [];
                            if (empty($company_worked_list)) {
                                $company_worked_list = [['company_name' => '', 'supervisor_name' => '', 'supervisor_contact' => '']];
                            }
                            foreach($company_worked_list as $index => $work): 
                            ?>
                                <div class="row mb-2 pb-2 border-bottom company-work-row">
                                    <div class="col-md-4">
                                        <input type="text" name="company_work[<?= $index ?>][company_name]" class="form-control form-control-sm" placeholder="Company Name" value="<?= esc($work['company_name'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3 pl-0">
                                        <input type="text" name="company_work[<?= $index ?>][supervisor_name]" class="form-control form-control-sm" placeholder="Supervisor" value="<?= esc($work['supervisor_name'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4 pl-0">
                                        <input type="text" name="company_work[<?= $index ?>][supervisor_contact]" class="form-control form-control-sm" placeholder="Contact" value="<?= esc($work['supervisor_contact'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-center">
                                        <button type="button" class="btn btn-sm btn-link text-danger remove-company-work" <?= count($company_worked_list) <= 1 ? 'style="display: none;"' : '' ?>><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Rates, Credentials, etc. -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Rate</label>
                                    <input type="number" step="0.01" name="rate" class="form-control" value="<?= esc($clinician['rate']) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Agency Provider</label>
                                    <select name="agencies[]" class="form-select form-control selectpicker" style="width: 100%;" multiple>
                                        <?php foreach($agencies as $agency): ?>
                                            <option value="<?= $agency['id'] ?>" <?= in_array($agency['id'], $clinician['agencies']) ? 'selected' : '' ?>><?= $agency['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">Handglove User Credentials</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?php if ($user_record): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <label class="small text-muted d-block mb-0">User Access Status</label>
                                            <span class="badge <?= $user_record['status'] == 1 ? 'bg-success' : 'bg-danger' ?> text-white">
                                                <?= $user_record['status'] == 1 ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </div>
                                        <button type="button" 
                                            class="btn btn-sm <?= $user_record['status'] == 1 ? 'btn-outline-danger' : 'btn-outline-success' ?> toggle-user-access" 
                                            data-user-id="<?= $user_record['id'] ?>" 
                                            data-status="<?= $user_record['status'] == 1 ? 0 : 1 ?>">
                                            <?= $user_record['status'] == 1 ? 'Deactivate Access' : 'Activate Access' ?>
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <label class="small text-muted">Reset Password</label>
                                        <input type="password" name="handglove_password" class="form-control form-control-sm" placeholder="Leave blank to keep current">
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info py-2 small">
                                        No Handglove user account found for this clinician.
                                    </div>
                                    <div class="form-group mb-1 d-flex align-items-center">
                                        <input type="checkbox" name="create_clinician_access" id="create_clinician_access" class="form-check-input" style="margin-left: 0; display: block; position: relative; margin-top: 0; margin-right: 10px; margin-bottom: .5rem;">
                                        <label class="form-label fw-bold">Create User Access</label>
                                    </div>
                                    <div id="handglove-user-access" style="display: none;">
                                        <label class="small text-muted">Initial Password</label>
                                        <input type="password" name="handglove_password" class="form-control form-control-sm">
                                    </div>
                                <?php endif; ?>
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
                            <?php $selectedFacilities = array_keys($pcc_credentials ?? []); ?>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Facility</label>
                                    <select id="client_ids" class="form-select form-control selectpicker" data-title="Select Facility">
                                        <?php foreach($facilities as $facility): ?>
                                            <option value="<?= $facility['id'] ?>" <?= in_array($facility['id'], $selectedFacilities) ? 'selected' : '' ?>><?= $facility['company_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 pcc-credentials-container" style="display: none;">
                                <div id="pcc-fields-wrapper">
                                    <div class="pcc-facility-row mb-3 pb-2 border-bottom">
                                        <div class="small fw-bold mb-1 text-truncate pcc-facility-name"></div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="text" id="pcc_username" name="" class="form-control form-control-sm" placeholder="Username">
                                            </div>
                                            <div class="col-6">
                                                <input type="password" id="pcc_password" name="" class="form-control form-control-sm" placeholder="Password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary px-4">Update Clinician</button>
                <a href="<?= base_url('admin/clinicians') ?>" class="btn btn-outline-secondary px-4">Reset</a>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('customJS') ?>
<!-- Handled by js/pages/admin/clinicians_dropzone.js -->
<?= $this->endSection() ?>
