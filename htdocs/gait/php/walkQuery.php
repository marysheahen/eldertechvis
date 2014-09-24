<?php 


  require_once 'config.php';

  //--------------------------------------------------------------------------
  // Connect to DB
  //--------------------------------------------------------------------------
  $con = mysql_connect($host,$user,$pass);
  $dbs = mysql_select_db($databaseName, $con);

  //--------------------------------------------------------------------------
  // Query database for data
  //--------------------------------------------------------------------------
  $sid = $_POST['sid'];
  $wst = $_POST['startTime'];
  $wet = $_POST['endTime'];

  $query = "SELECT Height,StrideTime,StrideLength,WalkSpeed,StrideStatus FROM $walkTable WHERE SID=$sid AND StartTime>='$wst' AND StartTime<='$wet';";
  $result = mysql_query($query); 
                
  if($result === FALSE) {
     die(mysql_error());
  }
          
  //--------------------------------------------------------------------------
  // Make array
  //--------------------------------------------------------------------------
  $jsonData = array();
  while ($array = mysql_fetch_row($result)) {
    $jsonData[] = $array;
  }

  echo json_encode($jsonData);

?>