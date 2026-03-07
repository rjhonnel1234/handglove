<div id="profile-sidebar" class="col-lg-3 col-md-12 col-sm-12 col-12">
    <ul class="facility_nav">
        <li class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>"><a href="<?php echo base_url('facility/manage/'); ?>">Dashboard</a></li>
        <li class="<?php echo $page == 'onboarding' ? 'active' : ''; ?>"><a href="<?php echo base_url('facility/manage/onboarding'); ?>">Onboarding</a></li>
        <li class="<?php echo $page == 'profile' ? 'active' : ''; ?>"><a href="<?php echo base_url('facility/manage/profile'); ?>">Profile</a></li>
        <!-- <li class="<?php echo $page == 'clinicians' ? 'active' : ''; ?>"><a href="<?php echo base_url('facility/manage/clinicians'); ?>">Clinicians</a></li> -->
        <li class="<?php echo $page == 'units' ? 'active' : ''; ?>"><a href="<?php echo base_url('facility/manage/units'); ?>">Units</a></li>
        <li class="<?php echo $page == 'jobs' ? 'active' : ''; ?>"><a href="<?php echo base_url('facility/manage/jobs'); ?>">Jobs</a></li>
        <!-- <li class="<?php echo $page == 'shifts' ? 'active' : ''; ?>"><a href="<?php echo base_url('facility/manage/shifts'); ?>">Shifts</a></li> -->
        <li class="<?php echo $page == 'votes' ? 'active' : ''; ?>"><a href="<?php echo base_url('facility/manage/votes'); ?>">Voting</a></li>
        <li class="<?php echo $page == 'personnel' ? 'active' : ''; ?>"><a href="<?php echo base_url('facility/manage/personnel'); ?>">Personnel</a></li>
        <li class="<?php echo $page == 'timekeeping' ? 'active' : ''; ?>"><a href="<?php echo base_url('facility/manage/timekeeping'); ?>">Time Logs</a></li>
    </ul>                
</div>