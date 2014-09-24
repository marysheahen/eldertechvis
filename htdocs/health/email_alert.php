<?php
require 'includes/session.php';
if(isset($_SERVER["REMOTE_USER"])&&!empty($_SERVER['REMOTE_USER'])){ 

if(!isset($_SESSION['uid'])||!isset($_SESSION['utype'])||empty($_SESSION['uid'])||empty($_SESSION['utype'])||$_SESSION['uid']==NULL){
	$_SESSION['uid']=$_SERVER['REMOTE_USER'];
	$_SESSION['utype']='ICLIN';
}

require 'includes/dbQuery.php';
require 'includes/usefulFunctions.php';

print "<div class='autosize'>";
//insert into Query VALUES(74, 'S', 'select alert.id as id, alert.UserID as uid, alert.AlertName as alert, alert.Occurred as date, alg.Description as descr from tblalertlog alert, tblalertalg alg where alert.id=? AND alert.AlgorithmID=alg.AlgorithmID', 2, 'Get alert information for email', 1); 
if(isset($_GET['alert'])&&!empty($_GET['alert'])){
	$alertid=htmlentities($_GET['alert'], ENT_QUOTES);
	$params=array($alertid);
	$results=array('id', 'day', 'Name', 'Notes', 'AlgID', 'Algorithm', 'UserID');
	$query_results=DB_Query(74, NULL, $params, $results);
	//print_r($query_results);
	if(empty($query_results)){
		print "<h1>This alert is not yet in database. Most likely the new alerts did not get replicated to the database yet</h1>";
		exit(1);
	}
	print "<h1>Waiting For Database Queries to Run</h1>";
	print "<h2>Resident: ".$query_results[0]['UserID']."</h2>";
	print "<h2>Date: ".$query_results[0]['day'];
	print "<h2>Alert: ".$query_results[0]['Name'].$query_results[0]['Notes'].$query_results[0]['AlgID'];
	
	//print_r($query_results);
	$start_date=date("Y-m-d", strtotime($query_results[0]['day'])-604800*4);
     
	$end_date=date("Y-m-d", strtotime($query_results[0]['day'])+24*3600);
	// print $end_date;
	//$start_date=date_from_DB($start_date);
	//$end_date=date_from_DB($end_date);
	
	
	$algorithm=$query_results[0]['Algorithm'];
	$resid=$query_results[0]['UserID'];
	$curr = strtotime($query_results[0]['day'])*1000;
	//echo $query_results[0]['Algorithm'];
//	print $resid;
//	print "<br/> $start_date, $end_date";
	if(is_int(stripos($query_results[0]['Name'], 'Motion Density'))){
	$end_date=date("Y-m-d", strtotime($query_results[0]['day']));
	//$start_date=date("Y-m-d", strtotime($query_results[0]['day']."- 6 months"));
		$_SESSION['end_date'] = $end_date;
		$_SESSION['start_date'] = $start_date;
		print "<form name='make_chart' method='post' action='https://vis2.eldertech.missouri.edu/health/density2.php?resid=".$query_results[0]['UserID']."'>";
	}
	else if(is_int(stripos($query_results[0]['Name'], 'Pulse'))){
		print "<form name='make_chart' method='post' action='https://vis2.eldertech.missouri.edu/health/'>";
		print "<input type='hidden' name='resident' value='$resid' />";
		print "<input type='hidden' name='EndDate' value='$end_date' />";
		print "<input type='hidden' name='StartDate' value='$start_date' />";
		print "<input type='hidden' name='timetoggle' value='Daily' />";
		print "<input type='hidden' name=''HydroPulse' value='on'/>";
		print "<input type='hidden' name='alg' value='$algorithm' />";
	}
	else{
	print "<form name='make_chart' method='post' action='https://vis2.eldertech.missouri.edu/health/'>";
	print "<input type='hidden' name='resident' value='$resid' />";
	print "<input type='hidden' name='EndDate' value='$end_date' />";
	print "<input type='hidden' name='StartDate' value='$start_date' />";
	print "<input type='hidden' name='timetoggle' value='Daily' />";
	print "<input type='hidden' name='alg' value='$algorithm' />";
}
	print "<script language='JavaScript' type='text/javascript'>
document.make_chart.submit();
</script>";
	
}

print "</div>";
include 'Global_Pages/Footer.php';
}
else{
		session_destroy();
	echo '<h1>You have been logged out.</h1>';
	echo '<h2><a href=\'index.php\'>Click Here to Log back in</a></h2>';
	exit(1);
}
?>