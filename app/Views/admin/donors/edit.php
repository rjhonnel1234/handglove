<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="heading mb-4">
            <h2>Donors</h2>
            <div class="kicker-bottom">Update Donor</div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="donor-form-update" 
                    action="<?= base_url('admin/donors/update/'.$donor['id']) ?>" 
                    method="post"
                    data-upload-url="<?= base_url('admin/donors/upload') ?>"
                    data-csrf-token="<?= csrf_token() ?>"
                    data-csrf-hash="<?= csrf_hash() ?>"
                    data-base-url="<?= base_url() ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="logo_path" id="logo_path" value="<?= $donor['logo'] ?>">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Donor Name</label>
                                <input type="text" name="donor_name" class="form-control" value="<?= old('donor_name', $donor['name']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Website</label>
                                <input type="url" name="website" class="form-control" placeholder="https://example.com" value="<?= old('website', $donor['url']) ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Logo</label>
                                <div id="logo-dropzone" class="dropzone">
                                    <div class="dz-message" data-dz-message>
                                        <span>Drop logo here or click to upload</span>
                                    </div>
                                </div>
                                <small class="text-muted">Preferred format: PNG or SVG with transparent background.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" id="submit-btn-update" class="btn thm-btn">Update Donor</button>
                        <a href="<?= base_url('admin/donors') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endSection() ?>
