<?php
echo view('facility/includes/profile_banner');
?>
<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.3/dist/dotlottie-wc.js" type="module"></script>
<style>
    .clinician-item {
        display: flex;
        align-items: flex-start;
        padding: 15px;
        border-bottom: 1px solid #eee;
        transition: background 0.3s;
    }

    .clinician-item:hover {
        background: #f9f9f9;
        cursor: pointer;
    }

    .clinician-item.selected {
        border-color: #007bff;
        background-color: #e7f1ff;
        border-radius: 8px;
        margin-bottom: 5px;
    }

    .pending-replacement {
        border: 2px solid #ffc107 !important;
        position: relative;
        animation: pulse-yellow 2s infinite;
    }

    .replacement {
        border: 2px solid #28a745 !important;
        position: relative;
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-yellow {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
        }
    }

    @keyframes pulse-green {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }

    .clinician-item .profile-container {
        position: relative;
        width: 60px;
        margin-right: 15px;
        text-align: center;
    }

    .clinician-item .profile-container img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .clinician-item .role-badge {
        display: block;
        font-size: 10px;
        font-weight: bold;
        background: #4a5568;
        color: #fff;
        padding: 1px 4px;
        border-radius: 4px;
        margin-top: -10px;
        position: relative;
        z-index: 1;
        text-transform: uppercase;
    }

    .clinician-item .role-rn {
        background: #3182ce;
    }

    .clinician-item .role-lpn {
        background: #e53e3e;
    }

    .clinician-item .role-cna {
        background: #38a169;
    }

    .clinician-item .clinician-info {
        flex-grow: 1;
    }

    .clinician-item .clinician-name {
        font-weight: bold;
        color: #ed8936;
        font-size: 1.1rem;
        margin-bottom: 2px;
    }

    .clinician-item .clinician-company {
        font-size: 0.85rem;
        color: #718096;
    }

    .clinician-item .distance-info {
        text-align: right;
        font-size: 0.9rem;
        color: #4a5568;
        white-space: nowrap;
    }
</style>

<div id="profile-bios">
    <div class="container">
        <div class="row">
            <div id="profile-main" class="col-lg-12 col-md-12 col-sm-12 col-12">
                <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="shifts-tab" data-toggle="tab" href="#shifts" role="tab"
                            aria-controls="shifts" aria-selected="true">Shifts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="invoices-tab" data-toggle="tab" href="#invoices" role="tab"
                            aria-controls="invoices" aria-selected="false">Invoices</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="time-logs-tab" data-toggle="tab" href="#time-logs" role="tab"
                            aria-controls="time-logs" aria-selected="false">Time Logs</a>
                    </li>
                </ul>
                <div class="tab-content" id="profileTabsContent">
                    <div class="tab-pane fade show active" id="shifts" role="tabpanel" aria-labelledby="shifts-tab">
                        <div class="row">

                            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="heading">
                                            <h2>Shifts</h2>
                                            <div class="buttons">
                                                <select id="shiftUnit" class="selectpicker">
                                                    <?php foreach ($units as $unit) { ?>
                                                        <option value="<?php echo $unit['id']; ?>">
                                                        

                                                                                                                    <?php echo $unit['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div id="formConfirmation"></div>
                                        <div id="facilityShifts">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                <div id="callout-heatmap">
                                    <h4>Call out Heatmap</h4>
                                    <div class="heatmap-grid-container">
                                        <div class="heatmap-grid">
                                            <!-- Labels -->
                                            <div class="grid-label"></div>
                                            <div class="grid-day">Mon</div>
                                            <div class="grid-day">Tue</div>
                                            <div class="grid-day">Wed</div>
                                            <div class="grid-day">Thu</div>
                                            <div class="grid-day">Fri</div>
                                            <div class="grid-day">Sat</div>
                                            <div class="grid-day">Sun</div>

                                            <!-- 3am Row -->
                                            <div class="grid-time">3 am</div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-1"></div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-1"></div>

                                            <!-- 6am Row -->
                                            <div class="grid-time">6 am</div>
                                            <div class="grid-cell lvl-2">2x</div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-3">4x</div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-1"></div>
                                            <div class="grid-cell lvl-0"></div>

                                            <!-- 9am Row -->
                                            <div class="grid-time">9 am</div>
                                            <div class="grid-cell lvl-1"></div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-1"></div>
                                            <div class="grid-cell lvl-4">5x</div>
                                            <div class="grid-cell lvl-1"></div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-0"></div>

                                            <!-- 12pm Row -->
                                            <div class="grid-time">12 pm</div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-2">2x</div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-2">2x</div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-2">2x</div>
                                            <div class="grid-cell lvl-0"></div>

                                            <!-- 3pm Row -->
                                            <div class="grid-time">3 pm</div>
                                            <div class="grid-cell lvl-1"></div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-0"></div>
                                            <div class="grid-cell lvl-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading d-flex justify-content-between align-items-center mb-4">
                                    <h2 class="mb-0">Invoices</h2>
                                    <div class="buttons d-flex align-items-center">
                                        <button id="paySelectedBtn" class="btn btn-success btn-sm mr-3"
                                            style="display: none;"><i class="fas fa-money-bill-wave"></i> Pay
                                            Selected</button>
                                        <select id="invoiceStatus" class="selectpicker">
                                            <option value="">All Status</option>
                                            <option value="10">Unpaid</option>
                                            <option value="20">Paid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table id="invoices-table" class="table table-hover paystub-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="border-top-0 text-center pl-4">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="checkAllInvoices">
                                                                <label class="custom-control-label"
                                                                    for="checkAllInvoices"></label>
                                                            </div>
                                                        </th>
                                                        <th class="border-top-0">Clinician</th>
                                                        <th class="border-top-0 text-center">Hours</th>
                                                        <th class="border-top-0 text-right">Rate</th>
                                                        <th class="border-top-0 text-right">Total Amount</th>
                                                        <th class="border-top-0 text-center">Status</th>
                                                        <th class="border-top-0"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="time-logs" role="tabpanel" aria-labelledby="time-logs-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading d-flex justify-content-between align-items-center mb-4">
                                    <h2 class="mb-0">Time Logs</h2>
                                </div>
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table id="timekeepingTable" class="table table-hover paystub-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="border-top-0">Punch Datetime</th>
                                                        <th class="border-top-0">Clinician</th>
                                                        <th class="border-top-0">Shift Info</th>
                                                        <th class="border-top-0">Punch Type</th>
                                                        <th class="border-top-0 text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
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
        </div>
    </div>
</div>



<div class="modal fade" id="pccModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <div id="pcc-modal-title" class="font-weight-bold">
                    PCC Request
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="pcc-modal-body">
                        Are you sure you want to submit PCC for this shift?
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-right">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <a href="javascript:;" class="pcc-request-shift btn btn-success" id="btnPccSubmit">Submit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="calloffModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <div id="modal-title">
                    Call-out Shift
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="online-clinicians-loading">
                            <iframe src="https://lottie.host/embed/2d20c248-18ea-40e2-bd3e-8f9576168437/G1U5l3CjNo.json"
                                style="border: none;width: 100%;"></iframe>
                        </div>
                        <div id="online-clinicians-container" style="display: none;">
                            <!-- allow to select clinician, add Request Shift button and once click send shift request to selected clinician -->
                            <div id="onlineCliniciansList" class="text-left"
                                style="max-height: 400px; overflow-y: auto;">
                                <!-- Clinicians will be listed here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnRequestShift" disabled>Request Shift</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="dnrModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <div id="modal-title">
                    DNR Shift
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Reason</label>
                            <textarea name="dnr_message" id="dnr_message" class="form-control" rows="5"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-right">
                            <a href="javascript:;" class="dnr-shift btn btn-danger">Submit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="unitModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <div id="modal-title">
                    <strong>Request for Clinicians</strong>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="requestConfirmation"></div>
                <table id="clinicians_table" class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Clinician Details</th>
                            <th style="width: 15%;">Type</th>
                            <th style="width: 15%;">Level</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 15%;text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo view("facility/manage/_clinician_details_modal"); ?>