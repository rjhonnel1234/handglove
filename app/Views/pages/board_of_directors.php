<div id="board-of-directors" class="main-pages page-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="section-title text-center mb-5">
                    <h2>BOARD OF DIRECTORS</h2>
                </div>
                
                <div class="row board-grid">
                    <?php if(!empty($board_members)){ ?>
                        <?php for($ctr = 0; $ctr < count($board_members) && $ctr < 9; $ctr++){ ?>
                            <div class="col-md-4 mb-5">
                                <div class="member-card d-flex align-items-center">
                                    <div class="member-avatar">
                                        <?php if($board_members[$ctr]['picture']){ ?>
                                            <img src="<?= base_url($board_members[$ctr]['picture']) ?>" alt="<?= $board_members[$ctr]['ceo_name'] ?>">
                                        <?php }else{ ?>
                                            <img src="<?= base_url('assets/img/blank-img.png') ?>" alt="Default">
                                        <?php } ?>
                                    </div>
                                    <div class="member-info ml-3">
                                        <h5 class="mb-0"><?= $board_members[$ctr]['ceo_name'] ?></h5>
                                        <p class="mb-0 text-muted small"><?= $board_members[$ctr]['position'] ?></p>
                                        <p class="mb-0 text-muted small"><?= $board_members[$ctr]['company_name'] ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if(count($board_members) > 9){ ?>
                            <div class="col-md-12 text-right">
                                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                    View More
                                </button>

                                <div class="collapse" id="collapseExample">
                                    <div class="col-md-12 text-center">
                                        <?php for($ctr = 9; $ctr < count($board_members); $ctr++){ ?>
                                            <h4 class="mb-0"><?= $board_members[$ctr]['ceo_name'] ?></h4>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div>

                        <?php } ?>
                    <?php } ?>
                </div>

                <div class="media-section mt-5 py-5 border-top">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <h3 class="mb-4">Media</h3>
                            <ul class="media-links list-unstyled">
                                <li class="d-flex align-items-center mb-4">
                                    <div class="icon-box mr-3">
                                        <i class="fas fa-comment-dots fa-2x"></i>
                                    </div>
                                    <div class="text-box">
                                        <h6 class="mb-0">Commercial</h6>
                                        <small class="text-muted">mentorship team</small>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center mb-4">
                                    <div class="icon-box mr-3">
                                        <i class="fas fa-chart-line fa-2x"></i>
                                    </div>
                                    <div class="text-box">
                                        <h6 class="mb-0">Progress Report</h6>
                                        <small class="text-primary">quality of service.</small>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center">
                                    <div class="icon-box mr-3">
                                        <i class="fas fa-user-friends fa-2x"></i>
                                    </div>
                                    <div class="text-box">
                                        <h6 class="mb-0">Look Inside</h6>
                                        <small class="text-muted">client/driver.</small>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="partner-logos-grid row no-gutters">
                                <?php if(!empty($board_members)){ ?>
                                    <?php for($ctr = 0; $ctr < count($board_members) && $ctr < 9; $ctr++){ ?>
                                        <?php if($board_members[$ctr]['company_logo']){ ?>
                                            <div class="col-4 p-2 text-center">
                                                <?php if($board_members[$ctr]['company_website']){ ?>
                                                    <a href="<?= $board_members[$ctr]['company_website'] ?>" target="_blank">
                                                        <img src="<?= base_url($board_members[$ctr]['company_logo']) ?>" alt="<?= $board_members[$ctr]['company_name'] ?>" class="img-fluid grayscale">
                                                    </a>
                                                <?php }else{ ?>
                                                    <img src="<?= base_url($board_members[$ctr]['company_logo']) ?>" alt="<?= $board_members[$ctr]['company_name'] ?>" class="img-fluid grayscale">
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
