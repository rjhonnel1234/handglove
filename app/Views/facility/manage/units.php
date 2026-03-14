<div class="card">
    <div class="card-body">
        <div class="heading">
            <h2>Units</h2>
            <div class="buttons">
                <a href="" class="btn thm-btn"  data-toggle="modal" data-target="#addUnitModal">Add Unit</a>
            </div>
        </div>

        <table id="units_table" class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 40%;">Name</th>
                    <th style="width: 15%;text-align: center;">Census</th>
                    <th style="width: 30%;">Description</th>
                    <th style="width: 15%;text-align: center;">Actions</th>
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
                    <strong>Add Unit</strong>
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
                                <label for="">Name</label>
                                <input type="text" class="form-control" name="name" id="name">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Unit Manager</label>
                                <select name="unit_manager_id" id="unit_manager_id" class="selectpicker form-control" data-title="Select unit manager" data-live-search="true">
                                    <?php foreach ($personnel as $person): ?>
                                        <option value="<?= $person['id'] ?>"><?= esc($person['first_name'] . ' ' . $person['last_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Census</label>
                                <div style="display: flex; align-items: center;gap: 20px;">
                                    <input type="number" min="1" step="1" class="form-control" name="census[]" value="" style="width: 60px;">:<input type="number" min="1" step="1" name="census[]" class="form-control" style="width: 60px;">
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
                    <strong>Unit Details</strong>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="unitUpdateForm">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input type="text" class="form-control" name="name" id="unitName">
                                <input type="hidden" class="form-control" name="unitID" id="unitID">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Description</label>
                                <textarea class="form-control" id="unitDescription" name="description" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Unit Manager</label>
                                <select name="unit_manager_id" id="edit_unit_manager_id" class="selectpicker form-control" data-title="Select unit manager" data-live-search="true">
                                    <?php foreach ($personnel as $person): ?>
                                        <option value="<?= $person['id'] ?>"><?= esc($person['first_name'] . ' ' . $person['last_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Census</label>
                                <div style="display: flex; align-items: center;gap: 20px;">
                                    <input type="number" min="1" step="1" class="form-control" value="" style="width: 60px;" name="census[]" id="unitCensus_1">:<input type="number" id="unitCensus_2" min="1" step="1" class="form-control"  name="census[]" style="width: 60px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="buttons">
                    <a href="javascript:;" class="btn thm-btn" id="updateUnit">Update</a>
                </div>
            </div>
        </div>
    </div>
</div>
