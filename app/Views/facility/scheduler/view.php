<div id="profile-bios">
    <div class="container">
        <div class="row">
            <div id="profile-main" class="col-lg-12 col-md-12 col-sm-12 col-12">
                <div id="scheduler_view">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="mb-4 d-flex justify-content-between align-items-center">
                                <h2 class="mb-0">Schedule Viewer - <?= date('F d, Y', strtotime($selectedDate)) ?></h2>
                                <div class="badge badge-success p-2">Manual Entry</div>
                            </div>
                            <input type="hidden" id="selected_date" value="<?= $selectedDate ?>">
                            
                            <div id="units-schedule-container">
                                <?php foreach($units as $unit): ?>
                                    <div class="unit-schedule-section mb-5" data-unit-id="<?= $unit['id'] ?>">
                                        <div class="unit-header mb-3 border-bottom pb-2">
                                            <h3 class="mb-0 text-primary"><i class="fa fa-hospital mr-2"></i> <?= $unit['name'] ?></h3>
                                        </div>

                                        <div class="row">
                                            <?php 
                                            $shifts = [
                                                'Day' => '07:00 AM - 03:00 PM',
                                                'Evening' => '03:00 PM - 11:00 PM',
                                                'Night' => '11:00 PM - 07:00 AM'
                                            ];
                                            foreach($shifts as $name => $time): 
                                            ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card shift-card shadow-sm border-light h-100">
                                                    <div class="card-header bg-light border-bottom-0 pb-0">
                                                        <div class="shift-header-content p-2">
                                                            <h6 class="mb-0 text-center font-weight-bold"><?= $name ?> Shift</h6>
                                                            <small class="text-muted d-block text-center"><?= $time ?></small>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="staff-assignment-list" id="list-unit-<?= $unit['id'] ?>-<?= strtolower($name) ?>">
                                                            <div class="empty-shift-notice text-muted italic p-2">No staff assigned.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-4">
                                <a href="<?= base_url('facility/') ?>" class="btn btn-warning"><i class="fa fa-arrow-left mr-1"></i> Back to Calendar</a>
                                <div>
                                    <?php if (!empty($scheduleUpload)): ?>
                                        <a href="<?= base_url('facility/schedules/download/'.$scheduleUpload['id']) ?>" class="btn btn-info mr-2"><i class="fa fa-download mr-1"></i> Download PDF</a>
                                    <?php endif; ?>
                                    <?php if($scheduleUpload['status'] == 10) { ?>
                                    <a href="<?= base_url('facility/schedules/add?date='.$selectedDate) ?>" class="btn btn-success"><i class="fa fa-edit mr-1"></i> Edit Schedule</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>