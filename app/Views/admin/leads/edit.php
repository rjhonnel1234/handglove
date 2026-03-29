<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<style>
    .management-row .btn.dropdown-toggle{
        padding: 8px;   
    }
    .management-row .remove-mgmt-row{
        position: absolute;
        right: -5px;
    }
    .pl-0 {
        padding-left: 0 !important;
    }
</style>
<div class="container-fluid">
    <div class="heading mb-4">
        <h2>Leads</h2>
        <div class="kicker-bottom">Edit Lead</div>
    </div>

    <form id="lead-form" action="<?= base_url('admin/leads/update/' . $lead['id']) ?>" method="post">
        <?= csrf_field() ?>
        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <div class="pb-2">
                                    <span class="badge bg-warning text-white rounded-pill px-3 py-2">
                                        <?= $origin_mapping[$lead['origin']] ?? 'Unknown' ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Schedule Date</label>
                                            <input type="text" name="date" class="form-control datepicker" placeholder="Select Date" value="<?= esc($lead['date'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Schedule Time</label>
                                            <input type="time" name="time" class="form-control" value="<?= esc($lead['time'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Company Name</label>
                                            <select name="institution_id" id="institution_id" class="form-select form-control selectpicker" data-live-search="true" required>
                                                <option value="">Select Institution</option>
                                                <?php foreach ($institutions as $inst): ?>
                                                    <option value="<?= $inst['id'] ?>" <?= ($lead['provider_id'] == $inst['id']) ? 'selected' : '' ?>><?= esc($inst['name']) ?></option>
                                                <?php endforeach; ?>
                                                <option value="other" <?= (empty($lead['provider_id'])) ? 'selected' : '' ?>>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="other_company_div" style="<?= (empty($lead['provider_id'])) ? '' : 'display: none;' ?>">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-bold">Other Company Name</label>
                                                <input type="text" name="company_name" id="company_name" class="form-control" placeholder="Enter company name" value="<?= (empty($lead['provider_id'])) ? esc($lead['company_name']) : '' ?>">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label fw-bold">Country</label>
                                                        <select name="country" class="form-select form-control selectpicker" data-live-search="true">
                                                            <option value="">Select Country</option>
                                                            <?php foreach ($countries as $country): ?>
                                                                <option value="<?= $country['id'] ?>" <?= (empty($lead['provider_id']) && $lead['country'] == $country['id']) ? 'selected' : ($country['id'] == 233 && empty($lead['country']) ? 'selected' : '') ?>><?= esc($country['name']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label fw-bold">State</label>
                                                        <select name="state" class="form-select form-control selectpicker" data-live-search="true">
                                                            <option value="">Select State</option>
                                                            <?php foreach ($states as $state): ?>
                                                                <option value="<?= $state['id'] ?>" <?= (empty($lead['provider_id']) && $lead['state'] == $state['id']) ? 'selected' : '' ?>><?= esc($state['name']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Address</label>
                                    <textarea name="address" class="form-control" rows="2" required><?= esc($lead['address']) ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Zip Code</label>
                                    <input type="text" name="zip_code" class="form-control" value="<?= esc($lead['zip_code']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <?php if($lead['origin'] == 4){ ?>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="">Average Census</label>
                                    <div><?php echo $lead['census']; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Features interested</label>
                                    <?php 
                                        $features = json_decode($lead['features']);
                                        foreach($features as $feature){
                                            echo '<div>'.$feature.'</div>';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Agency Providers</label>
                                    <?php $selectedAgencies = explode(',', $lead['agencies'] ?? ''); ?>
                                    <select name="agencies[]" class="form-select form-control selectpicker" data-live-search="true" multiple data-actions-box="true" title="Select Agencies">
                                        <?php foreach ($agencies as $agency): ?>
                                            <option value="<?= $agency['id'] ?>" <?= in_array($agency['id'], $selectedAgencies) ? 'selected' : '' ?>><?= esc($agency['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= esc($lead['email']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Contact Number</label>
                                    <input type="text" name="contact_number" class="form-control" value="<?= esc($lead['contact_number']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <select name="status" class="form-select form-control selectpicker">
                                        <?php foreach ($status_mapping as $key => $value) { ?>
                                            <option value="<?= $key ?>" <?= $lead['status'] == $key ? 'selected' : '' ?>><?= $value ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">Management</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div id="management-container">
                            <!-- Rows will be added dynamically by JS -->
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-management-row">
                                <i class="fas fa-plus"></i> Add Row
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea name="notes" class="form-control" rows="4"><?= esc($lead['notes']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex justify-content-end gap-2">
                <button type="submit" class="btn thm-btn px-4 mr-2">Update Lead</button>
                <a href="<?= base_url('admin/leads') ?>" class="btn btn-outline-secondary px-4">Cancel</a>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('customJS') ?>
<script type="text/javascript">
    $(document).ready(function() {
        const userTypes = <?= json_encode($user_types) ?>;
        const managementData = <?= json_encode($management) ?>;
        let rowCount = 0;

        function createManagementRow(data = {}) {
            const index = rowCount++;
            const rowHtml = `
                <div class="row management-row mb-2 align-items-center" data-index="${index}">
                    <div class="col-md-2">
                        <select name="mgmt_user_type[]" class="form-control selectpicker show-tick" data-live-search="true" title="User Type">
                            ${userTypes.map(type => `<option value="${type.id}" ${data.user_type == type.id ? 'selected' : ''}>${type.name}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-3 pl-0">
                        <input type="text" name="mgmt_name[]" class="form-control" placeholder="Name" value="${data.name || ''}">
                    </div>
                    <div class="col-md-2 pl-0">
                        <input type="text" name="mgmt_contact_number[]" class="form-control" placeholder="Contact No" value="${data.contact_number || ''}">
                    </div>
                    <div class="col-md-3 pl-0">
                        <input type="email" name="mgmt_email[]" class="form-control" placeholder="Email" value="${data.email || ''}">
                    </div>
                    <div class="col-md-2 pl-0">
                        <div class="d-flex align-items-center gap-2">
                            <select name="mgmt_approver_status[]" class="form-select form-control approver-select">
                                <option value="0" ${data.is_approver == 0 ? 'selected' : ''}>Non-Approver</option>
                                <option value="1" ${data.is_approver == 1 ? 'selected' : ''}>Approver</option>
                            </select>
                            <input type="hidden" name="mgmt_is_approver" value="${data.is_approver == 1 ? index : ''}" class="approver-hidden" ${data.is_approver == 1 ? '' : 'disabled'}>
                            <button type="button" class="btn btn-link text-danger remove-mgmt-row p-0">
                                <i class="fas fa-times-circle fs-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            const $row = $(rowHtml);
            $('#management-container').append($row);
            $row.find('.selectpicker').selectpicker();
        }

        // Load existing management data
        if (managementData && managementData.length > 0) {
            managementData.forEach(row => createManagementRow(row));
        } else {
            // Default rows if none exist (same as create)
            const initialPositions = [3, 4, 5]; // DON, HR, Scheduler
            createManagementRow({user_type: 2}); // Admin/CEO
            initialPositions.forEach(id => createManagementRow({user_type: id}));
        }

        $('#add-management-row').on('click', function() {
            createManagementRow();
        });

        $(document).on('click', '.remove-mgmt-row', function() {
            $(this).closest('.management-row').remove();
        });

        $(document).on('change', '.approver-select', function() {
            const $current = $(this);
            const val = $current.val();
            const $row = $current.closest('.management-row');
            const currentIndex = $row.data('index');

            if (val == 1) {
                $('.approver-select').not($current).val('0');
                $('.approver-hidden').prop('disabled', true).val('');
                $row.find('.approver-hidden').prop('disabled', false).val(currentIndex);
            } else {
                $row.find('.approver-hidden').prop('disabled', true).val('');
            }
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });

        $('#institution_id').on('change', function() {
            if ($(this).val() === 'other') {
                $('#other_company_div').show();
                $('#company_name').attr('required', true);
                $('select[name="country"]').attr('required', true);
                $('select[name="state"]').attr('required', true);
            } else {
                $('#other_company_div').hide();
                $('#company_name').attr('required', false);
                $('select[name="country"]').attr('required', false);
                $('select[name="state"]').attr('required', false);
            }
        });

        // Trigger change on load to set initial state
        if ($('#institution_id').val() === 'other') {
            $('#company_name').attr('required', true);
            $('select[name="country"]').attr('required', true);
            $('select[name="state"]').attr('required', true);
        }

        $('#lead-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);

            // Client-side management check
            let hasMgmt = false;
            $('input[name="mgmt_name[]"]').each(function() {
                if ($(this).val().trim() !== '') {
                    hasMgmt = true;
                    return false;
                }
            });

            if (!hasMgmt) {
                toastr.error('At least one management contact name is required.');
                return false;
            }

            const $btn = $form.find('button[type="submit"]');
            
            $btn.prop('disabled', true).text('Updating...');

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1000);
                    } else {
                        if (response.errors) {
                            let errorMsg = Object.values(response.errors).join('<br>');
                            toastr.error(errorMsg);
                        } else {
                            toastr.error(response.message || 'Update failed');
                        }
                        $btn.prop('disabled', false).text('Update Lead');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                    $btn.prop('disabled', false).text('Update Lead');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
