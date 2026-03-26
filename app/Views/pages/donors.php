
<!--Page Title-->
<section class="page-title" style="background-image: url(assets/images/background/page-title-2.jpg);">
    <div class="pattern-layer" style="background-image: url(assets/images/shape/pattern-35.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <div class="title-box centred">
                <h1>Awards</h1>
            </div>
            <ul class="bread-crumb clearfix">
                <li><a href="index.html">Home</a></li>
                <li>Awards</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->


<!-- awards-section -->
<section class="awards-section">
    <div class="pattern-layer" style="background-image: url(assets/img/shape/pattern-10.png);"></div>
    <div class="auto-container">
        <div class="sec-title centred">
            <span class="top-title">Our Excellence</span>
            <h2>Awards & Major Achievements</h2>
            <p>Long established fact that a reader will be distracted by the <br />readable content of a
                page.</p>
        </div>
        <div class="row clearfix">
            <div class="col-lg-4 col-md-12 col-sm-12 inner-column">
                <div class="inner-block">

                    <?php if (!empty($gna_winners)): ?>
                        <?php foreach ($gna_winners as $winner): ?>
                        <div class="single-award-block">
                            <div class="inner-box">
                                <div class="upper-box">
                                    <figure class="icon-box"><img src="assets/img/icons/icon-19.png" alt="">
                                    </figure>
                                    <h3>GNA of<br>the Week</h3>
                                </div>
                                <ul class="lower-box">
                                    <li><span><?php echo $winner['name']; ?></li>
                                    <li><span>Award by</span>:<?php echo $winner['company_name']; ?></li>
                                </ul>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 image-column">
                <figure class="image-box js-tilt"><img src="assets/img/resource/award-1.png" alt="">
                </figure>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 inner-column">
                <div class="inner-block">
                    <?php if (!empty($nurse_winners)): ?>
                        <?php foreach ($nurse_winners as $winner): ?>
                        <div class="single-award-block">
                            <div class="inner-box">
                                <div class="upper-box">
                                    <figure class="icon-box"><img src="assets/img/icons/icon-19.png" alt="">
                                    </figure>
                                    <h3>Nurse of<br>the Week</h3>
                                </div>
                                <div class="d-flex align-items-center pb-3">
                                    <img src="/assets/img/blank-img.png" alt="Member" class="winner-avatar mr-2">
                                    <ul class="lower-box">    
                                        <li><span><?php echo $winner['name']; ?></li>
                                        <li><span>Award by:</span><?php echo $winner['company_name']; ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- awards-section end -->
    

<!-- team-style-four -->
<section id="bod-companies" class="team-style-four bg-color-2">
    <div class="auto-container">
        <div class="sec-title centred">
            <span class="top-title">Donors / Sponsors</span>
            <h2>Companies Behind Our Successful Service</h2>
            <!-- <p>Long established fact that a reader will be distracted by the <br />readable content of a page.</p> -->
        </div>
        <div class="row clearfix">

            <?php if(!empty($donors)){ ?>
                <?php for($ctr = 0; $ctr < count($donors); $ctr++){ ?>
                    <div class="col-lg-3 col-md-6 col-sm-12 team-block">
                        <div class="team-block-one wow fadeInUp animated animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                            <div class="inner-box">
                                <figure class="image-box">
                                    <?php if($donors[$ctr]['url']){ ?>
                                        <a href="<?= $donors[$ctr]['url'] ?>" target="_blank">
                                            <img src="<?= base_url($donors[$ctr]['logo']) ?>" alt="<?= $donors[$ctr]['name'] ?>" class="img-fluid grayscale">
                                        </a>
                                    <?php }else{ ?>
                                        <img src="<?= base_url($donors[$ctr]['logo']) ?>" alt="<?= $donors[$ctr]['name'] ?>" class="img-fluid grayscale">
                                    <?php } ?>
                                    <span class="singature">Our Champ</span>
                                </figure>
                                <div class="lower-content">
                                    <h3><a href="index.html"><?= $donors[$ctr]['name'] ?></a></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            
        </div>
    </div>
</section>
<!-- team-style-four end -->


        <!-- Scholarship Section -->
        <!-- <div class="scholarship-section mt-5 pt-5 border-top">
            <h2 class="section-title mb-5">Scholarship</h2>
            <div class="row justify-content-center scholarship-cards">
                <div class="col-md-3 mb-4">
                    <img src="<?= base_url('assets/img/scholarship-card1.jpg') ?>" alt="Scholarship" class="img-fluid rounded shadow">
                </div>
                <div class="col-md-3 mb-4">
                    <img src="<?= base_url('assets/img/scholarship-card2.jpg') ?>" alt="Scholarship" class="img-fluid rounded shadow">
                </div>
                <div class="col-md-3 mb-4">
                    <img src="<?= base_url('assets/img/scholarship-card3.jpg') ?>" alt="Scholarship" class="img-fluid rounded shadow">
                </div>
            </div>
        </div> -->
