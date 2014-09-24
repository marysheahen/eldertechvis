<?php
include('videoplayer/player_functions_db.php');

global $BASE_VIDEO_PATH;
global $FILE_BASE_VIDEO_PATH;
global $IS_DB_ORIENTED;
global $IS_REMOTE_PATH;
global $MAX_VIDEO_LIST_COUNT;
global $MIN_VIDEO_SIZE;
global $DB_DBNAME;
global $fileType;

$fileType = '.mp4';

//Database:--------------------------
// $DB_USERNAME = "smartamerica";
// $DB_PASSWORD = "PWtMN4TaZUWYMus6";
$DB_USERNAME = "kinectrewindmgr";
$DB_PASSWORD = "8ke8SyAJ5ebW";
$DB_HOSTNAME = "robby.cirl.missouri.edu";
$DB_DBNAME = "kinectrewind";

// Load UserIDs from database
$roomsMap = getPatientsIDArray();


$GLOBALS['MAX_VIDEO_LIST_COUNT'] = 96;
$GLOBALS['MIN_VIDEO_SIZE'] = 200*1024;


////Working Config "REMOTE-DB"------------------
$GLOBALS['BASE_VIDEO_PATH'] = '/mnt/mcpweb/';
$GLOBALS['FILE_BASE_VIDEO_PATH'] = '/mnt/mcpweb/';
$GLOBALS['IS_REMOTE_PATH'] = 1;
$GLOBALS['IS_DB_ORIENTED'] = 1;

function patientIDSelector(){
	$html_output="";
	foreach($GLOBALS['roomsMap'] as $key => $value){
		$html_output .= '<option value="' . $key . '">' . $value . '</option>'."\n";
	}
	return $html_output;
}

?>