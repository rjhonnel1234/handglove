<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="heading mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2>Credential Types</h2>
            <div class="kicker-bottom">Manage Credential Types</div>
        </div>
        <a href="<?= base_url('admin/credential-types/create') ?>" class="btn thm-btn px-4">
            <i class="fas fa-plus mr-2"></i>Add New Credential Type
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="credentialTypesTable" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th width="90">Status</th>
                                    <th class="text-center" width="90">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('customJS') ?>
<script type="text/javascript">
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';
    const base_url = '<?= base_url() ?>';
</script>
<?= $this->endSection() ?>