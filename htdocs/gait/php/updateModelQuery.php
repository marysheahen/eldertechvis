<?php 

  require_once 'config.php';

  $tkey = $_POST['mu_key'];
  $sid = $_POST['sid'];
  $date = $_POST['date'];
  $obj = $_POST['obj'];

  if( $tkey == $mu_key){

     //--------------------------------------------------------------------------
     // Connect to DB
     //--------------------------------------------------------------------------
     $con = mysql_connect($host,$user,$pass);
     $dbs = mysql_select_db($databaseName, $con);

     //--------------------------------------------------------------------------
     // Update model (all window sizes), and clear all model data after it, 
     //  and gait data too...
     //-------------------------------------------------------------------------- 
     $query = "UPDATE $modelTable SET obj='$obj' WHERE SID=$sid AND ModelDate='$date';";
     $result = mysql_query($query);

     if($result){
        //-----------------
        // Success
        //-----------------
        $query = "DELETE FROM $gaitTable WHERE (RID=$sid OR (RID>=($sid*10) AND RID<=($sid*10 + 9))) AND GaitDate >='$date';";
        mysql_query($query);

        $query = "DELETE FROM $modelTable WHERE SID=$sid AND ModelDate > '$date';"; 
        mysql_query($query);

        echo '1'; 
     }
     else
        echo '0';  //fail

  }
  else{
     echo '0'; //fail
  }
                      
?>