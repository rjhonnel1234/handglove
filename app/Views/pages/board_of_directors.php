
<!--Page Title-->
<section class="page-title" style="background-image: url(assets/images/background/page-title-2.jpg);">
    <div class="pattern-layer" style="background-image: url(assets/images/shape/pattern-35.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <div class="title-box centred">
                <h1>Meet Our Team</h1>
                <p>Thanks to our team, it is now possible for us to get the finest & services</p>
            </div>
            <ul class="bread-crumb clearfix">
                <li><a href="index.html">Home</a></li>
                <li>About</li>
                <li>Our Team</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->


<!-- team-style-three -->
<section class="team-style-three">
    <div class="auto-container">
        <div class="sec-title centred">
            <span class="top-title">Our leadership Team</span>
            <h2>Board of Directors & Senior Executives</h2>
            <p>Long established fact that a reader will be distracted by the <br />readable content of a page.</p>
        </div>
        <?php if(!empty($board_members)){ ?>
        <div class="three-item-carousel  owl-carousel owl-theme owl-dot-style-two owl-nav-none">
            <?php for($ctr = 0; $ctr < count($board_members); $ctr++){ ?>
            <div class="team-block-one">
                <div class="inner-box">
                    <figure class="image-box">
                        <?php if($board_members[$ctr]['picture']){ ?>
                            <img src="<?= base_url($board_members[$ctr]['picture']) ?>" alt="<?= $board_members[$ctr]['ceo_name'] ?>">
                        <?php }else{ ?>
                            <img src="<?= base_url('assets/img/blank-img.png') ?>" alt="Default">
                        <?php } ?>
                        <span class="singature">Our Champ</span>
                        <div class="share-box">
                            <p><i class="fas fa-share-alt"></i>Share</p>
                            <ul class="social-links clearfix">
                                <li><a href="index.html"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="index.html"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="index.html"><i class="fab fa-google-plus-g"></i></a></li>
                                <li><a href="index.html"><i class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </figure>
                    <div class="lower-content">
                        <h3><a href="index.html"><?= $board_members[$ctr]['ceo_name'] ?></a></h3>
                        <span class="designation"><?= $board_members[$ctr]['position'] ?></span>
                        <span class="designation"><?= $board_members[$ctr]['company_name'] ?></span>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</section>


<!-- team-style-four -->
<section id="bod-companies" class="team-style-four bg-color-2">
    <div class="auto-container">
        <div class="sec-title centred">
            <span class="top-title">Our Executive Team</span>
            <h2>Companies Behind Our Successful Service</h2>
            <!-- <p>Long established fact that a reader will be distracted by the <br />readable content of a page.</p> -->
        </div>
        <div class="row clearfix">

            <?php if(!empty($board_members)){ ?>
                <?php for($ctr = 0; $ctr < count($board_members); $ctr++){ ?>
                    <div class="col-lg-3 col-md-6 col-sm-12 team-block">
                        <div class="team-block-one wow fadeInUp animated animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                            <div class="inner-box">
                                <figure class="image-box">
                                    <?php if($board_members[$ctr]['company_website']){ ?>
                                        <a href="<?= $board_members[$ctr]['company_website'] ?>" target="_blank">
                                            <img src="<?= base_url($board_members[$ctr]['company_logo']) ?>" alt="<?= $board_members[$ctr]['company_name'] ?>" class="img-fluid grayscale">
                                        </a>
                                    <?php }else{ ?>
                                        <img src="<?= base_url($board_members[$ctr]['company_logo']) ?>" alt="<?= $board_members[$ctr]['company_name'] ?>" class="img-fluid grayscale">
                                    <?php } ?>
                                    <span class="singature">Our Champ</span>
                                </figure>
                                <div class="lower-content">
                                    <h3><a href="index.html"><?= $board_members[$ctr]['company_name'] ?></a></h3>
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