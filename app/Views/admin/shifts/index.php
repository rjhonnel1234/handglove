<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="heading d-flex justify-content-between align-items-center mb-4">
            <h2>Clinician Shifts</h2>
        </div>
        
        <div class="legend mb-4 d-flex align-items-center flex-wrap">
            <span class="mr-3 font-weight-bold">Legend:</span>
            <div class="d-flex align-items-center mr-4 mb-2">
                <span style="display:inline-block; width:15px; height:15px; background:#48bb78; border-radius:3px; margin-right:5px;"></span>
                <span>Completed</span>
            </div>
            <div class="d-flex align-items-center mr-4 mb-2">
                <span style="display:inline-block; width:15px; height:15px; background:#ecc94b; border-radius:3px; margin-right:5px;"></span>
                <span>Clocked In</span>
            </div>
            <div class="d-flex align-items-center mr-4 mb-2">
                <span style="display:inline-block; width:15px; height:15px; background:#4299e1; border-radius:3px; margin-right:5px;"></span>
                <span>Overtime</span>
            </div>
            <div class="d-flex align-items-center mr-4 mb-2">
                <span style="display:inline-block; width:15px; height:15px; background:#f56565; border-radius:3px; margin-right:5px;"></span>
                <span>Late</span>
            </div>
            <div class="d-flex align-items-center mb-2">
                <span style="display:inline-block; width:15px; height:15px; background:#a0aec0; border-radius:3px; margin-right:5px;"></span>
                <span>No Logs</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div id="admin-shifts-calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Shift Quick Details Modal -->
<div class="modal fade" id="shiftQuickDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Shift Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="text-muted small text-uppercase font-weight-bold mb-1 d-block">Clinician</label>
                    <div id="modal-clinician-name" class="h5 mb-0"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="text-muted small text-uppercase font-weight-bold mb-1 d-block">Facility</label>
                        <div id="modal-facility-name" class="font-weight-bold"></div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small text-uppercase font-weight-bold mb-1 d-block">Unit</label>
                        <div id="modal-unit-name" class="font-weight-bold"></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="text-muted small text-uppercase font-weight-bold mb-1 d-block">Shift Type</label>
                        <div id="modal-shift-type" class="font-weight-bold"></div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small text-uppercase font-weight-bold mb-1 d-block">Status</label>
                        <div id="modal-status-label" class="badge"></div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6 text-center">
                        <label class="text-muted small text-uppercase font-weight-bold mb-1 d-block">Clock In</label>
                        <div id="modal-clock-in" class="h6 mb-0 text-success">--:--</div>
                    </div>
                    <div class="col-6 text-center">
                        <label class="text-muted small text-uppercase font-weight-bold mb-1 d-block">Clock Out</label>
                        <div id="modal-clock-out" class="h6 mb-0 text-danger">--:--</div>
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
    #admin-shifts-calendar {
        min-height: 800px;
        background: #fff;
    }
    .fc-event {
        cursor: pointer;
        padding: 5px 8px;
        border-radius: 6px;
        font-size: 0.85em;
        margin-bottom: 2px !important;
        border: none !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 700;
        color: #2d3748;
    }
    .fc-button-primary {
        background-color: #3182ce !important;
        border-color: #3182ce !important;
        box-shadow: none !important;
    }
    .fc-button-primary:hover {
        background-color: #2b6cb0 !important;
        border-color: #2b6cb0 !important;
    }
    .fc-button-active {
        background-color: #2c5282 !important;
        border-color: #2c5282 !important;
    }
    .fc .fc-daygrid-day-frame {
        background: #fafafa;
    }
    .fc-day-today {
        background: #f0f7ff !important;
    }
</style>
<?php echo $this->endSection() ?>

<?php echo $this->section('customJS') ?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.10/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('admin-shifts-calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'resourceTimelineWeek',
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            themeSystem: 'bootstrap',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'resourceTimelineDay,resourceTimelineWeek'
            },
            views: {
                resourceTimelineWeek: {
                    buttonText: 'Weekly',
                    slotDuration: { days: 1 },
                    slotLabelFormat: [
                        { weekday: 'short', day: 'numeric', month: 'short' }
                    ]
                },
                resourceTimelineDay: {
                    buttonText: 'Daily',
                    slotDuration: '01:00:00',
                    slotLabelFormat: [
                        { hour: 'numeric', minute: '2-digit', omitZeroMinute: true, meridian: 'short' }
                    ]
                },
                resourceTimelineMonth: {
                    buttonText: 'Monthly'
                }
            },
            resourceAreaWidth: '20%',
            resourceAreaHeaderContent: 'Clinicians',
            resources: '<?= base_url('admin/shifts/resources') ?>',
            events: {
                url: '<?= base_url('admin/shifts/list') ?>',
                method: 'POST',
                failure: function() {
                    alert('There was an error while fetching shifts!');
                }
            },
            eventClick: function(info) {
                var props = info.event.extendedProps;
                
                $('#modal-clinician-name').text(props.clinician_name);
                $('#modal-facility-name').text(props.facility_name);
                $('#modal-unit-name').text(props.unit_name);
                $('#modal-shift-type').text(props.shift_type);
                $('#modal-status-label').text(props.status_label).css('background-color', info.event.backgroundColor).css('color', '#fff');
                $('#modal-clock-in').text(props.clock_in || '--:--');
                $('#modal-clock-out').text(props.clock_out || '--:--');
                
                $('#shiftQuickDetailsModal').modal('show');
            },
            eventDidMount: function(info) {
                // Add tooltip or custom styling if needed
            }
        });

        calendar.render();
    });
</script>
<?php echo $this->endSection() ?>
