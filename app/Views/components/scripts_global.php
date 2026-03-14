<script>
var GLOBAL_IMG_DATA_URL = '<?php echo IMG_DATA_URL; ?>';
var GLOBAL_IMG_URL = '<?php echo IMG_URL; ?>';
var GLOBAL_BASE_URL = '<?php echo BASE_URL; ?>';
var GLOBAL_USER_TYPE = '<?php echo session()->get('facility_id') == 0 ? session()->get('type') : 0; ?>';
var GLOBAL_USER_ID = '<?php echo session()->get('id'); ?>';
</script>
