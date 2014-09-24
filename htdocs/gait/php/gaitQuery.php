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
  $uId = $_POST['user_id'];
  $mWs = $_POST['mws'];
  $gWs = $_POST['gws'];

  $result = mysql_query("SELECT * FROM $gaitTable WHERE RID=$uId AND ModelWS=$mWs and ParameterWS=$gWs;"); 
                
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