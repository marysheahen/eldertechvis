<?php
/*
 * Author: Moein
*
* Goal:
* 	This script suposed to just provide a database connection for all other scripts, 
* but now it has some database-related functions as well.
* 	These functions maily work with the files(txt/mp4) and create a record for them in the database.
* 	auto_update_all_falls() and auto_update_all_videos() will find the newly added files for each user
* sience the latest database record and update database with those records. 
*/

$FILE_DATE_FORMAT ='m_d_Y-H_i_s';
$COMPARE_DATE_FORMAT ='Y/m/d H:i:s';

$FOLDER_DATE_FORMAT ='m_d_Y';
$FOLDER_COMPARE_DATE_FORMAT ='Y/m/d';


function auto_update_all_falls(){
	/*	Finds the latest record for each user and base on that date, searches for the new files
	 * 	If there were no data in the database, it will seach all the folders, otherwise it will just 
	 * update records related for the existing users in the database.
	 * 	NOTE:
	 * 	I you have a new user, you need to add an empty record for him, to this table first.
	 */
	//1. extract max date-time from database
	$link = mysqli_connect($GLOBALS['DB_HOSTNAME'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_DBNAME'])
	or die("Unable to connect to MySQL");
	echo "Connected to MySQL<br>";
	
	$sqlString = "SELECT userid, MAX(VideoDateTimeString) as maxDateTime FROM tblkinectfalls WHERE BaseVideoPath='".$GLOBALS['BASE_VIDEO_PATH']."' GROUP BY userid;";
	
	$MySQLResult = $link->query($sqlString)
		or die("Unable to run the select query");

	if (!$MySQLResult) {
		echo "Could not successfully run query from DB: " . mysql_error();
		exit;
	}
	if (mysqli_num_rows($MySQLResult) == 0) {
		echo "No rows found, nothing to print so am starting from the scratch<br>";

		extract_all_sub_falls_filtered(null,null,null,TRUE);	
	}else{
		while ($row = $MySQLResult->fetch_assoc()) {
			$userID = $row["userid"];
			$startDate = $row["maxDateTime"];
			echo 'inserting for user:'.$userID." from date:".$startDate."<br>";
			extract_all_sub_falls_filtered($userID,$startDate,null,TRUE);
		}
	}

	//close the connection
	mysql_close($link);
}

function extract_all_sub_falls_filtered($userID,$startDate,$endDate,$saveToDB){
	//$userID = '102';//delete me
	set_time_limit(0);

	$totalRowsInserted = 0;
	$resultVideos = array();//new ArrayObject();
	//2011_12_09
	$FOLDER_DATE_REGEX_ARRAY=[
								//'09_30_2013',
								'[0-9][0-9][0-9][0-9]_[0-9][0-9]_[0-9][0-9]',
								'[0-9][0-9]_[0-9][0-9]_[0-9][0-9][0-9][0-9]',
							];

	//z:/100
	$UserFolders = null;

	if(is_null($userID))
		$UserFolders = glob($GLOBALS['BASE_VIDEO_PATH'].'[0-9]*', GLOB_ONLYDIR);
	else
		$UserFolders = Array($GLOBALS['BASE_VIDEO_PATH'].$userID);
	//print_r($UserFolders);
	//echo("<br>");

	
	foreach($UserFolders as $userKey => $userFolderPath){
		//UserID
		$userID = pathinfo($userFolderPath)['filename'];
		//print_r($userFolderPath.'| '.$userID.' | '.$totalRowsInserted.'|<br>');
		
		//AvailableDates
		////z:/100/KinectData/FallData/08_02_2013
		$userAllDatesPath = array();
		foreach($FOLDER_DATE_REGEX_ARRAY as $FOLDER_DATE_REGEX){	
			$userAllDatesPath = array_merge($userAllDatesPath , glob($userFolderPath.'/KinectData/FallData/'.$FOLDER_DATE_REGEX, GLOB_ONLYDIR));
		}
		//print_r($userAllDatesPath);
		//echo("<hr>");
		
		//Filter folders by startDate & endDate
		$userAllDatesPath = array_filter($userAllDatesPath,
				function($userDatePath) use($startDate,$endDate){
					$folderDateString = pathinfo($userDatePath)['filename'];
					$boolVal = False;
					//
					foreach(['Y_m_d','m_d_Y'] as $dateFormat){
						$folderDateObject = datetime::createfromformat($dateFormat,$folderDateString);
						if($folderDateObject==null || $folderDateObject==''){
						}else{
							$folderDateStringFormatted = $folderDateObject->format($GLOBALS['FOLDER_COMPARE_DATE_FORMAT']);
							$boolVal = $boolVal || ((is_null($startDate) || $folderDateStringFormatted>=substr($startDate,0,14)) &&
								(is_null($endDate) || $folderDateStringFormatted<=substr($endDate,0,19)));
							//echo $dateFormat.' '.$folderDateStringFormatted.' '.$boolVal.'<br>';
						}
					}
								
					return $boolVal;
				});

		//print_r($userAllDatesPath);
		//echo("++<br>");
		
		foreach($userAllDatesPath as $dateKey => $userDatePath){
			echo '['.$userDatePath.']<br>';
			
			//videoFiles for that user in that date
			////z:/100/KinectData/FallData/08_02_2013/KDSmall-12_09_2011-11_26_36_627.mp4
			$userAllVideosPath = array();
			foreach($FOLDER_DATE_REGEX_ARRAY as $FOLDER_DATE_REGEX){
				foreach(['','[0-9][0-9][0-9][0-9]-','[0-9][0-9][0-9][0-9][0-9]-'] as $prefix){		
					//echo '>>'.$userDatePath.'/'.$prefix.$FOLDER_DATE_REGEX.'*.mp4'.'<br>';
					$userAllVideosPath = array_merge($userAllVideosPath , glob($userDatePath.'/'.$prefix.$FOLDER_DATE_REGEX.'*'.$fileType));
				}
			}
			//print_r($userAllVideosPath);
			
			foreach($userAllVideosPath as $videoKey => $userVideoPath){
				$userVideoName = pathinfo($userVideoPath)['filename'];
				$len = strlen($userVideoName);
				//echo $userVideoName.' '.$len."*<br>";
				//extract date_time from video name
				$videoDateTimeString = substr($userVideoName,$len-23,19);
				//echo $videoDateTimeString."**<br>";

				$videoDateTimeObject = datetime::createfromformat('m_d_Y-H_i_s',$videoDateTimeString);
				//print_r($videoDateTimeObject);
				if($videoDateTimeObject==null){
					$videoDateTimeObject = datetime::createfromformat('Y_m_d-H_i_s',$videoDateTimeString);
					if($videoDateTimeObject==null){
						continue;
					}
				}
				$videoDateTimeStringFormatted = $videoDateTimeObject->format($GLOBALS['COMPARE_DATE_FORMAT']);
				//echo($videoDateTimeStringFormatted."****<br>");

				//print_r($videoDateTimeStringFormatted.'&nbsp;&nbsp;|&nbsp;&nbsp;');

				//filter videos by date-time
				if ((is_null($startDate) || $videoDateTimeStringFormatted>=$startDate) && (is_null($endDate) || $videoDateTimeStringFormatted<=$endDate)){
					$VideoInfo = array(
							'UserID'=>$userID,
							'VideoPath'=>str_replace($GLOBALS['BASE_VIDEO_PATH'],'',$userVideoPath),
							//'BaseVideoPath'=>$GLOBALS['BASE_VIDEO_PATH'],
							'BaseVideoPath'=>$GLOBALS['BASE_VIDEO_PATH'],
							'VideoDateTimeString'=>$videoDateTimeStringFormatted,
							'VideoName'=>$userVideoName,
							'VideoSize'=>filesize($userVideoPath)
					);
					//print_r($userVideoName.' | ');
					//print_r($VideoInfo);
					//$resultVideos = $resultVideos->append($VideoInfo);
					array_push($resultVideos,$VideoInfo);
					$totalRowsInserted++;
				}
				else
					continue;

			}
			//foreach date we will save the results into the DB and clean up the array
			//if($saveToDB){
				//echo '<br><br>';
				//print_r($resultVideos);
				//add_array_of_falls_to_database($resultVideos);
			//}
			//unset($resultVideos);
			//$resultVideos = array();//ArrayObject();

			//echo('<br><b>'.$totalRowsInserted.'</b><hr>');
		}
	}

	if($saveToDB){
		//echo '<br><br>';
		//print_r($resultVideos);
		add_array_of_falls_to_database($resultVideos);
	}
	unset($resultVideos);
	$resultVideos = array();//ArrayObject();

	echo('<br><h1>'.$totalRowsInserted.'</h1>');
	set_time_limit(30);
}

function add_array_of_falls_to_database($videosArray){
	//echo 'the array<br>';
	//print_r($videosArray);
	//connection to the database
	$mysqli = new mysqli($GLOBALS['DB_HOSTNAME'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_DBNAME'])
	or die("Unable to connect to MySQL");
	echo "Connected to MySQL<br>";

	$stmt = $mysqli->prepare("INSERT INTO tblkinectfalls (UserID, VideoDateTimeString, VideoPath, BaseVideoPath , VideoSize) VALUES(?, ?, ?, ?, ?)")
	or die("Unable to create Prepared Statement");
	echo "Prepared Statement is created<br>";

	try{
		foreach( $videosArray as $row ) {
			$UserID = $row['UserID'];
			$VideoDateTimeString = $row['VideoDateTimeString'];
			$VideoPath = $row['VideoPath'];
			$BaseVideoPath = $row['BaseVideoPath'];
			$VideoSize = $row['VideoSize'];

			$stmt->bind_param('isssi', $UserID, $VideoDateTimeString, $VideoPath, $BaseVideoPath, $VideoSize);
			$stmt->execute()
			or print_r('Exception on INSERT ['.$row['UserID'].' | '. $row['VideoDateTimeString'].' | '. $row['VideoPath'].'] <br> '.$stmt->error);
			//echo "insert done<br>";
		}
	}catch(Exception $err){
		echo "Failed to insert to MySQL: " . $err;
	};

	//close the connection
	mysqli_close($mysqli);
}

function auto_update_all_videos(){
	/*	Finds the latest record for each user and base on that date, searches for the new files
	 * 	If there were no data in the database, it will seach all the folders, otherwise it will just
	* update records related for the existing users in the database.
	* 	NOTE:
	* 	I you have a new user, you need to add an empty record for him, to this table first.
	*/	
	//1. extract max date-time from database
	$link = mysqli_connect($GLOBALS['DB_HOSTNAME'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_DBNAME'])
		or die("Unable to connect to MySQL");
	echo "Connected to MySQL<br>";
	
	$sqlString = "SELECT userid, MAX(VideoDateTimeString) as maxDateTime FROM tblkinectvideos WHERE BaseVideoPath='".$GLOBALS['BASE_VIDEO_PATH']."' GROUP BY userid;";
	
	$MySQLResult = $link->query($sqlString)
		or die("Unable to run the select query");
	if (!$MySQLResult) {
		echo "Could not successfully run query from DB: " . mysql_error();
		exit;
	}
	if (mysqli_num_rows($MySQLResult) == 0) {
		echo "No rows found, nothing to print so am starting from the scratch<br>";
		extract_all_sub_videos_filtered(null,null,null,TRUE);
		
	}else{
		while ($row = $MySQLResult->fetch_assoc()) {
			$userID = $row["userid"];
			$startDate = $row["maxDateTime"];
		
			extract_all_sub_videos_filtered($userID,$startDate,null,TRUE);
		}
	}	
	//close the connection
	mysql_close($link);
}

function extract_all_sub_videos_filtered($userID,$startDate,$endDate,$saveToDB){
	
	set_time_limit(0);

	$totalRowsInserted = 0;
	$resultVideos = new ArrayObject();
	//12_09_2011
	$FOLDER_DATE_REGEX = '[0-9][0-9]_[0-9][0-9]_[0-9][0-9][0-9][0-9]';



	//z:/100
	$UserFolders = null;
	
	if(is_null($userID)){
		$UserFolders = glob($GLOBALS['BASE_VIDEO_PATH'].'[0-9]*', GLOB_ONLYDIR);
	}else{ 
		$UserFolders = Array($GLOBALS['BASE_VIDEO_PATH'].$userID);
		print_r($GLOBALS['BASE_VIDEO_PATH'].$userID);
	}
		
	foreach($UserFolders as $userKey => $userFolderPath){
		//UserID
		$userID = pathinfo($userFolderPath)['filename'];
		print_r($userFolderPath.'| '.$userID.' | '.$totalRowsInserted.'|<br>');
		
		//AvailableDates
		////z:/100/KinectData/12_09_2011
		$userAllDatesPath =  glob($userFolderPath.'/KinectData/'.$FOLDER_DATE_REGEX, GLOB_ONLYDIR);

		//Filter folders by startDate & endDate
		$userAllDatesPath = array_filter($userAllDatesPath,
										function($userDatePath) use($startDate,$endDate){
											$folderDateString = pathinfo($userDatePath)['filename'];
											$folderDateObject = datetime::createfromformat($GLOBALS['FOLDER_DATE_FORMAT'],$folderDateString);
											$folderDateStringFormatted = $folderDateObject->format($GLOBALS['FOLDER_COMPARE_DATE_FORMAT']);
											return ((is_null($startDate) || $folderDateStringFormatted>=substr($startDate,0,10)) && 
													(is_null($endDate) || $folderDateStringFormatted<=substr($endDate,0,10)));
										});
		//print_r($userAllDatesPath);
		foreach($userAllDatesPath as $dateKey => $userDatePath){
			$userDateString = pathinfo($userDatePath)['filename'];

			//videoFiles for that user in that date
			////z:/100/KinectData/12_09_2011/KDSmall-12_09_2011-11_26_36_627.mp4
			$searchString = $userDatePath.'/KDSmall*'.$GLOBALS['fileType'];
			$userAllVideosPath = glob($searchString);
			
			foreach($userAllVideosPath as $videoKey => $userVideoPath){
				$userVideoName = pathinfo($userVideoPath)['filename'];
					
				//extract date_time from video name
				$videoDateTimeString = substr(str_replace(array('ForeSmall','KDSmall'),'',$userVideoName),1,19);
				$videoDateTimeObject = datetime::createfromformat($GLOBALS['FILE_DATE_FORMAT'],$videoDateTimeString);
				$videoDateTimeStringFormatted = $videoDateTimeObject->format($GLOBALS['COMPARE_DATE_FORMAT']);
					
				print_r($videoDateTimeStringFormatted.'&nbsp;&nbsp;|&nbsp;&nbsp;');
				
				//filter videos by date-time
				if ((is_null($startDate) || $videoDateTimeStringFormatted>=$startDate) && (is_null($endDate) || $videoDateTimeStringFormatted<=$endDate)){
					$VideoInfo = array(
							'UserID'=>$userID,
							'VideoPath'=>str_replace($GLOBALS['BASE_VIDEO_PATH'],'',$userVideoPath),
							'BaseVideoPath'=>$GLOBALS['BASE_VIDEO_PATH'],
							'VideoDateTimeString'=>$videoDateTimeStringFormatted,
							'VideoName'=>$userVideoName,
							'VideoSize'=>filesize($userVideoPath)
					);
					//print_r($userVideoName.' | ');
					$resultVideos->append($VideoInfo);
					$totalRowsInserted++;
				}
				else
					continue;
				
				
			}
			//foreach date we will save the results into the DB and clean up the array
			if($saveToDB){
				add_array_of_videos_to_database($resultVideos);
			}
			unset($resultVideos);
			$resultVideos = new ArrayObject();
			
			echo('<b>'.$totalRowsInserted.'</b><hr>');
		}
	}

	if($saveToDB){
		add_array_of_videos_to_database($resultVideos);
	}
	unset($resultVideos);
	$resultVideos = new ArrayObject();
	
	echo('<br><h1>'.$totalRowsInserted.'</h1>');
	set_time_limit(30);
	// 		return $resultVideos;
}
function extract_all_sub_videos($basePath){
	set_time_limit(0);

	$totalRowsInserted = 0;
	$resultVideos = new ArrayObject();
	//12_09_2011
	$FOLDER_DATE_REGEX = '[0-9][0-9]_[0-9][0-9]_[0-9][0-9][0-9][0-9]';


	//z:/100
	$UserFolders = glob($GLOBALS['BASE_VIDEO_PATH'].'[0-9]*', GLOB_ONLYDIR);
	foreach($UserFolders as $userKey => $userFolderPath){
		//UserID
		$userID = pathinfo($userFolderPath)['filename'];
		print_r($userFolderPath.'| '.$userID.' | '.$totalRowsInserted.'|<br>');

		//AvailableDates
		////z:/100/KinectData/12_09_2011
		$userAllDatesPath =  glob($userFolderPath.'/KinectData/'.$FOLDER_DATE_REGEX, GLOB_ONLYDIR);
		//print_r($userAllDatesPath);
		foreach($userAllDatesPath as $dateKey => $userDatePath){
			$userDateString = pathinfo($userDatePath)['filename'];
			//print_r($userDateString.' | ');

			//videoFiles for that user in that date
			////z:/100/KinectData/12_09_2011/KDSmall-12_09_2011-11_26_36_627.mp4
			$userAllVideosPath = glob($userDatePath.'/KDSmall*'.$fileType);

			foreach($userAllVideosPath as $videoKey => $userVideoPath){
				$userVideoName = pathinfo($userVideoPath)['filename'];
					
				//extract date_time from video name
				$videoDateTimeString = substr(str_replace(array('ForeSmall','KDSmall'),'',$userVideoName),1,19);
				$videoDateTimeObject = datetime::createfromformat($GLOBALS['FILE_DATE_FORMAT'],$videoDateTimeString);
				$videoDateTimeStringFormatted = $videoDateTimeObject->format($GLOBALS['COMPARE_DATE_FORMAT']);
					
				$VideoInfo = array(
						'UserID'=>$userID,
						'VideoPath'=>str_replace($GLOBALS['BASE_VIDEO_PATH'],'',$userVideoPath),
						'BaseVideoPath'=>$GLOBALS['BASE_VIDEO_PATH'],
						'VideoDateTimeString'=>$videoDateTimeStringFormatted,
						'VideoName'=>$userVideoName,
						'VideoSize'=>filesize($userVideoPath)
				);
				//print_r($userVideoName.' | ');
				$resultVideos->append($VideoInfo);
				$totalRowsInserted++;
			}
			//foreach date we will save the results into the DB and clean up the array
			add_array_of_videos_to_database($resultVideos);
			unset($resultVideos);
			$resultVideos = new ArrayObject();
				
			echo('<hr>');
		}

		echo('<br><h2>'.$totalRowsInserted.'</h2>');
		echo('<hr>');
	}

	echo('<br><h1>'.$totalRowsInserted.'</h1>');
	set_time_limit(30);
	// 		return $resultVideos;
}

function add_array_of_videos_to_database_TEST(){
	$videosArray = array(array('UserID'=>1021,'VideoDateTimeString'=>'2014_02_13_05_10_30','VideoPath'=>'c:/testFolder/anotherFolder/theFile.mp4','VideoSize'=>101));
	add_array_of_videos_to_database($videosArray);
}
function add_array_of_videos_to_database($videosArray){
	//connection to the database
	$mysqli = new mysqli($GLOBALS['DB_HOSTNAME'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_DBNAME']) 
	  or die("Unable to connect to MySQL".$mysqli->error);
	echo "Connected to MySQL<br>";
	$stmt = $mysqli->prepare("INSERT INTO tblkinectvideos (UserID, VideoDateTimeString, VideoPath, BaseVideoPath , VideoSize) VALUES(?, ?, ?, ?, ?)")
		or die("Unable to create Prepared Statement 208".$mysqli->error)
	;
// 	echo "Prepared Statement is created<br>";
	
	try{
		foreach( $videosArray as $row ) {
			$UserID = $row['UserID'];
			$VideoDateTimeString = $row['VideoDateTimeString'];
			$VideoPath = $row['VideoPath'];
			$BaseVideoPath = $row['BaseVideoPath'];
			$VideoSize = $row['VideoSize'];
				
		    //echo ('Inserting '.$UserID.'  '. $VideoDateTimeString.'  '. $VideoPath.'  '. $BaseVideoPath.'  '. $VideoSize.'<br>');
		    
			$stmt->bind_param('isssi', $UserID, $VideoDateTimeString, $VideoPath, $BaseVideoPath, $VideoSize);
		    $stmt->execute();
		    	//or print_r('Exception on INSERT ['.$row['UserID'].' | '. $row['VideoDateTimeString'].' | '. $row['VideoPath'].'] <br> ');
		}
	}catch(Exception $err){
		echo "Failed to insert to MySQL: " . $err;
	};

	//close the connection
	mysqli_close($mysqli);
}

function add_array_of_texts_to_database($textsArray){
	//connection to the database
	$mysqli = new mysqli($GLOBALS['DB_HOSTNAME'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_DBNAME'])
	or die("Unable to connect to MySQL");
	echo "Connected to MySQL<br>";
	
	$sqlString = "INSERT INTO tblkinecttexts 
	(VideoUniqIndex,TimeStamp,FlagValue)
	VALUES ".implode(', ',$textsArray).' ;';
	
	//echo $sqlString;
	//exit;
	
	$stmt = $mysqli->prepare($sqlString)
	or die("Unable to create Prepared Statement 245");
	// 	echo "Prepared Statement is created<br>";

	try{
		$stmt->execute();
	}catch(Exception $err){
		echo "Failed to insert to MySQL: " . $err;
	};
	

// 	try{
// 		foreach( $textsArray as $row ) {
// 			$UserID = $row['UserID'];
// 			$FilePath = $row['FilePath'];
// 			$FileDate = $row['FileDate'];
// 			$TimeStamp = $row['TimeStamp'];
// 			$FlagValue = $row['FlagValue'];
// 			$BaseVideoPath = $row['BaseVideoPath'];
	
// 			$stmt->bind_param('isssis', $UserID, $FilePath, $FileDate, $TimeStamp, $FlagValue, $BaseVideoPath);
// 			$stmt->execute();
// 			//or print_r('Exception on INSERT ['.$row['UserID'].' | '. $row['VideoDateTimeString'].' | '. $row['VideoPath'].'] <br> ');
// 		}
// 	}catch(Exception $err){
// 		echo "Failed to insert to MySQL: " . $err;
// 	};
	
	//close the connection
	mysqli_close($mysqli);
}

function select_falls_by_roomnumber($roomNumber){
	//1. extract max date-time from database
	$link = mysqli_connect($GLOBALS['DB_HOSTNAME'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_DBNAME'])
	or die("Unable to connect to MySQL");
	//echo "Connected to MySQL<br>";
	
	$sqlString = "SELECT unix_timestamp(substring(VideoDateTimeString,1,10)) as Date,count(1) numFalls FROM tblkinectfalls".
								" WHERE BaseVideoPath='".$GLOBALS['BASE_VIDEO_PATH']."'  AND UserId='".$roomNumber."'".
								" GROUP BY substring(VideoDateTimeString,1,10)";
	$MySQLResult = $link->query($sqlString)
				or die("Unable to run the select query");
	
	if (!$MySQLResult) {
		echo "Could not successfully run query from DB: " . mysql_error();
		exit;
	}
	
	$result_array =array();
	if (mysqli_num_rows($MySQLResult) == 0) {
		//echo "No rows found, nothing to print so am starting from the scratch<br>";
	}else{
		while ($row = $MySQLResult->fetch_assoc()) {
			$date = $row["Date"];
			$numFalls = intval($row["numFalls"]);
			//adding new items to the array
			$result_array[$date] = $numFalls;
			//echo 'Date:'.$date.' numFalls:'.$numFalls.'<br>';
		}
		//print_r($result_array);
	}
	
	//close the connection
	mysql_close($link);
	return $result_array;
}