<?php

/* Function used for formatting the Dates obtained from User for 
   Database insertion. People are used to mm-dd-yyyy, but DB stores
   as yyyy-mm-dd. 
*/

function date_to_DB($month, $day, $year, $hour=NULL, $minute=NULL, $second=NULL){
	$toReturn='';
	if($hour==NULL && $minute==NULL && $second==NULL){
		$toReturn=$year."-".$month."-".$day;
	} 

	else{
		$minute="00";
		$second="00";
		$toReturn=$year."-".$month."-".$day." ".$hour.":".$minute.":".$second;
	}
	return $toReturn;
}

/* The reverse of the previous function. Converts from 'yyyy-mm-dd hh:mm:ss' or 'yyyy-mm-dd' to 
   mm-dd-yyyy
*/
function date_from_DB($date){
	$toReturn;
	$date=preg_split("/[\s,]+/", $date);
	if(count($date)<2){
		$date=preg_split('/-/', $date[0]);
		$toReturn=array($date[1], $date[2], $date[0]);	
	}

	else{
	  $date_arr=preg_split('/-/', $date[0]);
	  $time_arr=preg_split('/:/', $date[1]);
	  $toReturn=array($date_arr[1], $date_arr[2], $date_arr[0], $time_arr[0], $time_arr[1], $time_arr[2]);
	}
	return $toReturn;
}

function year_starting_until($start, $end){
	for($i=$start; $i<=$end; $i++){
	print "<option id='$i' value='$i'>$i</option>";
	}
}

function year_ending_since($start, $end){
	for($i=$end; $i>=$start; $i--){
	print "<option id='$i' value='$i'>$i</option>";
	}

}



function more_months($month_in_list=NULL){

	for($i=1;$i<=12;$i++){
		if($i!=$month_in_list){
			if($i>=10)
			print "<option id='$i' value='$i'>$i</option>";
			else
			print "<option id='0$i' value='0$i'>0$i</option>";
		}
		else
		continue;
	}

}

function more_days($day_in_list=NULL){
	for($i=1;$i<=31;$i++){
		if($i!=$day_in_list){
			if($i>=10)
			print "<option id='$i' value='$i'>$i</option>";
			else
			print "<option id='0$i' value='0$i'>0$i</option>";
		}
		else
		continue;
	}

}

function more_hours($hour_in_list=NULL){
	
	for($i=1;$i<=24;$i++){
		if($i!=$hour_in_list){
			if($i>=10)
			print "<option id='$i' value='$i'>$i</option>";
			else
			print "<option id='0$i' value='0$i'>0$i</option>";
		}
		else
		continue;
	}
	
}

function date_dropdown($start_or_end, $date, $name=NULL){

	if($start_or_end=="start"){
		$sdatetime=date_from_DB($date);
		print "<select name='smnth' id='mnth'>";
		print "<option value='$sdatetime[0]' id='$sdatetime[0]' selected='selected'>$sdatetime[0]</option>";
		more_months($sdatetime[0]);
		print "</select>";
		print "<select name='sdy' id='dy'>";
		print "<option value='$sdatetime[1]' id='$sdatetime[1]' selected='selected'>$sdatetime[1]</option>";
		more_days($sdatetime[1]);
		print "</select>";
		print "<select name='syr' id='yr'>";
		print "<option value='$sdatetime[2]' id='$sdatetime[2]' selected='selected'>$sdatetime[2]</option>";
		year_starting_until("2010", "2050");
		print "</select>";
	}
	else if($start_or_end=="end"){
		$edatetime=date_from_DB($date);
		print "<select name='emnth' id='mnth'>";
		print "<option value='$edatetime[0]' id='$edatetime[0]' selected='selected'>$edatetime[0]</option>";
		more_months($edatetime[0]);
		print "</select>";
		print "<select name='edy' id='dy'>";
		print "<option value='$edatetime[1]' id='$edatetime[1]' selected='selected'>$edatetime[1]</option>";
		more_days($edatetime[1]);
		print "</select>";
		print "<select name='eyr' id='yr'>";
		print "<option value='$edatetime[2]' id='$edatetime[2]' selected='selected'>$edatetime[2]</option>";
		year_ending_since("2010", "2050");
		print "</select>";
	}
	
	else if($start_or_end==NULL&&$date!=NULL&&$name!=NULL){
		$sdatetime=date_from_DB($date);
		print "<select name='$name.mnth' id='mnth'>";
		print "<option value='$sdatetime[0]' id='$sdatetime[0]' selected='selected'>$sdatetime[0]</option>";
		more_months($sdatetime[0]);
		print "</select>";
		print "<select name='$name.dy' id='dy'>";
		print "<option value='$sdatetime[1]' id='$sdatetime[1]' selected='selected'>$sdatetime[1]</option>";
		more_days($sdatetime[1]);
		print "</select>";
		print "<select name='$name.yr' id='yr'>";
		print "<option value='$sdatetime[2]' id='$sdatetime[2]' selected='selected'>$sdatetime[2]</option>";
		year_starting_until($sdatetime[2], "2050");
		print "</select>";
	}
}
function make_dates($begin, $end){
				$days_array=array();
				$begin_date=date_from_DB($begin);
				$b_day=$begin_date[1];
				$b_month=$begin_date[0];
				$b_year=$begin_date[2];
				$end_date=date_from_DB($end);
				$e_day=$end_date[1];
				$e_month=$end_date[0];
				$e_year=$end_date[2];
				
				$start_date=gregoriantojd($b_month, $b_day, $b_year);
				$end_date=gregoriantojd($e_month, $e_day, $e_year);
				
				$diff=$end_date-$start_date;
			
				for($p=0; $p<=$diff; $p++){
					$new_date=date("Y-m-d", mktime(0, 0, 0, $b_month, $b_day+$p, $b_year));
					array_push($days_array, $new_date);
				}
				return $days_array;
}
function two_weeks_before(){
	$two_weeks=date("Y-m-d", time() - 2*(7 * 24 * 60 * 60));
	$two_weeks=$two_weeks." 00:00:00";
	return $two_weeks;
}

function ymd_gregoriantojd($date){
	$date_arr=date_from_DB($date);
	$month=$date_arr[1];
	$day=$date_arr[2];
	$year=$date_arr[0];
	return gregoriantojd($month, $day, $year);
}

?>
