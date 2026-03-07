<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="heading mb-4">
            <h2>Tax Types</h2>
            <div class="kicker-bottom">Edit Tax Type</div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="tax-type-form" 
                    action="<?= base_url('admin/tax-types/update/' . $tax_type['id']) ?>" 
                    method="post"
                    data-csrf-token="<?= csrf_token() ?>"
                    data-csrf-hash="<?= csrf_hash() ?>"
                    data-base-url="<?= base_url() ?>">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tax Name</label>
                                <input type="text" name="name" class="form-control" value="<?= esc($tax_type['name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Percentage (%)</label>
                                <input type="number" step="0.01" name="percentage" class="form-control" value="<?= esc($tax_type['percentage']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?= esc($tax_type['description']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" <?= $tax_type['status'] == 1 ? 'selected' : '' ?>>Active</option>
                                    <option value="0" <?= $tax_type['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" id="submit-btn" class="btn thm-btn">Update Tax Type</button>
                        <a href="<?= base_url('admin/tax-types') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endSection() ?>
