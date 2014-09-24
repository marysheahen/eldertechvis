<?php
//error_reporting(E_ALL);
//ini_set("display_errors",1);
require('includes/session.php');
require('includes/dbQuery.php');
require('includes/new-chartfunctions.php');
require('includes/usefulFunctions.php');
      //  echo "SESSION resid: ".$_REQUEST['resid'];
      //  echo "SESSION start_date: ".$_SESSION['start_date'];
      //  echo "SESSION end_date: ".$_SESSION['end_date'];

      //	$_SESSION['resid'] = 3004;
      //	$_REQUEST['resid'] = $_SESSION['resid'];
      //$_SESSION [ 'start_date' ] = "2014-01-31";
      //$_SESSION [ 'end_date'   ] = "2014-05-31";
  
      if ( isset ( $_REQUEST [ 'resid' ] ) && isset ( $_SESSION [ 'start_date' ] ) && isset ( $_SESSION [ 'end_date' ] ) ) {

        //require('System/dbQuery.php');
        $resid=$_REQUEST['resid'];
        $_SESSION['resid']=$_REQUEST['resid'];
        //$title=$resid." Data";
  
        //if(!ob_start("ob_gzhandler")) ob_start();
  
        $_SESSION['density_start']=date("Y-m-d", strtotime($_SESSION['end_date']." - 18 months"))." 00:00:00";
        $_SESSION['density_end']=date("Y-m-d", strtotime($_SESSION['end_date']))." 23:59:59";
		$dates = make_dates($_SESSION['density_start'], $_SESSION['density_end']);
        $anticache=rand(0, 10000);	
		$params=array($resid, $_SESSION['density_start'], $_SESSION['density_end']);
		//$params=array($resid, $_SESSION['start_date'], $_SESSION['end_date']);
        $results=array('id', 'day', 'Name', 'Notes', 'Algorithm', 'UserID');
        $array_key='day';
        $alerts=DB_Query(87, NULL, $params, $results, NULL, NULL, NULL, $array_key);
	//	print_r($alerts);
		 list($Alerts, $details) = create_alertarray($dates, $alerts);
  
    ?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

  <title>Universal Design Interface</title>

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
  <script language="javascript" type="text/javascript" src="js/jquery.flot.image.js"></script>
    <script language="javascript" type="text/javascript" src="js/jquery.flot.stack.min.js"></script>

  <script src="js/bootstrap.js"></script>
  
   <script language="javascript" type="text/javascript">
   $(document).ready(function(){
   
   
  
  var currmin = <?php echo strtotime($_SESSION['end_date']." - 6 months")*1000?>;
  var currmax  = <?php echo strtotime($_SESSION['end_date'])*1000?>;
	var startdate = <?php echo strtotime($_SESSION['end_date']." - 18 months")*1000?>;
  var enddate = <?php echo strtotime($_SESSION['end_date'])*1000?>;
  var densityplot;
  var bathroomplot;
  var hydroplot;
  var rangeselectionCallback = function(o){
    var xaxisD = densityplot.getAxes().xaxis;
    var xaxisB = bathroomplot.getAxes().xaxis;
    var xaxisH = hydroplot.getAxes().xaxis;
    var xaxisA = alertplot.getAxes().xaxis;
	 var xaxisA2 = alertplot2.getAxes().xaxis;
	  var xaxisA3= alertplot3.getAxes().xaxis;
	
     currmin = o.start;
    currmax = o.end;
  
    xaxisD.options.min = currmin;
    xaxisD.options.max = currmax;
    densityplot.setupGrid();
    densityplot.draw();
    
    xaxisB.options.min = currmin;
    xaxisB.options.max = currmax;
    bathroomplot.setupGrid();
    bathroomplot.draw();
    
    xaxisH.options.min = currmin;
    xaxisH.options.max = currmax;
    hydroplot.setupGrid();
    hydroplot.draw();
		
	xaxisA.options.min = currmin;
    xaxisA.options.max = currmax;
	alertplot.setupGrid();
    alertplot.draw();
	
	xaxisA2.options.min = currmin;
    xaxisA2.options.max = currmax;
	alertplot2.setupGrid();
    alertplot2.draw();
	
	xaxisA2.options.min = currmin;
    xaxisA2.options.max = currmax;
	alertplot2.setupGrid();
    alertplot2.draw();
   };
   

   function showTooltip(x, y, contents) {
                    $("<div id='tooltip'>" + contents + "</div>").css({
                       position: 'absolute',
                display: 'none',
                top: y - 100,
                left: x - 100,
                border: '2px solid  black',
                padding: '3px',
                'font-size': '14px',
				'font-weight':'bold',
                'border-radius': '5px',
        'webkit-border-radius': '5px',
                'background-color': '#fff',
                'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
                opacity: 0.9,
				'z-index': 999
                    }).appendTo("body").fadeIn(200);
                }
   
      var data = [[["image.php?what=Density", startdate, -24, enddate, 0]]];
        var data2 = [[["image.php?what=Bathroom", startdate, -24, enddate, 0]]];
      var data3 = [[["image.php?what=Bedtime(Hydraulic)", startdate, -24, enddate, 0]]];
	   var alerts =  <?php echo json_encode($Alerts); ?>;
	   
      var options= {
      series: {
        images: {
          show: true
        }
      },
      xaxis: {
        mode: 'time',
        min: currmin,
        max: currmax,
        minTickSize: [1,"day"],
		//show:false
	
      },
      yaxis: {
        min: -24,
        max: 0,
		tickFormatter: function(val, axis) { 
			var newval = Math.abs(val);
			return (newval.toString());
		}
      },
      grid: {
                        hoverable: true,
            clickable:true,
      }
    };

    var options1= {
      series: {
        images: {
          show: true
        }
      },
      xaxis: {
        mode:'time',
        min: startdate,
        max: enddate,
        minTickSize:[1, "day"],
		show:false,
      },
      yaxis: {
        min: -24,
        max: 0,
		tickFormatter: function(val, axis) { 
			var newval = Math.abs(val);
			return (newval.toString());
		}
      },
       rangeselection:{
                   // color: pcolors[4], 
                    start: currmin,
                    end: currmax,
                    enabled: true,
                    callback: rangeselectionCallback
                },
				grid:{
				hoverable: true,
				},
    };
    
		var optionsalert = {
          legend:{
            noColumns: 0,
           // labelFormatter: labelformat,
          },
		  series:{
		  color:"red",
		   bars: {
				show: true,
				barWidth: 60*60*1000*24,
				
		  },
		  },
		 // stack:true,
          //color:"red",
          xaxis: {
            mode: "time",
            min: currmin,
            max: currmax,
            minTickSize:  [1, "day"],
		//show:false,
		//	tickFormatter: tickformat,
				
          },
          yaxis: {
            min:0,
			max:1,
            position: 'left',
            tickDecimals: 0,
			show:false,
          
         },			
          grid: {
                        hoverable: true,
                       // clickable: true
                    },
        };
		
    $.plot.image.loadDataImages(data, options, function () {
      densityplot = $.plot("#placeholder", data, options);
    });
    
    $.plot.image.loadDataImages(data2, options, function () {
      bathroomplot = $.plot("#placeholder2", data2, options);
    });
    
    $.plot.image.loadDataImages(data3, options, function () {
      hydroplot = $.plot("#placeholder3", data3, options);
    });
    
      $.plot.image.loadDataImages(data, options1, function () {
      $.plot("#overview", data, options1);
    });
    
	alertplot = $.plot("#alertgraph", [alerts], optionsalert);
	alertplot2 = $.plot("#alertgraph2", [alerts], optionsalert);
	alertplot3 = $.plot("#alertgraph3", [alerts], optionsalert);
	alertplot3 = $.plot("#alertgraph4", [alerts], optionsalert);
    $("#placeholder").bind("plothover", function (event, pos, item) {   
    if(pos){
                            $("#tooltip").remove();											
                           var x = Math.floor(pos.x.toFixed(2));
              var y = pos.y.toFixed(2);			
              var someTime = (Math.floor(y)+1)*(-1);
              var d = new Date(x);				
              var someDay = d.getDate() + 1;
              var someMonth = d.getMonth() + 1; //months are zero based
              var someYear = d.getFullYear();				
              var stringDate = someMonth + "/" + someDay + "/" + someYear + " " + someTime + ":00:00 to " + (someTime+1) + ":00:00";
                            showTooltip(pos.pageX, pos.pageY,
                              stringDate);    
                }
                else
          $("#tooltip").remove();
                      
                });
          $("#placeholder2").bind("plothover", function (event, pos, item) {   
                           if(pos){
                            $("#tooltip").remove();											
                           var x = Math.floor(pos.x.toFixed(2));
              var y = pos.y.toFixed(2);			
              var someTime = (Math.floor(y)+1)*(-1);
              var d = new Date(x);				
              var someDay = d.getDate() + 1;
              var someMonth = d.getMonth() + 1; //months are zero based
              var someYear = d.getFullYear();				
              var stringDate = someMonth + "/" + someDay + "/" + someYear + " " + someTime + ":00:00 to " + (someTime+1) + ":00:00";
                            showTooltip(pos.pageX, pos.pageY,
                              stringDate);    
                }
                else
          $("#tooltip").remove();
                });
        $("#placeholder3").bind("plothover", function (event, pos, item) {   
                           if(pos){
                            $("#tooltip").remove();											
                           var x = Math.floor(pos.x.toFixed(2));
              var y = pos.y.toFixed(2);			
              var someTime = (Math.floor(y)+1)*(-1);
              var d = new Date(x);				
              var someDay = d.getDate() + 1;
              var someMonth = d.getMonth() + 1; //months are zero based
              var someYear = d.getFullYear();				
              var stringDate = someMonth + "/" + someDay + "/" + someYear + " " + someTime + ":00:00 to " + (someTime+1) + ":00:00";
                            showTooltip(pos.pageX, pos.pageY,
                              stringDate);    
                }
                else
          $("#tooltip").remove();
                });
        var plotarray = ["#alertgraph", "#alertgraph2", "#alertgraph3", "#alertgraph4"];
				for(var i = 0; i < 3; i++){
				$(plotarray[i]).bind("plothover", function (event, pos, item) {
				if(pos){
                    $("#tooltip").remove();											
                    var x = Math.floor(pos.x.toFixed(2));
					var someTime = (Math.floor(y)+1)*(-1);
					var d = new Date(x);				
					var someDay = d.getDate() + 1;
					var someMonth = d.getMonth() + 1; //months are zero based
					var someYear = d.getFullYear();				
				
						var y = Math.floor(item.datapoint[1].toFixed(2))			  
						var stringDate = someMonth + "/" + someDay + "/" + someYear ;
						showTooltip(item.pageX, item.pageY,
                             y + " Alerts <br>" + "Date:" + stringDate + " <br>" +  item.series.data[item.dataIndex][2]);
					
				}
				else
					$("#tooltip").remove();
                });
				
		}
      
        
    });
   </script>
   <style >
/* Reset body padding and margins */
body { margin:0; padding:0; }
 
/* Make Header Sticky */
#header_container { background:#eee; border:1px solid #666; height:80px; left:0; width:100%; /* position:fixed; top:42px;z-index:998 */ }
#header{ line-height:80px; margin:0 auto; width:100%; text-align:center; z-index:999}
 
/* CSS for the content of page. I am giving top and bottom padding of 80px to make sure the header and footer do not overlap the content.*/
#container { margin:0 auto; overflow:auto; padding-bottom:150px; width:100%; /* padding-top:80px; */ }
#content{}
 
/* Make Footer Sticky */
#footer_container { background:#eee; background-color:73AFB6; border:1px solid #666; bottom:0; height:150px; left:0; position:fixed; width:100%; }
#footer { line-height:150px; margin:0 auto; width:100%; text-align:center; }

#legend{
  float:right;
  width:12%;
  height:300px;
  margin:20px;
  padding:10px;
}

#legend2{
  float:right;
  width:12%;
  height:300px;
  margin:20px;
  padding:10px;
}

#legend3{
  float:right;
  width:12%;
  height:300px;
  margin:20px;
  padding:10px;
}

#container-placeholder{
  width:82%;
  height:300px;
  margin:20px;
}

#container-placeholder2{
  width:82%;
  height:300px;
  margin:20px;
}

#container-placeholder3{
  width:82%;
  height:300px;
  margin:20px;
}



@media screen and (max-width:1400px) {
#legend{
  float:right;
  width:130;
  height:300px;
  margin:10px;
}

#legend2{
  float:right;
  width:130px;
  height:300px;
  margin:10px;
}

#legend3{
  float:right;
  width:130px;
  height:300px;
  margin:10px;
}
#container-placeholder{
  width:80%;
  height:300px;
  margin:10px;
}

#container-placeholder2{
  width:80%;
  height:300px;
  margin:10px;
}

#container-placeholder3{
  width:80%;
  height:300px;
  margin:10px;
}
}
@media screen and (max-width:900px){
#container-placeholder{
  width:75%;
  height:300px;
  margin:10px;
}

#container-placeholder2{
  width:75%;
  height:300px;
  margin:10px;
}

#container-placeholder3{
  width:75%;
  height:300px;
  margin:10px;
}
}

  </style>
  </head>
  <body>
  
    <div class="tabBox" style="clear:both;">
      <div class="tabArea">
        <a class="tab" href="/index.php">Home</a>
        <a class="tab" href="/health/index.php">Health</a>
        <a class="tab" href="/health/density2.php?resid='.$_SESSION['resid'].'">Density</a>
        <a class="tab" href="/gait/index.html">Gait</a>
        <a class="tab" href="/rewind/index.php">Rewind</a>
		 <a class="tab" href="/floorplan/index.php?resid=<?php echo $_SESSION['resid'];?>">Floorplan</a>
      </div>
      <div class="tabMain">



  <div id='header_container'>
    <div id='header'>
      <p><h2>Daily Activity for <?php echo $resid ?> (<?php echo date ( "M d, Y", strtotime ( $_SESSION [ 'end_date' ] ." - 6 months") ) ?>-<?php echo date ( "M d, Y", strtotime ( $_SESSION [ 'end_date' ] ) ) ?>)</h2>
    </div>
  </div>

  <div id="container">
    <div id="content">

      <div id="Density">
        <div id='container-placeholder' class="demo-container">
          <div id="placeholder" class="demo-placeholder" style="float:left;height:88%"></div>
		   <div id="alertgraph" class="demo-placeholder" style="float:left;height:50px;"></div>
        </div>
        <div id='legend' class='demo-container'  >
          Density
          <img src='image.php?what=Density&legend=true' height="255" width="100" alt='no image' style='float:left' />
        </div>
      </div>

      <div id="Bathroom">
        <div id='container-placeholder2' class="demo-container" >
          <div id="placeholder2" class="demo-placeholder" style="float:left;height:88%"></div>
		   <div id="alertgraph2" class="demo-placeholder" style="float:left;height:50px;"></div>
        </div>
        <div id='legend2' class='demo-container'  >
          Bathroom
          <?php echo "<img src='image.php?what=Bathroom&legend=true' height='255' width='100' style='float:left' />";?>
        </div>
      </div>

      <div id="Rest">
        <div id='container-placeholder3' class="demo-container" >
          <div id="placeholder3" class="demo-placeholder" style="float:left;height:88%"></div>
		   <div id="alertgraph3" class="demo-placeholder" style="float:left;height:50px;"></div>
        </div>
        <div id='legend3' class='demo-container' >
          Bed Restlessness
          <?php echo "<img src='image.php?what=Bedtime(Hydraulic)&legend=true' height='245' width='100' style='float:left'>";?>
        </div>
      </div>

    </div>
  </div>

  <div id="footer_container">
    <div id="footer" style="z-index:105">
      <div id='container-overview' class="demo-container" style="width:98%;height:98%margin:15px;" >
        <div id="overview" class="demo-placeholder" style="height:75%;, margin:2px;"></div>
		<div id="alertgraph4" class="demo-placeholder" style="float:left;height:39px;"></div>
      </div>
    </div>
  </div>

<?php
        $what=array();
        
        array_push($what, 'Density');

        //ob_end_flush();
        //}
      }
      else
        echo "no resid set ".$_REQUEST['resid']." ".$_SESSION['start_date']." ".$_SESSION['end_date']." ";
?>
      </div>      <!-- class="tabMain"       -->
    </div>        <!-- class="tabBox"        -->
  </body>
</html>
