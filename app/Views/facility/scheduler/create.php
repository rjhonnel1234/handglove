<div id="profile-bios">
    <div class="container">
        <div class="row">
            <div id="profile-main" class="col-lg-12 col-md-12 col-sm-12 col-12">
                <div id="scheduler_form">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="mb-4">
                                <h2 class="mb-0">Manual Schedule - <?= date('F d, Y', strtotime($selectedDate)) ?></h2>
                            </div>
                            <input type="hidden" id="selected_date" value="<?= $selectedDate ?>">
                            <div id="units-schedule-container">
                                <?php foreach($units as $unit): ?>
                                    <div class="unit-schedule-section mb-5" data-unit-id="<?= $unit['id'] ?>">
                                        <div class="unit-header mb-3 border-bottom pb-2 d-flex align-items-center">
                                            <h3 class="mb-0"><i class="fa fa-hospital mr-2"></i> <?= $unit['name'] ?></h3>
                                        </div>

                                        <div class="row">
                                            <?php 
                                            $shifts = [
                                                'Day' => '07:00 AM - 03:00 PM',
                                                'Mid' => '03:00 PM - 11:00 PM',
                                                'Night' => '11:00 PM - 07:00 AM'
                                            ];
                                            foreach($shifts as $name => $time): 
                                            ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card shift-card shadow-sm border-light h-100">
                                                    <div class="card-header bg-white border-bottom-0 pb-0">
                                                        <div class="shift-header-content p-2 bg-light rounded">
                                                            <h6 class="mb-0 text-center"><?= $name ?> Shift</h6>
                                                            <small class="text-muted d-block text-center"><?= $time ?></small>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="staff-assignment-list" id="list-unit-<?= $unit['id'] ?>-<?= strtolower($name) ?>">
                                                            <div class="empty-shift-notice text-muted italic p-2Small">No staff assigned yet.</div>
                                                        </div>
                                                        <div class="mt-2 text-center">
                                                            <button class="btn btn-primary btn-xs add-staff-btn" 
                                                                    data-shift="<?= strtolower($name) ?>" 
                                                                    data-unit-id="<?= $unit['id'] ?>" 
                                                                    data-unit-name="<?= $unit['name'] ?>">
                                                                <i class="fa fa-plus"></i> Add
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <a href="<?= base_url('facility/') ?>" class="btn btn-warning mr-2">Back to Calendar</a>
                                <?php if (!empty($scheduleUpload)): ?>
                                    <a href="<?= base_url('facility/schedules/download/'.$scheduleUpload['id']) ?>" class="btn btn-info mr-2"><i class="fa fa-download mr-1"></i> Download PDF</a>
                                <?php endif; ?>
                                <button id="btnSaveScheduleAsShifts" class="btn btn-success ml-2 mr-2">Save as Shifts</button>
                                <button id="btnSaveSchedule" class="btn thm-btn">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickAddPersonnelModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Add Personnel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="quickAddPersonnelForm">
                    <input type="hidden" name="type" value="7">
                    <input type="hidden" name="status" value="1">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Clinician Type</label>
                        <select name="clinician_type" class="form-control selectpicker" required>
                            <?php foreach($clinicianTypes as $type): ?>
                                <option value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmQuickAdd">Save Personnel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addStaffModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Staff to <span id="modal-shift-name"></span> Shift</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Select Personnel</label>
                    <select id="staff_select" class="selectpicker form-control" data-live-search="true" data-size="5" multiple data-title="Select Staff">
                    </select>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-link text-primary p-0" id="btnShowQuickAdd"><i class="fa fa-user-plus"></i> Clinician not in list? Add here</button>
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="confirmAddStaff">Add Selected</button>
                </div>
            </div>
        </div>
    </div>
</div>