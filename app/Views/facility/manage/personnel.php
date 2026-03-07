<div class="card">
    <div class="card-body">
        <div class="heading">
            <h2>Personnel</h2>
            <div class="buttons">
                <a href="" class="btn thm-btn" data-toggle="modal" data-target="#addPersonnelModal">Add Personnel</a>
            </div>
        </div>

        <table id="personnel_table" class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 25%;">Name</th>
                    <th style="width: 20%;">Email</th>
                    <th style="width: 15%;">Contact</th>
                    <th style="width: 15%;">Type</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 15%;text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addPersonnelModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <div id="modal-title">
                    <strong>Add Personnel</strong>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="validationErrors"></div>
                <form action="" id="personnelAddForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Type</label>
                                <select name="type" id="createPersonnelType" class="selectpicker form-control  " data-title="Select Type" required>
                                    <?php foreach($userTypes as $type): ?>
                                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div id="add-clinician-type-row" class="clinician-type-row" style="display: none;">
                                <div class="form-group">
                                    <label for="">Clinician Type</label>
                                    <select name="clinician_type" id="createPersonnelClinicianType" class="selectpicker form-control" data-title="Select Clinician Type">
                                        <?php foreach($clinicianTypes as $type): ?>
                                            <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">First Name</label>
                                <input type="text" class="form-control" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Last Name</label>
                                <input type="text" class="form-control" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="add-password-row" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Password</label>
                                <input type="password" class="form-control" name="password" id="addPassword" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Contact Number</label>
                                <input type="text" class="form-control" name="contact_number">
                            </div>
                        </div>
                    </div>
                    <div class="row" id="add-signature-row" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group mt-3">
                                <label for="">Signature</label>
                                <div id="add-signature-pad" class="signature-pad text-center">
                                    <div class="signature-pad--body" style="text-align: center;">
                                        <canvas style="border: 1px solid #ccc;"></canvas>
                                    </div>
                                    <div class="buttons text-center mt-2">
                                        <button type="button" class="btn btn-black btn-clear" data-action="clear">Clear</button>
                                        <button type="button" class="btn btn-black btn-undo" data-action="undo">Undo</button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label for="">Or Upload Signature</label>
                                    <input type="file" class="form-control signature-upload" accept="image/*">
                                </div>
                                <input type="hidden" name="signature" id="add-signature-input">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select name="status" class="selectpicker form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="add-clinician-profile-row" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="create_clinician_profile" id="addCreateClinicianProfile" value="1">
                                    <label class="form-check-label" for="addCreateClinicianProfile">
                                        Create Clinician Profile
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="buttons">
                    <a href="javascript:;" class="btn thm-btn" id="submitPersonnel">Submit</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="personnelModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <div id="modal-title">
                    <strong>Personnel Details</strong>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="validationErrors"></div>
                <form action="" id="personnelUpdateForm">
                    <input type="hidden" name="personnelID" id="personnelID">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Type</label>
                                <select name="type" id="personnelType" class="selectpicker form-control personnel-type-select" required>
                                    <?php foreach($userTypes as $type): ?>
                                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div id="edit-clinician-type-row" class="clinician-type-row" style="display: none;">
                                <div class="form-group">
                                    <label for="">Clinician Type</label>
                                    <select name="clinician_type" id="personnelClinicianType" class="selectpicker form-control" data-title="Select Clinician Type">
                                        <?php foreach($clinicianTypes as $type): ?>
                                            <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">First Name</label>
                                <input type="text" class="form-control" name="first_name" id="personnelFirstName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Last Name</label>
                                <input type="text" class="form-control" name="last_name" id="personnelLastName" required>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="update-password-row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Password (Leave blank to keep current)</label>
                                <input type="password" class="form-control" name="password" id="personnelPassword">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input type="email" class="form-control" name="email" id="personnelEmail" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Contact Number</label>
                                <input type="text" class="form-control" name="contact_number" id="personnelContact">
                            </div>
                        </div>
                    </div>
                    <div class="row" id="update-signature-row">
                        <div class="col-md-12">
                            <div class="form-group mt-3">
                                <label for="">Signature</label>
                                <div id="edit-signature-preview-container" class="mb-2 text-center" style="display: none;">
                                    <img id="edit-signature-preview" src="" style="max-width: 100%; border: 1px solid #ccc;">
                                    <br>
                                    <button type="button" class="btn btn-sm btn-yellow mt-1" id="btn-change-signature">Change Signature</button>
                                </div>
                                <div id="edit-signature-pad" class="signature-pad text-center">
                                    <div class="signature-pad--body" style="text-align: center;">
                                        <canvas style="border: 1px solid #ccc; "></canvas>
                                    </div>
                                    <div class="buttons text-center mt-2">
                                        <button type="button" class="btn btn-black btn-clear" data-action="clear">Clear</button>
                                        <button type="button" class="btn btn-black btn-undo" data-action="undo">Undo</button>
                                    </div>
                                </div>
                                <div class="mt-3 signature-upload-container">
                                    <label for="">Or Upload Signature</label>
                                    <input type="file" class="form-control signature-upload" accept="image/*">
                                </div>
                                <input type="hidden" name="signature" id="edit-signature-input">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select name="status" id="personnelStatus" class="selectpicker form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="update-clinician-profile-row" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="create_clinician_profile" id="updateCreateClinicianProfile" value="1">
                                    <label class="form-check-label" for="updateCreateClinicianProfile">
                                        Create Clinician Profile
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="buttons">
                    <a href="javascript:;" class="btn thm-btn" id="updatePersonnel">Update</a>
                </div>
            </div>
        </div>
    </div>
</div>
