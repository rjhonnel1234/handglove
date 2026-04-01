<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="heading d-flex justify-content-between align-items-center mb-4">
            <h2>Schedules Management</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <?php if (!empty($facilities)): ?>
            <?php foreach ($facilities as $facility): ?>
                <div class="facility-schedule-card mb-4 shadow-sm">
                    <div class="row no-gutters">
                        <div class="col-md-3 facility-info p-4 d-flex flex-column justify-content-center border-right">
                            <div class="text-center mb-3">
                                <img src="<?= $facility['company_logo'] ?: base_url('assets/img/logo-placeholder.png') ?>"
                                    alt="Logo" class="facility-logo mb-2">
                                <h5 class="mb-1 font-weight-bold"><?= esc($facility['company_name']) ?></h5>
                            </div>
                            <div class="small text-muted mb-2">
                                <i class="fas fa-map-marker-alt mr-1 text-primary"></i> <?= esc($facility['company_address']) ?>
                                <?= esc($facility['zip_code']) ?>
                            </div>
                            <div class="small text-muted">
                                <i class="fas fa-phone mr-1 text-primary"></i> <?= esc($facility['company_number']) ?>
                            </div>
                        </div>
                        <div class="col-md-9 p-4 bg-light">
                            <div class="schedule-grid">
                                <?php foreach ($dates as $index => $date): ?>
                                    <?php
                                    $shiftData = $shift_counts[$facility['id']][$date] ?? ['count' => 0, 'posted' => 0];
                                    $hasShifts = $shiftData['count'] > 0;
                                    $hasPosted = $shiftData['posted'] > 0;

                                    $statusClass = '';
                                    if ($hasPosted) {
                                        $statusClass = 'has-posted-shifts';
                                    } elseif ($hasShifts) {
                                        $statusClass = 'has-unposted-shifts';
                                    }

                                    $isToday = $date == date('Y-m-d');
                                    $isWeekend = in_array(date('N', strtotime($date)), [6, 7]);
                                    ?>
                                    <?php if ($index == 13): // 14th slot replaced by "More" ?>
                                        <a href="#"
                                            class="day-slot more-slot text-decoration-none d-flex flex-column align-items-center justify-content-center"
                                            data-facility-id="<?= $facility['id'] ?>">
                                            <span class="font-weight-bold h5 mb-0">More</span>
                                        </a>
                                    <?php else: ?>
                                        <div class="day-slot <?= $statusClass ?> <?= $isToday ? 'is-today' : '' ?> <?= $isWeekend && !$hasShifts ? 'is-weekend' : '' ?>"
                                            data-date="<?= $date ?>" data-facility-id="<?= $facility['id'] ?>">
                                            <div class="day-name"><?= date('D', strtotime($date)) ?></div>
                                            <div class="date-label"><?= date('M d', strtotime($date)) ?></div>
                                            <div class="shift-count"></div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="pagination-wrapper mt-4">
                <?= $pager->links('default', 'admin_full') ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info py-4 text-center shadow-sm">
                <i class="fas fa-info-circle mr-2"></i> No facilities found.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Shift Details Modal -->
<div class="modal fade" id="shiftDetailsModal" tabindex="-1" role="dialog" aria-labelledby="shiftDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 700px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title" id="shiftDetailsModalLabel">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Shift Details - <span id="modal-date-display"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="modal-details-body">
                <div class="text-center p-5" id="modal-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Fetching operational details...</p>
                </div>
                <div id="modal-content" style="display:none;">
                    <div class="p-4 bg-light border-bottom mb-4" id="scheduler-header-wrapper">
                        <!-- Schedulers will be injected here -->
                    </div>
                    <div class="px-4 pb-2" id="supervisor-section" style="display:none;">
                        <h6 class="font-weight-bold text-uppercase text-muted mb-3"
                            style="font-size: 0.8rem; letter-spacing: 1px;">Supervisor:</h6>
                        <div id="supervisor-list" class="mb-4">
                            <!-- Supervisors will be injected here -->
                        </div>
                    </div>
                    <div id="shifts-list-container" class="px-4 pb-4">
                        <!-- Shifts list will be injected here -->
                    </div>
                </div>
                <!-- Assignment Interface (Hidden by default) -->
                <div id="assignment-interface" style="display:none;" class="p-4">
                    <div class="d-flex align-items-center mb-4">
                        <button class="btn btn-sm btn-light mr-3" id="back-to-details"><i
                                class="fas fa-arrow-left"></i></button>
                        <h6 class="mb-0 font-weight-bold">Assign Clinician to <span id="assign-unit-name"></span> (<span
                                id="assign-shift-time"></span>)</h6>
                    </div>
                    <div class="form-group mb-4">
                        <input type="text" class="form-control" id="clinician-search"
                            placeholder="Search clinician by name...">
                    </div>
                    <div id="available-clinicians-list" class="list-group shadow-sm">
                        <!-- Clinician options will be injected here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .facility-schedule-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .facility-logo {
        max-width: 100%;
        max-height: 80px;
        object-fit: contain;
    }

    .schedule-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 12px;
    }

    .day-slot {
        background: #fff;
        border-radius: 6px;
        padding: 10px 5px;
        text-align: center;
        transition: all 0.2s ease;
        cursor: pointer;
        min-height: 90px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border: 1px solid #edf2f7;
    }

    .day-slot:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e0;
    }

    .day-slot.has-posted-shifts {
        background: #c6f6d5;
        border-color: #38a169;
    }

    .day-slot.has-unposted-shifts {
        background: #fff9c4;
        border-color: #fdd835;
    }

    .day-slot.is-today {
        border: 2px solid #4299e1;
    }

    .day-slot.is-weekend {
        background: #f8fafc;
        color: #94a3b8;
    }

    .day-slot.is-weekend .date-label {
        color: #94a3b8;
    }

    .day-name {
        font-size: 0.7rem;
        font-weight: 600;
        color: #718096;
        text-transform: capitalize;
        margin-bottom: 2px;
    }

    .date-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .shift-count {
        font-size: 0.75rem;
        font-weight: 600;
        color: #718096;
    }

    .has-unposted-shifts .shift-count {
        color: #b7791f;
    }

    .has-posted-shifts .shift-count {
        color: #2f855a;
    }

    .more-slot {
        border: 2px solid #2d3748;
        color: #2d3748;
    }

    .more-slot:hover {
        background: #f7fafc;
        color: #1a202c;
    }

    .scheduler-profile-img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .supervisor-item {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        margin-bottom: 0.75rem;
        font-size: 0.9rem;
    }

    .supervisor-info-col {
        min-width: 140px;
    }

    .shift-detail-card {
        border-left: 4px solid #3182ce;
        background: #fff;
        margin-bottom: 15px;
        padding: 15px;
        border-radius: 0 8px 8px 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .available-clinician-item {
        cursor: pointer;
        transition: background 0.2s;
    }

    .available-clinician-item:hover {
        background: #f0fff4 !important;
    }
</style>
<?php echo $this->endSection() ?>

<?php echo $this->section('customJS') ?>
<script>
    $(document).ready(function () {
        $('.day-slot:not(.more-slot)').on('click', function () {
            var date = $(this).data('date');
            var facilityId = $(this).data('facility-id');

            if (!date || !facilityId) return;

            $('#modal-date-display').text('...');
            $('#modal-loading').show();
            $('#modal-content').hide();
            $('#shiftDetailsModal').modal('show');

            $.ajax({
                url: '<?= base_url('admin/schedules/details') ?>',
                method: 'POST',
                data: {
                    date: date,
                    facility_id: facilityId
                },
                success: function (res) {
                    if (res.success) {
                        $('#modal-date-display').text(res.date);
                        renderShiftDetails(res);
                    } else {
                        alert(res.message || 'Failed to fetch details');
                        $('#shiftDetailsModal').modal('hide');
                    }
                },
                error: function () {
                    alert('Network error while fetching details');
                    $('#shiftDetailsModal').modal('hide');
                }
            });
        });

        $('.more-slot').on('click', function (e) {
            e.preventDefault();
            // Optional: Redirect to a full calendar for this facility
            var facilityId = $(this).data('facility-id');
            alert('Opening full calendar for facility #' + facilityId);
        });

        function renderShiftDetails(data) {
            var shifts = data.shifts;
            var schedulers = data.schedulers;
            var supervisors = data.supervisors;
            var currentDate = data.date;

            // Render Schedulers
            var schedHtml = '';
            if (schedulers && schedulers.length > 0) {
                schedulers.forEach(function (s) {
                    schedHtml += `
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?= base_url('assets/img/blank-img.png') ?>" class="scheduler-profile-img mr-3" alt="Scheduler">
                            <div>
                                <div class="mb-1 d-flex align-items-center">
                                    <span class="badge badge-soft-primary mr-2 px-2 py-1" style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.5px;">Scheduler</span>
                                    <span class="font-weight-bold text-dark">${s.first_name} ${s.last_name}</span>
                                </div>
                                <div class="mb-0 small text-muted"><i class="fas fa-phone mr-1"></i> ${s.contact_number || 'N/A'}</div>
                            </div>
                        </div>
                    `;
                });
                $('#scheduler-header-wrapper').html(schedHtml).show();
            } else {
                $('#scheduler-header-wrapper').hide();
            }

            // Render Supervisors
            var supHtml = '';
            if (supervisors && supervisors.length > 0) {
                supervisors.forEach(function (sup) {
                    supHtml += `
                        <div class="supervisor-item">
                            <div class="supervisor-info-col font-weight-bold text-dark">
                                <i class="fas fa-user-tie mr-2 text-muted" style="width: 15px;"></i> ${sup.name} 
                                ${sup.time ? `<span class="badge badge-light font-weight-normal border ml-2">${sup.time}</span>` : ''}
                            </div>
                            <div class="supervisor-info-col text-muted small"><i class="fas fa-phone mr-1"></i> ${sup.phone || ''}</div>
                            <div class="supervisor-info-col text-muted small"><i class="fas fa-envelope mr-1"></i> ${sup.email || ''}</div>
                        </div>
                    `;
                });
                $('#supervisor-list').html(supHtml);
                $('#supervisor-section').show();
            } else {
                $('#supervisor-section').hide();
            }

            // Render Shifts
            var shiftsHtml = '';
            if (shifts && shifts.length > 0) {
                shifts.forEach(function (shift) {
                    var totalAssigned = (shift.clinicians ? shift.clinicians.length : 0);
                    var remainingSlots = shift.slots - totalAssigned;
                    var isFull = remainingSlots <= 0;
                    var isPosted = shift.posted == 1;

                    var cliniciansHtml = '';
                    if (shift.clinicians && shift.clinicians.length > 0) {
                        shift.clinicians.forEach(function (c) {
                            cliniciansHtml += `
                                <div class="p-2 border-bottom d-flex align-items-center justify-content-between" ${c.is_external ? 'style="background-color: #f0fff4;"' : ''}>
                                    <span class="text-dark"><i class="fas fa-user-check text-success mr-2"></i> ${c.display_name}</span>
                                    <span class="badge ${c.is_external ? 'badge-success text-white' : 'badge-light'} px-2 py-1" style="font-size: 0.65rem; text-transform: uppercase;">${c.is_external ? 'Clinician' : 'Internal'}</span>
                                </div>`;
                        });
                    }

                    var statusBadge = isPosted ? '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Posted</span>' : '<span class="badge badge-warning px-2 py-1"><i class="fas fa-pencil-alt mr-1"></i> Draft</span>';
                    var fillPercent = (totalAssigned / shift.slots) * 100;
                    var progressBarColor = isFull ? 'bg-success' : (totalAssigned > 0 ? 'bg-warning' : 'bg-secondary opacity-25');

                    var assignBtn = '';
                    if (!isFull) {
                        assignBtn = `<button class="btn btn-block btn-sm btn-outline-primary assign-clinician-trigger mt-2" data-shift-id="${shift.id}" data-unit-name="${shift.unit_name || 'General Unit'}" data-shift-time="${shift.shift_start_time} - ${shift.shift_end_time}">
                            <i class="fas fa-plus-circle mr-1"></i> Assign Clinician
                        </button>`;
                    }

                    shiftsHtml += `
                        <div class="shift-detail-card-enhanced mb-4 shadow-sm border-0">
                            <div class="p-3 bg-white" style="border-radius: 12px; border: 1px solid #eef2f7;">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <div class="text-uppercase text-muted small font-weight-bold mb-1" style="letter-spacing: 1px; font-size: 0.65rem;">
                                            <i class="fas fa-hospital-alt mr-1"></i> ${shift.unit_name || 'General Unit'} <span class="ml-2 text-primary">Shift ID: #${shift.id}</span>
                                        </div>
                                        <h5 class="font-weight-bold mb-0 text-dark">
                                            <span class="badge badge-primary mr-2">${shift.shift_type_name || 'Shift'}</span>
                                            ${shift.shift_start_time} - ${shift.shift_end_time}
                                        </h5>
                                    </div>
                                    <div class="text-right">
                                        ${statusBadge}
                                        <div class="small mt-1 text-muted font-weight-bold">${totalAssigned}/${shift.slots} Slots</div>
                                    </div>
                                </div>
                                
                                <div class="progress mb-3" style="height: 6px; border-radius: 10px; background-color: #f1f5f9;">
                                    <div class="progress-bar ${progressBarColor}" role="progressbar" style="width: ${fillPercent}%; border-radius: 10px;"></div>
                                </div>

                                <div class="clinicians-area py-1">
                                    ${cliniciansHtml || '<div class="text-muted small italic px-1"><i class="fas fa-info-circle mr-1"></i> No personnel assigned to this shift yet.</div>'}
                                </div>
                                
                                ${assignBtn}
                            </div>
                        </div>
                    `;
                });
                $('#shifts-list-container').html(shiftsHtml);
            } else {
                $('#shifts-list-container').html('<div class="text-center p-5 text-muted"><i class="fas fa-calendar-times mb-3 opacity-25" style="font-size: 3rem;"></i><p>No operational shifts or plans found for this selected date.</p></div>');
            }

            $('#modal-content').fadeIn();
            $('#modal-loading').hide();
            $('#assignment-interface').hide();
        }

        var activeShiftId = null;

        // Trigger Assignment Interface
        $(document).on('click', '.assign-clinician-trigger', function () {
            activeShiftId = $(this).data('shift-id');
            $('#assign-unit-name').text($(this).data('unit-name'));
            $('#assign-shift-time').text($(this).data('shift-time'));

            $('#modal-content').hide();
            $('#assignment-interface').show();
            $('#available-clinicians-list').html('<div class="text-center p-4"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ml-2">Searching available clinicians...</span></div>');

            $.post('<?= base_url('admin/schedules/get-available-clinicians') ?>', { shift_id: activeShiftId }, function (res) {
                if (res.success) {
                    renderAvailableClinicians(res.clinicians);
                } else {
                    alert(res.message);
                    $('#back-to-details').trigger('click');
                }
            });
        });

        $('#back-to-details').on('click', function () {
            $('#assignment-interface').hide();
            $('#modal-content').fadeIn();
        });

        function renderAvailableClinicians(clinicians) {
            var html = '';
            if (clinicians && clinicians.length > 0) {
                clinicians.forEach(function (c) {
                    html += `
                        <a href="javascript:void(0)" class="list-group-item list-group-item-action available-clinician-item" data-clinician-id="${c.id}">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold">${c.name}</span>
                                <span class="badge badge-light">Assign <i class="fas fa-chevron-right ml-1"></i></span>
                            </div>
                        </a>
                    `;
                });
            } else {
                html = '<div class="alert alert-warning m-0">No available clinicians found for this shift type.</div>';
            }
            $('#available-clinicians-list').html(html);
        }

        // Perform Assignment
        $(document).on('click', '.available-clinician-item', function () {
            var clinicianId = $(this).data('clinician-id');
            var $item = $(this);

            if (!confirm('Are you sure you want to assign this clinician?')) return;

            $item.addClass('disabled').html('<div class="text-center py-1"><i class="fas fa-spinner fa-spin"></i> Assigning...</div>');

            $.post('<?= base_url('admin/schedules/assign-clinician') ?>', {
                shift_id: activeShiftId,
                clinician_id: clinicianId
            }, function (res) {
                if (res.success) {
                    // Success! Refresh details
                    var facilityId = $('.day-slot.is-today').data('facility-id') || $('[data-date="' + $('#modal-date-display').text() + '"]').data('facility-id');
                    // Actually we have the original trigger date in the modal
                    // We need to re-trigger the details AJAX or simplify
                    $('#shiftDetailsModal').modal('hide');
                    alert('Clinician assigned successfully.');
                    location.reload(); // Simplest for now to refresh grid counts too
                } else {
                    alert(res.message);
                    $item.removeClass('disabled');
                }
            });
        });

        // Search filtering
        $('#clinician-search').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $("#available-clinicians-list .available-clinician-item").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
<?php echo $this->endSection() ?>