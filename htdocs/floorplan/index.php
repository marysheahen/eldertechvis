
<html>
  <head>
 

  <link href="/tabs.css" rel="stylesheet" />
  <style>
  table {
	margin:10px;
    border-collapse: collapse;
	 width: 380px;
	}
  table, th, td {
    border: 1px solid black;
	}
	th{
		height:100px;
	}
	td{
		height:30px;
	}
  </style>

<?php
/**********************************
this is a simple php file which loads the floor map image for a resident
*******************************/
require('../health/includes/session.php');
require("../health/includes/dbQuery.php");

?>
</head><body>
 <div class="tabBox" style="clear:both;">
    <div class="tabArea">
      <a class="tab" href="/index.php">Home</a>
      <a class="tab" href="/health/index.php">Health</a>
      <?php echo '<a class="tab" href="/health/density2.php?resid='.$_SESSION['resid'].'">Density</a>'; ?>
      <a class="tab" href="/gait/index.html">Gait</a>
      <a class="tab" href="/rewind/index.php">Rewind</a>
	  <?php echo '<a class="tab" href="/floorplan/index.php?resid='.$_SESSION['resid'].'">Floorplan</a>';?>
    </div>
    <div class="tabMain">
	
<?php
if(isset($_REQUEST['resid']))
{
	
	echo "<div class='demo-placeholder'><img style='float:left;margin:5%;margin-right:2%;border: 4px solid black;'  height='700px' src='../floorplans/".$_REQUEST['resid'].".png' alt='Floorplan not currently Available for Resident ".$_REQUEST['resid']."'></div>";
	$params = array($_REQUEST['resid']);
	$results = array('UserID',
    'Apartment',
    'Sensor Location',
    'Sensor Type',
    'Sensor X10 Address',
    'Sensor DB ID',
    'Battery Last Changed',
    'Battery Status');
	$params=array($_SESSION['resid']);
			$results=array('LocationType', 'SensorType', 'SensorID');
			$array_key='LocationType';
			
				$sensorlist=DB_Query(91, NULL, $params, $results, NULL, NULL, NULL, $array_key);
	//print_r($sensorlist);		
	?>
	<div style='background-color:white;float:left;
	margin-top:5%;height:700px;width:400px;border: 4px solid black; ' ><table>
	
	<tr>
<th>Sensor Type</th>
<th>Sensor Location</th>
</tr>
<?php
	foreach($sensorlist as $key=>$value)
	{
		echo "<tr>";
		echo "<td>" . $value[0]['SensorType'] . "</td>";
		echo "<td>" . $key . "</td>";
		echo "</tr>";	
	}
	
	echo "</table></div></div></body></html>";
}
else
echo "No resid Specified";
?>
