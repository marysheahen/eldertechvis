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
  $result = mysql_query("SELECT SID,InitialParameters FROM $initTable;"); 
                           

  //--------------------------------------------------------------------------
  // Echo result as json 
  //--------------------------------------------------------------------------
  $jsonData = array();
  while ($array = mysql_fetch_row($result)) {
    $jsonData[] = $array;
  }
  echo json_encode($jsonData);

?>