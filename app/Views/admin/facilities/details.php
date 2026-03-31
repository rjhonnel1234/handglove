<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="heading mb-4">
            <h2>Facilities</h2>
            <div class="kicker-bottom">Facility details</div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Facility Basic Info -->
            <div class="col-md-12">
                <style>
                    .facility-profile-container {
                        display: flex;
                        align-items: flex-start;
                        gap: 40px;
                        margin-bottom: 30px;
                        background: #fff;
                        padding: 20px;
                        border-radius: 15px;
                        /* box-shadow: 0 4px 6px rgba(0,0,0,0.02); */
                    }

                    .logo-column {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 10px;
                        width: 180px;
                    }

                    .facility-logo-main {
                        width: 160px;
                        height: 160px;
                        object-fit: contain;
                    }

                    .company-label {
                        font-weight: 800;
                        font-size: 1.5rem;
                        letter-spacing: 1px;
                        color: #1a1a1a;
                        text-transform: uppercase;
                        margin: 0;
                    }

                    .info-column {
                        flex: 1;
                        padding-top: 10px;
                    }

                    .facility-title {
                        font-size: 2.2rem;
                        font-weight: 800;
                        color: #1a1a1a;
                        margin-bottom: 12px;
                        line-height: 1;
                    }

                    .contact-item {
                        display: flex;
                        align-items: flex-start;
                        gap: 10px;
                        margin-bottom: 8px;
                        color: #6c757d;
                        font-size: 1rem;
                    }

                    .contact-item i {
                        font-size: 1.1rem;
                        width: 20px;
                        text-align: center;
                        margin-top: 3px;
                    }

                    .stats-row {
                        display: flex;
                        gap: 40px;
                        margin-top: 25px;
                    }

                    .stats-block {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        text-align: center;
                    }

                    .stats-icon-wrapper {
                        width: 45px;
                        height: 45px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin-bottom: 8px;
                        position: relative;
                    }

                    .stats-icon-wrapper i {
                        font-size: 1.4rem;
                        color: #3498db;
                    }

                    /* Circular Progress mimics */
                    .circular-icon {
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                        border: 4px solid #e9ecef;
                        border-top-color: #3498db;
                        position: relative;
                    }

                    .circular-icon.census-icon {
                        border-right-color: #3498db;
                        border-bottom-color: #3498db;
                        /* ~75% */
                        transform: rotate(45deg);
                    }

                    .circular-icon.wf-icon {
                        border-top-color: #3498db;
                        /* ~25% */
                        transform: rotate(0deg);
                    }

                    .stats-value {
                        font-weight: 700;
                        font-size: 1.1rem;
                        color: #2c3e50;
                        line-height: 1.2;
                    }

                    .stats-label {
                        font-size: 0.8rem;
                        color: #6c757d;
                        font-weight: 500;
                        margin-top: 2px;
                    }

                    .header-actions {
                        position: absolute;
                        top: 20px;
                        right: 20px;
                    }

                    @media (max-width: 768px) {
                        .facility-profile-container {
                            flex-direction: column;
                            align-items: center;
                            text-align: center;
                        }

                        .info-column {
                            width: 100%;
                        }

                        .contact-item {
                            justify-content: center;
                        }

                        .stats-row {
                            justify-content: center;
                            gap: 20px;
                        }
                    }
                </style>

                <div class="facility-profile-container position-relative p-0">
                    <div class="header-actions">
                        <a href="<?= base_url('admin/facilities/edit/' . $facility['id']) ?>"
                            class="btn btn-outline-primary btn-sm rounded-pill">
                            <i class="fas fa-edit mr-1"></i> Edit Profile
                        </a>
                    </div>

                    <div class="logo-column">
                        <img class="facility-logo-main"
                            src="<?= $facility['company_logo'] ? $facility['company_logo'] : base_url('assets/dist/img/default-facility.png') ?>"
                            alt="Facility logo">
                    </div>

                    <div class="info-column">
                        <h1 class="facility-title"><?= $facility['company_name'] ?></h1>

                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= esc($facility['company_address']) ?: 'No address provided' ?>,
                                <?= esc($facility['state_name']) ?>, <?= esc($facility['country_name']) ?></span>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-phone-alt rotate-90"></i>
                            <span><?= esc($facility['company_number']) ?: 'No contact number' ?></span>
                        </div>

                        <div class="stats-row">
                            <div class="stats-block">
                                <div class="stats-icon-wrapper">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                                <div class="stats-value">300</div>
                                <div class="stats-label">Total Beds</div>
                            </div>

                            <div class="stats-block">
                                <div class="stats-icon-wrapper">
                                    <div class="circular-icon census-icon"></div>
                                </div>
                                <div class="stats-value">1:55</div>
                                <div class="stats-label">Census</div>
                            </div>

                            <div class="stats-block">
                                <div class="stats-icon-wrapper">
                                    <div class="circular-icon wf-icon"></div>
                                </div>
                                <div class="stats-value">26%</div>
                                <div class="stats-label">Work Friendly</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Tabbed Content -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills" id="detailTabs">
                            <li class="nav-item"><a class="nav-link active" href="#units" data-toggle="tab">Units</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#personnel" data-toggle="tab">Personnel</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#shifts" data-toggle="tab">Shifts</a></li>
                            <li class="nav-item"><a class="nav-link" href="#invoices" data-toggle="tab">Invoices</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#receipts" data-toggle="tab">Receipts</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Units Tab -->
                            <div class="tab-pane active" id="units">
                                <div class="mb-3 text-right">
                                    <a href="<?= base_url('admin/facilities/units/create/' . $facility['id']) ?>"
                                        class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add Unit</a>
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
                                    <a href="<?= base_url('admin/facilities/personnel/create/' . $facility['id']) ?>"
                                        class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add Personnel</a>
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
<?= $this->endSection() ?>

<?= $this->section('customJS') ?>
<script>
    $(document).ready(function () {
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
        $(document).on('click', '.delete-item', function () {
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
                    $.get(url, function (res) {
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