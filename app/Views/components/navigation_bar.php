<header class="">
    <nav class="main-nav-one stricky">
        <div class="container">
            <div class="inner-container">
                <div class="logo-box">
                    <a href="<?php echo base_url(); ?>" class="nav-logo">
                        <img src="<?php echo base_url('assets/img/handglove-logo.png'); ?>" alt="" width="100">
                        Handglove
                    </a>
                    <a href="#" class="side-menu__toggler"><i class="fa fa-bars"></i></a>
                </div><!-- /.logo-box -->
                <div class="main-nav__main-navigation">
                    <!-- <ul class="main-nav__navigation-box">
                        <li class="">
                            <a href="about-1.html">About Us</a>
                        </li>
                        <li class="dropdown">
                            <a href="services.html">Clinician</a>
                        </li>
                        <li class="dropdown">
                            <a href="#">Facility</a>
                            <ul>
                                <li><a href="blog-standard.html">MyShift</a></li>
                            </ul>
                        </li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>/.main-nav__navigation-box -->

                    <?php  if( session()->get('isLoggedIn') == 1 ){ ?>
                        <ul class="main-nav__navigation-box" id="user_menu">
                            <li class="list-inline-item dropdown mr-2">
                                <a href="javascript:void(0)" class="header-item noti-icon position-relative" id="notification" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-bell"></i>
                                    <div class="count position-absolute notification-count" style="display:none; background-color: #E41E3F; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; display: flex; align-items: center; justify-content: center; top: -5px; right: -5px;">0</div>
                                </a>
                                <div class="dropdown-menu p-0 notification-dropdown" aria-labelledby="notification">
                                    <div class="notification-header">
                                        <h6>Notifications</h6>
                                        <a href="javascript:void(0)" id="mark-all-read" style="color: #0084FF; font-size: 13px; font-weight: 500; text-decoration: none;">Mark all as read</a>
                                    </div>
                                    <div class="notification-wrapper dropdown-scroll" id="notification-list">
                                        <div class="p-4 text-center text-muted">Loading notifications...</div>
                                    </div><!--end notification-wrapper-->
                                    <div class="notification-footer text-center">
                                        <a href="<?= base_url('notifications') ?>" class="fs-13">See all notifications</a>
                                    </div>
                                </div>
                            </li>
                            <li class="list-inline-item dropdown mr-2">
                                <a href="javascript:void(0)" class="header-item" id="location-dropdown" data-toggle="dropdown" aria-expanded="false" data-auto-close="outside">
                                    <i class="fa fa-map-marker-alt"></i>
                                </a>
                                <div class="dropdown-menu p-3 location-dropdown-menu dropdown-menu-right" aria-labelledby="location-dropdown" style="min-width: 350px;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="online-status-container mt-2">
                                                <input type="checkbox" id="online-status-toggle" class="d-none" <?php echo (session()->get('online_status') == 1) ? 'checked' : ''; ?>>
                                                <label for="online-status-toggle" class="mb-0 cursor-pointer">
                                                    <div class="online-status-icon <?php echo (session()->get('online_status') == 1) ? '' : 'offline'; ?>">
                                                        <i class="fa <?php echo (session()->get('online_status') == 1) ? 'fa-wifi' : 'fa-wifi'; ?>" id="status-icon"></i>
                                                    </div>
                                                    <span id="status-text"><?php echo (session()->get('online_status') == 1) ? 'Online' : 'Offline'; ?></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <small class="d-block mb-2" style="line-height: 1em;">Click the button to get your coordinates.</small>
                                            <a href="javascript:void(0);" class="btn btn-warning pt-1 pb-1 pl-2 pr-2">Update Location</a>
                                            <div class="dropdown-divider"></div>
                                            <h6 class="dropdown-header p-0">Your Location</h6>
                                            <div class="form-group mb-2">
                                                <div><label class="small text-muted mb-1">Latitude: <span id="user-lat">Detecting...</span><br>Longitude: <span id="user-long">Detecting...</span></label></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <?php if( session()->get('type') == 10){ ?>
                                        <div id="shift-offers-container" style="display: none;">
                                            <div class="notification-header border-0 pb-0">
                                                <h6 style="font-size: 18px;">Shift Offers</h6>
                                            </div>
                                            <div id="shift-offers-list"></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </li>
                            <li class="dropdown">
                                <a href="#">
                                    <div class="user-image">
                                        <img src="<?php echo base_url('/assets/img/blank-img.png'); ?>" class="" alt="User Image">
                                    </div>
                                    <div class="user-name">
                                        <div><strong>Hi, <?php echo session()->get('first_name');?></strong></div>
                                    </div>
                                </a>
                                <ul>
                                    <?php if( session()->get('type') == 10){ ?>
                                        <li><a href="<?php echo base_url('profile'); ?>">View Profile</a></li>
                                        <li><a href="<?php echo base_url('profile/shifts'); ?>">My Shifts</a></li>
                                    <?php } ?>
                                    <?php if( session()->get('facility_id') != 0){ ?>
                                        <li><a href="<?php echo base_url('facility'); ?>">Facility Profile</a></li>
                                        <?php if(session()->get('type') != 5){ ?>
                                            <li><a href="<?php echo base_url('facility/manage'); ?>">Manage Facility</a></li>
                                        <?php } ?>
                                    <?php } ?>
                                    <li><a href="<?php echo base_url('login/logout'); ?>">Logout</a></li> 
                                </ul> 
                            </li> 
                        </ul>
                    <?php } ?>

                </div>

            </div><!-- /.inner-container -->
        </div><!-- /.container -->
    </nav><!-- /.main-nav-one -->
</header>