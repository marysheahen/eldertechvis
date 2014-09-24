<?php
include('../../common/Config.php');
$GLOBALS['IS_DB_ORIENTED'] = 0;

include('Init_DB.php');
auto_update_all_videos();
?>