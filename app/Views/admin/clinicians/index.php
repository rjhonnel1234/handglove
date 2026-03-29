<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="heading d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Clinicians</h2>
            <div class="kicker-bottom">Manage Clinicians</div>
        </div>
        <a href="<?= base_url('admin/clinicians/create') ?>" class="btn thm-btn d-flex align-items-center">
            <i class="fas fa-plus me-2"></i>&nbsp;&nbsp;Add Clinician
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">

            <?php if(session()->getFlashdata('message')):?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('message') ?>
                </div>
            <?php endif;?>

            <?php if(session()->getFlashdata('error')):?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif;?>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="cliniciansTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">Photo</th>
                            <th>Clinician Details</th>
                            <th width="80">Type</th>
                            <th width="70">Status</th>
                            <th class="text-center" width="200">Action</th>
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

<!-- Credentials Upload Modal -->
<div class="modal" id="credentialsModal" tabindex="-1" aria-labelledby="credentialsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="credentialsModalLabel">Upload Credentials for <span id="modal-clinician-name"></span></h5>
                <button type="button" class="btn btn-close pl-2 pr-2" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Credential Type</th>
                                <th>Current File</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody id="credentials-list-body">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>



<?php echo $this->section('customJS') ?>
<script type="text/javascript">

    const baseUrl = '<?= base_url() ?>';
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';
    
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
            "columnDefs": [
                { "orderable": false, "targets": [0, 3, 4] } // Disable sorting on Logo and Action
            ]
        });


        // Clinician Credentials Upload Handling
        $(document).on('click', '.upload-credentials', function () {
            const clinicianId = $(this).data('id');
            const clinicianName = $(this).data('name');

            $('#modal-clinician-name').text(clinicianName);
            var myModal = new bootstrap.Modal(document.getElementById('credentialsModal'));
            myModal.show();
            loadClinicianCredentials(clinicianId);
        });

        function loadClinicianCredentials(clinicianId) {
            const $body = $('#credentials-list-body');
            $body.html('<tr><td colspan="3" class="text-center"><div class="spinner-border spinner-border-sm text-primary"></div> Loading...</td></tr>');

            $.ajax({
                url: `${baseUrl}admin/clinicians/get-credentials`,
                type: 'GET',
                data: { clinician_id: clinicianId },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        let html = '';
                        response.data.forEach(item => {
                            const fileLink = item.file_path ? `<a href="${baseUrl}${item.file_path}" target="_blank" class="text-primary"><i class="fas fa-file-alt"></i> ${item.filename}</a>` : '<span class="text-muted">No file uploaded</span>';
                            html += `
                                <tr>
                                    <td><strong>${item.type_name}</strong></td>
                                    <td id="file-status-${item.type_id}">${fileLink}</td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="file" class="form-control d-none credential-file-input" id="input-${item.type_id}" data-type-id="${item.type_id}" data-clinician-id="${clinicianId}">
                                            <button class="btn btn-sm btn-outline-primary w-100 trigger-file-input" data-target="#input-${item.type_id}">
                                                <i class="fas fa-upload me-1"></i> ${item.file_path ? 'Update' : 'Upload'}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        $body.html(html);
                    } else {
                        $body.html(`<tr><td colspan="3" class="text-center text-danger">${response.message}</td></tr>`);
                    }
                },
                error: function () {
                    $body.html('<tr><td colspan="3" class="text-center text-danger">Failed to load credentials.</td></tr>');
                }
            });
        }

        $(document).on('click', '.trigger-file-input', function () {
            $($(this).data('target')).click();
        });

        $(document).on('change', '.credential-file-input', function () {
            const file = this.files[0];
            if (!file) return;

            const clinicianId = $(this).data('clinician-id');
            const typeId = $(this).data('type-id');
            const $btn = $(this).closest('td').find('.trigger-file-input');
            const originalText = $btn.html();

            const formData = new FormData();
            formData.append('file', file);
            formData.append('clinician_id', clinicianId);
            formData.append('credential_id', typeId);
            formData.append(csrfName, csrfHash);

            $btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm"></div>');

            $.ajax({
                url: `${baseUrl}admin/clinicians/upload-credential`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $(`#file-status-${typeId}`).html(`<a href="${response.file_path}" target="_blank" class="text-primary"><i class="fas fa-file-alt"></i> ${file.name}</a>`);
                        $btn.html('<i class="fas fa-upload me-1"></i> Update');
                    } else {
                        toastr.error(response.message);
                        $btn.html(originalText);
                    }
                    $btn.prop('disabled', false);
                },
                error: function () {
                    toastr.error('Upload failed. Please try again.');
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

    });
</script>
<?php echo $this->endSection() ?>
