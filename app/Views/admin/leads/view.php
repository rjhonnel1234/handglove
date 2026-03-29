<?= $this->extend('admin/includes/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="heading d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Leads</h2>
            <div class="kicker-bottom">View Lead Details</div>
        </div>
        <div>
            <?php if($lead['status'] != 50): ?>
                <a href="<?= base_url('admin/leads/edit/' . $lead['id']) ?>" class="btn btn-info text-white"><i class="fas fa-edit"></i> Edit Lead</a>
            <?php endif; ?>
            <a href="<?= base_url('admin/leads') ?>" class="btn btn-outline-secondary ml-2">Back to List</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Lead Basic Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle text-primary me-2"></i>Lead Information</h5>
                    <span class="badge <?= $status_badge_color[$lead['status']] ?> text-white rounded-pill px-3 py-2">
                        <?= $status_mapping[$lead['status']] ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Company Name:</div>
                        <div class="col-md-8"><?= esc($lead['company_name']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Email:</div>
                        <div class="col-md-8"><?= esc($lead['email']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Contact Number:</div>
                        <div class="col-md-8"><?= esc($lead['contact_number']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Address:</div>
                        <div class="col-md-8">
                            <?= esc($lead['address']) ?><br>
                            <?= esc($lead['zip_code']) ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Schedule:</div>
                        <div class="col-md-8">
                            <?= !empty($lead['date']) ? date('d M Y', strtotime($lead['date'])) : '---' ?> 
                            at 
                            <?= !empty($lead['time']) ? date('h:i A', strtotime($lead['time'])) : '---' ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 fw-bold">Notes:</div>
                        <div class="col-md-8"><?= nl2br(esc($lead['notes'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- Management Contacts -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-users text-primary me-2"></i>Management Contacts</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless">
                            <thead class="text-muted small">
                                <tr>
                                    <th>Role</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th class="text-center">Approver</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($management)): ?>
                                    <tr><td colspan="5" class="text-center py-3 text-muted">No management contacts added.</td></tr>
                                <?php else: ?>
                                    <?php foreach($management as $mgmt): ?>
                                        <tr>
                                            <td><?= $user_types[$mgmt['user_type']] ?? 'Unknown' ?></td>
                                            <td class="fw-bold"><?= esc($mgmt['name']) ?></td>
                                            <td><?= esc($mgmt['contact_number']) ?></td>
                                            <td><?= esc($mgmt['email']) ?></td>
                                            <td class="text-center">
                                                <?php if($mgmt['is_approver']): ?>
                                                    <span class="badge bg-success-soft text-success"><i class="fas fa-check-circle"></i> Yes</span>
                                                <?php else: ?>
                                                    <span class="text-muted">No</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status History Tracking -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-history text-primary me-2"></i>Status History</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php 
                        $history = [
                            ['label' => 'Presentation', 'date' => $lead['presentation_datetime'], 'color' => 'bg-warning'],
                            ['label' => 'Awaiting Contract', 'date' => $lead['awaiting_contract_datetime'], 'color' => 'bg-primary'],
                            ['label' => 'On Contract', 'date' => $lead['on_contract_datetime'], 'color' => 'bg-success'],
                            ['label' => 'Cancelled', 'date' => $lead['cancelled_datetime'], 'color' => 'bg-danger'],
                        ];
                        ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($history as $item): ?>
                                <div class="list-group-item px-0 border-0 pb-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 fw-bold"><?= $item['label'] ?></h6>
                                        <?php if($item['date']): ?>
                                            <span class="badge <?= $item['color'] ?> text-white small"><?= date('d M Y, h:i A', strtotime($item['date'])) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small italic">Pending</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($item['date']): ?>
                                        <p class="mb-1 text-muted small">Status reached at the date above.</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="card border-0 shadow-sm mb-4 text-center p-3">
                <div class="small text-muted mb-1">Lead Created</div>
                <div class="fw-bold"><?= date('d M Y, h:i A', strtotime($lead['created_datetime'])) ?></div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-soft {
        background-color: #e8f5e9;
    }
    .italic {
        font-style: italic;
    }
</style>
<?= $this->endSection() ?>
