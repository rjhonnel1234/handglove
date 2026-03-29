<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $page_title ?></h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="<?= base_url('admin/facilities') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Facility Basic Info -->
            <div class="col-md-3">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle" src="<?= $facility['company_logo'] ? base_url($facility['company_logo']) : base_url('assets/dist/img/default-facility.png') ?>" alt="Facility logo" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <h3 class="profile-username text-center"><?= $facility['company_name'] ?></h3>
                        <p class="text-muted text-center"><?= $facility['contact_number'] ?: 'No Contact Number' ?></p>
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Status</b> <a class="float-right"><?= $facility['status'] == 10 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>State</b> <a class="float-right"><?= $facility['state_name'] ?: 'N/A' ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Country</b> <a class="float-right"><?= $facility['country_name'] ?: 'N/A' ?></a>
                            </li>
                        </ul>
                        <a href="<?= base_url('admin/facilities/edit/' . $facility['id']) ?>" class="btn btn-primary btn-block"><b>Edit Profile</b></a>
                    </div>
                </div>
            </div>

            <!-- Tabbed Content -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills" id="detailTabs">
                            <li class="nav-item"><a class="nav-link active" href="#units" data-toggle="tab">Units</a></li>
                            <li class="nav-item"><a class="nav-link" href="#personnel" data-toggle="tab">Personnel</a></li>
                            <li class="nav-item"><a class="nav-link" href="#shifts" data-toggle="tab">Shifts</a></li>
                            <li class="nav-item"><a class="nav-link" href="#invoices" data-toggle="tab">Invoices</a></li>
                            <li class="nav-item"><a class="nav-link" href="#receipts" data-toggle="tab">Receipts</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Units Tab -->
                            <div class="tab-pane active" id="units">
                                <div class="mb-3 text-right">
                                    <a href="<?= base_url('admin/facilities/units/create/' . $facility['id']) ?>" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add Unit</a>
                                </div>
                                <table id="unitsTable" class="table table-bordered table-striped w-100">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Census</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <!-- Personnel Tab -->
                            <div class="tab-pane" id="personnel">
                                <div class="mb-3 text-right">
                                    <a href="<?= base_url('admin/facilities/personnel/create/' . $facility['id']) ?>" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add Personnel</a>
                                </div>
                                <table id="personnelTable" class="table table-bordered table-striped w-100">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Contact</th>
                                            <th>Type</th>
                                            <th width="100">Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <!-- Shifts Tab -->
                            <div class="tab-pane" id="shifts">
                                <table id="shiftsTable" class="table table-bordered table-striped w-100">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Clinician</th>
                                            <th>Unit</th>
                                            <th>Hours</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <!-- Invoices Tab -->
                            <div class="tab-pane" id="invoices">
                                <table id="invoicesTable" class="table table-bordered table-striped w-100">
                                    <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Date</th>
                                            <th>Clinician</th>
                                            <th>Hours</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <!-- Receipts Tab -->
                            <div class="tab-pane" id="receipts">
                                <div class="text-center py-5">
                                    <i class="fas fa-file-invoice-dollar fa-4x text-muted mb-3"></i>
                                    <p class="text-muted">No receipts records found.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    const clientId = '<?= $facility['id'] ?>';

    // Units Table
    const unitsTable = $('#unitsTable').DataTable({
        ajax: {
            url: `<?= base_url('admin/facilities/units/list/') ?>${clientId}`,
            type: 'POST'
        },
        responsive: true
    });

    // Personnel Table
    const personnelTable = $('#personnelTable').DataTable({
        ajax: {
            url: `<?= base_url('admin/facilities/personnel/list/') ?>${clientId}`,
            type: 'POST'
        },
        responsive: true
    });

    // Shifts Table
    const shiftsTable = $('#shiftsTable').DataTable({
        ajax: {
            url: `<?= base_url('admin/facilities/shifts/list/') ?>${clientId}`,
            type: 'POST'
        },
        responsive: true
    });

    // Invoices Table
    const invoicesTable = $('#invoicesTable').DataTable({
        ajax: {
            url: `<?= base_url('admin/facilities/invoices/list/') ?>${clientId}`,
            type: 'POST'
        },
        responsive: true
    });

    // Delete Item Handler
    $(document).on('click', '.delete-item', function() {
        const url = $(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get(url, function(res) {
                    if (res.status === 'success') {
                        Toast.fire({ icon: 'success', title: res.message });
                        unitsTable.ajax.reload();
                        personnelTable.ajax.reload();
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
