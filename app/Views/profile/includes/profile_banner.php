<div id="profile-banner">
    <div class="container">
        <div class="profile">
            <div class="profile-img">
                <img src="<?php echo $profileData['profile_pic_url'] != '' ? $profileData['profile_pic_url'] : base_url('assets/img/blank-img.png'); ?>" alt="">
            </div>
            <div class="profile-details">
                <div class="profile-name">
                    <h2><?php echo $profileData['name']; ?></h2>
                </div>
                <div class="profile-extras">
                    <div class="profile-detail-info">
                        <?php $color = ($profileData['tier'] == 30 ? '#FFD700' : ($profileData['tier'] == 20 ? '#C0C0C0' : '#CD7F32')); ?>
                        <ul class="profile-agency">
                            <li><?php echo $profileData['type_name']; ?></li>
                            <li style="background: #fff;color: <?php echo $color; ?>;border: 1px solid <?php echo $color; ?>;"><i class="fa fa-award"></i> <?php echo ($profileData['tier'] == 30 ? 'Senior' : ($profileData['tier'] == 20 ? 'Reliable' : 'Junior')); ?></li>
                        </ul>
                        
                        <ul class="profile-short-info">
                            <li><i class="fa fa-map-marker-alt"></i> <?php echo $profileData['address']; ?></li>
                            <li><i class="fa fa-phone"></i> </i> <?php echo $profileData['contact_number']; ?></li>
                            <li>SMS Facility ID: </i> <?php echo $pin; ?></li>
                        </ul>
                    </div>

                    <div class="profile-statistics">
                        <div class="no-of-shifts-cont stats">
                            <div class="person-card">
                                <i class="fa fa-user"></i>
                                <?php 
                                $totalShifts = $profileData['total_shifts'] ?? 0;
                                $displayShifts = $totalShifts > 999 ? '999+' : $totalShifts;
                                ?>
                                <div class="card-counter"><?php echo $displayShifts; ?></div>
                                <p>Shifts</p>
                            </div>

                        </div>
                        <div class="attendance-cont stats">
                            <div class="circle-card">
                                <?php $attPer = $profileData['attendance_percentage'] ?? 0; ?>
                                <div class="circle" data-percent="<?= $attPer ?>" style="background: conic-gradient(#3bb4e5 <?php echo $attPer*3.6.'deg'; ?>, #eee <?php echo $attPer*3.6.'deg'; ?>);"></div>
                                <div class="card-counter"><?= $attPer ?>%</div>
                                <p>Attendance</p>
                            </div>
                        </div>
                        <div class="tardiness-cont stats">
                            <div class="circle-card">
                                <?php $latePer = $profileData['lateness_percentage'] ?? 0; ?>
                                <div class="circle" data-percent="<?= $latePer ?>" style="background: conic-gradient(#3bb4e5 <?php echo $latePer*3.6.'deg'; ?>, #eee <?php echo $latePer*3.6.'deg'; ?>);"></div>
                                <div class="card-counter"><?= $latePer ?>%</div>
                                <p>Lateness</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>