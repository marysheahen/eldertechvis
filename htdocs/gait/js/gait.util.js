//-----------------------------------------
// Click functionality...
//-----------------------------------------
function sidClickUpdate(event, pos, item){
    
    if( init_K > 0 && sid_update.disabled === false){
        
        //---------------------------------------------
        // Setup stuff
        //---------------------------------------------
        var nH = new Array(init_K);
        var nS = new Array(init_K);
        var nSL = new Array(init_K);
        var nST = new Array(init_K);
        
        if( new_init.length > 0){
            //--------------------------------------
            // Load existing...
            //--------------------------------------
            var mParams = new_init.split(/\[|,| |;|\]/);
            for(var k = 0; k < init_K; k++){
                nH[k] = parseFloat(mParams[1 + k*5]);
                nST[k] = parseFloat(mParams[2 + k*5]);
                nSL[k] = parseFloat(mParams[3 + k*5]);
                nS[k] = parseFloat(mParams[4 + k*5]);
            }
        }
        else{
            //---------------------------------
            // Blank to start
            //---------------------------------
            for(var k = 0; k < init_K; k++){
                nH[k] = -50;   //Off screen...
                nST[k] = -50;
                nSL[k] = -50;
                nS[k] = -50;
            }              
        }
        
        //-------------------------------------------
        // Which distro are we updating
        //-------------------------------------------
        var k = update_res_select.selectedIndex;
        
        //------------------------------------------
        // Update it based on click...
        //------------------------------------------
        if( event.target.id === 'flotSid1'){
            //--------------------------------------
            // Click on height/speed graph
            //--------------------------------------
            nH[k] = pos.x;
            nS[k] = pos.y;
            if( nSL[k] < 0){
                nSL[k] = nH[k]*0.4;  //Guess...
            }
            nST[k] = nSL[k]/nS[k];

        }
        else if( event.target.id === 'flotSid2'){
            //--------------------------------------
            // Click on height/stride length graph
            //--------------------------------------
            nH[k] = pos.x;
            nSL[k] = pos.y;
            if( nS[k] < 0){
                nS[k] = nSL[k]/1.5;  //Guess...
            }
            nST[k] = nSL[k]/nS[k];
        }
        else if( event.target.id === 'flotSid3'){
            //--------------------------------------
            // Click on height/stride time graph
            //--------------------------------------    
            nH[k] = pos.x;
            nST[k] = pos.y;
            if( nS[k] < 0){
                nS[k] = (nH[k]*0.4)/nST[k];  //Guess...
            }
            nSL[k] = nS[k]*nST[k];
        }
        
        new_init = "[";
        for(k = 0; k < init_K; k++){
            new_init = new_init + nH[k].toFixed(2) + " " + nST[k].toFixed(2) + " " + nSL[k].toFixed(2) + " " + nS[k].toFixed(2) + "; ";
        }
        new_init = new_init + "]";
        
        onInitChange();
    }
}


function modelClickUpdate(event, pos, item){

    if( c_id > 0 && raw_update.disabled === false){
        
        
        //-------------------------------------------
        // Which distro are we updating
        //-------------------------------------------
        var rK = 1;
        if( (mParams.length > 30) )
            rK = 2;
        
        var k = 0;
        if((id_options[c_id] > 9999) && (id_options[c_id] % 2 === 0))
            k = 1;
        
        //--------------------------------------------
        // Needed indicies into mParams
        //--------------------------------------------
        var hIdx = 1 + k*4;        //Only going to adjust the mean...
        var spIdx = 4 + k*4;
        var stIdx = 2 + k*4;
        var slIdx = 3 + k*4;
        
        //------------------------------------------
        // Update it based on click...
        //------------------------------------------
        if( event.target.id === 'flotM1'){
            //--------------------------------------
            // Click on height/speed graph
            //--------------------------------------
            mParams[hIdx] = (pos.x / 2.54).toString();
            mParams[spIdx] = (pos.y / 2.54).toString();
            mParams[stIdx] = (parseFloat(mParams[slIdx])/parseFloat(mParams[spIdx])).toString();
        }
        else if( event.target.id === 'flotM2'){
            //--------------------------------------
            // Click on height/stride length graph
            //--------------------------------------
            mParams[hIdx] = (pos.x / 2.54).toString();
            mParams[slIdx] = (pos.y / 2.54).toString();
            mParams[stIdx] = (parseFloat(mParams[slIdx])/parseFloat(mParams[spIdx])).toString();
        }
        else if( event.target.id === 'flotM3'){
            //--------------------------------------
            // Click on height/stride time graph
            //--------------------------------------    
            mParams[hIdx] = (pos.x / 2.54).toString();
            mParams[stIdx] = (pos.y).toString();
            mParams[slIdx] = (parseFloat(mParams[spIdx])*parseFloat(mParams[stIdx])).toString();
        }
         
         
        //-------------------------------------------------------------
        // Make string representation for new model
        //-------------------------------------------------------------
        objStringToSave = "[" + parseFloat(mParams[1]).toFixed(2) + " " + 
                                       parseFloat(mParams[2]).toFixed(2) + " " + parseFloat(mParams[3]).toFixed(2) + " " + parseFloat(mParams[4]).toFixed(2);
        if( rK === 2){
            objStringToSave = objStringToSave + ";" + parseFloat(mParams[5]).toFixed(2) + " " + parseFloat(mParams[6]).toFixed(2) + " " + 
                                       parseFloat(mParams[7]).toFixed(2) + " " + parseFloat(mParams[8]).toFixed(2) + "],[";
        }
        else{
            objStringToSave = objStringToSave  + "],[";
        }
            
        var bIdx = 7 + (rK-1)*4;
        var eIdx = bIdx + rK*16;
            
        for(var k = bIdx; k < eIdx-1; k++){
            objStringToSave = objStringToSave + parseFloat(mParams[k]).toFixed(4) + " ";
        }
        objStringToSave = objStringToSave + parseFloat(mParams[eIdx-1]).toFixed(4) + "],[";
        
        if( rK === 1)
            objStringToSave = objStringToSave + parseFloat(mParams[eIdx+1]).toFixed(3) + "]";
        else
            objStringToSave = objStringToSave + parseFloat(mParams[eIdx+2]).toFixed(3) + " " + parseFloat(mParams[eIdx+3]).toFixed(3) + "]";
        

        //----------------------------------
        // Update plots
        //----------------------------------
        plotModelImages(); 
    }
}


//---------------------------------------------
// Hover functionality
//---------------------------------------------
function hoverUpdate(event, pos, item) {
    if (item) {
        if( previousPoint !== item.dataIndex ){
            previousPoint = item.dataIndex;
            $("#tooltip3").remove();
            var td = new Date(item.datapoint[0]);

            if( (item.datapoint[1] === 100 || item.datapoint[1] === 55) && 
                (item.dataIndex < alertsToPlot.length) && (alertsToPlot[item.dataIndex][2] > 0) ){
                //---------------------------------------
                // Hovering over a gait alert 
                //---------------------------------------
                var aCode = "";
                if( Number(alertsToPlot[item.dataIndex][2])&0x01 ){
                     aCode = aCode + "increased stride time, ";
                }
                if(Number(alertsToPlot[item.dataIndex][2])&0x02 ){
                    aCode = aCode + "increased stride length, ";
                }
                if(Number(alertsToPlot[item.dataIndex][2])&0x04 ){
                    aCode = aCode + "increased gait speed, ";
                }
                if(Number(alertsToPlot[item.dataIndex][2])&0x08 ){
                    aCode = aCode + "decreased stride time, ";
                }
                if(Number(alertsToPlot[item.dataIndex][2])&0x10 ){
                    aCode = aCode + "decreased stride length, ";
                }
                if(Number(alertsToPlot[item.dataIndex][2])&0x20 ){
                    aCode = aCode + "decreased gait speed ";
                }
                
                showTooltip(item.pageX, item.pageY,
                        "Date=" + $.datepicker.formatDate('yy-mm-dd', td) + ", Cause: " + aCode); 
                
            }
            else{
                //----------------------------------------
                // Normal point
                //----------------------------------------
                showTooltip(item.pageX, item.pageY,
                    "Date=" + $.datepicker.formatDate('yy-mm-dd', td) + ", Y=" + Number(item.datapoint[1]).toFixed(2));
            }
        }
    }
    else{
        $("#tooltip3").remove();
        previousPoint = -1;                
    }
}
 function showTooltip(x, y, contents) {
    $("<div id='tooltip3'>" + contents + "</div>").css({
        position: "absolute",
        display: "none",
        top: y + 5,
        left: x + 5,
        border: "1px solid #fdd",
        padding: "2px",
        "background-color": "#fee",
	'z-index': 105,
        opacity: 1.0
    }).appendTo("body").fadeIn(200);
};   

//---------------------------------------------
// Re-populates drop down menu
//---------------------------------------------
function popDropDown(menu_id, option_array) {
    var select = document.getElementById(menu_id);

    for(var i=select.options.length-1;i>=0;i--){
        select.remove(i);
    }

    for (var i = 0; i < option_array.length; i++) {
        var opt = option_array[i];
        var el = document.createElement("option");
        el.textContent = opt;
        el.value = opt;
        select.appendChild(el);
    }
}

//-----------------------------------------
// Updates param plot when slider changed..
//-----------------------------------------
var rangeselectionCallback = function(o){
    var xaxis = paramGraph.getAxes().xaxis;
    dsStart = o.start;
    dsEnd = o.end;
    xaxis.options.min = dsStart;
    xaxis.options.max = dsEnd;
    xaxis.options.minTickSize = [Math.ceil((dsEnd - dsStart)/msecDay/7),"day"];
    xaxis.options.ticks = 7;
    paramGraph.setupGrid();
    paramGraph.draw();
};

var rangeselectionCallback2 = function(o){
    var xaxis = riskGraph.getAxes().xaxis;
    dsStart = o.start;
    dsEnd = o.end;
    xaxis.options.min = dsStart;
    xaxis.options.max = dsEnd;
    xaxis.options.minTickSize = [Math.ceil((dsEnd - dsStart)/msecDay/7),"day"];
    xaxis.options.ticks = 7;
    riskGraph.setupGrid();
    riskGraph.draw();
};

//-----------------------------------------
// Updates param plot based on selections..
//-----------------------------------------
function cbChanged() {
    if( c_id > 0){
        plotParamData();
    }
};

function cbChanged2(){
    if( c_id > 0){
        plotFallRiskData();
    }
};

//--------------------------------------
// Estimate TUG from average speed
//--------------------------------------
function estimateTUG(value){
    var tug = (value - TUGmodel[0])/TUGmodel[1];
    tug = tanh( (TUGmodel[2] + (tug*TUGmodel[3])));
    return (TUGmodel[4] + (tug*TUGmodel[5]));
}
function tanh(arg) {
    //Math.tanh is experimental (only works in FireFox right now...)
    var pos = Math.exp(arg);
    var neg = Math.exp(-arg);
    return (pos - neg) / (pos + neg);
}

//-------------------------------------------------
// Draw hook, for plotting walk data...
//-------------------------------------------------
function plot_walks_or_model(plot, ctx) {
    var data = plot.getData();
    var axes = plot.getAxes();
    var offset = plot.getPlotOffset();

    for (var i = 0; i < data.length; i++) {
        var series = data[i];
        for (var j = 0; j < series.data.length; j++) {
            if( series.data[j].length === 3){
                //----------------------------------
                // Density plot
                //----------------------------------
                var d = (series.data[j]);
                var x = offset.left + axes.xaxis.p2c(d[0]);
                var y = offset.top + axes.yaxis.p2c(d[1]);
                var color = "rgb(0,0," + Math.min(d[2]*3,255) + ")";
                var radius = 1;

                ctx.lineWidth = 0;
                ctx.beginPath();
                ctx.arc(x,y,radius,0,Math.PI*2,true);
                ctx.closePath(); 
                ctx.fillStyle = color;
                ctx.fill();
            }
            else if (series.data[j].length === 4){
                //-----------------------------------
                // Draw ellipse at point
                //-----------------------------------
                var d = (series.data[j]);
                var x = d[0]; 
                var y = d[1];  

                ctx.lineWidth = 2;
                if( j % 2 === 0)
                    ctx.strokeStyle = "rgb(255,0,0)";
                else
                    ctx.strokeStyle = "rgb(0,255,0)";
                

                for(var stdc = 1; stdc <= 2; stdc++){
                    ctx.beginPath();
                    for(var t = 0; t <= 360; t+=15){
                        var rad = Math.PI*t/180.0;
                        var dx = Math.cos(rad)*d[2]*stdc;
                        var dy = Math.sin(rad)*d[3]*stdc;
                       
                        if( t === 0)
                            ctx.moveTo(offset.left + axes.xaxis.p2c(x+dx), offset.top + axes.yaxis.p2c(y+dy));
                        else
                            ctx.lineTo(offset.left + axes.xaxis.p2c(x+dx), offset.top + axes.yaxis.p2c(y+dy));
                    }
                    ctx.closePath();    
                    ctx.stroke();
                } 
            }
            else if( series.data[j].length === 1){
                //Make a vertical line...
                var d = (series.data[j]);
                var x = offset.left + axes.xaxis.p2c(d[0]);
               
                ctx.lineWidth = 1;
                ctx.strokeStyle = "rgb(150,150,255)";
                ctx.beginPath();
                ctx.moveTo(x,offset.top);
                ctx.lineTo(x,offset.top + axes.yaxis.p2c(axes.yaxis.min));  //Off screen
                ctx.closePath(); 
                ctx.stroke();
            }
        } 
    }
}; 


//-------------------------------------------------
// Draw hook, for alerts...
//-------------------------------------------------
function draw_alerts(plot, ctx) {
    var data = plot.getData();
    var axes = plot.getAxes();
    var offset = plot.getPlotOffset();

    for (var i = 0; i < data.length; i++) {
        var series = data[i];
        for (var j = 0; j < series.data.length; j++) {
            if( series.data[j].length === 3 && series.data[j][2] !== 0){
                //-----------------------------
                // Make a vertical line...
                //-----------------------------
                var d = (series.data[j]);
                var x = offset.left + axes.xaxis.p2c(d[0]);
               
                ctx.lineWidth = 1;
                ctx.strokeStyle = "rgb(0,0,0)";
                ctx.beginPath();
                ctx.moveTo(x,offset.top);
                ctx.lineTo(x,offset.top + axes.yaxis.p2c(axes.yaxis.min));  //Off screen
                ctx.closePath(); 
                ctx.stroke();
            }
        } 
    }
}; 


//--------------------------------------
// Compute point density
//--------------------------------------
function compute_point_density(data, cutoff, adj, step){
    
    var nP = data.length;
    if( nP <= 0)
        return;
    
    var nD = data[0].length;  //nD-1 will hold density count
    var lA = new Array(nP);
    
    for(var i = 0; i < nP; i++){
        data[i][nD-1] = -1;
        lA[i] = 0;
    }
    
    for(var i = 0; i < nP; i++){
        if( data[i][nD-1] < 0 ){
            data[i][nD-1] = 0;
            for(var j = 0; j < nP; j++){
                var dis = 0.0;
                for(var d = 0; d < nD-1; d++){
                    if( d === 0 )
                        dis += Math.abs(data[i][d] - data[j][d]);
                    else
                        dis += Math.abs(data[i][d] - data[j][d])*adj;
                }
                if( dis <= cutoff){
                    data[i][nD-1] += step;
                    lA[j] = 1;
                }
            }
            
            if( data[i][nD-1] > 0){
                for(var j = 0; j < nP; j++){
                    if( lA[j] === 1){
                        data[j][nD-1] = data[i][nD-1]; 
                        lA[j] = 0;
                    }
                }
            }
        }
    }
}

function plotModelImages(){
    
    var eD = new Date(modelDataString[0][0]);
    var sD = new Date(eD.getTime() - mw_options[c_mw]*msecDay);
    
    //--------------------------------------------
    // Extract mParam stuff
    //--------------------------------------------
    var rK = 1;
    if( mParams.length > 30)
        rK = 2;

    var hs_mpd = new Array(rK);
    var st_mpd = new Array(rK);
    var sl_mpd = new Array(rK);

    for(var k = 0; k < rK; k++){
        hs_mpd[k] = new Array(4);
        st_mpd[k] = new Array(4);
        sl_mpd[k] = new Array(4);

        hs_mpd[k][0] = parseFloat(mParams[1 + k*4])*2.54;
        st_mpd[k][0] = hs_mpd[k][0];
        sl_mpd[k][0] = hs_mpd[k][0];

        hs_mpd[k][1] = parseFloat(mParams[4 + k*4])*2.54;
        st_mpd[k][1] = parseFloat(mParams[2 + k*4]);
        sl_mpd[k][1] = parseFloat(mParams[3 + k*4])*2.54;

        hs_mpd[k][2] = Math.sqrt(parseFloat(mParams[7 + ((rK-1)*4) + k*16]))*2.54;   
        hs_mpd[k][3] = Math.sqrt(parseFloat(mParams[22 + ((rK-1)*4) + k*16]))*2.54;

        st_mpd[k][2] = Math.sqrt(parseFloat(mParams[7 + ((rK-1)*4) + k*16]))*2.54;  
        st_mpd[k][3] = Math.sqrt(parseFloat(mParams[12 + ((rK-1)*4) + k*16]));

        sl_mpd[k][2] = Math.sqrt(parseFloat(mParams[7 + ((rK-1)*4) + k*16]))*2.54; 
        sl_mpd[k][3] = Math.sqrt(parseFloat(mParams[17 + ((rK-1)*4) + k*16]))*2.54;
    }

    //----------------------------------------------
    // Load walk data for model...
    //----------------------------------------------
    var walkData = [];
    $.ajax({
        type: "POST",
        url: "php/walkQuery.php",
        data:{sid: sid_options[c_id], startTime: sD.toJSON(), endTime: eD.toJSON() },
        async:   false,
        success:function(data){
              walkData = data;   
        },
        error: function (res){ alert("Error loading walk data!");  }
     });

     walkData = jQuery.parseJSON(walkData);
     if(walkData.length === 0){
         return;
     }

    //---------------------------------------------
    // Down sample
    //---------------------------------------------
    var step = 1; //Math.max( Math.round(walkData.length/1600), 1);
    var pTk = Math.floor(walkData.length/step);


    //---------------------------------------------
    // Make array of points...
    //---------------------------------------------
    var hs_data = new Array(pTk);
    var st_data = new Array(pTk);
    var sl_data = new Array(pTk);
    var cc = 0;
    for(var i = 0; i < walkData.length; i+= step, cc++){
        hs_data[cc] = new Array(3);
        st_data[cc] = new Array(3);
        sl_data[cc] = new Array(3);

        hs_data[cc][0] = walkData[i][0]*2.54;
        st_data[cc][0] = walkData[i][0]*2.54;
        sl_data[cc][0] = walkData[i][0]*2.54;

        hs_data[cc][1] = walkData[i][3]*2.54;
        st_data[cc][1] = walkData[i][1];
        sl_data[cc][1] = walkData[i][2]*2.54;

        hs_data[cc][2] = 0;
        st_data[cc][2] = 0;
        sl_data[cc][2] = 0;
    }

    //---------------------------------------------
    // Fill in point densities...
    //---------------------------------------------
    //compute_point_density(hs_data,2.5,1,step);
    //compute_point_density(sl_data,2.5,1,step);
    //compute_point_density(st_data,2.5,75,step); 

    //----------------------------------
    // Plots
    //----------------------------------
    var pD1 =[];
    pD1.push( { data: hs_data, lines: { show: false }, points: { show: false}  });
    pD1.push( { data: hs_mpd, color: pcolors[2], lines: {show: false}, points:{show: false }});

    $.plot($("#flotM1"), pD1, {   grid: {color: "#000",borderWidth: 0,hoverable: false, clickable: true },                
                                            xaxis: { ticks: 10, min: 30, max: 210 },
                                            yaxis: { min:0, max: 160,ticks: 9 },
                                            hooks: { draw : [plot_walks_or_model] } });        

    var pD2 =[];
    pD2.push( { data: sl_data, lines: { show: false }, points: { show: false}  });
    pD2.push( { data: sl_mpd, color: pcolors[2], lines: {show: false}, points:{show: false }});

    $.plot($("#flotM2"), pD2, {   grid: {color: "#000",borderWidth: 0,hoverable: false, clickable: true },                
                                            xaxis: { ticks: 10, min: 30, max: 210 },
                                            yaxis: { min:20, max: 160,ticks: 8 },
                                            hooks: { draw : [plot_walks_or_model] } }); 

    var pD3 =[];
    pD3.push( { data: st_data, lines: { show: false }, points: { show: false}  });
    pD3.push( { data: st_mpd, color: pcolors[2], lines: {show: false}, points:{show: false }});

    $.plot($("#flotM3"), pD3, {   grid: {color: "#000",borderWidth: 0,hoverable: false, clickable: true },                
                                            xaxis: { ticks: 10, min: 30, max: 210 },
                                            yaxis: { min:0.7, max: 3.1,ticks: 9 },
                                            hooks: { draw : [plot_walks_or_model] } }); 
                                        
                                        
    modelslider.hidden = false;    
    rwin_date_label.innerHTML = "(" + sD.toDateString() + ", " + eD.toDateString() + ")";
   
}
