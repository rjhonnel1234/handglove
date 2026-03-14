<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="heading d-flex justify-content-between align-items-center mb-4">
            <h2>Schedules Calendar</h2>
            <div class="filter-wrapper" style="min-width: 300px;">
                <select id="facility-filter" class="form-control selectpicker" data-live-search="true" data-title="Select Facility">
                    <?php foreach ($facilities as $facility): ?>
                        <option value="<?= $facility['id'] ?>"><?= esc($facility['company_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="legend mb-4 d-flex align-items-center">
            <span class="mr-3 font-weight-bold">Legend:</span>
            <div class="d-flex align-items-center mr-4">
                <span style="display:inline-block; width:15px; height:15px; background:#faf089; border-radius:3px; margin-right:5px; border:1px solid #ecc94b;"></span>
                <span>Active Shift</span>
            </div>
            <div class="d-flex align-items-center">
                <span style="display:inline-block; width:15px; height:15px; background:#9ae6b4; border-radius:3px; margin-right:5px; border:1px solid #48bb78;"></span>
                <span>Call-out Replacement</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div id="admin-schedules-calendar"></div>
            </div>
        </div>
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
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    #admin-schedules-calendar {
        min-height: 700px;
        background: #fff;
    }
    .fc-event {
        cursor: pointer;
        padding: 2px 5px;
        border-radius: 4px;
        font-size: 0.85em;
    }
    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 700;
        color: #2d3748;
    }
    .fc-button-primary {
        background-color: #3182ce !important;
        border-color: #3182ce !important;
    }
    .fc-button-primary:hover {
        background-color: #2b6cb0 !important;
        border-color: #2b6cb0 !important;
    }
    .fc .fc-daygrid-day-frame {
        background: #f3efef;
    }
    .shift-detail-card {
        border-left: 4px solid #3182ce;
        transition: transform 0.2s;
    }
    .shift-detail-card:hover {
        transform: translateY(-2px);
    }
    .clinician-pill {
        font-size: 0.8rem;
        padding: 4px 10px;
        background: #ebf8ff;
        color: #2b6cb0;
        border-radius: 20px;
        border: 1px solid #bee3f8;
        display: inline-block;
        margin-right: 5px;
        margin-bottom: 5px;
    }
    .clinician-pill.external {
        background: #f0fff4;
        color: #2f855a;
        border-color: #c6f6d5;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 4px;
        text-transform: uppercase;
        font-weight: 700;
    }
    .scheduler-profile-img {
        width: 60px;
        height: 60px;
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
        font-size: 0.95rem;
    }
    .supervisor-info-col {
        min-width: 150px;
    }
    .supervisor-info-col.time { min-width: 100px; }
    .supervisor-info-col.phone { min-width: 150px; }
    .supervisor-info-col.email { min-width: 200px; }
</style>
<?php echo $this->endSection() ?>

<?php echo $this->section('customJS') ?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('admin-schedules-calendar');
        var facilityFilter = document.getElementById('facility-filter');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            fixedWeekCount: false,
            dayMaxEvents: true,
            themeSystem: 'bootstrap',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            events: {
                url: '<?= base_url('admin/schedules/list') ?>',
                method: 'POST',
                extraParams: function() {
                    return {
                        facility_id: facilityFilter.value
                    };
                },
                failure: function() {
                    alert('There was an error while fetching events!');
                }
            },
            eventContent: function (info) {
                console.info(info);
                return { html: info.event.extendedProps.html };
            },
            eventClick: function(info) {
                var date = info.event.extendedProps.date;
                if (!date) return;

                $('#modal-date-display').text('...');
                $('#modal-loading').show();
                $('#modal-content').hide();
                $('#shiftDetailsModal').modal('show');

                $.ajax({
                    url: '<?= base_url('admin/schedules/details') ?>',
                    method: 'POST',
                    data: {
                        date: date,
                        facility_id: facilityFilter.value
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
            },
            eventDidMount: function(info) {
                // Add tooltip or custom styling if needed
            }
        });

        calendar.render();

        facilityFilter.addEventListener('change', function() {
            calendar.refetchEvents();
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
                                <div class="mb-0"><span class="font-weight-bold">Phone:</span> &nbsp;&nbsp;&nbsp; ${s.contact_number || 'N/A'}</div>
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
                            <div class="supervisor-info-col phone text-muted">${sup.phone || ''}</div>
                            <div class="supervisor-info-col email text-muted">${sup.email || ''}</div>
                        </div>
                    `;
                });
                $('#supervisor-list').html(supHtml);
                $('#supervisor-section').show();
            } else {
                $('#supervisor-section').hide();
            }

            $('#modal-content').fadeIn();
            $('#modal-loading').hide();
        }
    });
</script>
<?php echo $this->endSection() ?>
