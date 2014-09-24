<?php
//error_reporting(E_ALL);
//ini_set("display_errors",1);
/*
	Created By: Tatiana Alexenko
	Created On: 4/16/2010
	
	This is pretty much the most important file...ever created. 
	There are three functions here:
	DB_Error -- used for putting Database error information into the Error_Log table. 
	curr_date -- just returns todays data in this format: 2010-04-17 01:27:00. Saves some typing, format works for MySQL. 
	DB_Query -- You supply it with a query id, optional table name (NULL if not necessary),optionally parameters (what you want to use in mysqli_bind_param), 
	optinally results (what name you want to give to the result fields--has to match the return result of query), and rel_name (see example in comments below for explanation). 
	It returns you the output of the query if there is any (SELECT Query), or nothing if there is nothing to return (Insert, Update, Delete). 
	
	Here is an example of how to use it:
	----------------------------------------------------------------------------------------------------------------------
	$conn=connect_to_database(); -- we need to call connect_to_database() to connect to a certain Database. No argument to connect_to_database() means we use this system's database (as opposed to the EMR or sensor Databases);
	$username="some value"; //declaring and giving value to variables that we want to use for bind_param
	$password="some value";
	
	$params=array($username, $password); //put them into an array so the DB_Query function understands. Even if you only want to bind 1 variable, you still need to put it into an array.
	$results=array('uid', 'utype'); //This is what we want to call the results. Again, even if you expect only one result field, still put it into an array
	$uid=NULL; // you can declare the variables that will store results wherever you plan on working with them--in the file where you call DB_Query.
	$utype=NULL;
	$result=DB_Query(1, NULL, $params, $results); //Call to DB_Query. 1 is the qid (numeric ID of the query). NULL is there because we did not specify a table. Some queries need it. 
		any query that has tbl_name in it needs a valid table to be passed to DB_Query. Also if the query has rel_name, that's something like Resident R2, R2 would be the rel_name in that case.
					rel_name is the last argument, optional
	print_r($result); //always a good idea to look at what you get. In this case it's a really obnoxious array. 
	
	$uid=$result[0]['uid']; //so because the array is so obnoxious, this is how you will get the variables out of it. 
	$utype=$result[0]['utype']; //You can look through rows by number and the fields by their name
	
	print $uid." ".$utype; //just to make sure this is what you wanted
	---------------------------------------------------------------------------------------------------------------------------------
	
	The reason for doing it this way is how little you need to type now to query a database...any database (in the External_Database table of course)! Laziness is, as it turns out, a virtue. 
	A big thing to remember here is that the Query has to be placed into the Database for this to work. So do that before you start calling the function. 
	
	Error Checking: This checks for DB errors in the process and puts them into the Error_Log. For something like an authentication error, you will call the DB_Error function from wherever you are calling DB_Query 
	or you can define your own function for putting errors into the Error_Log table. 
	
	Examples of all of this can be found in Form_Submit_Scripts/login_check.php --that is probably the simplest example of its use. 

*/
require('dbconf.php'); //we kind of need that for obvious reasons

function curr_date(){ //pretty self-explanatory
	return date("Y-m-d H:i:s");
}

function refValues($arr){ 
	//print "function bein exectucted";
	if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
	{
		$refs = array();
		//print "function bein exectucted";
		foreach($arr as $key => $value)
			$refs[$key] = &$arr[$key];
		//print_r($refs);
		return $refs;
	}
	//print "function bein exectucted";
	return $arr;
}	
	
function DB_Error($when, $URI, $message, $error_number=NULL, $uid=NULL){

	$error_conn=connect_to_database(); //connect_to_database always passed no argument because the Errors are put into our DBs Error_Log table..ALWAYS

	$URI=htmlentities($URI, ENT_QUOTES);
	
	if(mysqli_connect_errno()){
		print "Connection error";
	}
	if($uid!=NULL){ //we know who caused the error
		$stmt=mysqli_prepare($error_conn, "INSERT into Error_Log (Occured_At, URI, Error_Message, Error_Number, Who) VALUES(?,?,?,?,?)");
		mysqli_stmt_bind_param($stmt, 'sssii', $when, $URI, $message, $error_number, $uid);
	}
	else if($error_number!=NULL){ //when there is no $user id specified--we don't know who caused the error because it did not occur when somebody was logged in
		$stmt=mysqli_prepare($error_conn, "INSERT into Error_Log (Occured_At, URI, Error_Message, Error_Number) VALUES(?,?,?,?)");
		mysqli_stmt_bind_param($stmt, 'sssi', $when, $URI, $message, $error_number);
	}
	else{
		$stmt=mysqli_prepare($error_conn, "INSERT into Error_Log (Occured_At, URI, Error_Message) VALUES(?,?,?)");
		mysqli_stmt_bind_param($stmt, 'sss', $when, $URI, $message);
	}
	
	if(!mysqli_stmt_execute($stmt)){ //this error gets printed...for now
		echo "ERROR: ".mysqli_error($stmt)."<br />\n";
		exit(1);
	}

	mysqli_stmt_close($stmt);
	mysqli_close($error_conn);
}


function DB_Query($qid, $table=NULL, $params=NULL, $results=NULL, $rel_name=NULL, $persist=false, $add_where=NULL, $array_key=NULL, $return_string=NULL, $secondary_key=NULL, $no_duplicates=NULL, $sqlitefile=NULL){ 
	$conn=connect_to_database();
	$stmt=mysqli_prepare($conn, "SELECT SQL_Query, DB_To_Query from Query WHERE qid=?");
	
	
	if(mysqli_connect_errno()){
		DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Connection failed during Query lookup:".mysqli_connect_error(), mysqli_connect_errno());
		exit(1);
	}

	if(!mysqli_stmt_bind_param($stmt, 'i', $qid)){
		DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Binding Parameters failed during Query lookup:".mysqli_stmt_error($stmt), mysqli_stmt_errno($stmt));
		exit(1);
	}

	if(!mysqli_stmt_execute($stmt)){
		$err_no=mysqli_stmt_errno($stmt);
		DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Executing Statement failed during Query lookup:".mysqli_stmt_error($stmt), $err_no);
		exit(1);
	}

	if(!mysqli_stmt_bind_result($stmt, $Query, $DB_To_Query)){
		DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Binding Result failed during Query lookup:".mysqli_stmt_error($stmt), mysqli_stmt_errno($stmt));
		exit(1);
	}
	
	if(!mysqli_stmt_fetch($stmt)){
		DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Fetching Results failed during Query lookup:".mysqli_stmt_error($stmt), mysqli_stmt_errno($stmt));
		exit(1);
	}
	//print $DB_To_Query;
	
	if($persist==false){
	if($DB_To_Query==NULL)
		$conn=connect_to_database();
	else if($DB_To_Query==2)
		$conn=connect_to_database('alerts');
	else 
		$conn=connect_to_database('adl');
	}
	else
		$conn=$conn;
	//print_r($params);
	mysqli_stmt_close($stmt);
	$type_string="";
	if($params!=NULL){ 
	//so advanced
		foreach($params as $value){ //some checks of variable type so we know what to give to bind_param
			if(is_numeric($value)&&!is_double($value)) //so we don't append i and then d when it's a double
				$type_string.="i";
			if(!is_numeric($value)&&is_string($value)){ //this is why Phone_Number is Varchar
				if(strlen($value)>1400) //This is the only way I could think of to differentiate strings from blobs--in our system blobs are always>400 chars
					$type_string="b";
				else
				$type_string.="s";
			}
			if(is_double($value))
				$type_string.="d"; //we don't really need that, but just in case we will
		}
	}
	
	//print $type_string;
	
	if($table!=NULL){ //if a table was specified, replace variable table name in query with the table_name passed;
	
		if($rel_name!=NULL){
			$Query=preg_replace('/rel_name/', $rel_name, $Query, 5);
		}
		$Query=preg_replace('/tbl_name/', $table, $Query, 5);
	}
	//print $add_where;
	if($add_where!=NULL){
		$Query=preg_replace('/GROUP/', $add_where, $Query, 1);
	//print $add_where;
	//print $Query;
	//	print "<br />";
	}
	//print $Query;
	if($sqlitefile!=NULL){
		//echo "<br>Will try reading from missouri.edu here<br>";
		try{
			$db=new PDO('sqlite:/home/psense/viz.proactivesense.com/www/sim_images/'.$sqlitefile);
			$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch(Exception $e){
			die($e);
			//die();
		}
		try {
			$posts = $db->prepare($Query);
		}catch (Exception $e) {
			die ($e);
			//die();
		}	
			//print_r($params);
			//print_r($results);
			try{	
				if(count($params)>0)$posts->execute($params);
				else $posts->execute(); 
			}
			catch(Exception $e){die($e); }
			//$posts>debugDumpParams();
			$data=array();
			while($row=$posts->fetch(PDO::FETCH_ASSOC)){
				//print_r($row);
				if($array_key!=NULL){
						$key=array_shift($row);
						if(!is_array($data[$key]))
							$data[$key][0]=$row;
						else
							array_push($data[$key], $row);
				}
				else{
					$data=$row;
				}
			}
		//print_r($data);
		return $data;
		//print_r($results);
	}
	else{
		$stmt=mysqli_prepare($conn, $Query);
		if (!$stmt) {
			echo "Error preparing statement";
			exit(1);
		}	
		if(mysqli_connect_errno()){
			DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Connection failed for Query ".$qid.":".mysqli_connect_error(), mysqli_connect_errno());
			exit(1);
		}
		
		if($params!=NULL){
			array_unshift($params, $type_string);
		}

		//print "<br/>The Query ID is: ".$qid."</br>";
		//print "The arguments for the Query are:<br />";
		//print_r ($params);
		//print "<br/>The type string is: <br/>".$type_string;
		//print "<br />The actual Query SQL is: ".$Query."<br />";

		//var_dump($stmt);
		if($params!=NULL){
		if(!(call_user_func_array(array($stmt, 'bind_param'), refValues($params)))){ // Problem here ***GC
		//if(!(call_user_func_array('mysqli_stmt_bind_param', $args))){
			
			$error_message=mysqli_stmt_errno($stmt);
			print "<br />The error message is: <br/>";
			print $error_message;
			print "<br /> The SQL of query is:<br />";
			print $Query;
			
		//	DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Binding Params failed for Query ".$qid.":".$error_message, mysqli_stmt_errno($stmt));
			//exit(1); // ***GC: comment this out to continue, but it still craps out later. Obv some problem with the binding above
		}
		}
		if(!mysqli_stmt_execute($stmt)){
			//There are two times this can happen:
			//1) If a resident is editing their access right and queries are added to Access_Right_Queries that are already in there
			if($qid==32)
				exit(1);
			//so we just exit in this case and don't store this in Error_Log because it's a normal thing
			else{
			//2) if the user is editing their account information and they use username/email that's already in DB
			//	so we return an error message that will get displayed as a javascript alert box
				DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Executing Statement failed for Query ".$qid.":".mysqli_stmt_error($stmt), mysqli_stmt_errno($stmt));
				return "Try a different Username or Email--this one is already in our database!"; 
				exit(1);
			}
		}
	
		if($results!=NULL){
			$result = mysqli_stmt_result_metadata($stmt);
			//print "<br/> the results of the query are: <br/>";
			//print_r($result);	//print "*************************<br />";
			$fields = array();
			while ($field = mysqli_fetch_field($result)) {
				$name = $field->name;
				$fields[$name] = &$$name;
			}
			array_unshift($fields, $stmt);
		
			//Field Count Check
			//print count($fields);	//print "*************************<br />";
			//call_user_func_array('mysqli_stmt_bind_result', $fields);
			call_user_func_array('mysqli_stmt_bind_result', refValues($fields));
		
			array_shift($fields);
			$results = array();
			//print_r($fields);	//print "*************************<br />";
			while (mysqli_stmt_fetch($stmt)){
				//print_r($fields);
				if($array_key!=NULL&&$secondary_key==NULL){
				//print "*************************<br />";
				//print_r ($fields);
					if($return_string===true)
						$result_string="";
					else
						$result_string=array();
						
					foreach($fields as $key => $value){
						if($key!=$array_key){
							if($return_string===true)
								$result_string.=$key.": ".$value." ";
							else
								$result_string[$key]=$value;
						}
					}
					//print_r($result_string);
				if(array_key_exists($fields[$array_key], $results)){
						if(is_array($results[$fields[$array_key]])&&array_key_exists(0, $results[$fields[$array_key]])){
							array_push($results[$fields[$array_key]], $result_string);
						}
						else{
							$temp=$results[$fields[$array_key]];
						//	print_r($temp);
						//	print "<br />";
							$results[$fields[$array_key]]=array();
							array_push($results[$fields[$array_key]], $temp);
							array_push($results[$fields[$array_key]], $result_string);
						}
				}
				else{
					//print "!!!!!!!!!".$fields[$array_key]."!!!!!!!!!!!";
					$results[$fields[$array_key]]=array($result_string);
				}
			}
			else if($secondary_key!=NULL&&$array_key!=NULL){
				//print_r($fields);
				$day=NULL;
				$hour=NULL;
				$contents=array();
				foreach($fields as $key => $value){
					if($key==$array_key){
						$day=$fields[$key];
					}
					else if($key==$secondary_key){
						$hour=$fields[$key];
					}
					else if($no_duplicates==true){
						$density=$value;
					}
					else{
						$contents[$key]=$value;
					}
				}
				//echo "$$$$$$$".$day."$$".$hour."$$$";
			//	print_r($contents);
			if($no_duplicates==true){
				$results[$day][$hour]=$density;
			}	
			else if($no_duplicates==NULL&&!is_array($results[$day][$hour]))
					$results[$day][$hour]=array();
					array_push($results[$day][$hour], $contents);
			}
			else{
				//print "*************************<br />";
				$temp = array();
				foreach($fields as $key => $val){ 
					$temp[$key] = $val; 
				}
				array_push($results, $temp);
			}
        }
//print_r($fields);
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
		if($persist==false)
			mysqli_close($conn);
		//print "<br /> The resulst are: <br />";
		//print_r($results);
		return $results; 
	}
	
		else{
			mysqli_stmt_close($stmt);
			mysqli_close($conn);
		}
	}
}

/*function DB_Query_pass_query($query_array){ 
	$conn=connect_to_database('adl');
	foreach($query_array as $query){
		$stmt=mysqli_prepare($conn, $query[0]);
		
	if(mysqli_connect_errno()){
		DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Connection failed during Query lookup:".mysqli_connect_error(), mysqli_connect_errno());
		exit(1);
	}

	if(!mysqli_stmt_bind_param($stmt, 'i', $qid)){
		DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Binding Parameters failed during Query lookup:".mysqli_stmt_error($stmt), mysqli_stmt_errno($stmt));
		exit(1);
	}

	if(!mysqli_stmt_execute($stmt)){
		$err_no=mysqli_stmt_errno($stmt);
		DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Executing Statement failed during Query lookup:".mysqli_stmt_error($stmt), $err_no);
		exit(1);
	}

	if(!mysqli_stmt_bind_result($stmt, $Query, $DB_To_Query)){
		DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Binding Result failed during Query lookup:".mysqli_stmt_error($stmt), mysqli_stmt_errno($stmt));
		exit(1);
	}
	
	if(!mysqli_stmt_fetch($stmt)){
		DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Fetching Results failed during Query lookup:".mysqli_stmt_error($stmt), mysqli_stmt_errno($stmt));
		exit(1);
	}
	$params=$query[1];
	$results=$query[2];
	$type_string="";
	
	if($params!=NULL){ //so advanced
		foreach($params as $value){ //some checks of variable type so we know what to give to bind_param
			if(is_numeric($value)&&!is_double($value)) //so we don't append i and then d when it's a double
				$type_string.="i";
			if(!is_numeric($value)&&is_string($value)){ //this is why Phone_Number is Varchar
				if(strlen($value)>400) //This is the only way I could think of to differentiate strings from blobs--in our system blobs are always>400 chars
					$type_string="b";
				else
				$type_string.="s";
			}
			if(is_double($value))
				$type_string.="d"; //we don't really need that, but just in case we will
		}
	}
	
	//print $type_string;
	
	$args=array($stmt, $type_string);
	
	if($params!=NULL){
		for($i=0; $i<count($params); $i++){
			$args[$i+2]=$params[$i];
		}
	}
	//print "<br />";
	//print_r ($args);
	//print "<br />".$Query."<br />";
	
	
	//if(!(call_user_func_array('mysqli_stmt_bind_param', $args))){
	if(!(call_user_func_array('mysqli_stmt_bind_param', refValues($args)))){
		$error_message=mysqli_stmt_error($stmt);
		DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Binding Params failed for Query ".$qid.":".$error_message, mysqli_stmt_errno($stmt));
		exit(1);
	}
		
	if(!mysqli_stmt_execute($stmt)){
		//There are two times this can happen:
		//1) If a resident is editing their access right and queries are added to Access_Right_Queries that are already in there
		DB_Error(curr_date(), $_SERVER['PHP_SELF'], "Executing Statement failed for Query ".$qid.":".mysqli_stmt_error($stmt), mysqli_stmt_errno($stmt));
		exit(1);
		}
	}
	
	if($results!=NULL){
	
		$result = mysqli_stmt_result_metadata($stmt);
		$fields = array();
        while ($field = mysqli_fetch_field($result)) {
            $name = $field->name;
            $fields[$name] = &$$name;
        }
        array_unshift($fields, $stmt);
		
//Field Count Check
	//	print count($fields);
        //call_user_func_array('mysqli_stmt_bind_result', $fields);
        call_user_func_array('mysqli_stmt_bind_result', refValues($fields));

        array_shift($fields);
        $results = array();
        while (mysqli_stmt_fetch($stmt)){
            $temp = array();
            foreach($fields as $key => $val){ 
				$temp[$key] = $val; 
			}
            array_push($results, $temp);
        }

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
		return $results; 
	}

	}
	mysqli_close($conn);
}
*/
function DB_Access($Whose_Data, $EMR_Or_Sensor, $Query_Ran, $Who_Ran, $Ran_On){
	$params=array($Whose_Data, $EMR_Or_Sensor, $Query_Ran, $Who_Ran, $Ran_On);
	DB_Query(40, NULL, $params);
}
	

?>
