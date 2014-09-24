<link type="stylesheet/css" href="css/example.css">
<?php
require 'includes/session.php';

//if(isset($_SESSION['uid'])&&isset($_SESSION['utype'])&&!empty($_SESSION['uid'])&&!empty($_SESSION['utype'])&&$_SESSION['uid']!=NULL){

require 'includes/dbQuery.php';
require 'includes/usefulFunctions.php';

echo "<div class='autosize'>";
//insert into Query VALUES(74, 'S', 'select alert.id as id, alert.UserID as uid, alert.AlertName as alert, alert.Occurred as date, alg.Description as descr from tblalertlog alert, tblalertalg alg where alert.id=? AND alert.AlgorithmID=alg.AlgorithmID', 2, 'Get alert information for email', 1); 
if(isset($_GET['alert'])&&!empty($_GET['alert'])){
	$alertid=htmlentities($_GET['alert'], ENT_QUOTES);
	$params=array($alertid);
	$results=array('id','uid','alert','date','descr', 'algid');
	$query_results=DB_Query(74, NULL, $params, $results);
	//echo_r($query_results);
	$start_date=date("Y-m-d", strtotime($query_results[0]['date'])-604800);
	$end_date=date("Y-m-d", strtotime($query_results[0]['date'])+604800);
	
	$start_date=date_from_DB($start_date);
	$end_date=date_from_DB($end_date);
	
	$date=date("Y-m-d", strtotime($query_results[0]['day']));
	$resid=$query_results[0]['UserID'];
echo"<form method='post' action='submit_feedback.php'><fieldset>
<input type='hidden' name='alertid' value ='".$alertid."'/>
<input type='hidden' name='date' value='$date'/>
<input type='hidden' name='algid' value='".$query_results[0]['Algorithm']."'/>
<input type='hidden' name='descr' value='".$query_results[0]['Name']."'/>
<input type='hidden' name='resid' value='".$query_results[0]['UserID']."'/>
<h2>Alert Feedback<br/></h2>
<pre><b>Resident:</b>$resid  <b>Date:</b>".$query_results[0]['day']." <b>Alert:</b>".$query_results[0]['Name']."</pre></p>

<p>
<h3>Rating<br/></h3>
Please rate the significance of the alert (1 = insignificant, 5 = very significant)<br/><br/>
1   <input type='radio' value=1 name='rating'>
<input type='radio' value=2 name='rating'>
<input type='radio' value=3 name='rating' checked>
<input type='radio' value=4 name='rating'>
<input type='radio' value=5 name='rating'>    5
</p>
<p>
<h3>Thoughts<br/></h3>
Resident suspected to be out of apartment:<input type = 'checkbox' value='Out of apartment' name='awayFromHome'><br />
There was a sensor failure and/or power outage:<input type='checkbox' value='Sensor Failure' name='sensor_failure'><br/>
There was a visitor present in the apartment:<input type = 'checkbox' value='Visitor Present' name='visitor_present'>
</p>
<p>
<h3>Comments<br/></h3>
<textarea rows='5' cols='60' name='comments' wrap='physical'></textarea>
</p>
<p>
<h3>Perspective:</h3>
<select name='perspective'>
<option value=0>Other-Clinician</option>
<option value=1>TP Care Provider</option>
<option value=1>CedarFalls Care Provider</option>
</select>
</p>

<h3>Review Time:</h3>

<select name='review_time'>
<option value=0>Under 1 minute</option>
<option value=1>1-2 minutes</option>
<option value=2>2-3 minutes</option>
<option value=3>3-4 minutes</option>
<option value=4>Over 4 minutes</option>
</select>


<p>
<input type='submit' value='submit' name='submit'>
</p>


</center></fieldset></form>";
//	echo $resid;
//	echo "<br/> $start_date, $end_date";
	
	
	
}

echo "</div>";

/*}
else{
	session_destroy();
	echo '<h1>You have been logged out.</h1>';
	echo '<h2><a href=\'index.php\'>Click Here to Log back in</a></h2>';
	exit(1);

}*/
?>
