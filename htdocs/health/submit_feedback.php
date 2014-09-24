<?php
require 'includes/session.php';
if(isset($_SERVER["REMOTE_USER"])&&!empty($_SERVER['REMOTE_USER'])){
	
	/*if(!isset($_SESSION['uid'])||!isset($_SESSION['utype'])||empty($_SESSION['uid'])||empty($_SESSION['utype'])){
				session_destroy();
		echo '<h1>You have been logged out.</h1>';
		echo '<h2><a href=\'../index.php\'>Click Here to Log back in</a></h2>';
	exit(1);
	}*/

//require '../Global_Pages/Header.php';
//require '../Global_Pages/Menu.php';
require 'includes/dbQuery.php';
require 'includes/usefulFunctions.php';
print "<div class='autosize'>";
	$Algorithm=htmlentities($_POST['algid'], ENT_QUOTES);
	$Alert=htmlentities($_POST['descr'], ENT_QUOTES);
	$Date=htmlentities($_POST['date'], ENT_QUOTES);
	$resid=htmlentities($_POST['resid'], ENT_QUOTES);	
	$comments=htmlentities($_POST['comments'], ENT_QUOTES);	
	$perspective=htmlentities($_POST['perspective'], ENT_QUOTES);
	$rating=htmlentities($_POST['rating'], ENT_QUOTES);
	$review_time=htmlentities($_POST['review_time'], ENT_QUOTES);
	$AlertID = htmlentities($_POST['alertid'], ENT_QUOTES);
	$invalidParam = 1;
	if (isset($_POST['invalidParam'])) {
		$invalidParam = 2;
	}
	$invalidChange = 1;
	if (isset($_POST['invalidChange'])) {
		$invalidChange = 2;
	}
	$awayFromHome = 'no';
	if(isset($_POST['awayFromHome'])) {
		$awayFromHome = 'yes';
	}
	$sensor_failure = 1;
	if(isset($_POST['sensor_failure'])) {
		$sensor_failure = 2;
	}
	$visitor_present = 1;
	if(isset($_POST['visitor_present'])) {
		$visitor_present = 2;
	}
	$SubmitTime=date("Y-m-d H:i:s");
//	$SubmitBy=$_SERVER['REMOTE_ADDR'];
	$SubmitBy=$_SERVER['PHP_AUTH_USER'];
	$Rating=$rating;
	$reviewStatus = 'open';
//	$thoughts=null;
//	$thoughts=$_POST['thoughts'];
//	$notes = "Perspective:".$perspective.";Algorithm:".$alg_id.";";
//if ($thoughts!=null) {
//	$notes = $notes."Thoughts:";
//	foreach ($thoughts as $t) {
//		$notes = $notes."[".$t."]";
//	}
//	$notes = $notes.";";
//}

//if (!empty($comments)) {
//	$notes = $notes."Comments:".$comments.";";
//}
//OLD FEEDBACK INSERT --Dane 11/23/2010
//insert into Query VALUES(75, 'S', 'insert into tblfeedback(UserID, Alert, Date, SubmitTime, SubmitBy, Notes, Rating, Algorithm) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', 2, 'Insert feedback', 1);
//$params=array($resid, $Alert, $Date, $SubmitTime, $SubmitBy, $notes, $Rating, $Algorithm);

//NEW FEEDBACK INSERT --Dane 11/23/2010
// INSERT INTO feedback_new (alert_id, submit_by, submit_time, perspective_id, rating, comments, invalid_param, invalid_change, resident_away) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)
//echo $AlertID." ".$SubmitBy." ".$SubmitTime." ".$perspective." ".$Rating." ".$comments." ".$invalidParam." ".$invalidChange." ".$awayFromHome." ".$reviewStatus." ".$review_time." ".$sensor_failure." ".$visitor_present;
//New feedback insert--tatiana 5/15/2012, added review time, DB query updated
$params=array($AlertID, $SubmitBy, $SubmitTime, $perspective, $Rating, $comments, $invalidParam, $invalidChange, $awayFromHome, $reviewStatus, $review_time, $sensor_failure, $visitor_present);


$results=array();
DB_Query(80, NULL, $params, $results);
print "<h2>Feedback Submitted Successfully! Thank you.</h2>";
print "</div>";

}

else{
	print "<h1>You are not authorized to do anything.</h1>";
	exit(1);
}

?>
