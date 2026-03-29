<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="heading d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Facilities</h2>
            <div class="kicker-bottom">Manage Facilities/Clients</div>
        </div>
        <div>
            <a href="<?= base_url('admin/facilities/create') ?>" class="btn thm-btn px-4"><i class="fas fa-plus mr-2"></i> Add Facility</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="facilities-table" class="table table-hover" data-list-url="<?= base_url('admin/facilities/list') ?>">
                    <thead>
                        <tr>
                            <th width="80">Logo</th>
                            <th>Facility Details</th>
                            <th>Location & Contact</th>
                            <th width="100">Status</th>
                            <th class="text-center" width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('customJS') ?>
<script type="text/javascript">
    $(function() {
        // Initialize DataTable
        var table = $('#facilities-table').DataTable({
            "ajax": {
                "url": $('#facilities-table').data('list-url'),
                "type": "POST"
            },
            "order": [[1, "asc"]],
            "columns": [
                { "data": 0, "orderable": false },
                { "data": 1 },
                { "data": 2 },
                { "data": 3 },
                { "data": 4, "className": "text-center", "orderable": false }
            ]
        });
    });
</script>
<?= $this->endSection() ?>
