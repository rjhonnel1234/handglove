<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="heading d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Tax Types</h2>
                <div class="kicker-bottom">Manage Tax Types</div>
            </div>
            <a href="<?= base_url('admin/tax-types/create') ?>" class="btn thm-btn">Add Tax Type</a>
        </div>

        <?php if(session()->getFlashdata('message')):?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif;?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="tax-types-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Percentage</th>
                                <th>Description</th>
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
        $("#tax-types-table").DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('admin/tax-types/list') ?>",
                "type": "POST",
                "data": function(d) {
                    d[csrfName] = "<?= csrf_hash() ?>";
                }
            },
            "order": [[0, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": [4] }
            ]
        });
    });
</script>
<?php echo $this->endSection() ?>
