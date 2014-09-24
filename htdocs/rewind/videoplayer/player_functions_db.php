<?php
	$USER_ID_MAP = array();
	$FIRST_DATE='2009/01/01';
	$LIST_DATE_FORMAT ='Y/M/d , H:i:s';
	$COMPARE_DATE_FORMAT ='Y/m/d H:i:s';
	$FOLDER_COMPARE_DATE_FORMAT ='Y/m/d';
	$FILE_DATE_FORMAT ='m_d_Y-H_i_s';
	$FOLDER_DATE_FORMAT = 'Y/m/d';
	
	$START_DATE=NULL;
	$END_DATE=NULL;
	$ROOM_NUMBER=100;
	$ONLY_FALLS=0;

	//Extract POST parameters
	//print_r($_POST);
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
	
	$VIDEO_LIST = null;


	/*Method:find_patient_videos ************************************************************************************
	Description:
		This method would be used to find all videos recorded in between startDate and endDate. The return value would be an array of all featured videos.
	Author:
		Moein - 2014-01-12
	Params:
		$patiendID : int value of the patientID.
		$startDate : String or Date value for the beginning of the time range.[in case of NULL or '' would be replaced by the $FIRST_DATE]
		$endDate   : String or Date value for the end of the time range.[in case of NULL or '' would be replaced by the today]
	Returns:
		$filteredfoundVideoArray : would be null in case no videos found, or contains full-path of all found videos.
	*****************************************************************************************************************/
	function find_patients_last_n_videos_twoDimensional($patiendID,$startDate,$endDate,$n,$ONLY_FALLS){
		$filteredfoundVideoArray = array();

		//fix startDate in case of null
		if($startDate==NULL||$startDate==''){$startDate = $GLOBALS['FIRST_DATE'];}
		//fix endDate in case of null
		if($endDate==NULL||$endDate==''){$endDate = date('Y/m/d');}
		
		//1. extract max date-time from database
		$link = mysqli_connect($GLOBALS['DB_HOSTNAME'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_DBNAME'])
		or die("Unable to connect to MySQL");
		//echo "Connected to MySQL<br>";

		//--------------------------------------------------------------------
		$queryString = "SELECT DISTINCT v.UserID, v.VideoDateTimeString, concat(v.BaseVideoPath, v.VideoPath) as VideoPath, CONCAT(v.VideoSize DIV 1024,' KB') as VideoSize ,(CASE WHEN ISNULL(f.VideoDateTimeString) THEN 0 ELSE 1 END) as hasFall,1 
						FROM tblkinectvideos as v LEFT OUTER JOIN tblkinectfalls as f
						ON (v.UserId=f.UserID) AND(v.BaseVideoPath=f.BaseVideoPath) AND(TIMESTAMPDIFF(SECOND,v.VideoDateTimeString,f.VideoDateTimeString) between 0 and 15*60)
							AND (v.userid=".$patiendID.") AND (v.BaseVideoPath='".$GLOBALS['BASE_VIDEO_PATH']."')
						WHERE (v.userid=".$patiendID.") AND (v.BaseVideoPath='".$GLOBALS['BASE_VIDEO_PATH']."') 
							AND (v.VideoDateTimeString>='".$startDate."')
							AND (v.VideoDateTimeString<='".$endDate."')
							AND (v.VideoSize>=".$GLOBALS['MIN_VIDEO_SIZE'].") ";
		
		if($ONLY_FALLS==="true"){
			$queryString = $queryString." AND (f.UserID>0) ";
		}
		$queryString = $queryString." ORDER BY v.VideoDateTimeString DESC ";
		if($n!=null||$n!=''){$queryString=$queryString." LIMIT ".$n.";";}
										
		//echo('<br>'.$queryString.'<br>');
		
		$MySQLResult = null;
		try{
			$MySQLResult = $link->query($queryString);
		}catch (exception $e){ throw new Exception("Unable to run the select query".$e->getMessage());}
		if (!$MySQLResult) {
			throw new Exception("Could not successfully run query from DB: " . mysql_error());
			exit;
		}
		if (mysqli_num_rows($MySQLResult) == 0) {
			//echo "No rows found, nothing to print so am starting from the scratch<br>";
		}else{
			while ($row = $MySQLResult->fetch_assoc()) {
				$userID = $row['UserID'];
				$VideoDateTimeString = $row['VideoDateTimeString'];
				$VideoPath = $row['VideoPath'];
				$VideoSize = $row['VideoSize'];
				$hasFall = $row['hasFall'];
				
				array_push($filteredfoundVideoArray,array($VideoPath,$VideoDateTimeString,$VideoSize,$hasFall));
			}
		}
		
		//close the connection
		mysql_close($link);		

		return $filteredfoundVideoArray;
	}
	
	function find_falls_within_video($patiendID,$video_date_time,$n){
		$filteredfoundVideoArray = array();
	
		//1. extract max date-time from database
		$link = mysqli_connect($GLOBALS['DB_HOSTNAME'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_DBNAME'])
		or die("Unable to connect to MySQL");
		//echo "Connected to MySQL<br>";
	
		$queryString = "SELECT VideoDateTimeString as fallTimeStamp
						FROM tblkinectfalls
						WHERE (UserID=".$patiendID.") AND (BaseVideoPath='".$GLOBALS['BASE_VIDEO_PATH']."') 
								AND (TIMESTAMPDIFF(SECOND,'".$video_date_time."',VideoDateTimeString) between 0 and 15*60);";

		if($n!=null||$n!=''){$queryString=$queryString." LIMIT ".$n.";";}
	
		//echo('<br>'.$queryString.'<br>');
	
		$MySQLResult = null;
		try{
			$MySQLResult = $link->query($queryString);
		}catch (exception $e){ throw new Exception("Unable to run the select query".$e->getMessage());}
		if (!$MySQLResult) {
			throw new Exception("Could not successfully run query from DB: " . mysql_error());
			exit;
		}
		if (mysqli_num_rows($MySQLResult) == 0) {
			//echo "No rows found, nothing to print so am starting from the scratch<br>";
		}else{
			while ($row = $MySQLResult->fetch_assoc()) {
				$fallTimeStamp = $row["fallTimeStamp"];
				array_push($filteredfoundVideoArray,$fallTimeStamp);
			}
		}
	
		//close the connection
		mysql_close($link);
	
	
		return $filteredfoundVideoArray;
	}
	
	function getPatientsIDArray(){
		$PatientIDsArray = array();
		$link = mysqli_connect($GLOBALS['DB_HOSTNAME'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_DBNAME'])
		or die("Unable to connect to MySQL");
		
		$queryString = "SELECT DISTINCT UserID,count(1) as count FROM tblkinectvideos
						GROUP BY UserID
						ORDER BY  cast(UserID as unsigned)";
		
		$MySQLResult = null;
		try{
			$MySQLResult = $link->query($queryString);
		}catch (exception $e){ throw new Exception("Unable to run the select query".$e->getMessage());}
		if (!$MySQLResult) {
			throw new Exception("Could not successfully run query from DB: " . mysql_error());
			exit;
		}
		if (mysqli_num_rows($MySQLResult) == 0) {
			//echo "No rows found, nothing to print so am starting from the scratch<br>";
		}else{
			while ($row = $MySQLResult->fetch_assoc()) {
				$UserID = $row["UserID"];
				//array_push($PatientIDsArray,$UserID);
				$PatientIDsArray[$UserID]=$UserID;
			}
		}
		
		//close the connection
		mysql_close($link);
		return $PatientIDsArray;		
	}
?>