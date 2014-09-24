//-----------------------------------------
// Plots already loaded parameter data...
//-----------------------------------------     
function plotParamData(){

    var newData =[];
    var minTickSize = Math.ceil((dsEnd - dsStart)/msecDay/7);

    if( height_check.checked ){
        newData.push({label: "height (in)",
        data: gaitData[0],
        color: pcolors[0], 
        lines: { lineWidth: 3}
        });
    }
    else{
        // So that yaxis is always used...
        newData.push({label: "height (in)",
        data: gaitData[0],
        color: pcolors[0], 
        lines: {lineWidth: 0} 
        });
    }
    if( st_check.checked){

        newData.push({label: "stride time (sec)",
        data: gaitData[1],
        yaxis: 2,
        color: pcolors[1], 
        lines: {show: drawlines.checked, lineWidth: 3},
        points: { show: drawpoints.checked, fill: true, radius: 1 }});

        if( conf_check.checked){
            newData.push({label: "st_95_ub (sec)",
            data: gaitData[8],
            yaxis: 2,
            color: pcolors[1], lines: {lineWidth: 1}});

            newData.push({label: "st_95_lb (sec)",
            data: gaitData[9],
            yaxis: 2,
            color: pcolors[1], lines: {lineWidth: 1}});
        }
    }
    if( sl_check.checked){

        newData.push({label: "stride length (cm)",
        data: gaitData[2],
        color: pcolors[2], 
        lines: {show: drawlines.checked, lineWidth: 3},
        points: { show: drawpoints.checked, fill: true, radius: 1 }});

        if( conf_check.checked){
            newData.push({label: "sl_95_ub (cm)",
            data: gaitData[6],
            color: pcolors[2], lines: {lineWidth: 1}});

            newData.push({label: "sl_95_lb (cm)",
            data: gaitData[7],
            color: pcolors[2], lines: {lineWidth: 1}});
        }
    }
    if( as_check.checked){

        newData.push( {label: "average speed (cm/sec)",
        data: gaitData[3],
        color: pcolors[3], 
        lines: {show: drawlines.checked, lineWidth: 3},
        points: { show: drawpoints.checked, fill: true, radius: 1 }});

        if( conf_check.checked){
            newData.push({label: "as_95_ub (cm/sec)",
            data: gaitData[4],
            color: pcolors[3], lines: {lineWidth: 1}});

            newData.push({label: "as_95_lb (cm/sec)",
            data: gaitData[5],
            color: pcolors[3], lines: {lineWidth: 1}});               
        }
    }

    newData.push({label: "system down time",
    data: gaitData[10], 
    lines: {lineWidth: 1, fill: true, fillColor: "rgba(100, 100, 100, 0.4)"} });

    if( alert_check.checked ){
        newData.push({
                data: alertsToPlot,
                color: "rgb(0,0,0)",
                yaxis: 1,
                lines: {show: false},
                points: {show: true, radius: 4} });
    }

    paramGraph = $.plot($("#flot1"),
        newData,

        {       
            grid: {
                color: "#000",
                borderWidth: 0,
                hoverable: true
            },

            xaxis: {
                mode: "time",
                timeformat: "%m/%d/%y",
                minTickSize: [minTickSize,"day"],
                ticks: 7,
                min: dsStart,
                max: dsEnd
            },
            yaxes: [{
                min:20, max: 100,  position:"left", tickSize: 10 
            },
            {

               min:1, max: 2.4, position:"right", alignTicksWithAxis: 1
            }],
            legend: {
                show: false
            },
            hooks: { draw : [draw_alerts] }
        }
    );

    $.plot($("#smallgraph"),
        newData,
        {
            xaxis: {
                mode: "time",
                show:false
            },
            yaxes: [{
                min:20, max: 100,  position:"left", tickSize: 10, show: false 
            },
            {

               min:1, max: 2.4, position:"right", alignTicksWithAxis: 1, show: false
            }],
            legend:{
              show:false  
            },
            grid:{
                color: "#666",
                borderWidth: 2
            },
            rangeselection:{
                color: pcolors[4], //"#feb",
                start: dsStart,
                end: dsEnd,
                enabled: true,
                callback: rangeselectionCallback
            }
        }
    );

};

//----------------------------------------
// Plots estimaed TUG (based as avg spd)
//----------------------------------------
function plotFallRiskData(){

    var newData =[];
    var minTickSize = Math.ceil((dsEnd - dsStart)/msecDay/7);

    newData.push( {label: "TUG time (sec)",
    data: gaitData[11],
    color: pcolors[2], 
    lines: { show: drawlines2.checked, lineWidth: 3 },
    points: { show: drawpoints2.checked, fill: true, radius: 1 } 
    });

    newData.push({label: "",
    data: gaitData[10], 
    lines: {lineWidth: 1, fill: true, fillColor: "rgba(100, 100, 100, 0.4)"} });

    newData.push({label: "",
    data: [[dStart,17], [dEnd,17]],
    color: pcolors[0],
    lines: { lineWidth: 1 } });

    newData.push({label: "",
    data: [[dStart,30], [dEnd,30]],
    color: pcolors[0],
    lines: { lineWidth: 1 } });


    if( conf_check2.checked){
        newData.push({label: "",
        data: gaitData[12],
        color: pcolors[2], lines: {lineWidth: 1}});

        newData.push({label: "",
        data: gaitData[13],
        color: pcolors[2], lines: {lineWidth: 1}});               
    }
    
    if( alert_check2.checked ){
        newData.push({
                data: alertsToPlot_RG,
                color: "rgb(0,0,0)",
                yaxis: 1,
                lines: {show: false},
                points: {show: true, radius: 4} });
    }
    

    riskGraph = $.plot($("#flot2"),
        newData,
        {       
            grid: {
                color: "#000",
                borderWidth: 0,
                hoverable: true
            },

            xaxis: {
                mode: "time",
                timeformat: "%m/%d/%y",
                minTickSize: [minTickSize,"day"],
                ticks: 7,
                min: dsStart,
                max: dsEnd
            },
            yaxis: {
                min:5, max: 55,  position:"left", tickSize: 5 
            },
            legend: {
                show: true
            },
            hooks: { draw : [draw_alerts] }
        }
    );

    $.plot($("#smallgraph2"),
        newData,
        {
            xaxis: {
                mode: "time",
                show:false
            },
            yaxis: {
                min:5, max: 55,  position:"left", tickSize: 5, show: false 
            },
            legend:{
              show:false  
            },
            grid:{
                color: "#666",
                borderWidth: 2
            },
            rangeselection:{
                color: pcolors[4], //"#feb",
                start: dsStart,
                end: dsEnd,
                enabled: true,
                callback: rangeselectionCallback2
            }
        }
    );       

};


