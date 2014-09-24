<?php
//require('System/dbQuery.php');

function get_data_daily($resid, $startdate, $enddate, $trend)
{
	/*create arrays of Sensor data for the charts*/
			$params = array($resid, $startdate, $enddate);
			$results = array('date', 'avg', 'max', 'min');
			switch($trend){
			case 'km':
				$query = 102;
				break;
			case 'ht':
				$query = 127;
				break;
			case 'wppd':
				$query = 133;
				break;
			case 'resp':
				$query = 103;
				break;
			}					
			$data = DB_Query($query, NULL, $params, $results, NULL, NULL, NULL, NULL);
			/*	$ht = DB_Query(127, NULL, $params, $results, NULL, NULL, NULL, NULL);
			$wppd = DB_Query(133, NULL, $params, $results, NULL, NULL, NULL, NULL);
			$resp = DB_Query(103, NULL, $params, $results, NULL, NULL, NULL, NULL);
			*/
			return $data;

}

function get_hydro_data($resid, $precision, $startdate, $enddate)
{
	//echo $precision;
	if($precision=='Hourly'){
		/*hourly data*/
		$params = array($resid, $startdate, $enddate);
		$results = array('time', 'date', 'avg', 'max', 'min');
		$km = DB_Query(104, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$ht = DB_Query(128, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$wppd = DB_Query(134, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$resp = DB_Query(105, NULL, $params, $results, NULL, NULL, NULL, NULL);
		//$results = array('num', 'date', 'sym');
		/*$rest1 = DB_Query(114, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$rest2 = DB_Query(115, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$rest3 = DB_Query(116, NULL, $params, $results, NULL, NULL, NULL, NULL);
		*/
		$results = array('date', 'hour', 'time');
		$NightTimeInBed = DB_Query(159, NULL, $params, $results, NULL, NULL, NULL, 'date');
		//$NightTimeInBed = DB_Query(158, NULL, $params, $results, NULL, NULL, NULL, 'date');
		//$results = array('date', 'time');
		//$TimeInBed2 = DB_Query(156, NULL, $params, $results, NULL, NULL, NULL, 'date');
		//$TimeInBed3 = DB_Query(157, NULL, $params, $results, NULL, NULL, NULL, 'date');
		$rest = DB_Query(160, NULL, $params, $results, NULL, NULL, NULL, 'date');
		//$results = array('num', 'date', 'sym');
		}
		if($precision=='Second')
		{
		/*15 second*/
		$params = array($resid, $startdate, $enddate);
		$results = array('date', 'time', 'value');
		$km = DB_Query(147, NULL, $params, $results, NULL, NULL, NULL, NULL);
		
		$ht = DB_Query(148, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$wppd = DB_Query(149, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$resp = DB_Query(146, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$rest1 = DB_Query(148, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$results = array('time', 'value');
		$rest = DB_Query(161, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$NightTimeInBed = array();
		$rest2 = array();
		$rest3 = array();
		}
	else if($precision=='Daily')
	{
		$params = array($resid, $startdate, $enddate);
		$results = array('date', 'avg', 'max', 'min');
		$km = DB_Query(102, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$ht = DB_Query(127, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$wppd = DB_Query(133, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$resp = DB_Query(103, NULL, $params, $results, NULL, NULL, NULL, NULL);
		//print_r($resp);
		$results = array('date', 'time');
		$TimeInBed = DB_Query(154, NULL, $params, $results, NULL, NULL, NULL, 'date');
		$NightTimeInBed = DB_Query(158, NULL, $params, $results, NULL, NULL, NULL, 'date');
		//$results = array('date', 'time');
		//$TimeInBed2 = DB_Query(156, NULL, $params, $results, NULL, NULL, NULL, 'date');
		//$TimeInBed3 = DB_Query(157, NULL, $params, $results, NULL, NULL, NULL, 'date');
		$rest = DB_Query(155, NULL, $params, $results, NULL, NULL, NULL, 'date');
		
		//foreach($TimeInBed1 as $key=>$value)
		//{
			
			//if($TimeInBed2[$key])
			//	$TimeInBed[0]['time']
			//$TimeInBed[$key] = $value[0]['time'] + $TimeInBed2[$key][0]['time'] + $TimeInBed3[$key][0]['time'];
		//}
		$results = array('num', 'date', 'sym');
		$rest1 = DB_Query(141, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$rest2 = DB_Query(142, NULL, $params, $results, NULL, NULL, NULL, NULL);
		$rest3 = DB_Query(143, NULL, $params, $results, NULL, NULL, NULL, NULL);
		
	//print_r($TimeInBed);
	//print_r($rest);
	}
			return array($km, $ht, $wppd, $resp, $rest1, $rest2, $rest3, $rest, $NightTimeInBed);
	
	
}

function create_line_array($dates, $array, $trend, $precision='Daily')
{
	if($precision=='Hourly')
	{
		$col = 0;
			foreach($dates as $day){
				for($i = 0; $i < 24; $i++)
				{
						if($array[$col]['time'] < 10)
							$correctime = $array[$col]['date']." 0".$array[$col]['time'].":00:00 GMT";
						else
							$correctime = $array[$col]['date']." ".$array[$col]['time'].":00:00 GMT";
						$unixtime = strtotime($correctime) * 1000;
						//echo $unixtime
					
					if($day != $array[$col]['date'] || $i != $array[$col]['time'])
						$unixtime = null;
					else
						$col++;	
					
					$arr[] = array($unixtime, $array[$col][$trend]);
				//	$KMzoommin[] =  array($unixtime, $zoompulse[$col]['min']);
				//	$KMzoommax[] =  array($unixtime, $zoompulse[$col]['max']);
			}
		}
	
	}
	else if($precision=='Second')
	{
	
			for($col=0;$col < count($array); $col++){
					$correctime = $array[$col]['time']." GMT";
					$unixtime = strtotime($correctime) * 1000;
					$arr[] = array($unixtime, $array[$col]['value']);
			}
	}
	else{
		$col = 0;
		foreach($dates as $day){
				$correctime = $array[$col]['time']." 00:00:00 GMT";
				$unixtime = strtotime($correctime) * 1000;
				//	echo $unixtime;
			if($day != $array[$col]['time'])
				{
					$unixtime = null;
					$arr[] = array($unixtime, $array[$col][$trend]);
				}
				else
				{
					//echo $array[$col][$trend];
					$arr[] = array($unixtime, $array[$col][$trend]);
					$col++;
				}
					//$min[] = array($unixtime, $array[$col]['min']);
				//	$max[] = array($unixtime, $array[$col]['max']);
					
		
		}
	}
	//print_r($arr);
	return $arr;
}

function create_bar_array($dates, $array, $level, $precision='Daily')
{
	if(empty($array)){
	$arr[] = array();
	return $arr;
	}
	if($precision=='Hourly')
	{
	$col = 0;
		foreach($dates as $day){
				for($i = 0; $i < 24; $i++)
				{
						if($array[$col]['time'] < 10)
							$correctime = $array[$col]['date']." 0".$array[$col]['time'].":00:00 GMT";
						else
							$correctime = $array[$col]['date']." ".$array[$col]['time'].":00:00 GMT";
						$unixtime = strtotime($correctime) * 1000;
						//echo $unixtime
					
					if($day != $array[$col]['date'] || $i != $array[$col]['time'])
						$unixtime = null;
					else
						$col++;	
					
					$arr[] = array($unixtime, $array[$col][$level]);
				//	$KMzoommin[] =  array($unixtime, $zoompulse[$col]['min']);
				//	$KMzoommax[] =  array($unixtime, $zoompulse[$col]['max']);
			}
			}
		}
		else if($precision=='Second')
		{
	
			for($col=0;$col < count($array); $col++){
					$correctime = $array[$col]['time']." GMT";
						$unixtime = strtotime($correctime) * 1000;
					$arr[] = array($unixtime, $array[$col]['value']);
			}
		}
		else{
		$col = 0;
		foreach($dates as $day){
					$correctime = $array[$col]['date']." 00:00:00 GMT";
					$unixtime = strtotime($correctime) * 1000;
					//echo $unixtime;
				if($day != $array[$col]['date'])
				{
					$unixtime = null;
						$arr[] = array($unixtime, $array[$col][$level]);
						}
				else
				{
					$arr[] = array($unixtime, $array[$col][$level]);
					$col++;
				}	
					//$min[] = array($unixtime, $array[$col]['min']);
				//	$max[] = array($unixtime, $array[$col]['max']);
					
		
		}
		}
		return $arr;
}

function create_restless_array($dates, $rest, $TimeInBed, $precision)
{
	if($precision=='Daily')
	{
		foreach($dates as $day)
		{
			foreach($rest as $key=>$value)
			{
				$correctime = $key." 00:00:00 GMT";
				$unixtime = strtotime($correctime) * 1000;
			
					if($day != $key)
					{
						$unixtime = strtotime($day." 00:00:00 GMT")*1000;
						$restlesstime[] = array($unixtime, 0);
						$arr[] = array($unixtime, 0, "");
					}
				else{
						
						if($TimeInBed[$key][0]['time'])
							$remaindertime = $TimeInBed[$key][0]['time']/3600 - $rest[$key][0]['time']/3600;
						else
							$remaindertime = 0;
							
						if($remaindertime == 0)
							$tooltip = floor($rest[$key][0]['time']/3600)." hours ".(floor($rest[$key][0]['time']/60) % 60)." minutes"; 
						else
							$tooltip = floor($TimeInBed[$key][0]['time']/3600)." Hours and ".(floor($TimeInBed[$key][0]['time']/60) % 60)." minutes"; 
							
						$restlesstime[] = array($unixtime, $value[0]['time']/3600, $tooltip);
						$arr[] = array($unixtime, $remaindertime, $tooltip);
					}	
			}					
		}
	}
	elseif($precision=='Hourly')
	{
		//$col = 0;
		//col2 = 0;
		foreach($dates as $day)
		{
			foreach($rest as $key=>$value)
			{
				$col = 0;
				$col2 = 0;
				for($i = 0; $i < 24; $i++)
				{
					
					$tooltip = "";
					//echo $unixtime;
				if($day != $key || $i != $value[$col]['hour'])
				{
					$unixtime = strtotime($day." ".$i.":00:00 GMT")*1000;
					$arr[] = array($unixtime, 0);
					$restlesstime[] = array($unixtime, 0);
				}
				else
				{			
					if($value[$col]['hour'] < 10)
							$correctime = $key." 0".$value[$col]['hour'].":00:00 GMT";
					else
							$correctime = $key." ".$value[$col]['hour'].":00:00 GMT";
					$unixtime = strtotime($correctime) * 1000;
					
					
					
					
					
					if($TimeInBed[$key])
					{
						if($value[$col]['hour'] > $TimeInBed[$key][$col2]['hour']){
							$col2++;
							}
						elseif($value[$col]['hour'] < $TimeInBed[$key][$col2]['hour'])
							$col2--;
						if($value[$col]['hour']==$TimeInBed[$key][$col2]['hour'])
						{
							/*This is a temporary patch... we need to go back and make a more efficient query*/
							if($TimeInBed[$key][$col2]['time']>3600){
								$remaindertime = 60 - $rest[$key][$col]['time']/60;
								$tooltip = "60 minutes";
								}
							else{
								$remaindertime = $TimeInBed[$key][$col2]['time']/60 - $rest[$key][$col]['time']/60;	
								$tooltip = floor($TimeInBed[$key][$col2]['time']/60)." minutes";
								}
						}
						else{
							$remaindertime = 0;
							$tooltip = (floor($value[$col]['time']/60 + $remaindertime))." minutes";
							}
					}
					else{
						$remaindertime = 0;
						$tooltip = (floor($value[$col]['time']/60 + $remaindertime))." minutes";
					}
					$restlesstime[] = array($unixtime, $value[$col]['time']/60, $tooltip);
					$arr[] = array($unixtime, $remaindertime, $tooltip);
					$col++;
					$col2++;
				}
				//$min[] = array($unixtime, $array[$col]['min']);
				//$max[] = array($unixtime, $array[$col]['max']);
					}
			}
		}
	}
	else{
	$arr[] = array();
		for($col=0;$col < count($rest); $col++){
					$correctime = $rest[$col]['time']." GMT";
					$unixtime = strtotime($correctime) * 1000;
					$restlesstime[] = array($unixtime, $rest[$col]['value']);
					
			}
	}
		
	
	return array($restlesstime, $arr);
}

function create_alertarray($dates, $array)
{
	if(empty($array)){
		foreach($dates as $day){
			$correctime = $day." 00:00:00 GMT";
			$unixtime = strtotime($correctime) * 1000;
			$arr[] = array($unixtime, 0);
		}
		return $arr;
	}
	
	foreach($array as $alertd)
		foreach($alertd as $key=>$value)
			$details[$key] = $value['Name']." ".$value['Notes'];
			
			
	foreach($dates as $day){
		foreach($array as $key=>$value){
					$tooltip = "";
					$correctime = $key." 00:00:00 GMT";
					$unixtime = strtotime($correctime) * 1000;
					//echo $unixtime;
				if($day != $key)
				{
					$unixtime = strtotime($day." 00:00:00 GMT")*1000;
					$arr[] = array($unixtime, 0, $tooltip);
				}
				else{
						$alert=count($value);
						for($i = 0;$i < count($value); $i++)
						{
							if($value[$i]['Algorithm']==1)
								$alg = "24 HOUR";
							if($value[$i]['Algorithm']==2)
							$alg = "NIGHT TIME";
							if($value[$i]['Algorithm']==5)
							$alg = "DAY TIME"; 
							$tooltip = $tooltip.$alg." ".$value[$i]['Name']." ".$value[$i]['Notes']."<br>";
						}
						$arr[] = array($unixtime, $alert, $tooltip);
					}	
		}					
	}
	return array($arr, $details);
}

function create_array($dates, $array, $level='all_hits', $precision='Daily', $location)
{
	if(empty($array)){
		$arr = array();
		return $arr;
	}
	

		if($precision=='Daily')
		{
			
				$col = 0;
				foreach($dates as $day){
					foreach($array[$location] as $key=>$value){
					$correctime = $array[$location][$key][$col]['day']." 00:00:00 GMT";
					$unixtime = strtotime($correctime) * 1000;
					//echo $unixtime;
				if($day != $array[$location][$key][$col]['day'])
				{
					$unixtime = strtotime($day." 00:00:00 GMT")*1000;
						$arr[] = array($unixtime, 0);
					}
				else{
					
					$amount = 0;
						while($day == $array[$location][$key][$col]['day'])
						{
							$amount += $array[$location][$key][$col][$level];
							
							$col++;
						}
						
						$arr[] = array($unixtime, $amount);
					}
					//$min[] = array($unixtime, $array[$col]['min']);
				//	$max[] = array($unixtime, $array[$col]['max']);
					
				
				}
			}
		}
		
		elseif($precision=='Hourly')
		{
			
			$col = 0;
			foreach($dates as $day){
				for($i = 0; $i < 24; $i++)
				{
					foreach($array[$location] as $key=>$value){
					if($array[$location][$key][$col]['hour'] < 10)
							$correctime = $array[$location][$key][$col]['day']." 0".$array[$location][$key][$col]['hour'].":00:00 GMT";
						else
							$correctime = $array[$location][$key][$col]['day']." ".$array[$location][$key][$col]['hour'].":00:00 GMT";
					$unixtime = strtotime($correctime) * 1000;
					//echo $unixtime;
				if($day != $array[$location][$key][$col]['day'] || $i != $array[$location][$key][$col]['hour'])
				{
					$unixtime = strtotime($day." ".$i.":00:00 GMT")*1000;
					$arr[] = array($unixtime, 0);
				}
				else
				{				
					$arr[] = array($unixtime, $array[$location][$key][$col]['all_hits']);
						$col++;
				}
				//$min[] = array($unixtime, $array[$col]['min']);
				//$max[] = array($unixtime, $array[$col]['max']);
					
				}
			}
			}
		}
		
		return $arr;
		//}
}

function create_zline_array($dates, $array, $trend){
		$col = 0;
		foreach($_SESSION['dates'] as $day){
			for($i = 0; $i < 24; $i++)
			{
						if($array[$col]['time'] < 10)
							$correctime = $array[$col]['date']." 0".$array[$col]['time'].":00:00 GMT";
						else
							$correctime = $array[$col]['date']." ".$array[$col]['time'].":00:00 GMT";
						$unixtime = strtotime($correctime) * 1000;
						//echo $unixtime
					
					if($day != $array[$col]['date'] || $i != $array[$col]['time'])
						$unixtime = null;
					else
						$col++;	
					
					$arr[] = array($unixtime, $array[$col][$trend]);
				//	$KMzoommin[] =  array($unixtime, $zoompulse[$col]['min']);
				//	$KMzoommax[] =  array($unixtime, $zoompulse[$col]['max']);
			}
		}
		return $arr;
}
?>

