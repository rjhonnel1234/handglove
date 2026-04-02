<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="heading d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Per Diem Networks</h2>
                <div class="kicker-bottom">Manage Your Resource Networks</div>
            </div>
            <a href="<?= base_url('admin/per-diem-networks/create') ?>" class="btn thm-btn">Add Network</a>
        </div>

        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success shadow-sm">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table table-striped table-hover" id="per-diem-table">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php echo $this->endSection() ?>

<?php echo $this->section('customJS') ?>
<script type="text/javascript">
    $(document).ready(function() {
        if ($("#per-diem-table").length > 0) {
            $("#per-diem-table").DataTable({
                "processing": true,
                "ajax": {
                    "url": "<?= base_url('admin/per-diem-networks/list') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d[csrfName] = "<?= csrf_hash() ?>";
                    }
                },
                "order": [[1, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [0, 4] }
                ]
            });
        }
    });
</script>
<?php echo $this->endSection() ?>
