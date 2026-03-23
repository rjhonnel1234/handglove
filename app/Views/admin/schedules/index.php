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
                                <img src="<?= $facility['company_logo'] ?: base_url('assets/img/logo-placeholder.png') ?>" alt="Logo" class="facility-logo mb-2">
                                <h5 class="mb-1 font-weight-bold"><?= esc($facility['company_name']) ?></h5>
                            </div>
                            <div class="small text-muted mb-2">
                                <i class="fas fa-map-marker-alt mr-1 text-primary"></i> <?= esc($facility['company_address']) ?> <?= esc($facility['zip_code']) ?>
                            </div>
                            <div class="small text-muted">
                                <i class="fas fa-phone mr-1 text-primary"></i> <?= esc($facility['company_number']) ?>
                            </div>
                        </div>
                        <div class="col-md-9 p-4 bg-light">
                            <div class="schedule-grid">
                                <?php foreach ($dates as $index => $date): ?>
                                    <?php 
                                        $count = $shift_counts[$facility['id']][$date] ?? 0;
                                        $isToday = $date == date('Y-m-d');
                                        $isWeekend = in_array(date('N', strtotime($date)), [6, 7]);
                                    ?>
                                    <?php if ($index == 13): // 14th slot replaced by "More" ?>
                                        <a href="#" class="day-slot more-slot text-decoration-none d-flex flex-column align-items-center justify-content-center" data-facility-id="<?= $facility['id'] ?>">
                                            <span class="font-weight-bold h5 mb-0">More</span>
                                        </a>
                                    <?php else: ?>
                                        <div class="day-slot <?= $count > 0 ? 'has-shifts' : '' ?> <?= $isToday ? 'is-today' : '' ?> <?= $isWeekend && $count == 0 ? 'is-weekend' : '' ?>" 
                                             data-date="<?= $date ?>" 
                                             data-facility-id="<?= $facility['id'] ?>">
                                            <div class="day-name"><?= date('D', strtotime($date)) ?></div>
                                            <div class="date-label"><?= date('M d', strtotime($date)) ?></div>
                                            <div class="shift-count"><?= $count > 0 ? $count . ' appts' : 'No appts' ?></div>
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
<div class="modal fade" id="shiftDetailsModal" tabindex="-1" role="dialog" aria-labelledby="shiftDetailsModalLabel" aria-hidden="true">
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
                        <h6 class="font-weight-bold text-uppercase text-muted mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Supervisor:</h6>
                        <div id="supervisor-list" class="mb-4">
                            <!-- Supervisors will be injected here -->
                        </div>
                    </div>
                    <div id="shifts-list-container" class="px-4 pb-4">
                        <!-- Shifts list will be injected here -->
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
    .day-slot.has-shifts {
        background: #fff9c4; /* Match mockup yellow */
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
    .has-shifts .shift-count {
        color: #b7791f;
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
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        margin-bottom: 10px;
        padding: 15px;
        border-radius: 0 8px 8px 0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
</style>
<?php echo $this->endSection() ?>

<?php echo $this->section('customJS') ?>
<script>
    $(document).ready(function() {
        $('.day-slot:not(.more-slot)').on('click', function() {
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
                success: function(res) {
                    if (res.success) {
                        $('#modal-date-display').text(res.date);
                        renderShiftDetails(res);
                    } else {
                        alert(res.message || 'Failed to fetch details');
                        $('#shiftDetailsModal').modal('hide');
                    }
                },
                error: function() {
                    alert('Network error while fetching details');
                    $('#shiftDetailsModal').modal('hide');
                }
            });
        });

        $('.more-slot').on('click', function(e) {
            e.preventDefault();
            // Optional: Redirect to a full calendar for this facility
            var facilityId = $(this).data('facility-id');
            alert('Opening full calendar for facility #' + facilityId);
        });

        function renderShiftDetails(data) {
            var shifts = data.shifts;
            var schedulers = data.schedulers;
            var supervisors = data.supervisors;

            // Render Schedulers
            var schedHtml = '';
            if (schedulers && schedulers.length > 0) {
                schedulers.forEach(function(s) {
                    schedHtml += `
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?= base_url('assets/img/blank-img.png') ?>" class="scheduler-profile-img mr-3" alt="Scheduler">
                            <div>
                                <div class="mb-0"><span class="font-weight-bold">Scheduler:</span> ${s.first_name} ${s.last_name}</div>
                                <div class="mb-0 small"><span class="font-weight-bold">Phone:</span> ${s.contact_number || 'N/A'}</div>
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
                supervisors.forEach(function(sup) {
                    supHtml += `
                        <div class="supervisor-item">
                            <div class="supervisor-info-col font-weight-bold text-dark">${sup.name} ${sup.time ? `<span class="font-weight-normal text-muted ml-1">${sup.time}</span>` : ''}</div>
                            <div class="supervisor-info-col text-muted">${sup.phone || ''}</div>
                            <div class="supervisor-info-col text-muted">${sup.email || ''}</div>
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
                shifts.forEach(function(shift) {
                    var cliniciansHtml = '';
                    if (shift.clinicians && shift.clinicians.length > 0) {
                        shift.clinicians.forEach(function(c) {
                            cliniciansHtml += `<span class="badge badge-info mr-1">${c.display_name}</span>`;
                        });
                    } else {
                        cliniciansHtml = '<span class="text-muted small">No clinicians assigned</span>';
                    }

                    shiftsHtml += `
                        <div class="shift-detail-card">
                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="font-weight-bold mb-0">${shift.unit_name || 'General Unit'}</h6>
                                <span class="badge badge-primary">${shift.shift_start_time} - ${shift.shift_end_time}</span>
                            </div>
                            <div class="clinicians-assigned">
                                ${cliniciansHtml}
                            </div>
                        </div>
                    `;
                });
                $('#shifts-list-container').html(shiftsHtml);
            } else {
                $('#shifts-list-container').html('<div class="text-center p-3 text-muted">No shifts found for this date.</div>');
            }

            $('#modal-content').fadeIn();
            $('#modal-loading').hide();
        }
    });
</script>
<?php echo $this->endSection() ?>
