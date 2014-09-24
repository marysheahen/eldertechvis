 /***********************************
 tooltip style function
 Mary Sheahen 6/10/2014
 ******************************/
 var previousLabel = 'null';
    function showTooltip(x, y, contents) {
                    $("<div id='tooltip'>" + contents + "</div>").css({
                       position: 'absolute',
                display: 'none',
                top: y + 50,
                left: x - 120,
                border: '2px solid  black',
                padding: '3px',
                'font-size': '9px',
                'border-radius': '5px',
        'webkit-border-radius': '5px',
                'background-color': '#fff',
                'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
                opacity: 0.9,
				'z-index': 105
                    }).appendTo("body").fadeIn(200);
                }
				
	/*********************************************
	tooltip for showing signals
	Mary Sheahen 7/17/2014
	*********************************************/
	function showSignal(x, y, month, day, year, time, min, sec) {
	
	
        $("<div id='tooltip'></div>").css({
            position: 'absolute',
            display: 'none',
            top: y + 50,
            left: x - 120,
            border: '2px solid  black',
            padding: '3px',
            'font-size': '9px',
            'border-radius': '5px',
			'webkit-border-radius': '5px',
            'background-color': '#fff',
            'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            opacity: 0.9,
			'z-index': 105,
			width: "50%",
			height: "100px"
        }).appendTo("body").fadeIn(200);
		
		$("<div id='tooltip2'></div>").css({
            position: 'absolute',
            display: 'none',
            top: y + 150,
            left: x - 120,
            border: '2px solid  black',
            padding: '3px',
            'font-size': '9px',
            'border-radius': '5px',
			'webkit-border-radius': '5px',
            'background-color': '#fff',
            'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            opacity: 0.9,
			'z-index': 105,
			width: "50%",
			height: "100px"
        }).appendTo("body").fadeIn(200);
		
		$("<div id='tooltip3'></div>").css({
            position: 'absolute',
            display: 'none',
            top: y + 250,
            left: x - 120,
            border: '2px solid  black',
            padding: '3px',
            'font-size': '9px',
            'border-radius': '5px',
			'webkit-border-radius': '5px',
            'background-color': '#fff',
            'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            opacity: 0.9,
			'z-index': 105,
			width: "50%",
			height: "100px"
        }).appendTo("body").fadeIn(200);
		
		$("<div id='tooltip4'></div>").css({
            position: 'absolute',
            display: 'none',
            top: y + 350,
            left: x - 120,
            border: '2px solid  black',
            padding: '3px',
            'font-size': '9px',
            'border-radius': '5px',
			'webkit-border-radius': '5px',
            'background-color': '#fff',
            'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            opacity: 0.9,
			'z-index': 105,
			width: "50%",
			height: "100px"
        }).appendTo("body").fadeIn(200);
		var dat;
		var field = 'activeUsers,totalUsers';
		var url = 'signal.php';
	/*	$.ajax({
			url: url,
			data: { Month: month, Day: day, Year: year, Time: time, Min: min, Sec: sec } ,
			method: 'GET',
			dataType: 'json',
			success: function(datasets){
				//alert(datasets);
				$.plot($("#tooltip"), datasets);
        }
	});*/
	  var options = {
          legend:{
            noColumns: 0,
           
          },
          xaxis: {
            mode: "time",
         
				
          },
          yaxis: {
			min:2.38,
			max:2.52,
            position: 'left',
            tickDecimals: 0,
            labelWidth:60,
       
         
            
          }};
		
		$.getJSON(url, {Month: month, Day: day, Year: year, Time: time, Min: min, Sec: sec, trans: 1}, function(json){
		
			$.plot($("#tooltip"), [json], options);
		});
		$.getJSON(url, {Month: month, Day: day, Year: year, Time: time, Min: min, Sec: sec, trans: 2}, function(json){
			
			$.plot($("#tooltip2"), [json], options);
		});
		$.getJSON(url, {Month: month, Day: day, Year: year, Time: time, Min: min, Sec: sec, trans: 3}, function(json){

			$.plot($("#tooltip3"), [json], options);
		});
		$.getJSON(url, {Month: month, Day: day, Year: year, Time: time, Min: min, Sec: sec, trans: 4}, function(json){
		
			$.plot($("#tooltip4"), [json], options);
		});
	
		
  
	}
	
/***********************************************
function to set options depending on the graph and precision which we want to display
***********************************************/
function getoptions(title, precision, currmin, currmax, lastDay){			
	/*initialize and default values*/
	var mintick = [1, "hour"];
	var tickformat = null;
	var labelformat = null;
	var ymax = null;
	
	/*precision based customization*/
	if(precision=='Daily')
	{
	    mintick = [1, "day"];
	}
	else
	{
		tickformat = function(val, axis) { 
			var d = new Date(val);
			var someTime = d.getHours();
            var someMin = d.getMinutes();
            var someDay = d.getDate() + 1;
            var someMonth = d.getMonth() + 1; //months are zero based
            var someYear = d.getFullYear();
            if(someTime >=19)
                someTime -=19;
            else{
                someTime +=5;
                someDay -= 1;
              }
			if(someMin < 10)
			someMin="0" + someMin;
			var rV = null;          
			if (lastDay == null) //lastDay is a global, set to null outside of plot call
			{
				//rV = $.plot.formatDate(d, "%l:%M"); // first date return time
				rV = (someTime + ":" + someMin);
			}
			else
			{
				if (someTime==0)  // we have a new day
				{
					//rV = $.plot.formatDate(d, "<b>New Day</b></br> %m/%d"); // return different format
					rV = "<b>New Day</b></br> " + someMonth + "/" + someDay;
				}
				else  // same day, just time
				{
					//rV = $.plot.formatDate(d, "%l:%M");
					rV = (someTime + ":" + someMin);
				}
			}
			lastDay = d; 
			return rV;
		}
	}
	
	/*title based customization*/
	if(title=='Motion Hits')
	{
		labelformat = function(label, series){
              return '<a href="#" id=\''+label+'\' onClick="plottoggle(\''+label+'\');  return false;">'+label+'</a>';
			  }
	}
	else if(title=="Alerts")
	{
		ymax = 4;
	}
	
	/*set it up!*/
	  var options = {
          legend:{
            noColumns: 0,
            labelFormatter: labelformat,
          },
          xaxis: {
            mode: "time",
            min: currmin,
            max: currmax,
            minTickSize: mintick,
			tickFormatter: tickformat,
				
          },
          yaxes: [{
            min:0,
			max:ymax,
            position: 'left',
            tickDecimals: 0,
            labelWidth:60,
            reserveSpace: true,
            tickFormatter: function(val, axis) { 
			
			return val < axis.max ? val.toFixed(0) : title;}
            
          }, 
 
          {
            min:0,
            position: 'right',
            tickDecimals: 0,
            reserveSpace: true,
            tickFormatter: function(val, axis) { return val < axis.max ? val.toFixed(0) : "Breath Rate";}
          },
            
          ],			
          grid: {
                        hoverable: true,
                        clickable: true
                    },
        };
		return options;
	 }
/**********************************************************************************************************************************
This functions checks for the number of graphs and adjusts the hight of the plots based on how many graphs there are.
Mary Sheahen 6/10/2014
*************************************************************************************************************************************/	
		
	function sizeGraph(precision, choicecount, check)
	{
	 if(precision=='Second')
		{
		if(choicecount>=2)
		{		
			$("#placeholder2").css({"height": "80%"});
			$("#placeholder3").css({"height": "10%"});
		}
		else
			$('.demo-placeholder').css({"height":"90%"});
		}
	else{
		//* ok now we need to adjust the size of the graphs depending on what is selected *//
		if(choicecount ==4)
			$('.demo-placeholder').css({"height": "30%"});	
		else if(choicecount ==3){
			if(check==2)
				$('.demo-placeholder').css({"height": "45%"});
			else
				$('.demo-placeholder').css({"height": "30%"});	
		}
		else if(choicecount==2)
		{
			if(check==2)
				$('.demo-placeholder').css({"height": "90%"});
			else
				$('.demo-placeholder').css({"height": "45%"});
		}
		else
			$('.demo-placeholder').css({"height": "90%"});
		}
		$('#placeholder4').css({"height": "10%"});
	}
				
				
	/*******************************************************************************************************************************************
	function bindEvents adds the hover, select, and click events to the plots. 
	Mary Sheahen 6/10/2014
	********************************************************************************************************************************************/
	function bindEvents(precision){
		var plotarray = ["#placeholder1", "#placeholder2", "#placeholder3", "#placeholder4"];
		//alert(plotarray);
		var i = 0;
		for(i = 0; i < 4; i++)
		{
			//alert(plotarray[i]);
			$(plotarray[i]).bind("plothover", function (event, pos, item) {
			
             if (item) {
			 // $("#tooltip").remove();
				if (previousPoint != item.dataIndex || previousLabel != item.series.label){
                    previousPoint = item.dataIndex;
					previousLabel = item.series.label;
                       $("#tooltip").remove();
						  $("#tooltip2").remove();
						    $("#tooltip3").remove();
							  $("#tooltip4").remove();
					if (item.series.bars.show==true)
					{
						
						var x = item.datapoint[0], 
							y = item.datapoint[1] - item.datapoint[2];
					}
					else
						var x = item.datapoint[0],
							y = item.datapoint[1].toFixed(2);

					var d = new Date(x);
					var someTime = d.getHours();
					var someMin = d.getMinutes();
					var someSec = d.getSeconds();
					var someDay = d.getDate() + 1;
					var someMonth = d.getMonth() + 1; //months are zero based
					var someYear = d.getFullYear();
			  
					/*adjust to our time zone, this is hard coded right now for CST*/
					if(someTime >=19)
						someTime -=19;
					else{
						someTime +=5;
						someDay -= 1;
					}
              
					/*add zero*/
					if(someMin < 10)
						someMin="0" + someMin;
					if(someSec < 10)
						someSec = "0" + someSec;
						
				
					/************************************
					Specific to Restlessness  tooltip
					************************************/
					if(item.series.label=="Restless" || item.series.label=="Non-Restless")
					{
						 if(precision=='Daily')
						{
							var hrs =Math.floor(y);
							var  mins =Math.floor((y % 1)*60) ;
							var stringDate = someMonth + "/" + someDay + "/" + someYear;
            
							showTooltip(item.pageX, item.pageY,
							item.series.label + "  " +  hrs + " hours " +  mins +  " minutes <br> out of  " + item.series.data[item.dataIndex][2] + " <br> Date:" + stringDate);
						}
             
						else if(precision=='Hourly')
						{
           
							var  mins =Math.floor(y) ;
							var stringDate = someMonth + "/" + someDay + "/" + someYear + " " + someTime + ":" + someMin + ":" + someSec;
            
							showTooltip(item.pageX, item.pageY,
							item.series.label + "  " + mins +  " minutes <br> out of  " + item.series.data[item.dataIndex][2] + " <br> Hour:" + stringDate);
						}
						else
						{
							var stringDate = someMonth + "/" + someDay + "/" + someYear + " " + someTime + ":" + someMin + ":" + someSec;
							showTooltip(item.pageX, item.pageY,
								item.series.label + "  " + y + " Time: " + stringDate);
						}
					}
					/************************************
					Specific to Alerts tooltip
					************************************/
					else if(item.series.label=='Alerts')
					{
						  var stringDate = someMonth + "/" + someDay + "/" + someYear ;
            
                            showTooltip(item.pageX, item.pageY,
                             y + " Alerts <br>" + "Date:" + stringDate + " <br>" +  item.series.data[item.dataIndex][2]);
					}
					
					/******************************************************
					show tooltip of signal
					******************************************************/
					else if(precision == 'Second' && (item.series.label=='HT' || item.series.label=='KM' || item.series.label=='WPPD'))
					{
					
						var stringDate = someMonth + "/" + someDay + "/" + someYear + " " + someTime + ":" + someMin + ":" + someSec;
						
					if(someMonth.length ==1)
						someMonth = '0' + someMonth;
					if(someDay.length ==1)
						someDay = '0' + someDay;
						
					//showTooltip(item.pageX, item.pageY,
					//	item.series.label + "  " +  y + "  Date:" + stringDate);
							
				 showSignal(item.pageX, item.pageY, someMonth, someDay, someYear, someTime, someMin, someSec);
					}
					/******************************************************
					Specific to motion, respiration, and pulse tooltips  
					*****************************************************/
					else
					{
						
						if(precision=='Daily')
							var stringDate = someMonth + "/" + someDay + "/" + someYear ;
						else 
							var stringDate = someMonth + "/" + someDay + "/" + someYear + " " + someTime + ":" + someMin + ":" + someSec;
			
						showTooltip(item.pageX, item.pageY,
							item.series.label + "  " +  y + "  Date:" + stringDate);
					
					}
				}
            } else{
                $("#tooltip").remove();
				$("#tooltip2").remove();
				$("#tooltip3").remove();
				$("#tooltip4").remove();
                previousPoint = null;            
                 }
			});
       
			/*************************************
			add clickable data point to plot1
			**************************************/
			
			$(plotarray[i]).bind("plotclick", function (event, pos, item){
              var x = item.datapoint[0]+(24*60*60);
              var error = false;
			  
              if(precision=='Hourly')
				{
				if(item.series.label!="Alerts"&&item.series.label!="Restless"&&item.series.label!="Non-Restless"&&item.series.label!="HT"&&item.series.label!="Respiration")
				{
					//we should not change time period if we try to drill down on motion
					alert("Cannot drill down further on motion graphs");
					error = true;
				}
				else
					$("#Second").prop("checked", true);
              }
              else if(precision=='Second')
                {
					alert("Currently cannot drill down further on graph. Signals coming soon");
					error = true;
				}
              else
                $("#Hourly").prop("checked", true);
               
			   if(error==false){
					submitVal = x;
					
					$('form').append("<input type='hidden' name='currentmax' value='"+
					submitVal+"' />");
					$("form").submit();
				}
			});
        
			/**************************
			connect our two graphs
			**************************/
			$(plotarray[i]).bind("plotrangeselected", function (event, ranges) {
        
			// do the zooming
			plot = $.plot(plotarray[i], data, $.extend(true, {}, options, {
				xaxis: {
				min: ranges.xaxis.from,
				max: ranges.xaxis.to
				}
			}));
				currmin = ranges.xaxis.from;
			currmax = ranges.xaxis.to;
			// don't fire event on the overview to prevent eternal loop

				overview.setrangeSelection(ranges, true);
			});
    
		}
		
		$("#overview").bind("plotselected", function (event, ranges) {
        //plot.setSelection(ranges);
		});
	}
				

   
    