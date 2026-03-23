<?php
    echo view('facility/includes/profile_banner');
?>
<div id="profile-bios">
    <div class="container">
        <div class="row">
            <div id="profile-main" class="col-lg-8 col-md-12 col-sm-12 col-12">
                <?php if(!empty($votingArr)){ ?>
                <h3>This week's Vote</h3>
                <div id="votingSection" class="pl-5 mt-4">
                    <?php foreach($votingArr as $voting){ ?>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if(!empty($voting) && !$voting['hasVoted']){ ?>
                                <h4><?php echo $voting['description']; ?></h4>
                                <div class="voting">
                                    <div id="voting<?php echo $voting['id']; ?>">
                                        <?php foreach($voting['details'] as $detail){ ?>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="clinician-details">
                                                        <img class="img-responsive" src="<?php echo $detail['clinician_details']['profile_pic_url'] != '' ? $detail['clinician_details']['profile_pic_url'] : base_url('assets/img/blank-img.png'); ?>" />
                                                        <div class="clinician-name pl-3"><?php echo $detail['clinician_details']['name']; ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 submit-button text-right">
                                                    <a href="javascript:;" class="vote-icon-btn vote-clinician pt-2 pb-2" data-clinician_id="<?php echo $detail['clinician_id']; ?>" data-voting_id="<?php echo $voting['id']; ?>">
                                                        <div class="vote-content">VOTE</div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="divider mb-5"></div>
                <?php } ?>
                <h3>Reviews</h3>
                <div id="reviewsSection">
                    <div class="reviews-header mb-4">
                        <div class="badge-clinician-choice mb-3 d-flex align-items-center">
                            <img src="<?php echo base_url('assets/img/trophy-badge.png'); ?>" style="width: 60px;" alt="Trophy">
                            <div class="ml-3">
                                <strong>Clinician Choice</strong>
                                <p class="mb-0 text-muted" style="font-size: 0.9rem;">Providers with this badge are highly rated, reliable, and recommended by other clinicians.</p>
                            </div>
                        </div>

                        <div class="row text-center mb-4">
                            <div class="col-md-4">
                                <h6>Cleanliness</h6>
                                <div class="rating-stars text-warning">
                                    <?php for($i=1; $i<=5; $i++){ ?>
                                        <i class="fa<?= $i <= round($aggregates['cleanliness']) ? 's' : 'r' ?> fa-star"></i>
                                    <?php } ?>
                                    <span class="ml-2 text-dark font-weight-bold"><?= $aggregates['cleanliness'] ?></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6>Work Environment</h6>
                                <div class="rating-stars text-warning">
                                    <?php for($i=1; $i<=5; $i++){ ?>
                                        <i class="fa<?= $i <= round($aggregates['work_environment']) ? 's' : 'r' ?> fa-star"></i>
                                    <?php } ?>
                                    <span class="ml-2 text-dark font-weight-bold"><?= $aggregates['work_environment'] ?></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6>Tools Needed</h6>
                                <div class="rating-stars text-warning">
                                    <?php for($i=1; $i<=5; $i++){ ?>
                                        <i class="fa<?= $i <= round($aggregates['tools_needed']) ? 's' : 'r' ?> fa-star"></i>
                                    <?php } ?>
                                    <span class="ml-2 text-dark font-weight-bold"><?= $aggregates['tools_needed'] ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="trust-disclaimer p-3 bg-light rounded d-flex align-items-center mb-4">
                            <i class="fas fa-shield-alt text-muted fa-2x mr-3"></i>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">Your trust is our top concern, so providers can't pay to alter or remove reviews. We also don't publish reviews that contain any private clinician health information. <a href="javascript:;">Learn more here</a></p>
                        </div>
                    </div>


                    <?php if($aggregates['count'] > 0){ ?>
                    <p><strong><?= $aggregates['count'] ?> Reviews</strong></p>
                    <?php } ?>
                    <div class="reviews-list" id="reviewsContainer" data-client_id="<?= $facility['id'] ?>" data-total="<?= $aggregates['count'] ?>">
                        <!-- Reviews will be loaded here via AJAX -->
                    </div>


                    <div id="paginationContainer" class="d-flex justify-content-end mt-4"></div>
                    <div id="noReviewsMsg" class="text-muted" style="display: none;">No reviews yet.</div>
                </div>

            </div>
            <div id="profile-sidebar" class="col-lg-4 col-md-12 col-sm-12 col-12">
                <!-- <h3>Overview</h3>
                <div class="divider"></div>
                <div class="sidebar-list-job mt-10">
                    
                </div>
                <div class="divider"></div>
                <div class="buttons">
                    <button class="btn thm-btn-secondary" data-toggle="modal" data-target="#uploadModal">Credentials</button>
                    <button class="btn thm-btn-white" data-toggle="modal" data-target="#availabilityModal">Availability</button>
                </div> -->
                <div id="per-diem-network">
                    <strong>Accepted Per Diem</strong>
                    <div class="mt-2">
                        <?php if(!empty($onboardingSettings['accepted_per_diem_network'])){ ?>
                            <?php foreach($onboardingSettings['accepted_per_diem_network'] as $network => $networkData){ ?>
                                <span class="badge badge-light border rounded-pill px-3 py-2 mr-2 mb-2"><?= $network ?></span>
                            <?php } ?>
                        <?php } else { ?>
                            <span class="text-muted small">None listed.</span>
                        <?php } ?>
                    </div>
                </div>
                <div id="connector">
                    <?php if(!empty($votingArr)){ ?>
                    <div id="voting-results">
                        <h4>This week Vote</h4>
                        <?php foreach($votingArr as $voting){ ?>
                            <?php if(!empty($voting)){ ?>
                                <div class="voting-results mb-5">
                                <h6><?php echo $voting['description']; ?></h6>
                                <?php foreach($voting['details'] as $detail){ ?>
                                    <div class="row mb-1">
                                        <div class="col-md-12">
                                            <div class="clinician-details">
                                                <div class="clinician-name"><?php echo $detail['clinician_details']['name']; ?></div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $detail['vote_percentage'] . '%'; ?>;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php } ?>
                    <!-- <a href="<?php echo base_url('employee-award'); ?>" class="btn thm-btn">Add a Referral</a> -->
                    <!-- <ul id="connector_list">
                        <li>
                            <a href="#"><img src="<?php echo base_url('assets/img/blank-img.png'); ?>" alt=""></a>
                        </li>
                        <li>
                            <a href="#"><img src="<?php echo base_url('assets/img/blank-img.png'); ?>" alt=""></a>
                        </li>
                        <li>
                            <a href="#"><img src="<?php echo base_url('assets/img/blank-img.png'); ?>" alt=""></a>
                        </li>
                        <li>
                            <a href="#"><img src="<?php echo base_url('assets/img/blank-img.png'); ?>" alt=""></a>
                        </li>
                        <li>
                            <a href="#"><img src="<?php echo base_url('assets/img/blank-img.png'); ?>" alt=""></a>
                        </li>
                        <li>
                            <a href="#"><img src="<?php echo base_url('assets/img/blank-img.png'); ?>" alt=""></a>
                        </li>
                        <li>
                            <a href="#"><img src="<?php echo base_url('assets/img/blank-img.png'); ?>" alt=""></a>
                        </li>
                        <li>
                            <a href="#"><img src="<?php echo base_url('assets/img/blank-img.png'); ?>" alt=""></a>
                        </li>
                    </ul> -->
                </div>

            </div>
        </div>
    </div>
</div>