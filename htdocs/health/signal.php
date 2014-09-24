<?php
/*********************

this page is to be used for viewing signal images

******************/

//echo "under construction";
//require("includes/dbQuery.php");
require("includes/session.php");



if(isset($_REQUEST['Month']) && isset($_REQUEST['Year']) && isset($_REQUEST['Day']) && isset($_REQUEST['Time']) && isset($_REQUEST['Min']) && isset($_REQUEST['Sec']))
{
	$month = $_REQUEST['Month'];
	$year = $_REQUEST['Year'];
	$day = $_REQUEST['Day'];
	$hour = $_REQUEST['Time'];
	$minute = $_REQUEST['Min'];
	$second = $_REQUEST['Sec'];
	$trans = $_REQUEST['trans'];
	$resid = $_SESSION['Resid'];
	$resid = 3004;
	if(strlen($month) < 2)
	$month = '0'.$month;
if(strlen($day) < 2)
	$day = '0'.$day;
	$currenttime=strtotime($year."-".$month."-".$day." ".$hour.":".$minute.".".$second);
	//echo $day." ".$hour.":".$minute.".".$second;
	//echo $year."-".$month."-".$day." ".$hour.":".$minute.".".$second;
//print($currenttime);
//$day = 27;
//$data = array();

$files =  scandir("/mnt/zigbee/".$resid."/data/".$year."/".$month."/".$day."/", 1);
//$files = scandir("/mnt/zigbee/3004/data/2014/08/08/", 1);

//print_r($files);
$max = sizeof($files)-3;


//find which file to process.
$i = 0;
while($i < $max)
{
	list($dates[$i], $filetype[$i]) = explode('.', $files[$i]);
	list($years[$i], $months[$i], $days[$i], $hours[$i], $minutes[$i], $seconds[$i]) = explode('_', $dates[$i]);
	
	 if(strtotime($years[$i]."-".$months[$i]."-".$days[$i]." ".$hours[$i].":".$minutes[$i].".".$seconds[$i]) <= $currenttime)
	 {
	//	print($years[$i]."-".$months[$i]."-".$days[$i]." ".$hours[$i].":".$minutes[$i].".".$seconds[$i]);
		$stringtime = strtotime($years[$i]."-".$months[$i]."-".$days[$i]." ".$hours[$i].":".$minutes[$i].".".$seconds[$i]);
		break;
	}
	 $i++;
}//print_r($files);
$timediffsec = $currenttime - $stringtime;
$timediff = $currenttime - $stringtime;
//process $files[$i];
$path = "/mnt/zigbee/".$resid."/data/".$year."/".$month."/".$day."/".$files[$i];
//$path = "/mnt/zigbee/3004/data/2014/08/08/2014_08_08_00_11_24.bed";
$filestring = "importbeddata('$path', $trans)";
//echo $filestring;
$cmd = '/usr/local/MATLAB/R2014a/bin/matlab -nosplash -nodisplay -r "'.$filestring.'"';

	exec($cmd, $output, $return);
//	echo $return;
	//print_r($output);
	//print_r($return);
	$d = ($timediffsec * 100 + 28);
			$max = $d + 1500;
			for($d; $d < $max; $d++){
			//	$hour2=$hour;
			//	$minute2 =$minute;
				//$sec2=$second;		
				$answer = $output[$d];
   $arr[] = array($d, $answer); 
}
	echo json_encode($arr); 
 }
 
 else

echo "ERROR";
?>