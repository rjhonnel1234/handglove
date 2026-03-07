<?php
    echo view('profile/includes/profile_banner');
?>
<div id="profile-bios">
    <div class="container">
        <div class="row">
            <div id="profile-main" class="col-lg-8 col-md-12 col-sm-12 col-12">

                <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="job-history-tab" data-toggle="tab" href="#job-history" role="tab" aria-controls="job-history" aria-selected="true">Job History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="shift-requests-tab" data-toggle="tab" href="#shift-requests" role="tab" aria-controls="shift-requests" aria-selected="false">Shift Requests</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pay-stub-tab" data-toggle="tab" href="#pay-stub" role="tab" aria-controls="pay-stub" aria-selected="false">Pay Stub</a>
                    </li>
                </ul>

                <div class="tab-content" id="profileTabsContent">
                    <div class="tab-pane fade show active" id="job-history" role="tabpanel" aria-labelledby="job-history-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading mb-4">
                                    <h2>Job History</h2>
                                </div>
                                
                                <div class="timeline-container">
                                    <?php if(!empty($job_history)){ ?>
                                        <div class="timeline">
                                            <?php foreach($job_history as $job){ ?>
                                                <div class="timeline-item">
                                                    <div class="timeline-date">
                                                        <div class="year"><?php echo date("M d, Y", strtotime($job['start_date'])); ?></div>
                                                    </div>
                                                    <div class="timeline-marker"></div>
                                                    <div class="timeline-content">
                                                        <h4 class="title"><?php echo $job['company_name']; ?></h4>
                                                        <div class="details">
                                                            <p><i class="far fa-building mr-1"></i> <?php echo $job['unit_name']; ?></p>
                                                            <p class="text-muted small"><i class="far fa-calendar-alt mr-1"></i> <?php echo date("M d, Y", strtotime($job['start_date'])); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="text-center py-5">
                                            <p class="text-muted">No completed jobs found.</p>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="shift-requests" role="tabpanel" aria-labelledby="shift-requests-tab">
                        <div class="row">
                            <div class="col-md-12">
                               <div class="heading">
                                    <h2>Shift Requests</h2>
                                </div>
                                <?php if (session()->getFlashdata('message') !== NULL) : ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?php echo session()->getFlashdata('message'); ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                <?php endif; ?>
                                <table id="requests_table" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 74%;">Details</th>
                                            <th style="width: 26%;text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($job_requests as $request){ ?>
                                            <tr>
                                                <td>
                                                    <div class="shift-view">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Date</label>
                                                                    <div class="form-details"><?php echo date("M d, Y", strtotime($request['start_date'])); ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="">Time</label>
                                                                    <div class="form-details"><?php echo date("h:i A", strtotime($request['shift_start_time'])) . ' - ' . date("h:i A", strtotime($request['shift_end_time'])); ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Unit</label>
                                                                    <div class="form-details"><?php echo $request['unit_name']; ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Census</label>
                                                                    <div class="form-details"><?php echo $request['unit_census']; ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Type</label>
                                                                    <div class="form-details"><?php echo $request['type_name']; ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                    
                                                <td>
                                                    <?php if($request['request_status'] == 10){ ?>
                                                        <a href="javascript:;" data-request-id="<?php echo $request['request_id']; ?>" data-shift-id="<?php echo $request['id']; ?>" data-client-id="<?php echo $request['client_id']; ?>" class="pl-3 pt-1 pb-1 pr-3 accept_request btn thm-btn">Accept</a>
                                                        <a href="javascript:;" data-request-id="<?php echo $request['request_id']; ?>" data-shift-id="<?php echo $request['id']; ?>" data-client-id="<?php echo $request['client_id']; ?>" class="pl-3 pt-1 pb-1 pr-3 decline_request btn btn-danger">Decline</a>
                                                    <?php }else{ ?>
                                                        <?php 
                                                        if($request['request_status'] == 20){
                                                            echo '<div class="alert alert-success text-center" role="alert">Accepted</div>';
                                                        }else if($request['request_status'] == 15){
                                                            echo '<div class="alert alert-secondary text-center" role="alert">Awaiting response</div>';
                                                        }else{
                                                            echo '<div class="alert alert-danger text-center" role="alert">Declined</div>';
                                                        }
                                                        ?>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pay-stub" role="tabpanel" aria-labelledby="pay-stub-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading mb-4 d-flex justify-content-between align-items-center">
                                    <h2>Earnings Statements</h2>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons" id="stubFilters">
                                        <label class="btn btn-outline-primary active">
                                            <input type="radio" name="options" id="filter-all" checked> All
                                        </label>
                                        <label class="btn btn-outline-primary">
                                            <input type="radio" name="options" id="filter-paid"> Paid
                                        </label>
                                        <label class="btn btn-outline-primary">
                                            <input type="radio" name="options" id="filter-pending"> Pending
                                        </label>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <div class="table-responsive">
                                            <table id="paystubs_table" class="table table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Pay Date</th>
                                                        <th>Period</th>
                                                        <th class="text-right">Gross</th>
                                                        <th class="text-right">Net Pay</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-right">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($pay_stubs as $stub): ?>
                                                    <tr class="stub-row" data-status="<?= $stub['status'] == 20 ? 'paid' : 'pending' ?>">
                                                        <td><strong><?= date('M d, Y', strtotime($stub['p_pay'])) ?></strong></td>
                                                        <td><span class="text-muted small"><?= date('M d', strtotime($stub['p_start'])) ?> - <?= date('M d, Y', strtotime($stub['p_end'])) ?></span></td>
                                                        <td class="text-right">$<?= number_format($stub['gross_pay'], 2) ?></td>
                                                        <td class="text-right font-weight-bold text-success">$<?= number_format($stub['net_pay'], 2) ?></td>
                                                        <td class="text-center">
                                                            <?php if($stub['status'] == 20): ?>
                                                                <span class="badge badge-success">Paid</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-warning">Pending</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-right text-nowrap">
                                                            <a href="<?= base_url('profile/view-stub/' . $stub['id']) ?>" class="btn btn-sm btn-outline-info" target="_blank">
                                                                <i class="fa fa-eye"></i> View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="profile-sidebar" class="col-lg-4 col-md-12 col-sm-12 col-12">
                <h3>Overview</h3>
                <div class="divider"></div>
                <div class="sidebar-list-job mt-10">
                    <!-- <ul>
                        <li>
                            <div class="sidebar-icon-item"><i class="fa fa-briefcase"></i></div>
                            <div class="sidebar-text-info">
                                <span class="text-description">Experience</span>
                                <strong class="small-heading">4 years</strong>
                            </div>
                        </li>
                        <li>
                            <div class="sidebar-icon-item"><i class="fa fa-map-marker-alt"></i></div>
                            <div class="sidebar-text-info">
                                <span class="text-description">From</span>
                                <strong class="small-heading">Dallas, Texas<br>Remote Friendly</strong>
                            </div>
                        </li>
                        <li>
                            <div class="sidebar-icon-item"><i class="fa fa-dollar-sign"></i></div>
                            <div class="sidebar-text-info">
                                <span class="text-description">Salary</span>
                                <strong class="small-heading">$35k - $45k</strong>
                            </div>
                        </li>
                        <li>
                            <div class="sidebar-icon-item"><i class="fa fa-clock"></i></div>
                            <div class="sidebar-text-info">
                                <span class="text-description">Member since</span>
                                <strong class="small-heading">Jul 2012</strong>
                            </div>
                        </li>
                    </ul> -->
                </div>
                <div class="divider"></div>
                <div class="buttons">
                    <button class="btn thm-btn-secondary" data-toggle="modal" data-target="#uploadModal">Credentials</button>
                    <button class="btn thm-btn-white" data-toggle="modal" data-target="#availabilityModal">Availability</button>
                </div>
                <div id="connector">
                    <h4>Referrals</h4>
                    <?php if(!empty($referrals)){ ?>
                        <ul id="connector_list" class="mb-3">
                            <?php foreach($referrals as $clinician){ ?>
                            <li>
                                <a href="<?php echo base_url('profile/'.$clinician['id']); ?>"  title="<?php echo $clinician['name']; ?>"><img src="<?php echo (!empty($clinician['profile_pic_url']) ? $clinician['profile_pic_url'] : base_url('assets/img/blank-img.png')); ?>" alt=""></a>
                            </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>

                    <a href="<?php echo base_url('employee-award'); ?>" class="btn thm-btn">Add a Referral</a>

                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="uploadModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="min-width: 70%;">
        <div class="modal-content">
        <div class="modal-header">
            <div id="modal-title">
                <strong>Credentials</strong>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-right">
                        <em>Max file size: 5mb<br>Allowed File types: PDF, JPG, PNG files</em>
                        </div>
                        <table style="border: 1px solid #a8a8a8;border-collapse: collapsed;width: 100%;" border="1" >
                            <?php foreach($credential_types as $credential_type){ ?>
                            <tr id="row<?php echo $credential_type->id; ?>" <?php echo (isset($clinician->credentials[$credential_type->id])) ? "style='background: #c1f0c1;'" : ""?>>
                                <th style="width: 150px;padding: 5px;"><?php echo $credential_type->name; ?></th>
                                <td style="padding: 5px;">
                                    <?php if(isset($profileData['credentials'][$credential_type->id])){ ?>
                                        <div id="fileUploaded<?php echo $credential_type->id; ?>">
                                            <a href="<?php echo base_url('profile/showCredential/' . $credential_type->id); ?>" target="_blank"><?php echo $profileData['credentials'][$credential_type->id]['filename']; ?></a>
                                        </div>
                                    <?php }else{ ?>
                                        <div id="fileName<?php echo $credential_type->id; ?>">
                                        Not uploaded yet...
                                        </div>
                                    <?php } ?>
                                    <form class="formCredential" id="formCredential<?php echo $credential_type->id; ?>"><input type="hidden" name="credential_id" value="<?php echo $credential_type->id; ?>"><input type="file" id="file_<?php echo $credential_type->id; ?>" name="file_<?php echo $credential_type->id; ?>"></form>
                                    <div class="progress" id="progress<?php echo $credential_type->id; ?>">
                                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 100%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td style="width: 200px;text-align: center;padding: 5px;">
                                    <div class="uploadFileUpload">
                                        <a href="javascript:;" class="btn btn-primary uploadFile" data-id="<?php echo $credential_type->id; ?>">Upload</a>

                                    </div>
                                    <div class="submitFileUpload">
                                        <a href="javascript:;" class="btn btn-warning submitFile" data-id="<?php echo $credential_type->id; ?>">Submit</a>
                                        <a href="javascript:;" class="btn btn-danger cancelFile" data-id="<?php echo $credential_type->id; ?>">Cancel</a>
                                    </div> 
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="availabilityModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px;">
        <div class="modal-content">
        <div class="modal-header">
            <div id="modal-title">
                <strong>Availability</strong>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        <div class="form-group">
                            <label for="">MON</label>
                            <select name="availability[mon][]" id="availability-monday" multiple data-header="Select availability" class="selectpicker form-control multiple">
                                <option value="I'm flexible">I'm flexible</option>
                                <option value="Morning">Morning</option>
                                <option value="Afternoon">Afternoon</option>
                                <option value="Night">Night</option>
                                <option value="Not Available">Not Available</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">TUE</label>
                            <select name="availability[tue][]" id="availability-tue" multiple data-header="Select availability" class="selectpicker form-control multiple">
                                <option value="I'm flexible">I'm flexible</option>
                                <option value="Morning">Morning</option>
                                <option value="Afternoon">Afternoon</option>
                                <option value="Night">Night</option>
                                <option value="Not Available">Not Available</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="">WED</label>
                            <select name="availability[wed][]" id="availability-wed" multiple data-header="Select availability" class="selectpicker form-control multiple">
                                <option value="I'm flexible">I'm flexible</option>
                                <option value="Morning">Morning</option>
                                <option value="Afternoon">Afternoon</option>
                                <option value="Night">Night</option>
                                <option value="Not Available">Not Available</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="">THU</label>
                            <select name="availability[thu][]" id="availability-thu" multiple data-header="Select availability" class="selectpicker form-control multiple">
                                <option value="I'm flexible">I'm flexible</option>
                                <option value="Morning">Morning</option>
                                <option value="Afternoon">Afternoon</option>
                                <option value="Night">Night</option>
                                <option value="Not Available">Not Available</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="">FRI</label>
                            <select name="availability[fri][]" id="availability-fri" multiple data-header="Select availability" class="selectpicker form-control multiple">
                                <option value="I'm flexible">I'm flexible</option>
                                <option value="Morning">Morning</option>
                                <option value="Afternoon">Afternoon</option>
                                <option value="Night">Night</option>
                                <option value="Not Available">Not Available</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="">SAT</label>
                            <select name="availability[sat][]" id="availability-sat" multiple data-header="Select availability" class="selectpicker form-control multiple">
                                <option value="I'm flexible">I'm flexible</option>
                                <option value="Morning">Morning</option>
                                <option value="Afternoon">Afternoon</option>
                                <option value="Night">Night</option>
                                <option value="Not Available">Not Available</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="">SUN</label>
                            <select name="availability[sun][]" id="availability-sun" multiple data-header="Select availability" class="selectpicker form-control multiple">
                                <option value="I'm flexible">I'm flexible</option>
                                <option value="Morning">Morning</option>
                                <option value="Afternoon">Afternoon</option>
                                <option value="Night">Night</option>
                                <option value="Not Available">Not Available</option>
                            </select>
                        </div>

                        <div class="buttons text-right mt-5">
                            <button id="submitAvailability" class="btn thm-btn">Submit Availability</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>