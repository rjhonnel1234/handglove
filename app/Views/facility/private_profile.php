<?php
    echo view('facility/includes/profile_banner');
?>

<div id="profile-bios">
    <div class="container">
        <div class="row">
            <div id="profile-main" class="col-lg-12 col-md-12 col-sm-12 col-12">
                <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="shifts-tab" data-toggle="tab" href="#shifts" role="tab" aria-controls="shifts" aria-selected="true">Shifts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="invoices-tab" data-toggle="tab" href="#invoices" role="tab" aria-controls="invoices" aria-selected="false">Invoices</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="time-logs-tab" data-toggle="tab" href="#time-logs" role="tab" aria-controls="time-logs" aria-selected="false">Time Logs</a>
                    </li>
                </ul>
                <div class="tab-content" id="profileTabsContent">
                    <div class="tab-pane fade show active" id="shifts" role="tabpanel" aria-labelledby="shifts-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading">
                                    <h2>Shifts</h2>
                                    <div class="buttons">
                                        <select id="shiftUnit" class="selectpicker">
                                            <?php foreach($units as $unit){ ?>
                                                <option value="<?php echo $unit['id']; ?>"><?php echo $unit['name']; ?></option>
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
                    <div class="tab-pane fade" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading d-flex justify-content-between align-items-center mb-4">
                                    <h2 class="mb-0">Invoices</h2>
                                    <div class="buttons d-flex align-items-center">
                                        <button id="paySelectedBtn" class="btn btn-success btn-sm mr-3" style="display: none;"><i class="fas fa-money-bill-wave"></i> Pay Selected</button>
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
                                                                <input type="checkbox" class="custom-control-input" id="checkAllInvoices">
                                                                <label class="custom-control-label" for="checkAllInvoices"></label>
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
                <div id="modal-title">
                    PCC Request
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        Are you sure you want to submit PCC for this shift?
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-right">
                            <a href="javascript:;" class="calloff-shift btn btn-success">Submit</a>
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
                    Call off Shift
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        Are you sure you want to call off this clinician for this shift?
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-right">
                            <a href="javascript:;" class="calloff-shift btn btn-warning">Call-off Shift</a>
                        </div>
                    </div>
                </div>
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