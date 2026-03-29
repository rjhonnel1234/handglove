<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="heading d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Leads</h2>
            <div class="kicker-bottom">Manage Leads</div>
        </div>
        <a href="<?= base_url('admin/leads/create') ?>" class="btn thm-btn">Add Lead</a>
    </div>

    <?php if(session()->getFlashdata('message')):?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif;?>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="leadsTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Company</th>
                            <th width="200">Approver Details</th>
                            <th width="150">Schedule</th>
                            <th width="90">Status</th>
                            <th class="text-center" width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
