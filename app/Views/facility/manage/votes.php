<div id="facility-votes">
        
    <div class="card">
        <div class="card-body">
            <div class="heading">
                <h2>Voting</h2>
                <div class="buttons">
                    <a href="" class="btn thm-btn"  data-toggle="modal" data-target="#addUnitModal">Add Voting</a>
                </div>
            </div>

            <div id="formConfirmation"></div>
            <table id="units_table" class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 40%;">Date</th>
                        <th style="width: 20%;">Description</th>
                        <th style="width: 20%;text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="addUnitModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 900px;">
            <div class="modal-content">
                <div class="modal-header">
                    <div id="modal-title">
                        <strong>Add Voting</strong>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="unitAddForm">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="">Weekly Voting Schedule</label>
                                    <select name="voting_week" id="voting_week" class="form-control selectpicker" data-title="Select Week">
                                        <?php 
                                            $today = new DateTime();
                                            // Find the previous Monday (or today if it's Monday)
                                            if ($today->format('N') != 1) {
                                                $today->modify('last monday');
                                            }
                                            
                                            // Start from the current week and generate 12 weeks forward
                                            $start = clone $today;
                                            
                                            for($i = 0; $i < 12; $i++) {
                                                $weekStart = clone $start;
                                                $weekEnd = clone $start;
                                                $weekEnd->modify('+6 days');
                                                
                                                $value = $weekStart->format('Y-m-d') . '|' . $weekEnd->format('Y-m-d');
                                                $label = "Week of " . $weekStart->format('M d, Y') . " - " . $weekEnd->format('M d, Y');
                                                
                                                // $selected = ($i == 0) ? 'selected' : '';
                                                
                                                echo "<option value=\"$value\">$label</option>";
                                                $start->modify('+1 week');
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Voting Type</label>
                                    <select name="voting_type" id="voting_type" class="form-control selectpicker" data-title="Select Type">
                                        <option value="gna">GNA</option>
                                        <option value="nurse">Nurse</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Clinicians</label>
                                    <div id="clinician-list">
                                        <?php foreach($clinicians as $clinician){ ?>
                                        <div class="clincian-checkbox <?php echo $clinician['clinician_type_grouping']; ?>">
                                            <label for="clinician-<?php echo $clinician['id']; ?>" class="image-checkbox">
                                                <img class="img-responsive" src="<?php echo $clinician['profile_pic_url'] != '' ? $clinician['profile_pic_url'] : base_url('assets/img/blank-img.png'); ?>" />
                                                <input type="checkbox" name="clincian[]" id="clinician-<?php echo $clinician['id']; ?>" value="<?php echo $clinician['id']; ?>">
                                            </label>
                                            <div>
                                                <strong><?php echo $clinician['name']; ?></strong><br>
                                                <?php echo $clinician['clinician_type_name']; ?>
                                            </div>

                                        </div>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="buttons">
                        <a href="javascript:;" class="btn thm-btn" id="submitUnit">Submit</a>
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
                        <strong>Voting Details</strong>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="unitUpdateForm">
                        <input type="hidden" name="unitID" id="unitID">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Voting Duration</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="">Start</label>
                                            <div id="voting_start_date_label"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="">End</label>
                                            <div id="voting_end_date_label"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Voting Type</label>
                                    <div id="unitVotingType"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Description</label>
                                    <div id="unitDescription"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Clinicians</label>
                                    <div id="cliniciansVote" class="pl-4"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" id="award-footer" style="display: none;">
                    <button type="button" class="btn btn-primary" id="generateCertificate">Generate Certificate</button>
                </div>
            </div>
        </div>
    </div>
</div>