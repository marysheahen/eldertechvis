<?php
include('../player_functions_db.php');
include('../../common/Config.php');
include('../database/Init_DB.php');

if (isset($_POST['file_path']) && isset($_POST['roomNumber']) && isset($_POST['videoDateTime'])) {
	$file_path=$_POST['file_path'];
	$ROOM_NUMBER=$_POST['roomNumber'];
	$VIDEO_DATE_TIME=$_POST['videoDateTime'];

	$resultArray =runService($ROOM_NUMBER,$file_path,$VIDEO_DATE_TIME);
	$resultArray = json_encode($resultArray, JSON_NUMERIC_CHECK);
	print_r($resultArray);
}else{
	print_r('null');
}

function runService($ROOM_NUMBER,$file_path,$VIDEO_DATE_TIME){
	//echo 'here33>';
	$row = 1;
	$resultArray = array();

	//$file_path=str_replace('/mnt/mcpweb/','/mnt/mcpweb/',$file_path);
	$VIDEO_DATE = substr($VIDEO_DATE_TIME,0,10);
	//$DB_DATE_TIMES = ['2014/04/07 11:58:42','2014/04/07 11:59:42'];
	$DB_DATE_TIMES = find_falls_within_video($ROOM_NUMBER,$VIDEO_DATE_TIME,null);

	$DB_DATE_TIMEStamps = array();
	foreach($DB_DATE_TIMES as $DB_DATE_TIME){
		array_push($DB_DATE_TIMEStamps, strtotime($DB_DATE_TIME));
	}

	if (($handle = fopen ( $file_path, "r" )) !== FALSE) {
		$preDate0 = 0;
		$addedDay = 0;

		while ( ($data = fgetcsv ( $handle, 30, "\t" )) !== FALSE ) {
			$data0TimeStamp = timeToTimeStamp($VIDEO_DATE,$data[0],$addedDay);
			//at midNight add oneDay
			if($data0TimeStamp<$preDate0){
				$addedDay = 1;
				$data0TimeStamp+=86400;
			}
			$preDate0 = $data0TimeStamp;

			$theValue = 0;
			foreach($DB_DATE_TIMEStamps as $DB_DATE_TIMEStamp){
				$difTime = abs($data0TimeStamp-$DB_DATE_TIMEStamp);
				//$theValueTemp = $difTime<=5?min($data[2],1):0;
				$theValueTemp = $difTime<=5?1:0;
				if($theValueTemp>$theValue){
					$theValue = $theValueTemp;
					break;
				}
			}
			$newRecord = array(
					$data[0],
					$data[2],
					$theValue
			);

			array_push($resultArray,$newRecord);
			$row ++;
		}
		fclose ( $handle );
	}

	return $resultArray;
}

function timeToTimeStamp($dateTime,$time,$dayOffset){
	return strtotime(substr($dateTime,0,10).' '.substr($time,0,8))+$dayOffset*86400;
}
?>		