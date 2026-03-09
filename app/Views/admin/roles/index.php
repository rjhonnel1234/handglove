<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="heading d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Roles</h2>
                <div class="kicker-bottom">Manage Roles</div>
            </div>
            <a href="<?= base_url('admin/roles/create') ?>" class="btn thm-btn">Add Role</a>
        </div>

        <?php if(session()->getFlashdata('message')):?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif;?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="roles-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Role</th>
                                <th>Status</th>
                                <th width="150" class="text-center">Action</th>
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

<?php echo $this->endSection() ?>

<?php echo $this->section('customJS') ?>
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize DataTable
        $("#roles-table").DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('admin/roles/list') ?>",
                "type": "POST",
                "data": function(d) {
                    d[csrfName] = "<?= csrf_hash() ?>";
                }
            },
            "order": [[0, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": [2] }
            ]
        });
    });
</script>
<?php echo $this->endSection() ?>
