<?php 

  require_once 'config.php';

  $tkey = $_POST['mu_key'];
  $sid = $_POST['sid'];
  $new_init = $_POST['new_init'];

  if( $tkey == $mu_key){

     //--------------------------------------------------------------------------
     // Connect to DB
     //--------------------------------------------------------------------------
     $con = mysql_connect($host,$user,$pass);
     $dbs = mysql_select_db($databaseName, $con);

     //--------------------------------------------------------------------------
     // Insert/update initialization, and clear any 
     //    existing model & gait data...
     //-------------------------------------------------------------------------- 
     $result = mysql_query("SELECT * FROM $initTable WHERE SID=$sid;");

 
     if ($array = mysql_fetch_row($result)){
        //-------------------------
        // Update existing
        //-------------------------
        $query = "UPDATE $initTable SET InitialParameters='$new_init' WHERE SID=$sid;";
        $result = mysql_query($query);

        if($result){
           //-----------------
           // Success
           //-----------------
           $query = "DELETE FROM $gaitTable WHERE RID=$sid OR (RID>=($sid*10) AND RID<=($sid*10 + 9));";
           mysql_query($query);

           $query = "DELETE FROM $modelTable WHERE SID=$sid;"; 
           mysql_query($query);

           echo '1'; 
        }
        else
           echo '0';  //fail
     }
     else{
        //-------------------------
        // Insert new
        //-------------------------
        $query = "INSERT INTO $initTable VALUES (null,$sid,'$new_init');";
        $result = mysql_query($query);

        if($result){
           //-----------------
           // Success
           //-----------------
           $query = "DELETE FROM $gaitTable WHERE RID=$sid OR (RID>=($sid*10) AND RID<=($sid*10 + 9));";
           mysql_query($query);

           $query = "DELETE FROM $modelTable WHERE SID=$sid;"; 
           mysql_query($query);

           echo '1';
        }
        else
           echo '0';  //fail
     }

  }
  else{
     echo '0'; //fail
  }
                      
?>