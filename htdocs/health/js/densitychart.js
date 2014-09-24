 
 function getoptions(){
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
        minTickSize: [1,"day"]
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
		//	tickFormatter: tickformat,
				
          },
          yaxis: {
            min:0,
			max:4,
            position: 'left',
            tickDecimals: 0,
			show:false,
          
         },			
          grid: {
                        hoverable: true,
                        clickable: true
                    },
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
        minTickSize:[1, "day"]
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
                }
    };
    return options
	}

    
function bindevents(){
	var plotarray = ["#placeholder", "#placeholder2", "#placeholder3", "#alertgraph", "#alertgraph2", "#alertgraph3"];	
		for(var i = 0; i<2; i++){
			$(plotarray[i]).bind("plothover", function (event, pos, item) {
				if(pos){
                    $("#tooltip").remove();											
                    var x = Math.floor(pos.x.toFixed(2));
					var someTime = (Math.floor(y)+1)*(-1);
					var d = new Date(x);				
					var someDay = d.getDate() + 1;
					var someMonth = d.getMonth() + 1; //months are zero based
					var someYear = d.getFullYear();				
					if(i < 3){
						var y = pos.y.toFixed(2);	
						var stringDate = someMonth + "/" + someDay + "/" + someYear + " " + someTime + ":00:00 to " + (someTime+1) + ":00:00";
							showTooltip(pos.pageX, pos.pageY,
                              stringDate);    
					}
					else{		
						var y = Math.floor(item.datapoint[1].toFixed(2))			  
						var stringDate = someMonth + "/" + someDay + "/" + someYear ;
						showTooltip(item.pageX, item.pageY,
                             y + " Alerts <br>" + "Date:" + stringDate + " <br>" +  item.series.data[item.dataIndex][2]);
					}
				}
				else
					$("#tooltip").remove();
                });
		}
	}