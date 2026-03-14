<div class="modal fade" id="clinicianCheckModal" role="dialog" aria-labelledby="clinicianCheckModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 900px;">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-body p-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; right: 20px; top: 15px; z-index: 10;">
                    <span aria-hidden="true">&times;</span>
                </button>
                
                <!-- Header / Profile Section -->
                <div class="profile-header p-4 d-flex align-items-center">
                    <div class="profile-img mr-4">
                        <img id="check-clinician-img" src="<?= base_url('assets/img/blank-img.png') ?>" alt="" style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px;">
                    </div>
                    <div class="profile-info">
                        <h2 id="check-clinician-name" class="mb-1" style="font-weight: 700;">Clinician Name</h2>
                        <div class="profile-meta text-muted mb-2">
                            <p class="mb-0" id="check-clinician-address"><i class="fa fa-map-marker-alt"></i> Address details here...</p>
                            <p class="mb-0" id="check-clinician-contact"><i class="fa fa-phone"></i> 123456789</p>
                        </div>
                        
                        <div class="profile-stats d-flex mt-3">
                            <div class="stat-item text-center mr-4">
                                <div class="stat-icon mb-1"><i class="fa fa-user" style="color: #3bb4e5; font-size: 20px;"></i></div>
                                <div id="check-clinician-shifts" class="stat-value font-weight-bold">0</div>
                                <div class="stat-label text-muted small">Shifts</div>
                            </div>
                            <div class="stat-item text-center mr-4">
                                <div class="circle-stat mb-1" id="check-attendance-circle"></div>
                                <div id="check-clinician-attendance" class="stat-value font-weight-bold">0%</div>
                                <div class="stat-label text-muted small">Attendance</div>
                            </div>
                            <div class="stat-item text-center">
                                <div class="circle-stat mb-1" id="check-lateness-circle"></div>
                                <div id="check-clinician-lateness" class="stat-value font-weight-bold">0%</div>
                                <div class="stat-label text-muted small">Lateness</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-sections p-4">
                    <div class="row">
                        <!-- Bedside Manner -->
                        <div class="col-md-4">
                            <h5 class="section-title mb-3" style="font-weight: 700; color: #666; text-transform: uppercase; font-size: 16px;">Bedside Manner</h5>
                            <div class="rating-list">
                                <div class="rating-item d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Lateness</span>
                                    <div class="stars text-warning"><?= str_repeat('<i class="fa fa-star"></i>', 4) ?><i class="far fa-star"></i></div>
                                </div>
                                <div class="rating-item d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Time Theft</span>
                                    <div class="stars text-warning"><?= str_repeat('<i class="fa fa-star"></i>', 4) ?><i class="far fa-star"></i></div>
                                </div>
                                <div class="rating-item d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Compassion</span>
                                    <div class="stars text-warning"><?= str_repeat('<i class="fa fa-star"></i>', 5) ?></div>
                                </div>
                                <div class="rating-item d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Attitude</span>
                                    <div class="stars text-warning"><?= str_repeat('<i class="fa fa-star"></i>', 4) ?><i class="far fa-star"></i></div>
                                </div>
                            </div>
                        </div>

                        <!-- DNR -->
                        <div class="col-md-4">
                            <h5 class="section-title mb-3" style="font-weight: 700; color: #666; text-transform: uppercase; font-size: 16px;">DNR</h5>
                            <div class="dnr-list">
                                <div class="dnr-item d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Connect RN</span>
                                    <span class="badge badge-danger">DNR</span>
                                </div>
                                <div class="dnr-item d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">SwiftKey</span>
                                    <span class="badge badge-danger">DNR</span>
                                </div>
                                <div class="dnr-item d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">scud</span>
                                    <span class="text-muted small"><i class="fa fa-info-circle"></i> Unknown</span>
                                </div>
                                <div class="dnr-item d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Gogo MED</span>
                                    <span class="text-muted small"><i class="fa fa-info-circle"></i> Unknown</span>
                                </div>
                            </div>
                        </div>

                        <!-- Crime & Abuse -->
                        <div class="col-md-4">
                            <h5 class="section-title mb-3" style="font-weight: 700; color: #666; text-transform: uppercase; font-size: 16px;">Crime & Abuse</h5>
                            <div class="abuse-list">
                                <div class="abuse-item d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Neglect</span>
                                    <span class="text-muted small"><i class="fa fa-info-circle"></i> Unknown</span>
                                </div>
                                <div class="abuse-item d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Physical Abuse</span>
                                    <span class="text-muted small"><i class="fa fa-info-circle"></i> Unknown</span>
                                </div>
                                <div class="abuse-item d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Sexual Abuse</span>
                                    <span class="text-muted small"><i class="fa fa-info-circle"></i> Unknown</span>
                                </div>
                                <div class="abuse-item d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Exploitation</span>
                                    <span class="text-muted small"><i class="fa fa-info-circle"></i> Unknown</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.circle-stat {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    margin: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    background: #eee;
}
.circle-stat::before {
    content: "";
    width: 15px;
    height: 15px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
}
.section-title {
    border-bottom: 2px solid #eee;
    padding-bottom: 5px;
}
#clinicianCheckModal .modal-content {
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
</style>
