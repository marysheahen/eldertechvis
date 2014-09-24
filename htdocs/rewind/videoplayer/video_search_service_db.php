<?php
	include('player_functions_db.php');
	include('../common/Config.php');
	
	if (isset($_POST['startDate'])) {
		$START_DATE=$_POST['startDate'];
	}
	if (isset($_POST['endDate'])) {
		$END_DATE=$_POST['endDate'];
	}
	if (isset($_POST['roomNumber'])) {
		$ROOM_NUMBER=$_POST['roomNumber'];
	}	
	
	if (isset($_POST['onlyFalls'])) {
		$ONLY_FALLS=$_POST['onlyFalls'];
	}
		
	sleep(1);
	
	$result = find_patients_last_n_videos_twoDimensional($ROOM_NUMBER,$START_DATE,$END_DATE,$GLOBALS['MAX_VIDEO_LIST_COUNT'],$ONLY_FALLS);
	$result = json_encode($result);
	print_r($result);
?>