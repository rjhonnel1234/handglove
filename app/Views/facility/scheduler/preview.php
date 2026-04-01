<?php
// High-fidelity PDF Schedule Preview
?>

<div id="profile-bios">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div id="profile-main" class="col-lg-11 col-md-12 col-sm-12 col-12">
                <div id="scheduler_preview">
                    
                    <!-- Paper-Style Preview Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 font-weight-bold text-dark mb-1">PDF Schedule Verification</h1>
                            <p class="text-muted mb-0"><i class="fa fa-calendar-alt mr-2"></i>Date: <?= date('F d, Y', strtotime($selectedDate)) ?></p>
                        </div>
                        <div class="d-flex gap-2">
                             <button type="button" class="btn btn-outline-info btn-sm mr-2" data-toggle="collapse" data-target="#raw-text-viewer">
                                <i class="fa fa-code mr-1"></i> View Raw Extraction
                            </button>
                            <span class="badge badge-success-soft p-2"><i class="fa fa-check-circle mr-1"></i> ID: #<?= $upload_id ?></span>
                        </div>
                    </div>

                    <!-- Raw Text Viewer (Collapsible) -->
                    <div class="collapse mb-4" id="raw-text-viewer">
                        <div class="card bg-dark text-light border-0 shadow-sm">
                            <div class="card-header border-bottom border-secondary d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-info font-weight-bold">Raw Extracted Text</h6>
                                <button type="button" class="close text-light" data-toggle="collapse" data-target="#raw-text-viewer">&times;</button>
                            </div>
                            <div class="card-body p-0">
                                <pre class="p-3 text-light m-0" style="max-height: 400px; overflow-y: auto; font-size: 0.8rem; line-height: 1.5; white-space: pre-wrap;"><?= htmlspecialchars($raw_text) ?></pre>
                            </div>
                        </div>
                    </div>

                    <!-- The "Paper" Container -->
                    <div class="paper-container shadow-lg mb-5">
                        <div class="paper-header border-bottom border-dark py-4 text-center">
                            <h2 class="h4 font-weight-bold mb-0 text-uppercase">Facility Daily Schedule</h2>
                            <p class="mb-0 font-weight-bold text-muted"><?= date('l, F d, Y', strtotime($selectedDate)) ?></p>
                        </div>

                        <form id="save_parsed_schedule" class="p-4 p-md-5">
                            <input type="hidden" name="date" value="<?= $selectedDate ?>">
                            <input type="hidden" name="upload_id" value="<?= $upload_id ?>">

                            <div id="units-schedule-container">
                                <?php if (isset($parsedData['units']) && !empty($parsedData['units'])): ?>
                                        <?php foreach ($parsedData['units'] as $unitName => $shiftData): 
                                            $unitExists = $shiftData['db_exists'] ?? false;
                                            $unitClass = $unitExists ? 'unit-exists' : 'unit-new';
                                        ?>
                                                <div class="unit-block mb-5 <?= $unitClass ?>" data-unit-name="<?= htmlspecialchars($unitName) ?>">
                                                    <div class="unit-title-row mb-3 d-flex justify-content-between align-items-center">
                                                        <h3 class="h5 font-weight-bold mb-0 border-left border-primary border-4 pl-3">
                                                            <?= $unitName ?>
                                                            <?php if ($unitExists): ?>
                                                                <small class="text-success ml-2 x-small font-weight-bold"><i class="fa fa-database mr-1"></i> (Existing Unit)</small>
                                                            <?php else: ?>
                                                                <small class="text-white bg-danger px-2 py-1 rounded ml-2 x-small font-weight-bold"><i class="fa fa-plus-circle mr-1"></i> (New Unit - Needs Setup)</small>
                                                            <?php endif; ?>
                                                        </h3>
                                                        <button type="button" class="btn btn-link text-danger p-0 remove-unit-block" title="Remove Entire Unit">
                                                            <i class="fa fa-trash-alt mr-1"></i> <span class="x-small font-weight-bold text-uppercase">Remove Unit</span>
                                                        </button>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered schedule-table mb-0">
                                                            <thead class="bg-gray-100 text-center">
                                                                <tr>
                                                                    <?php
                                                                    $shifts = [
                                                                        'Day' => '7AM - 3PM',
                                                                        'Mid' => '3PM - 11PM',
                                                                        'Night' => '11PM - 7AM'
                                                                    ];
                                                                    foreach ($shifts as $name => $time): ?>
                                                                            <th style="width: 33.33%;" class="p-2 border-bottom-0">
                                                                                <div class="shift-name font-weight-bold text-uppercase small"><?= $name ?> Shift</div>
                                                                                <div class="shift-time text-muted x-small font-weight-normal"><?= $time ?></div>
                                                                                <div class="mt-1 d-flex align-items-center justify-content-center">
                                                                                    <?php $count = count($shiftData[$name] ?? []); ?>
                                                                                    <label for="" class="small">Slots: </label>
                                                                                    <input type="number" class="form-control form-control-sm mx-auto shift-slots-preview" 
                                                                                           data-shift-key="<?= $name ?>" 
                                                                                           value="<?= $count ?>" min="0" 
                                                                                           style="width: 55px; height: 22px; font-size: 0.7rem; padding: 2px;">
                                                                                </div>
                                                                            </th>
                                                                    <?php endforeach; ?>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <?php foreach ($shifts as $name => $time):
                                                                        $shiftKey = $name;
                                                                        $parsedStaff = $shiftData[$shiftKey] ?? [];
                                                                        ?>
                                                                            <td class="p-0 align-top bg-white" style="height: 1px;" data-shift="<?= $shiftKey ?>">
                                                                                <div class="staff-assignment-list-preview d-flex flex-column h-100">
                                                                                    <!-- Header -->
                                                                                    <div class="staff-header d-flex border-bottom x-small font-weight-bold text-muted bg-gray-50 p-1 px-2">
                                                                                        <div style="flex: 1;">Clinician Name</div>
                                                                                        <div style="width: 60px;" class="text-right">Role</div>
                                                                                        <div style="width: 25px;"></div>
                                                                                    </div>

                                                                                    <?php if (!empty($parsedStaff)): ?>
                                                                                            <?php foreach ($parsedStaff as $staff): 
                                                                                                $staffExists = $staff['db_exists'] ?? false;
                                                                                                $staffRowClass = $staffExists ? 'staff-exists' : 'staff-new';
                                                                                            ?>
                                                                                                    <div class="staff-row d-flex py-1 px-2 border-bottom align-items-center <?= $staffRowClass ?>"
                                                                                                         data-name="<?= htmlspecialchars($staff['name']) ?>" 
                                                                                                         data-position="<?= htmlspecialchars($staff['position']) ?>" 
                                                                                                         data-other="<?= htmlspecialchars($staff['other'] ?? '') ?>">
                                                                                                        <div class="staff-name-col pr-1" style="flex: 1;">
                                                                                                            <div class="small font-weight-bold"><?= $staff['name'] ?></div>
                                                                                                            <?php if (!empty($staff['other'])): ?>
                                                                                                                <div class="x-small italic <?= $staffExists ? 'text-success' : 'text-white-50' ?>"><?= $staff['other'] ?></div>
                                                                                                            <?php endif; ?>
                                                                                                        </div>
                                                                                                        <div class="staff-role-col text-right" style="width: 60px;">
                                                                                                            <span class="role-badge"><?= $staff['position'] ?></span>
                                                                                                        </div>
                                                                                                        <div class="staff-action-col text-right" style="width: 25px;">
                                                                                                            <button type="button" class="btn btn-link p-0 remove-staff-row <?= $staffExists ? 'text-danger' : 'text-white' ?>" title="Remove line">
                                                                                                                <i class="fa fa-times-circle"></i>
                                                                                                            </button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                            <?php endforeach; ?>
                                                                                    <?php else: ?>
                                                                                            <div class="empty-shift-notice m-auto x-small text-muted italic py-4">Unassigned</div>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </td>
                                                                    <?php endforeach; ?>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                        <?php endforeach; ?>
                                <?php else: ?>
                                        <div class="alert alert-warning text-center py-5">
                                            <i class="fa fa-exclamation-triangle fa-2x d-block mb-3"></i>
                                            Unable to extract structured data from this PDF.
                                        </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-between gap-3 mt-5 pt-4 border-top">
                                <a href="<?= base_url('facility/schedules') ?>" class="btn btn-outline-secondary px-4">
                                    <i class="fa fa-arrow-left mr-2"></i> Close Preview
                                </a>

                                <button type="button" id="convert_to_schedule_btn" class="btn btn-primary px-5 shadow-sm">
                                    <i class="fa fa-magic mr-2"></i> Convert to schedules
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var BASE_URL = '<?= base_url() ?>';
</script>

<style>
    :root {
        --paper-bg: #ffffff;
        --border-color: #2c3e50;
        --primary-accent: #3182ce;
        --exists-bg: #f0fff4;
        --exists-border: #9ae6b4;
        --new-bg: #e53e3e;
        --new-text: #ffffff;
    }

    body {
        background-color: #f4f7f6;
    }

    .italic { font-style: italic; }
    .x-small { font-size: 0.65rem; }
    .small { font-size: 0.8rem; }
    .bg-gray-50 { background-color: #f9fafb; }
    .bg-gray-100 { background-color: #f3f4f6; }
    .badge-success-soft { background-color: #e6fffa; color: #319795; }
    
    .paper-container {
        background-color: var(--paper-bg);
        border: 1px solid #ddd;
        border-radius: 4px;
        min-height: 800px;
    }

    .paper-header {
        border-bottom: 2px solid var(--border-color) !important;
    }

    .border-4 { border-width: 4px !important; }

    .schedule-table {
        border: 2px solid var(--border-color) !important;
    }

    .schedule-table th, .schedule-table td {
        border: 1px solid #e2e8f0 !important;
    }

    .role-badge {
        font-size: 0.6rem;
        background-color: #f7fafc;
        color: #4a5568;
        font-weight: 800;
        padding: 0.1rem 0.3rem;
        border: 1px solid #e2e8f0;
        text-transform: uppercase;
    }

    .staff-row:nth-child(even) {
        background-color: #fcfcfc;
    }

    /* Existence Highlighting */
    .unit-exists h3 {
        color: #276749 !important;
    }
    .unit-new h3 {
        color: #c53030 !important;
    }

    .staff-exists {
        background-color: var(--exists-bg) !important;
    }
    .staff-exists:hover {
        background-color: #e6fffa !important;
    }

    .staff-new {
        background-color: var(--new-bg) !important;
        color: var(--new-text) !important;
    }
    .staff-new .role-badge {
        background-color: rgba(255, 255, 255, 0.2);
        color: #fff;
        border-color: rgba(255, 255, 255, 0.3);
    }
    .staff-new:hover {
        background-color: #c53030 !important;
    }

    .remove-staff-row {
        font-size: 0.9rem;
        opacity: 0.3;
        transition: opacity 0.2s;
    }
    .remove-staff-row:hover {
        opacity: 1;
        text-decoration: none;
    }

    @media print {
        body { background: white; }
        .Profile-Bios { padding: 0; }
        .btn, .badge, footer, header { display: none !important; }
        .paper-container { box-shadow: none !important; border: none !important; }
    }
</style>