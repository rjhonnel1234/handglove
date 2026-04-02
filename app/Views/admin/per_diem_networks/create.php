<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="heading d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Add Per Diem Network</h2>
                <div class="kicker-bottom"><a href="<?= base_url('admin/per-diem-networks') ?>">Back to Networks</a></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form id="per-diem-network-form" action="<?= base_url('admin/per-diem-networks/store') ?>" method="POST"
                      data-upload-url="<?= base_url('admin/per-diem-networks/upload') ?>"
                      data-csrf-token="<?= csrf_token() ?>"
                      data-csrf-hash="<?= csrf_hash() ?>"
                      data-base-url="<?= base_url() ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="name" class="font-weight-bold">Network Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" required placeholder="Enter network name">
                            </div>

                            <div class="form-group mb-4">
                                <label for="description" class="font-weight-bold">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Describe this network..."></textarea>
                            </div>

                            <div class="form-group mb-4">
                                <label for="status" class="font-weight-bold">Status</label>
                                <select name="status" id="status" class="form-control custom-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Network Logo</label>
                                <div id="logo-dropzone" class="dropzone rounded border-dashed bg-light text-center py-4">
                                    <div class="dz-message needsclick">
                                        <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-muted"></i>
                                        <p class="mb-0">Drop logo here or click to upload.</p>
                                    </div>
                                </div>
                                <input type="hidden" name="logo_path" id="logo_path">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="text-right">
                        <a href="<?= base_url('admin/per-diem-networks') ?>" class="btn btn-light mr-2">Cancel</a>
                        <button type="submit" id="submit-btn" class="btn thm-btn">Save Network</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $this->endSection() ?>

<?php echo $this->section('customJS') ?>
<!-- JS is handled by per_diem_networks.min.js -->
<?php echo $this->endSection() ?>
