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
  $mws = $_POST['mws'];
  $mdate = $_POST['modeldate'];

  $query = "SELECT ModelDate,obj FROM $modelTable WHERE SID=$sid AND ModelWS=$mws AND ModelDate<='$mdate' ORDER BY ModelDate DESC LIMIT 1;";
  $result = mysql_query($query); 
                
  if($result === FALSE) {
     die(mysql_error()); // TODO: better error handling
  }
          
  //--------------------------------------------------------------------------
  // Echo result as json 
  //--------------------------------------------------------------------------
  $jsonData = array();
  while ($array = mysql_fetch_row($result)) {
    $jsonData[] = $array;
  }
  echo json_encode($jsonData);

?>