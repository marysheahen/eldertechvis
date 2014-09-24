<?php

  $SiteTitle = 'SmartAmerica Test Site';

?>
<html>
  <head>
  <link href="css/bootstrap.css" rel="stylesheet">
  <link href="css/bootstrap-combobox.css" rel="stylesheet">
  <link href="css/datepicker.css" rel="stylesheet">
  <link href="css/examples.css" rel="stylesheet" type="text/css">
  <link href="/tabs.css" rel="stylesheet" />

  <script src="js/jquery-1.11.0.min.js"></script>
  <script language="javascript" type="text/javascript" src="js/jquery.flot.min.js"></script>
  <script language="javascript" type="text/javascript" src="js/jquery.flot.time.js"></script>
  <script language="javascript" type="text/javascript" src="js/jquery.flot.selection.min.js"></script>
  <script language="javascript" type="text/javascript" src="js/jquery.flot.rangeselection.js"></script>
  <script language="javascript" type="text/javascript" src="js/jquery.flot.stack.min.js"></script>
   <script src="js/bootstrap.js"></script>
   <script language="javascript" type="text/javascript" src="js/chart.js"></script>
  <style>

  </style>
  
  <?php	
    require("includes/dbQuery.php");
    require("includes/session.php");
    require("includes/usefulFunctions.php");
    require("includes/new-chartfunctions.php");
    
    /***********************************
    call Data from MYSQL database 
    ***********************************/
  
      $results=array('UserID');
      $get_ids=DB_Query(88, NULL, NULL, $results); 
    
      //thes ID's are either a test ID or rID with multiple residents (A and B), Currently hard coded
      $nonres = array(8675309, 3045, 3017, 3057);
      $startdate = date('Y-m-d', strtotime('today - 1 month'));
      $enddate = date('Y-m-d', strtotime('today'));
	 if(isset($_POST['resident']))
	 {
			$resid =  $_POST['resident'];
			$_SESSION['resid'] = $_POST['resident'];
			$sdate =  $_POST['StartDate'];
		 $_SESSION['start_date'] = $sdate;
		$edate =  $_POST['EndDate'];
	 $_SESSION['end_date'] = $edate;
	  }
    
    //	echo $_POST['resident'];
      //get post fields//
      if(isset($_SESSION['resid']))
      {
        $resid= $_SESSION['resid'];
       // $_SESSION['resid'] = $resid;
        if(strlen($resid)>4)
          $motresid = substr($resid, 0, -1);
        else
          $motresid = $resid;
      
      if(isset($_POST['timetoggle'])) 
        $precision = $_POST['timetoggle'];
      
      $results = array('users');
      //$params = array('param');
      $result= DB_Query(108, NULL, $params=NULL, $results, NULL, NULL, NULL, NULL);
      //print_r($reswithhydro);
      $reswithhydro = array();
      for($col = 0; $col < count($result); $col++){
        array_push($reswithhydro, $result[$col]['UserID']);
      }
      array_push($reswithhydro, '3017', '3045');
        
      if($precision=='Hourly'){
        $widthbar = 60*60*1000;
      
        if(isset($_SESSION['end_date']) && !empty($_SESSION['start_date']))
          $edate = $_SESSION['end_date'];
		if(isset($_POST['currentmax']))
          $edate = date('Y-m-d', ($_POST['currentmax']/1000));
		  if(isset($_POST['switch']) && $_POST['switch']=='true')
			$enddate = date('Y-m-d', strtotime($edate." + 0 days"));
		else
			 $enddate = date('Y-m-d', strtotime($edate." + 1 days"));
        $startdate = date('Y-m-d', strtotime($edate." - 4 days"));
        $currmin =  strtotime($enddate." -1 days")*1000;
        $currmax = strtotime($enddate." + 1 days")*1000;
        list($pulse, $ht, $wppd, $resp, $rest1, $rest2, $rest3, $rest, $TimeInBed) = get_hydro_data($_SESSION['resid'], $precision, $startdate, $enddate);
        //print_r($pulse);
        }
      /*Get Parameters for date range*/
      
      else if($precision=='Second'){
        $widthbar = 15*1000;
        if(isset($_POST['currentmax'])){
		 $edate = date('Y-m-d', ($_POST['currentmax']/1000));
		if(isset($_POST['switch']) && $_POST['switch']=='true')
			$enddate = date('Y-m-d', strtotime($edate." + 0 days"));
		else
			 $enddate = date('Y-m-d', strtotime($edate." + 1 days"));
		}
        else if(isset($_SESSION['end_date']) && !empty($_SESSION['start_date']))
		 $edate = $_SESSION['end_date'];
		
    //    $enddate = date("Y-m-d", strtotime($edate." + 0 day"));
        $startdate = date('Y-m-d', strtotime($edate." - 3 days"));
        $currmin = strtotime($edate." +19 hours")*1000;
        $currmax = strtotime($edate." + 43 hours")*1000;
        list($pulse, $ht, $wppd, $resp, $rest1, $rest2, $rest3) = get_hydro_data($_SESSION['resid'], $precision, $startdate, $enddate);
      }
      else{
        $widthbar = 60*60*1000*24;
        $params=array($motresid);
        $results=array('min_date', 'max_date');		
        $date_range=DB_Query(73, NULL, $params, $results); 
		$num_rows=count($date_range);
		if($num_rows<1){
			$date_range=DB_Query(5, NULL, $params, $results);
			//print_r($get_date_range);
			if(strtotime("now")-strtotime($date_range[0]['max_date'])>=5259487){
				$params=array($resid, $date_range[0]['min_date'], $date_range[0]['max_date']);
				//array_push($former_residents, $resid);
				$query=89;
			}
			else{
				$params=array($resid, $date_range[0]['min_date']);
				$date_range[0]['max_date']=NULL;
				$query=90;
			}
			DB_Query($query, NULL, $params);
		}
		
		//$sdatetime=date_from_DB($get_date_range[0]['min_date']);
		//$edatetime=date_from_DB($get_date_range[0]['max_date']);
        //print_r($date_range);
        //$startdate = date('Y-m-d', strtotime($date_range[0]['min_date']));
        if(isset($_POST['currentmax'])){
            $m = date('Y-m-d', ($_POST['currentmax']/1000));
           
          }
        else 
        {
          if(isset($_SESSION['end_date']) && !empty($_SESSION['start_date']))
          $m = $_SESSION['end_date'];
          
          if(!empty($date_range[0]['max_date']))
          {
			$m = $date_range[0]['max_date'];
          }
        
      }
          if(isset($_SESSION['end_date']) && !empty($_SESSION['start_date']))
          $startdate = $_SESSION['start_date'];
        
        //echo($startdate)
      
       if(isset($_SESSION['end_date']) && !empty($_SESSION['start_date']))
          $m = $_SESSION['end_date'];
          
        if(!empty($date_range[0]['max_date']))
          {
          $m = $date_range[0]['max_date'];
          }
		 
        $currmin= strtotime($m." - 2 weeks")*1000;
        $enddate = date('Y-m-d', strtotime($m));
		 $startdate = date("Y-m-d", strtotime($m." - 1 month"));
        $currmax = strtotime($m)*1000;
      
  //echo $startdate;
    //echo $enddate;
      /*create arrays of Sensor data for the charts*/
      list($pulse, $ht, $wppd, $resp, $rest1, $rest2, $rest3, $rest, $TimeInBed) = get_hydro_data($_SESSION['resid'], $precision, $startdate, $enddate);
    //	print_r($pulse);
      }
        
    // print_r($rest);	
      //print_r($TimeInBed);
      /*Create the alerts array*/
      
    
    if($precision !='Second')
    {
      
        
      $params=array($motresid);
      $results=array('LocationType', 'SensorType', 'SensorID');
      $array_key='LocationType';
      //echo date('H-i-s', strtotime('now'));
      $dates = make_dates($startdate, $enddate);
      $sensor_list=DB_Query(91, NULL, $params, $results, NULL, NULL, NULL, $array_key);
      //echo date('H-i-s', strtotime('now'));
            foreach($sensor_list as $location => $arr){
        if($location=='Default')//default is not a real sensor location
          continue;
        else
        $results=array('day', 'all_hits', 'hour', 'night_hits', 'day_hits', 'carryover'); //MySQL takes care of differentiating day/night/24 and counting
        $array_key='day';
    //print_r($sensor_list);
    /*************************************
      Motion Graphs
    ****************************************/
        
        if(is_array($arr)&& array_key_exists(0, $arr)){ //there are multiple sensors at this location, so shenanigans
          
          foreach($arr as $key => $value){
            $params=array($motresid, $value['SensorID'],  $startdate,  date('Y-m-d', strtotime($enddate." + 2 days")));
      
            $this_result=DB_Query(92, NULL, $params, $results, NULL, NULL, NULL, NULL);
          
            $count=count($this_result);
            if($count>0){
              if(is_array($all_sensors[$location])&&array_key_exists($value['SensorType'], $all_sensors[$location])){
                if(array_key_exists(0, $all_sensors[$location][$value['SensorType']])){
                  array_push($all_sensors[$location][$value['SensorType']], $this_result);
                }
                else{
                  $temp=$all_sensors[$location[$value['SensorType']]];
                //	print_r($temp);
                //	print "<br />";
                  $all_sensors[$location[$value['SensorType']]]=array();
                  array_push($all_sensors[$location[$value['SensorType']]], $temp);
                  array_push($all_sensors[$location[$value['SensorType']]], $this_result);
                }
              }
              else{
                $all_sensors[$location][$value['SensorType']]=$this_result;
              }
            }
          }
        }
        else{ //there is only one sensor at this location
          
          $params=array($motresid, $arr['SensorID'], $startdate,  date('Y-m-d', strtotime($enddate." + 2 days")));
          $sensor_string.="<tr><td>".$arr['SensorID']."</td><td>".$location."</td><td>".$arr['SensorType']."</td></tr>";
        
          
            $this_result=DB_Query(144, NULL, $params, $results, NULL, NULL, NULL, NULL);
          $count=count($this_result);
          if($count>0){
            if(is_array($all_sensors[$location])&&array_key_exists($arr['SensorType'], $all_sensors[$location])){
              if(array_key_exists(0, $all_sensors[$location][$arr['SensorType']])){
                array_push($all_sensors[$location][$arr['SensorType']], $this_result);
              }
              else{
                $temp=$all_sensors[$location[$arr['SensorType']]];
              
                $all_sensors[$location[$arr['SensorType']]]=array();
                array_push($all_sensors[$location[$arr['SensorType']]], $temp);
                array_push($all_sensors[$location[$arr['SensorType']]], $this_result);
              }
            }
            else{
              $all_sensors[$location][$arr['SensorType']]=$this_result;
            }
          }
        }
        if($location != 'Bed' && $location != 'Bed2' && $location != 'Shower'){
        $motion[$location] = create_array($dates, $all_sensors, 'all_hits', $precision, $location);
        $motionday[$location] = create_array($dates, $all_sensors, 'day_hits', $precision, $location);
        $motionnight[$location] = create_array($dates, $all_sensors, 'night_hits', $precision, $location);
        }
      }
     // print_r($all_sensors);
        
    
      }
    $params=array($motresid, $startdate, $enddate);
        $results=array('id', 'day', 'Name', 'Notes', 'Algorithm', 'UserID');
        $array_key='day';
        $alerts=DB_Query(87, NULL, $params, $results, NULL, NULL, NULL, $array_key);
    //echo $alerts['2014-04-14'][0]['Name'];
    //print_r($alerts);
    foreach($all_sensors['Kitchen'] as $key=>$value)
    {
      //echo $key.": ".$all_sensors['Kitchen'][$key][10]['day']." ".$all_sensors['Kitchen'][$key][10]['all_hits'];
    }

    /*******************************************************************************************
    load the php arrays in to correct format
    ***********************************************************************************************/
    $KMavg = create_line_array($dates, $pulse, 'avg', $precision);   
    $KMmin = create_line_array($dates, $pulse, 'min', $precision);   
    $KMmax = create_line_array($dates, $pulse, 'max', $precision);
    $HTavg= create_line_array($dates, $ht, 'avg', $precision);
    $HTmin =  create_line_array($dates, $ht, 'min', $precision);
    $HTmax =  create_line_array($dates, $ht, 'max', $precision);
    $WPPDavg =create_line_array($dates, $wppd, 'avg', $precision);
    $WPPDmin =  create_line_array($dates, $wppd, 'min', $precision);
    $WPPDmax =  create_line_array($dates, $wppd, 'max', $precision);
    $Respavg= create_line_array($dates, $resp, 'avg', $precision);
    $Respmin = create_line_array($dates, $resp, 'min', $precision);
    $Respmax = create_line_array($dates, $resp, 'max', $precision);
    $Rest1 = create_bar_array($dates, $rest1, 'low', $precision);
    $Rest2 = create_bar_array($dates, $rest2, 'mid', $precision);
    $Rest3 = create_bar_array($dates, $rest3, 'high', $precision);
    
    
    list($restlessness, $TIB) = create_restless_array($dates, $rest, $TimeInBed, $precision);
    //print_r($restlessness);
    //print_r($TIB);
    list($Alerts, $details) = create_alertarray($dates, $alerts);
    //print_r($details);
    $_SESSION['resid'] = $resid;
    $_SESSION['start_date'] = $startdate;
    $_SESSION['end_date'] = $enddate;
    }

	
  ?>
  
  <script>
  /*chart functions created by Mary Sheahen of Eldertech Rehabilitaiton and Technology 
  to be used with flot charting libraries to display sensor data */
  $(document).ready(function(){
  
    $('#loading_wrap').remove();
    /*some hard coded colors*/
     var colorarray = {"Bedroom": "#edc240", "Laundry": "#7ba7e1","Bathroom": "#cb4b4b","Office":"a9a9a9", "Front Door": "#4da74d", "Living Room":"#9440ed", "Closet":"#e697e6", "Laundry":"#44B4D5", "Kitchen":"#9588ec", "Patio":"#FF9797", "Den":"#423b33"};
    var motidk = null;
    var datasets;
    var widthbar = <?php if($widthbar) echo $widthbar; else echo "60*60*1000";?>;
    var alertwidth = 60*60*1000*24;

    
    /*This function adjusts the motion series selected by clicking on the legend*/
    plottoggle = function(seriesIdx)
    {
      motidk = seriesIdx;
      if(motidk=='All')
        motidk=null;
      plotAccordingToChoices(currmin, currmax);
    }
    
    
    //*This assigns the formatted php sctipts to the jquery *//
    function assign_data(){
      datasets = {
      "KM avg": {
        label: "KM",
        data: <?php echo json_encode($KMavg); ?>,
        <?php if($precision!='Second') echo "lines: {
          show:true
          },";
          else echo "lines: { show:false}, points: {show:true},";?>
          yaxis: 1,
          color: "black",
      }, 
      
      "KM min": {
        //label: "KM min",
        data: <?php echo json_encode($KMmin); ?>,
          lines: {
          show:true
          },
          yaxis: 1,
          color: "black",
      },
      
      "KM max": {
      //	label: "KM max",
        data: <?php echo json_encode($KMmax); ?>,
          lines: {
          show:true
          },
          yaxis: 1,
          color: "black",
      },
      "HT avg": {
        label: "HT",
        data: <?php echo json_encode($HTavg); ?>,
          <?php if($precision!='Second') echo "lines: {
          show:true
          },";
          else echo "lines: { show:false}, points: {show:true, radius: .5},";?>
          yaxis: 1,
          color: "red",
      },        
      "HT min": {
      //	label: "HT min",
        data: <?php echo json_encode($HTmin); ?>,
          lines: {
          show:true
          },
          yaxis: 1,
        color:"red"
      },
      "HT max": {
      //	label: "HT max",
        data: <?php echo json_encode($HTmax); ?>,
          lines: {
          show:true
          },
          yaxis: 1,
          color: "red",
      },
      "WPPD avg": {
        label: "WPPD",
        
        data: <?php echo json_encode($WPPDavg); ?>,
          <?php if($precision!='Second') echo "lines: {
          show:true
          },";
          else echo "lines: { show:false}, points: {show:true, radius: .3},";?>
          yaxis: 1,
          color: "blue",
      },        
      "WPPD min": {
        label: "WPPD min",
        
        data: <?php echo json_encode($WPPDmin); ?>,
          lines: {
          show:true
          },
          yaxis: 1,
          color:"blue",
      },
      "WPPD max": {
        label: "WPPD max",
        
        data: <?php echo json_encode($WPPDmax); ?>,
          lines: {
          show:true
          },
          yaxis: 1,
        color:"blue",
      },
      "Resp avg": {
        label: "Respiration",
        data: <?php echo json_encode($Respavg); ?>,
          <?php if($precision!='Second') echo "lines: {
          show:true
          },";
          else echo "lines: { show:false}, points: {show:true},";?>
          yaxis: 2,
          color:"navy",
      },        
      "Resp min": {
      //	label: "WPPD min",
        data: <?php echo json_encode($Respmin); ?>,
          lines: {
          show:true
          },
          yaxis: 2,
          color:"navy",
      },
      "Resp max": {
      //	label: "WPPD max",
        data: <?php echo json_encode($Respmax); ?>,
          lines: {
          show:true
          },
          yaxis: 2,
         color:"navy",
      },
     
      "Rest 1": {
        <?php echo "label: 'Restless";?>',
        data: <?php echo json_encode($restlessness); ?>,
          <?php if($precision!='Second') echo " bars: {
          show: true,
          barWidth: widthbar
        },
        stack: true,
        color:'cyan',";
          else echo " bars: {
          show: true,
          barWidth: widthbar
        },
        stack: true,
        color:'black',";?>
          
          yaxis: 1,
      },        
      "Rest 2": {
        <?php echo 'label: "Non-Restless",';?>
        data: <?php echo json_encode($TIB); ?>,
           <?php if($precision!='Second') echo " bars: {
          show: true,
          barWidth: widthbar
        },
        stack: true,
        color: 'gray',";
          else echo " bars: {
          show: true,
          barWidth: widthbar
        },
        stack: false,
        color:'black',";?>
          
          yaxis: 1,
      },
      "Alerts":{
        label: "Alerts",
		data: <?php echo json_encode($Alerts); ?>,
        bars: {
          
          show: true,
          barWidth: alertwidth
          },
          stack:true,
          color:"red",
        },
      "All":{
        label: "All",
          data: [[null, null]],
          bars:{
            show:true,
            barWidth:widthbar,
          },
            stack: true,
        
          },
        
        
      <?php
      $i = 1;
      while (list($key, $val) = each($motion)) {
          
          echo '"'.$key.'": {
          label: "'.$key.'",
          data: '; echo json_encode($val); echo ",
          bars:{
            show:true,
            barWidth:widthbar,
          },
            stack: true,
            color: colorarray['".$key."'],
            idx: ".$i."
          },";
          $i++;
        }
        $i = 0;
        while (list($key, $val) = each($motionday)) {
          echo '"'.$key.' day": {
          label: "'.$key.'",
          data: '; echo json_encode($val); echo ",
          bars:{
            show:true,
            barWidth:widthbar,
          },
            stack: true,
            color: colorarray['".$key."'],
            idx: ".$i."
          },";
          $i++;
        }
        $i = 0;
        while (list($key, $val) = each($motionnight)) {
          echo '"'.$key.' night": {
          label: "'.$key.'",
          data: '; echo json_encode($val); echo ",
          bars:{
            show:true,
            barWidth:widthbar,
          },
            stack: true,
            color: colorarray['".$key."'],
            idx: ".$i."
          },";
          $i++;
        }
        ?>
      };			
    }
    
      assign_data();
      
      /***********************************************************************
      //initialize some js variables based on php variables
      ************************************************************************/
      var currmin = <?php if($currmin) echo $currmin;else echo 'null';?>;
      var currmax = <?php if($currmax) echo $currmax; else echo 'null';?>;
      var sdate = <?php if($startdate) echo $startdate; else echo 'null';?>;
      var edate = <?php if($enddate) echo $enddate; else echo 'null';?>;
      var precision = '<?php if($precision) echo $precision; else echo 'null'?>';
	  
	  
    // make some variables for jquery ffunctions
      var choiceContainer = $("#choices");
      var chartchoices = $("#chart-radio");
      var timechoices = $("#time-radio");
      var daynightchoices = $("#daynight-radio");
      var lastDay = null;
	  
	  var rangeselectionCallback = function(o){
      
        currmin = o.start;
        currmax = o.end;
        
        var xaxis1 = motionplot.getAxes().xaxis;
        var xaxis2 = lineplot.getAxes().xaxis;
        var xaxis3 = restlessplot.getAxes().xaxis;
        var xaxis4 = alertsplot.getAxes().xaxis;
        
        xaxis1.options.min = currmin;
        xaxis1.options.max = currmax;
        motionplot.setupGrid();
        motionplot.draw();
        
        xaxis2.options.min = currmin;
        xaxis2.options.max = currmax;
        lineplot.setupGrid();
        lineplot.draw();
        
        xaxis3.options.min = currmin;
        xaxis3.options.max = currmax;
        restlessplot.setupGrid();
        restlessplot.draw();
        
        xaxis4.options.min = currmin;
        xaxis4.options.max = currmax;
        alertsplot.setupGrid();
        alertsplot.draw();
      };
	  
    //if we click anything we need to plot*/
    choiceContainer.find("input").click(function() {
        plotAccordingToChoices(currmin, currmax);
    });
    
    if(precision=='Second')
      $("#motion").css({"display":"none"});
    
    /*display algorithm choices if we are looking at pulse* and hide them otherwise*/
    chartchoices.change(function(event){
    //**start with these dynamic buttons hidden, then add them according to choices**//
      $("#choices").css({"display":"none"});
      $("#daynight-radio").css({"display":"none"});
      $("#15sec").css({"display":"none"});
      
      //add buttons based on charts
      chartchoices.find("input:checked").each(function () {
    
        if($(this).attr("name")=='Motion')
        {
          if(precision=='Daily')
            $("#daynight-radio").css({"display":"inline"});							
        }
        if($(this).attr("name")=='HydroPulse')
        {
          $("#choices").css({"display":"inline"});
          $("#minmax").css({"display":"inline"});
          $("#15sec").css({"display":"inline"});
        }
        if($(this).attr("name")=='HydroResp')
        {
          $("#15sec").css({"display":"inline"});
          $("#minmax").css({"display":"inline"});
        }
        if($(this).attr("name")=='HydroRest')
        {			
          $("#15sec").css({"display":"inline"});
        }
        plotAccordingToChoices(currmin, currmax);
      });		
    });
    
    
       
    //show min/max values for respiration and pulse
    $("#minmax").change(function(event)
    {
      plotAccordingToChoices(currmin, currmax);
    });
      
      
    //**show 24, day, or night values**/
    daynightchoices.change(function(event){
    chartchoices.find("input:checked").each(function () 
    {
        plotAccordingToChoices(currmin, currmax);
      });
    });
    
    //if we change time period we will submit the form and re query data	
    $("#time-radio").change(function() {
        submitVal = currmax;
	//	alert(submitVal);
			$('form').append("<input type='hidden' name='currentmax' value='"+
                         submitVal+"' /><input type='hidden' name='switch' value='true'/>");
			$("form").submit();
    });	
    
    
    /*main function to plot according to type, algorithms, time etc*/
    function plotAccordingToChoices(minimum, maximum) {
      
      var choicecount = 0;
      var check = 0;
      var data = [];
      var data1 = [];
      var data2 = [];
      var data3 = [];
      var data4 = [];
      var minmax = $('input[name=minmax]:checked', '#minmax').val();
      $('#placeholder1').css({"display":"none"});
      $('#placeholder2').css({"display":"none"});
      $('#placeholder3').css({"display":"none"});
      $("#choices").css({"display":"none"});
      
      data4.push(datasets['Alerts']);
      
      //what datasets to plot?
      $("#chart-radio input:checked").each(function(){
        charttype=$(this).attr('name');
        
        /*Pulse Data Options*/
        if(charttype=='HydroPulse'){
        choicecount++;
        check++;
        $('#placeholder2').css({"display":"inline"});
        $('#choices input:checked').each(function () {
        var key = $(this).attr("name");
        $("#choices").css({"display":"inline"});
          if (key =='KM') {
            data.push(datasets['KM avg']);
            data2.push(datasets['KM avg']);
          if(minmax=='yes'){
            data2.push(datasets['KM max']);
            data2.push(datasets['KM min']);
            data.push(datasets['KM max']);
            data.push(datasets['KM min']);
          }
        }
        else if (key =='HT') {
          data.push(datasets['HT avg']);
          data2.push(datasets['HT avg']);
          if(minmax=='yes'){
            data.push(datasets['HT max']);
            data.push(datasets['HT min']);
            data2.push(datasets['HT max']);
            data2.push(datasets['HT min']);
          }
        }
        else if(key =='WPPD') {
			data.push(datasets['WPPD avg']);
			data2.push(datasets['WPPD avg']);
          if(minmax=='yes'){
            data.push(datasets['WPPD max']);
            data.push(datasets['WPPD min']);
            data2.push(datasets['WPPD max']);
            data2.push(datasets['WPPD min']);
          }
        }
      });	
    }
    
    /*Respiration Data Options*/
    else if(charttype=='HydroResp')
    {
      choicecount++;
      check++;
      $('#placeholder2').css({"display":"inline"});
      data.push(datasets['Resp avg']);
      data2.push(datasets['Resp avg']);
      if(minmax=='yes'){
        data.push(datasets['Resp max']);
        data.push(datasets['Resp min']);
        data2.push(datasets['Resp max']);
        data2.push(datasets['Resp min']);
      }
    }
  
    /*Restlessness Data Options*/
    else if(charttype=="HydroRest")
    {
      choicecount++;
      $('#placeholder3').css({"display":"inline"});
      data3.push(datasets['Rest 1']);
      data3.push(datasets['Rest 2']);
      data.push(datasets['Rest 1']);
      data.push(datasets['Rest 2']);
    }
    
    /*Motion Data Options*/
    else if(charttype=="Motion")
    {
      if(precision != 'Second'){
        choicecount++;
        $('#placeholder1').css({"display":"inline"});
      }
      
      
        if($('input[name=daynight]:checked', '#daynight-radio').val()=='24' || precision=='Hourly')
        {
        
          if(motidk != null)
          {
            data1.push(datasets[motidk]);
            data.push(datasets[motidk]);
            data1.push(datasets["All"]);
			$('input#room').val(motidk);
          }
          else
          {
        <?php
          foreach ($motion as $key=>$value) {
          	if($key != "Kitchen"){
              echo 'data.push(datasets["'.$key.'"]);';
              echo 'data1.push(datasets["'.$key.'"]);';
          	}
          }
        ?>
        }
        
      }
        else if($('input[name=daynight]:checked', '#daynight-radio').val()=='day')
        {
          if(motidk != null)
          {
            data1.push(datasets[motidk +  ' day']);
            data.push(datasets[motidk + ' day']);
            data1.push(datasets["All"]);
			}
          else
          {
        <?php
          foreach ($motionday as $key=>$value) {
            if($key != "Kitchen"){
              echo 'data.push(datasets["'.$key.' day"]);';
              echo 'data1.push(datasets["'.$key.' day"]);';
            }
          }
          ?>
          }
        }
        
        else if($('input[name=daynight]:checked', '#daynight-radio').val()=='night')
        {
          if(motidk != null)
          {
            data1.push(datasets[motidk + ' night']);
            data.push(datasets[motidk + ' night']);
            data1.push(datasets["All"]);
          }
		  else
		  {
        <?php
          foreach ($motionnight as $key=>$value) {
          	if($key != "Kitchen"){
              echo 'data1.push(datasets["'.$key.' night"]);';
              echo 'data.push(datasets["'.$key.' night"]);';
        	}
        }
        ?>
        }
      }
    }
    
   
	  sizeGraph(precision, choicecount, check);
		
    /*  motionplot = $.plot("#placeholder1", data1, options);
      lineplot = $.plot("#placeholder2", data2, options2);
      restlessplot = $.plot("#placeholder3", data3, options3);
      alertsplot = $.plot("#placeholder4", data4, alertoptions);
      */
	  
	  /*
	  	if(precision =='Hourly')
			var increment = 60*60;
		else if(precision=='Daily')
			var increment = 24*60*60;
		
		//look and see if the highest value in the current window is less than 25% of the max column, then adjust accordingly.
		for(var i = currmin; currmin <= currmax; currmin+increment)
		{
			
		}
		
		*/
	  motionplot = $.plot("#placeholder1", data1, getoptions("Motion Hits", precision, currmin, currmax, lastDay));
      lineplot = $.plot("#placeholder2", data2, getoptions("Pulse Rate", precision, currmin, currmax, lastDay));
      restlessplot = $.plot("#placeholder3", data3, getoptions("In Bed Restlessness", precision, currmin, currmax, lastDay));
      alertsplot = $.plot("#placeholder4", data4, getoptions("Alerts", precision, currmin, currmax, lastDay));
      
      /*************************************
      plot options for our small time graph
      ***************************************/
      var overview = $.plot("#overview", data, {
		xaxis: {
			mode: "time"
		},
		yaxis: {
			min:0,
			show:false
		},
		legend: {
			show: false,
		},
		rangeselection:{
                    start: currmin,
                    end: currmax,
                    enabled: true,
                    callback: rangeselectionCallback
                }
		});
    
		bindEvents(precision);
		});
    }
	
   <?php
	if(isset($_POST['room']) && $_POST['room']!='all')
	{
		$room = $_POST['room'];
		?>
		$("#<?php echo $room;?>").click();
		<?php
	}
       ?>
    //onload, we want to plot choices initially
    plotAccordingToChoices(currmin, currmax);
    
    //if we resize the window, replot(makes it responsive)
    window.onresize = function(event) {
      plotAccordingToChoices(currmin, currmax);
    }
  });

  </script>
</head>
<body>
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

<div id='loading_wrap' style='position:fixed; height:100%, width:100%; overflow:hidden; top:0; left:0;'>Please Wait, Loading....</div>

<h2>Current Resident: <?php echo $resid;?></h2>

  <div id="content">    
    <form action="#" method="POST">
      <div id="options">
        <div class="control-group" style="margin-left:10px">
          <div class="controls">
            <select id="resident" name="resident" required="required"  class="combobox" style="width:50px">
              <option  style='width:50px' ></option>
              <?php
              for($c=0; $c<count($get_ids); $c++){
           
                  echo "<option id='".$get_ids[$c]['UserID']."' style='width:50px' onclick='clicked(\"".$get_ids[$c]['UserID']."\",\"resident\");' value='".$get_ids[$c]['UserID']."'"; if($resid==$get_ids[$c]['UserID']) echo ' selected="selected"'; echo ">".$get_ids[$c]['UserID']."</option>";
       
				}
            	echo"<option style='width:50px' id=''3017A' onclick='clicked(\"30171\",\"resident\");' value='30171'"; if($resid==='30171') echo ' selected="selected"'; echo ">3017A</option><option id='3017B' onclick='clicked(\"3017B\",\"resident\");' value='30172'"; if($resid==='30172') echo ' selected="selected"'; echo ">3017B</option><option id='3045A' onclick='clicked(\"3045A\",\"resident\");' value='30451'"; if($resid=='30451') echo ' selected="selected"'; echo ">3045A</option><option id='3045B' onclick='clicked(\"3045B\",\"resident\");' value='30452'"; if($resid==='30452') echo ' selected="selected"'; echo ">3045B</option><option style='width:50px' id=''3057A' onclick='clicked(\"30571\",\"resident\");' value='30571'"; if($resid==='30571') echo ' selected="selected"'; echo ">3057A</option><option style='width:50px' id=''3057B' onclick='clicked(\"30572\",\"resident\");' value='30572'"; if($resid==='30572') echo ' selected="selected"'; echo ">3057B</option>";
              ?>
            </select>
            <div id='dates' >
      <input type="text" class="span2" name="StartDate" style="width:100px;font-size:16px;" value="<?php echo $startdate;?>" data-date-format="yyyy-mm-dd" id="dpd1">
          to
        <input type="text" class="span2" name="EndDate" style="width:100px;font-size:16px;" value="<?php echo $enddate;?>" data-date-format="yyyy-mm-dd"  id="dpd2">
          <input type="text" style="display:none" name="smnth" value="<?php echo $smonth;?>">
        </div>
          </div>
          <input type="Submit" label="Submit" value="Submit"></input>
        </div>
	
        
      
  
		<br/>
        <div class="containerpanel">
          Precision<br/>
          <div class="Button-radio" id="time-radio">
            <label class="red"><input type="radio" name="timetoggle" id="Daily" value="Daily" <?php if(!isset($_POST['timetoggle']) || $_POST['timetoggle']=='Daily') echo 'checked="checked"';?>><span>Daily</span></label>
            <label class="red"><input type="radio" name="timetoggle" id="Hourly" value="Hourly" <?php if($_POST['timetoggle']=='Hourly') echo 'checked="checked"';?>><span>Hourly</span></label>
            <label id="15sec" class="red" style="display:inline"><input type="radio" name="timetoggle" id="Second" value="Second" <?php if($_POST['timetoggle']=='Second') echo 'checked="checked"';?>><span>15 Sec</span></label>
          <!--<label class="red"><input type="radio" name="toggle" value="HydroPulseZoom"><span>Hydraulic Pulse Hourly</span></label>-->
          </div>
          <br/>
      </div>
      
      <div class="containerpanel"  id="chart-radio">
        Feature<br/>
        <div class="Button-radio">
          
          <label class="green" id="motion"><input type="checkbox" name="Motion"   <?php if((!isset($_POST['HydroPulse']) && !isset($_POST['HydroResp']) && !isset($_POST['HydroRest'])) || $_POST['Motion']=='on') echo 'checked="checked"';?>><span>Motion</span></label>
          <?php
        if(in_array($resid, $reswithhydro))
          {
          ?>
          <label class="green" id="hydropulse"><input type="checkbox" name="HydroPulse"  <?php if($_POST['HydroPulse']=='on') echo 'checked="checked"';?>><span>Pulse</span></label>
          <label class="green" id="navy"><input type="checkbox" name="HydroResp"  <?php if($_POST['HydroResp']=='on') echo 'checked="checked"';?>><span>Breathing</span></label>
          <label class="green" id="hydrorest"><input type="checkbox" name="HydroRest"  <?php if($_POST['HydroRest']=='on') echo 'checked="checked"';?>><span>Restlessness</span></label>
          <?php
          }
        ?>

        </div>
      </div>
      
      <div class="containerpanel" id="choices" style="float:left;display:none">
      Pulse Algorithm<br/>
        <div class="algorithm">
        
          <div class='chk-button' id="black"><label><input type='checkbox' name='KM' id='idKM' <?php if($_POST['KM']=='on') echo 'checked="checked"';?>><span>KM</span></label></div>
          <div class='chk-button' id="red"><label><input type='checkbox' name='HT' checked='checked' id='idHT' <?php if((!isset($_POST['KM']) && !isset($_POST['WPPD'])) || $_POST['HT']=='on') echo 'checked="checked"';?>><span>HT</span></label></div>
          <div class='chk-button' id="blue"><label><input type='checkbox' name='WPPD' id='idWPPD' <?php if($_POST['WPPD']=='on') echo 'checked="checked"';?>><span>WPPD</span></label></div>
        </div>
      </div>
      
      <div class="containerpanel" id="minmax" style="float:left;display:none;width:100%;">
        <div class='chk-button' id="black"><label><input type='checkbox' name='minmax' id='idminmax' value='yes'><span>Min & Max</span></label></div>
      </div>
      <?php
      if($precision=='Daily'){
      echo '
      <div class="containerpanel" id="daynight-radio" style="float:left;display:inline">
      Hit Period</br>
        <div class="Button-radio" >
          
          <label class="red"><input type="radio" name="daynight" value="24" checked="checked"><span>24 hour</span></label>
          <label class="red"><input type="radio" name="daynight" value="day"'; if(isset($_POST['alg']) && $_POST['alg']=='6 am to 10 pm') echo ' checked="checked" '; echo '><span>Day Only</span></label>
          <label class="red"><input type="radio" name="daynight" value="night"'; if(isset($_POST['alg']) && $_POST['alg']=='Night Time Hits') echo ' checked="checked" '; echo '><span>Night Only</span></label>
          <!--<label class="red"><input type="radio" name="toggle" value="HydroPulseZoom"><span>Hydraulic Pulse Hourly</span></label>-->
        </div>
      </div>';
      }?>
    </div>  <!-- / id="options" -->
    
      <div id='container-placeholder1' class="demo-container">
        <div id="placeholder1" class="demo-placeholder" style="float:left;"></div>
        <div id="placeholder2" class="demo-placeholder" style="float:left;"></div>
        <div id="placeholder3" class="demo-placeholder" style="float:left;"></div>
        <div id="placeholder4" class="demo-placeholder" style="height:10%;float:left;"></div>
      </div>
      <div id='container-overview' class="demo-container">
        <div id="overview" ></div>

        <input type="text" style="display:none" name="sdy" value="<?php echo $sdy;?>">
        <input  type="text" style="display:none" name="syr" value="<?php echo $syr;?>">
        <input  type="text" style="display:none" name="emnth" value="<?php echo $emnth;?>">
        <input  type="text" style="display:none" name="edy" value="<?php echo $sdy;?>">
        <input  type="text" style="display:none" name="eyr" value="<?php echo $syr;?>">
		<input type="text" style="display:none" name="room" id="room" value="all"/>
      </div>
      </form>
  
    </div>
    <script src="js/bootstrap-combobox.js"></script>
  <script src="js/bootstrap-datepicker.js"></script>
  <script src="js/begin.js"></script>
      </div>
    </div>
  </body>
</html>



