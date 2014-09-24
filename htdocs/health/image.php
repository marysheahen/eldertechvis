<?php
require('includes/session.php');

	//echo $_SESSION['resid'];
//if(!ob_start("ob_gzhandler")) ob_start();
/**************************************************************************/
function get_density_color($what='Density', $NumHits){
	
		
		$colourTable = array('255:255:255', '204:204:204', '255:255:172', '255:255:0', '204:255:121', '190:238:0', '126:216:10', '81:255:168', '0:190:190', '100:170:255', '100:100:255', '0:0:255');
		
		if($what == 'Density'){
		if ($NumHits  >= 550) {
			//500 hits or more, return blue
			return $colourTable[11];
		} else if ($NumHits >= 500 && $NumHits <= 549) {
			return $colourTable[10];
		} else if ($NumHits >= 450 && $NumHits <= 499) {
			return $colourTable[9];
		} else if ($NumHits >= 400 && $NumHits <= 449) {
			return $colourTable[8];
		} else if ($NumHits >= 350 && $NumHits <= 399) {
			return $colourTable[7];
		} else if ($NumHits >= 300 && $NumHits <= 349) {
			return $colourTable[6];
		} else if ($NumHits >= 250 && $NumHits <= 299) {
			return $colourTable[5];
		} else if ($NumHits >= 200 && $NumHits <= 249) {
			return $colourTable[4];
		} else if ($NumHits >= 150 && $NumHits <= 199) {
			return $colourTable[3];
		} else if ($NumHits >= 100 && $NumHits <= 149) {
			return $colourTable[2];
		} else if ($NumHits >= 50 && $NumHits <= 99) {
			return $colourTable[1];
		}else {
			//else return white
			return $colourTable[0];
		}
		}
		if(is_int(stripos($what, 'Hydraulic')))
		{
				if ($NumHits  >= 800) {
			//500 hits or more, return blue
			return $colourTable[11];
		} else if ($NumHits >= 720 && $NumHits <= 799) {
			return $colourTable[10];
		} else if ($NumHits >= 640 && $NumHits <= 719) {
			return $colourTable[9];
		} else if ($NumHits >= 560 && $NumHits <= 639) {
			return $colourTable[8];
		} else if ($NumHits >= 480 && $NumHits <= 559) {
			return $colourTable[7];
		} else if ($NumHits >= 400 && $NumHits <= 479) {
			return $colourTable[6];
		} else if ($NumHits >= 320 && $NumHits <= 399) {
			return $colourTable[5];
		} else if ($NumHits >= 250 && $NumHits <= 319) {
			return $colourTable[4];
		} else if ($NumHits >= 150 && $NumHits <= 249) {
			return $colourTable[3];
		} else if ($NumHits >= 130 && $NumHits <= 194) {
			return $colourTable[2];
		} else if ($NumHits >= 0 && $NumHits <= 129) {
			return $colourTable[1];
		}else {
			//else return white
			return $colourTable[0];
		}
		}
}

function make_density_image($what=NULL, $square_width=12){
	require('includes/dbQuery.php');
	require('includes/usefulFunctions.php');
	//require('./System/chart_functions.php');
	//header('Content-type: image/png');
	
	/**********************************
	* Dates calculation
	*************************************/
	$start_date=$_SESSION['density_start'];
	$end_date=$_SESSION['density_end'];
	//print $start_date;
	//print $end_date;
	//$square_width=24;
	$dates=make_dates($start_date, $end_date);
	//print_r($dates);
	//print(count($dates));
	$num_dates=count($dates);
	$width=$num_dates*12;
	$height=24*12+25*1;
	$im = imagecreatetruecolor($width, $height);
	$bg = imagecolorallocate($im, 255, 255, 255);
	imagefill($im, 0, 0, $bg);
	$textcolor = imagecolorallocate($im, 0, 0, 0);
	$level0=imagecolorallocate($im, 255, 255, 255);
	$level1=imagecolorallocate($im, 204, 204, 204);
	$level2=imagecolorallocate($im, 255, 255, 172);
	$level3=imagecolorallocate($im, 255, 255, 0);
	$level4=imagecolorallocate($im, 204, 255, 121);
	$level5=imagecolorallocate($im, 190, 238, 0);
	$level6=imagecolorallocate($im, 126, 216, 10);
	$level7=imagecolorallocate($im, 81, 255, 168);
	$level8=imagecolorallocate($im, 0, 190, 190);
	$level9=imagecolorallocate($im, 100, 170, 255);
	$level10=imagecolorallocate($im, 100, 100, 255);
	$level11=imagecolorallocate($im, 0, 0, 255);
	
	if($what=='Density'){
		$legendTable = array('0-49'=>$bg, '50-99'=>$level1, '100-149'=>$level2,  '150-199'=>$level3, '200-249'=>$level4, '250-299'=>$level5, '300-349'=>$level6, '350-399'=>$level7, 
	'400-449'=>$level8, '450-499'=>$level9, 
	'500-549'=>$level10, '550+'=>$level11);
	}
	elseif($what == 'Bedtime(Hydraulic)'){
			$legendTable = array('0-64'=>$bg, '64-129'=>$level1, '130-194'=>$level2,  '195-259'=>$level3, '260-324'=>$level4, '325-389'=>$level5, '390-454'=>$level6, '455-519'=>$level7, 
	'520-584'=>$level8, '585-649'=>$level9, 
	'650-714'=>$level10, '715+'=>$level11);
	}
	// make the default layout
	/*imagestring($im, 3, 0, 0, 'Month', $textcolor);
	imagestring($im, 3, 0, 10, '-----', $textcolor);
	imagestring($im, 3, 0, 20, 'Day', $textcolor);
	imagestring($im, 3, 0, 30, '-----', $textcolor);
	imagestring($im, 3, 0, 40, 'Year', $textcolor);*/
	//imageLine($im, 0, 55, $width, 55, $textcolor);
	/*
	imageLine($im, 40, 0, 40, $height, $textcolor);
	imagestring($im, 3, 0, 55, '00:00', $textcolor);
    
	imagestring($im, 3, $width-145, 0, 'Month', $textcolor);
	imagestring($im, 3, $width-145, 10, '-----', $textcolor);
	imagestring($im, 3, $width-145, 20, 'Day', $textcolor);
	imagestring($im, 3, $width-145, 30, '-----', $textcolor);
	imagestring($im, 3, $width-145, 40, 'Year', $textcolor);
	//imageLine($im, 0, 55, $width, 55, $textcolor);
	//imageLine($im, $width-45, 0, $width-45, $height, $textcolor);
	imagestring($im, 3, $width-145, 55, '00:00', $textcolor);
	for($i=0; $i<24; $i++){ //24 hour loop
			if($i<10){
				imagestring($im, 3, $width-145, (56+($i*12)+($i*1)), ('0'.$i.':00'), $textcolor);
			}
			else{
				imagestring($im, 3, $width-145, (56+($i*12)+($i*1)), ($i.':00'), $textcolor);
			}
	}*/
	$resid=$_SESSION['resid'];
	if(strlen($resid)<=5)
		$motresid = substr($resid, 0, 4);
	else
		$motresid = $resid;
			
	$params=array($motresid, $start_date, $end_date);
	$results=array('sday', 'shour', 'sminute', 'eday', 'ehour', 'eminute', 'diff');
	$array_key='sday';
	$secondary_key='shour';
	
	if($what=='Bathroom'){
		$density_results=DB_Query(93, NULL, $params, $results,NULL, NULL, NULL, $array_key, NULL, $secondary_key);
		$rectcolor=imagecolorallocate($im, 255, 100, 100);
	}
	
	else if($what=='Bedtime'){
		$density_results=DB_Query(94, NULL, $params, $results,NULL, NULL, NULL, $array_key, NULL, $secondary_key);
		$rectcolor=imagecolorallocate($im, 150, 0, 170);
	}
	
	else if($what=='Chairtime'){
		$density_results=DB_Query(101, NULL, $params, $results,NULL, NULL, NULL, $array_key, NULL, $secondary_key);
		$rectcolor=imagecolorallocate($im, 150, 150, 20);
	}
	
	else if($what=='Density'){
		$density_results=DB_Query(95, NULL, $params, $results,NULL, NULL, NULL, $array_key, NULL, $secondary_key);
		//print_r($density_results);
		$rectcolor=imagecolorallocate($im, 10, 10, 10);
		$start_date=date("Y-m-d", strtotime($end_date)-15778463);
		$results=array('Date', 'Hour', 'Density');
		$array_key='Date';
		$secondary_key='Hour';
		$density=DB_Query(61, NULL, $params, $results, NULL, NULL, NULL, $array_key, NULL, $secondary_key, true);
		//print_r($density_results);
	}
	
else if($what=='Bedtime(Hydraulic)'){
$params=array($resid, $start_date, $end_date);
		$unformatted_density_results=DB_Query(153, NULL, $params, $results,NULL, NULL, NULL, NULL, NULL, NULL);
		$density_results = array();
		//print_r($unformatted_density_results);
		
		for($i = 1; $i < count($unformatted_density_results); $i++)
		{
			$nextd[$i-1]['sday'] = $unformatted_density_results[$i -1]['eday'];
			$nextd[$i-1]['sminute'] = $unformatted_density_results[$i -1]['eminute'];
			$nextd[$i-1]['shour'] = $unformatted_density_results[$i -1]['ehour'];
			$nextd[$i-1]['eday'] = $unformatted_density_results[$i]['sday'];
			$nextd[$i-1]['eminute'] = $unformatted_density_results[$i]['sminute'];
			$nextd[$i-1]['ehour'] = $unformatted_density_results[$i]['shour'];
			//echo strtotime($nextd[$i]['eday']) - strtotime($nextd[$i]['sday']);
			$nextd[$i-1]['diff'] = floor((strtotime($nextd[$i-1]['eday']." ".$nextd[$i-1]['ehour'].":".$nextd[$i-1]['eminute'].":00") - strtotime($nextd[$i-1]['sday']." ".$nextd[$i-1]['shour'].":".$nextd[$i-1]['sminute'].":00"))/300);
		}
		//print_r($nextd[51]);
		//echo(($nextd[51]['sday']." ".$nextd[51]['shour'].":0".$nextd[51]['sminute'].":00"));
	//	echo((strtotime($nextd[51]['eday']." ".$nextd[51]['ehour'].":".$nextd[51]['eminute'].":00") - strtotime($nextd[51]['sday']." ".$nextd[51]['shour'].":".$nextd[51]['sminute'].":00"))/720);
		//echo($nextd[51]['eday']." 0".$nextd[51]['ehour'].":0".$nextd[51]['eminute'].":00");
		//echo(strtotime($nextd[51]['sday']." 0".$nextd[51]['shour'].":0".$nextd[51]['sminute'].":00"));
		$density_results[$nextd[0]['sday']][$nextd[0]['shour']][0]['sminute'] = $nextd[0]['sminute'];
		$density_results[$nextd[0]['sday']][$nextd[0]['shour']][0]['eminute'] = $nextd[0]['eminute'];
		$density_results[$nextd[0]['sday']][$nextd[0]['shour']][0]['eday'] = $nextd[0]['eday'];
		$density_results[$nextd[0]['sday']][$nextd[0]['shour']][0]['ehour'] = $nextd[0]['ehour'];
		$density_results[$nextd[0]['sday']][$nextd[0]['shour']][0]['diff'] = $nextd[0]['diff'];
		
	//print_r($density_results);
	//	print_r($nextd);
		for($i = 1; $i < count($nextd); $i++)
		{
			if($nextd[$i]['diff']>0)
			{
				$density_results[$nextd[$i]['sday']][$nextd[$i]['shour']][$i]['sminute'] = $nextd[$i]['sminute'];
				$density_results[$nextd[$i]['sday']][$nextd[$i]['shour']][$i]['eminute'] = $nextd[$i]['eminute'];
				$density_results[$nextd[$i]['sday']][$nextd[$i]['shour']][$i]['eday'] = $nextd[$i]['eday'];
				$density_results[$nextd[$i]['sday']][$nextd[$i]['shour']][$i]['ehour'] = $nextd[$i]['ehour'];
				$density_results[$nextd[$i]['sday']][$nextd[$i]['shour']][$i]['diff'] = $nextd[$i]['diff'];
			}
		}
	
		//print_r($density_results);
		$rectcolor=imagecolorallocate($im, 255, 255, 255);
		$start_date=date("Y-m-d", strtotime($end_date)-15778463);
		$params=array($resid, $start_date, $end_date);
		$results=array('Date', 'Hour', 'Density');
		$array_key='Date';
		$secondary_key='Hour';
		$density=DB_Query(139, NULL, $params, $results, NULL, NULL, NULL, $array_key, NULL, $secondary_key, true);
		//$density = Array ( [2014-01-08] => Array ( [0] => 175 [1] => 98 [2] => 100 [3] => 52 [4] => 147 [5] => 190 [6] => 50 [7] => 18 [8] => 0 [9] => 0 [10] => 0 [11] => 0 [12] => 0 [13] => 0 [14] => 0 [15] => 0 [16] => 0 [17] => 0 [18] => 0 [19] => 0 [20] => 0 [21] => 275 [22] => 727 [23] => 176 ) );
		//print_r($density);
	}
	else{
		echo "*".$what;
		echo "Parameter not recognized";
		}
	$num_results=count($density_results);
	if($num_results<1 && $what != 'Bedtime(Hydraulic)'){
		imagedestroy($im);
		return;
	}

	for($d=0; $d<$num_dates; $d++){
		if(is_array($density[$dates[$d]])){

				for($i=1; $i<25; $i++){
				if(array_key_exists($i-1, $density[$dates[$d]])){
	
					if(is_int($density[$dates[$d]][$i])){
				
						$level=$density[$dates[$d]][$i];
						$density_color=get_density_color($what, $level);
						$density_color=split(":",$density_color);
						$d_color=imagecolorallocate($im, (int)$density_color[0], (int)$density_color[1], (int)$density_color[2]);
						imagefilledrectangle($im, $d*12+1, (0*2+(($i-1)*12)+(($i-1)*1)), ($d+1)*12-1, 12*1+(($i-1)*12)+(($i-1)*1), $d_color);
				
					}
					else{
					
						$density_color=get_density_color($what, $density[$dates[$d]][$i-1]);
						$density_color=split(":",$density_color);
						$d_color=imagecolorallocate($im, (int)$density_color[0], (int)$density_color[1], (int)$density_color[2]);
						imagefilledrectangle($im, $d*12+1, (0*2+(($i-1)*12)+(($i-1)*1)), ($d+1)*12-1, 12*1+(($i-1)*12)+(($i-1)*1), $d_color);
						
					}
				}
			}
		}
	}
	for($d=0; $d<$num_dates; $d++){
		$date_arr=split('-', $dates[$d]);
		$year=split('20', $date_arr[0]);
		imageLine($im, ($d+1)*12, 0, ($d+1)*12, $height, $textcolor);
					
		for($i=0; $i<24; $i++){ //24 hour loop
			if($i<10){
				//imagestring($im, 3, 0, (56+($i*12)+($i*1)), ('0'.$i.':00'), $textcolor);
			}
			else{
				//imagestring($im, 3, 0, (56+($i*12)+($i*1)), ($i.':00'), $textcolor);
			}
			imageLine($im, 0, ($i*12)+($i*1), $width, ($i*12)+($i*1), $textcolor);
			
			if(is_array($density_results[$dates[$d]])){
				if(is_array($density_results[$dates[$d]][$i])){
				
					for($p=0; $p<13; $p++){
						foreach($density_results[$dates[$d]][$i] as $row => $value){
							if($value['diff']>0 ){
								if($value['sminute']==$p){
								//check for blocks that are right next to each other and appear as 1 continous block and fix it by stealing 1 pixel from the first one
									$count_absences_for_this_hour=count($density_results[$dates[$d]][$value['ehour']]);
									if($count_absences_for_this_hour>0){
										$this_end_block=$value['eminute'];
										for($k=0; $k<$count_absences_for_this_hour; $k++){
											$suspect_row_block_number=$density_results[$dates[$d]][$value['ehour']][$k]['sminute'];
											if($suspect_row_block_number==$this_end_block){
												$value['diff']=$value['diff']+2;
												//echo "!!!!!!!!Found the thing!!!!!!";
												//print_r($density_results[$dates[$d]][$i]);
											}
											else if($suspect_row_block_number==($this_end_block+1)){
												$value['diff']=$value['diff']-1;
												//echo "!!! found on the same p@@@@";
												//print_r($density_results[$dates[$d]][$i]);
											}
										}
										unset($k, $this_end_block, $suspect_row_block);
									}
									unset($count_absences_for_this_hour);
									if($dates[$d]==$value['eday'])
										imagefilledrectangle($im, $d*12+1, $p*1+($i*12)+($i*1), ($d+1)*12-1,  $p*1+($i*12)+($i*1)+$value['diff'], $rectcolor);
									else{
									
										if($i<23)
											$next_day_diff=$value['diff']-(23-$i)*12-(12-$p);
										else
											$next_day_diff=$value['diff']-(12-$p)-1;
										
										$count_absences_for_next_day=count($density_results[$dates[$d+1]][$value['ehour']]);
										if($count_absences_for_next_day>0){
											$this_end_block=$value['eminute'];
											for($k=0; $k<$count_absences_for_next_day; $k++){
												$suspect_row_block_number=$density_results[$dates[$d+1]][$value['ehour']][$k]['sminute'];
												if($suspect_row_block_number==$this_end_block){
													if($next_day_diff>1)
														$next_day_diff=$next_day_diff-2;
												//echo "!!!!!!!!Found the thing!!!!!!";
												//print_r($density_results[$dates[$d]][$i]);
												}
												else if($suspect_row_block_number==($this_end_block+1)){
													if($next_day_diff>0){
														$next_day_diff=$next_day_diff-1;
													//echo "!!! found on the same p@@@@";
													//print_r($density_results[$dates[$d]][$i]);
													//echo "and $next_day_diff";
													}
												}
											}
											unset($k, $this_end_block, $suspect_row_block);
										}
										unset($count_absences_for_next_day);
									//print "This diff is on ".$value['eday'].": ".$this_diff.$value['diff']."!!";
										imagefilledrectangle($im, $d*12+1, $p*1+($i*12)+($i*1), ($d+1)*12-1, $p*1+($i*12)+($i*1)+(int)($value['diff'])*1+12*(23-$i), $rectcolor);
										if(is_array($density_results[$dates[$d+1]][0])){
											$hour_count=count($density_results[$dates[$d+1]][0]);
											$density_results[$dates[$d+1]][0][$hour_count]['StartDate']=$dates[$d+1]." 00:00:00"; 
											$density_results[$dates[$d+1]][0][$hour_count]['EndDate']=$value['EndDate'];
											$density_results[$dates[$d+1]][0][$hour_count]['sminute'] = 0;
											$density_results[$dates[$d+1]][0][$hour_count]['eday'] = $value['eday'];
											$density_results[$dates[$d+1]][0][$hour_count]['ehour'] = $value['ehour'];
											$density_results[$dates[$d+1]][0][$hour_count]['eminute'] = $value['eminute'];
											$density_results[$dates[$d+1]][0][$hour_count]['diff'] = $next_day_diff; 
									}
								else{
										$density_results[$dates[$d+1]][0][0]['StartDate']=$dates[$d+1]." 00:00:00"; 
										$density_results[$dates[$d+1]][0][0]['EndDate']=$value['EndDate'];
										$density_results[$dates[$d+1]][0][0]['eday'] = $value['eday'];
										$density_results[$dates[$d+1]][0][0]['ehour'] = $value['ehour'];
										$density_results[$dates[$d+1]][0][0]['sminute'] = 0;
										$density_results[$dates[$d+1]][0][0]['eminute'] = $value['eminute'];
                                        $density_results[$dates[$d+1]][0][0]['diff'] = $next_day_diff; 
										}
									}
								}
							}
						}
						unset($row, $value);
					}
					//imagefilledrectangle($im, 41+$key*17, (55+($i*17)), 37+($key+1)*17, (54+($i+1)*17), $rectcolor);
					//imagefilledrectangle($im, 41+$d*17, (55+($i*17)), 37+($d+1)*17, (54+($i+1)*17), $rectcolor);
				}
			}
			if($i==23){
				imageLine($im, 0, 12+(24*12)+12, $width-145, 12+(24*12)+12, $textcolor);
			}
			
		}
	}
	unset($row, $value, $date_arr, $year, $density_results);
	
	//imageLine($im, 0, (56+(24*12)+12), $width, (56+(24*12)+12), $textcolor);
// Output the image
	imageInterlace($im, 1);
	imagepng($im);
	imagedestroy($im);
}

function create_density_legend($what){
	$width=100;
	//$height=250;
	$height=24*12-30;
	$im = imagecreatetruecolor($width, $height);	
	$bg = imagecolorallocate($im, 255, 255, 255);
	imagefill($im, 0, 0, $bg);
	$textcolor = imagecolorallocate($im, 0, 0, 0);
	//$level0=imagecolorallocate($im, 255, 255, 255);
	$level1=imagecolorallocate($im, 204, 204, 204);
	$level2=imagecolorallocate($im, 255, 255, 172);
	$level3=imagecolorallocate($im, 255, 255, 0);
	$level4=imagecolorallocate($im, 204, 255, 121);
	$level5=imagecolorallocate($im, 190, 238, 0);
	$level6=imagecolorallocate($im, 126, 216, 10);
	$level7=imagecolorallocate($im, 81, 255, 168);
	$level8=imagecolorallocate($im, 0, 190, 190);
	$level9=imagecolorallocate($im, 100, 170, 255);
	$level10=imagecolorallocate($im, 100, 100, 255);
	$level11=imagecolorallocate($im, 0, 0, 255);
	$red = imagecolorallocate($im, 255, 100, 100);
	$black = imagecolorallocate($im, 10, 10, 10);
	
	if($what=='Density'){
		/*$legendTable = array('0-49'=>$bg, '50-99'=>$level1, '100-149'=>$level2,  '150-199'=>$level3, '200-249'=>$level4, '250-299'=>$level5, '300-349'=>$level6, '350-399'=>$level7, 
	'400-449'=>$level8, '450-499'=>$level9, 
	'500-549'=>$level10, '550+'=>$level11, 'Out Of Apartment'=>$black);*/
	$legendTable = array('550+'=>$level11, '500-549'=>$level10, '450-499'=>$level9, '400-449'=>$level8, '350-399'=>$level7,  '300-349'=>$level6, '250-299'=>$level5, '200-249'=>$level4, '150-199'=>$level3, '100-149'=>$level2,'50-99'=>$level1, '0-49'=>$bg, 'Out Of Apartment' => $black);
	}
		
	elseif($what =='Bedtime(Hydraulic)'){
		/*	$legendTable = array('64-129'=>$level1, '130-194'=>$level2,  '195-259'=>$level3, '260-324'=>$level4, '325-389'=>$level5, '390-454'=>$level6, '455-519'=>$level7, 
	'520-584'=>$level8, '585-649'=>$level9, 
	'650-714'=>$level10, '715+'=>$level11, 'Out Of Bed'=>$bg);*/
	
	$legendTable = array( 'Out Of Bed'=>$bg,  '715+'=>$level11, '650-714'=>$level10, '585-649'=>$level9, '520-584'=>$level8, '455-519'=>$level7,  '390-454'=>$level6,  '325-389'=>$level5,  '260-324'=>$level4,  '195-259'=>$level3, '130-194'=>$level2,  '0-129'=>$level1);
	}
	
	elseif($what=='Bathroom'){
		$legendTable = array('Bathroom Visit'=>$red);
	}
	
//	if($what=='Density' || $what =='Bedtime(Hydraulic)'){
		$counter=0;
		foreach($legendTable as $key => $value){		
			if($key=='Out Of Apartment')
				$textcolor = imagecolorallocate($im, 255, 255, 255);
			else
				$textcolor = imagecolorallocate($im, 0, 0, 0);
			imagefilledrectangle($im, $width-100, ($counter*20), $width, 20+($counter*20), imagecolorallocate($im, 0, 0, 0));
			imagefilledrectangle($im, $width-98, 1+($counter*20), $width-2, 20-2+($counter*20), $value);
			imagestring($im, 2, 5, 1+($counter*20), $key, $textcolor);
			$counter++;
		}
//	}
	
	
	imagepng($im);
	imagedestroy($im);
	unset($key, $value, $legendTable, $counter, $im);
}
/**************************************************************************/
foreach($_GET as $key => $value){
	$_GET[$key]=urldecode(htmlentities($value, ENT_QUOTES));
}

if(isset($_GET['what'])&&!empty($_GET['what'])){
	$what=$_GET['what'];
	if(isset($_GET['legend']) && $_GET['legend']=='true' )
		create_density_legend($what);
	else
		make_density_image($what);
}

else{
	make_density_image();
}
//ob_end_flush();

?>