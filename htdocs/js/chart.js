 /***********************************
 tooltip style function
 Mary Sheahen 6/10/2014
 ******************************/
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

/***********************************************
callback function for selecting dates
Mary Sheahen 6/10/2014
***********************************************/
			
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
			$('.demo-placeholder').css({"height": "45%"});
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
				if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                         $("#tooltip").remove();
          
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
                previousPoint = null;            
                 }
			});
       
			/*****************
			add clickable data point to plot1
			**************/
			$(plotarray[i]).bind("plotclick", function (event, pos, item){
              var x = item.datapoint[0];
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
        
			/******************
			connect our two graphs
			*********************/
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
				

   
    