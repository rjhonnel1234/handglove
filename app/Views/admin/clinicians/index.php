<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $page_title ?></h1>
        <a href="<?= base_url('admin/clinicians/create') ?>" class="btn thm-btn d-flex align-items-center">
            <i class="fas fa-plus me-2"></i>&nbsp;&nbsp;Add Clinician
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="cliniciansTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">Photo</th>
                            <th>Clinician Details</th>
                            <th>Type</th>
                            <th style="width: 75px;">Status</th>
                            <th class="text-center" style="width: 250px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>



<?php echo $this->section('customJS') ?>
<script type="text/javascript">
    $(document).ready(function() {
       
        // Initialize DataTable
        $("#cliniciansTable").DataTable({
            "processing": true,
            "serverSide": false, // Use client-side processing since we are returning all data (standard for this project pattern)
            "ajax": {
                "url": "<?= base_url('admin/clinicians/list') ?>",
                "type": "POST",
                "data": function(d) {
                    d[csrfName] = "<?= csrf_hash() ?>";
                }
            },
            "order": [[0, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": [2, 3] } // Disable sorting on Logo and Action
            ]
        });
    });
</script>
<?php echo $this->endSection() ?>
