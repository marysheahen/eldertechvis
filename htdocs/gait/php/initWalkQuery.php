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
  $wsize = $_POST['winSize'];

  $query = "SELECT Height,StrideTime,StrideLength,WalkSpeed,StartTime FROM $walkTable WHERE SID=$sid AND StartTime<=DATE_ADD( (SELECT MIN(StartTime) FROM $walkTable WHERE SID=$sid),INTERVAL $wsize DAY);";
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