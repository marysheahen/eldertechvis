<?php
global $BASE_VIDEO_PATH;
global $FILE_BASE_VIDEO_PATH;
global $IS_DB_ORIENTED;
global $IS_REMOTE_PATH;
global $MAX_VIDEO_LIST_COUNT;
global $MIN_VIDEO_SIZE;
global $DB_DBNAME;


//Database:--------------------------
$DB_USERNAME = "kinectrewindmgr";
$DB_PASSWORD = "8ke8SyAJ5ebW";
$DB_HOSTNAME = "robby.cirl.missouri.edu";
$DB_DBNAME = "kinectrewind";

$roomsMap = array(
	"100"=>"3004",
	"200"=>"3012",
	"3014"=>"3014",
	"104"=>"3017",
	"102"=>"3038",
	"216"=>"3045",
	"111"=>"3053",
	"112"=>"3054",
	"114"=>"3056",
	"201"=>"3057",
	"3061"=>"3061"
);


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