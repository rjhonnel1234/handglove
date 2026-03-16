<div id="home" class="main-pages">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center" id="home-container">
                <h1>Clinician, Shift like a Geniues</h1>
                <div class="sub-title">At Handglove, we're connecting every healthcare facility to the world's most skilled clinician, in the simplest way possible. A per diem app with No call out and no cancellation.</div>
                
                <div class="domain-search-holder">
                    <?php  if( session()->get('isLoggedIn') != 1 ){ ?>
                    <form action="<?php echo base_url('login'); ?>" method="post">
                        <input id="email" type="text" name="email" placeholder="Enter username/email address">
                        <input id="password" type="password" name="password" placeholder="Enter password">
                        <input id="search-btn" type="submit" name="submit" value="Login">
                    </form>
                    <?php } ?>
                </div>
                
                <div class="animation">
                    <img src="<?php echo base_url('assets/img/laptop.png'); ?>" alt="laptop">
                    <ul class="icons-list">
                        <li><a href="<?php echo base_url('board-of-directors'); ?>" title="Meet the team"><i class="fas fa-thumbs-up"></i></a></li>
                        <li><a href="<?php echo base_url('demo-request'); ?>" title="Demo Request"><i class="fas fa-comment-alt"></i></a></li>
                        <li><a href="<?php echo base_url('claim-facility'); ?>"><i class="fas fa-wifi"></i></a></li>
                        <li><a href="#"><i class="fas fa-envelope"></i></a></li>
                        <li><a href="<?php echo base_url('donors'); ?>"><i class="fas fa-share-alt"></i></a></li>
                        <li><a href="<?php echo base_url('jobs'); ?>"><i class="fas fa-bullhorn"></i></a></li>
                    </ul>
                    <canvas id="hand-animation" width="323" height="518" style="width: 323px; height: 518px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>