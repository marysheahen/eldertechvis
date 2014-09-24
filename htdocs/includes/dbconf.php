<?php
if($_SERVER['HTTPS']!="on")
  {
	//$newAddr = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
	//header("Location: $newAddr");  
  }
function connect_to_database($which=NULL){

if($which==NULL){

$conn = mysqli_init();
if (!$conn) {
    die('mysqli_init failed');
}

if (!$conn->options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
    die('Setting MYSQLI_INIT_COMMAND failed');
}

if (!$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
    die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
}

if (!$conn->real_connect("robby.cirl.missouri.edu", "misq4f", 
"X4fHsb7EdHp4", "viz_interface")) {
    die('Connect Error (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}

//echo "<br>Connected to mu_viz<br>";


/*$conn=mysqli_connect("littledipper.cirl.missouri.edu", "tatiana", 
"JhY7_/45", "viz_interface") 
or die("Could not connect to the database:".mysqli_connect_error());
*/
return $conn;
}

else{

$conn=mysqli_connect("robby.cirl.missouri.edu", "misq4f", "X4fHsb7EdHp4", "viz_interface") 
or die("Could not connect to the database:".mysqli_connect_error());
$stmt=mysqli_prepare($conn, "SELECT DBName, HostName, UserName, Password  from External_Database WHERE DBName=?");

	if(mysqli_connect_errno()){
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit(1);
	}

	if(!mysqli_stmt_bind_param($stmt, 's', $which)){
		echo "ERROR: ".mysqli_error($stmt)."<br />\n";
		exit(1);
	}

	if(!mysqli_stmt_execute($stmt)){
		echo "ERROR: ".mysqli_error($stmt)."<br />\n";
		exit(1);
	}

	if(!mysqli_stmt_bind_result($stmt, $DBName, $HostName, $UserName, $Password)){
		echo "ERROR: ".mysqli_error($stmt)."<br />\n";
		exit(1);
	}
	if(!mysqli_stmt_fetch($stmt)){
		exit(1);
	}

$ex_conn=mysqli_connect($HostName, $UserName, $Password, $DBName) or die("Could not connect to the database: ".mysqli_connect_error());
//echo "<br>Connected to " . $DBName ."<br>";

mysqli_stmt_close($stmt);
mysqli_close($conn);
return $ex_conn;


}
}
?>
