<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>


<div class="row">
    <div class="col-12">
        <div class="heading mb-4">
            <h2>Advisors</h2>
            <div class="kicker-bottom">Add New Advisor</div>

        </div>


        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="board-member-form" 
                    action="<?= base_url('admin/board-of-directors/store') ?>" 
                    method="post"
                    data-upload-url="<?= base_url('admin/board-of-directors/upload') ?>"
                    data-csrf-token="<?= csrf_token() ?>"
                    data-csrf-hash="<?= csrf_hash() ?>"
                    data-base-url="<?= base_url() ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="picture_path" id="picture_path">
                    <input type="hidden" name="logo_path" id="logo_path">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">CEO Name</label>
                                <input type="text" name="ceo_name" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Position</label>
                                <input type="text" name="position" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Company Website</label>
                                <input type="url" name="company_website" class="form-control" placeholder="https://example.com">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Picture</label>
                                <div id="picture-dropzone" class="dropzone">
                                    <div class="dz-message" data-dz-message>
                                        <span>Drop picture here or click to upload</span>
                                    </div>
                                </div>
                                <small class="text-muted">Recommended size: 300x300 pixels.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Company Logo</label>
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
                        <button type="submit" id="submit-btn" class="btn thm-btn">Save Advisor</button>
                        <a href="<?= base_url('admin/board-of-directors') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endSection() ?>
