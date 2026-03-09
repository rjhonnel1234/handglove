<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="heading d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Invoices</h2>
                <div class="kicker-bottom">Manage Client Invoices</div>
            </div>
        </div>

        <?php if(session()->getFlashdata('message')):?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif;?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-4 d-flex align-items-center">
                    <div class="btn-group" role="group" id="status-filters">
                        <button type="button" class="btn btn-outline-secondary active" data-status="">All</button>
                        <button type="button" class="btn btn-outline-success" data-status="20">Paid</button>
                        <button type="button" class="btn btn-outline-warning" data-status="10">Unpaid</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="invoices-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Invoice ID</th>
                                <th>Client</th>
                                <th>Clinician</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
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
        let currentStatus = '';

        const table = $("#invoices-table").DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('admin/invoices/list') ?>",
                "type": "POST",
                "data": function(d) {
                    d[csrfName] = "<?= csrf_hash() ?>";
                    d.status = currentStatus;
                }
            },
            "order": [[5, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [6] }
            ]
        });

        $('#status-filters button').on('click', function() {
            $('#status-filters button').removeClass('active');
            $(this).addClass('active');
            
            currentStatus = $(this).data('status');
            table.ajax.reload();
        });
    });
</script>
<?php echo $this->endSection() ?>
