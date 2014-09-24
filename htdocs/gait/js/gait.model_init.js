//-----------------------------------------------
// Init stuff
//-----------------------------------------------
function onInitChange(){

    //----------------------------------------
    // Did SID change ?
    //----------------------------------------
    if( sid_select.selectedIndex === 0 ){
        sid_select.selectedIndex = c_sid;
        return;
    }
    else if( sid_select.selectedIndex !== c_sid ){

        swin_select.disabled = false;  // Need this 
        swin_select.selectedIndex = 0;  //Reset this...

        //-----------------------------------------
        // Handle SID change
        //-----------------------------------------
        sid_update.disabled = true;  //Disable this...
        sid_enable.disabled = false;
        sid_enable.innerHTML = "Enable Updating";
        
        mu_key = [];                 //Clear this...

        //-----------------------------------------
        // Find RIDS
        //-----------------------------------------
        init_rids = new Array(4);
        init_heights = new Array(4);
        init_K = 0;
        for(var j = 0; j < rid_all.length; j++){
            var msd = rid_all[j];
            if( msd > 9999)
                msd = Math.floor(msd/10);

            if( msd === sid_all[sid_select.selectedIndex]){
                init_rids[init_K] = rid_all[j];
                init_heights[init_K++] = height_all[j];
            }
        }

        if( init_K === 0){
            alert('No RID(S) found in user table for system ' + sid_all[sid_select.selectedIndex] + "! Won't be able to initialize!");
        }

        init_K_label.innerHTML = "Residents: " + init_K;
        var tsa = new Array(init_K);
        for(var k = 0; k < init_K; k++){
            tsa[k] = (k+1).toFixed(0);
        }
        popDropDown('update_res_select',tsa);
        if( init_K > 0 )
            update_res_select.disabled = false;
        else
            update_res_select.disabled = true;

        //----------------------------------------------
        // Get current initialization, if it exists...
        //----------------------------------------------
        var mi = -1;
        for(var j = 0; j < init_data.length; j++){
            if( init_data[j][0] === sid_all[sid_select.selectedIndex]){
                mi = j;
                break;
            }
        }
        if( mi < 0){
            init_label.innerHTML = "Current: does not exist for " + sid_all[sid_select.selectedIndex] + "!";
            new_init = [];
        }
        else{
            init_label.innerHTML = "Current: " + init_data[mi][1];
            new_init = init_data[mi][1];
        } 
    }

    //-----------------------------------------
    // Re-load walk data if needed
    //-----------------------------------------
    if( c_sid !== sid_select.selectedIndex || c_swin !== swin_select.selectedIndex ){

        init_walkData = [];

        $.ajax({
            type: "POST",
            url: "php/initWalkQuery.php",
            data:{sid: sid_all[sid_select.selectedIndex], winSize: swin_options[swin_select.selectedIndex] },
            async:   false,
            success:function(data){
                  init_walkData = data;   
            },
            error: function (res){ alert("Error loading walk data!");  }
        });

        init_walkData = jQuery.parseJSON(init_walkData); 
        
        var ttD = new Date(init_walkData[0][4]);
        var ttD2 = new Date(init_walkData[init_walkData.length-1][4]);
        //swin_dates_label.innerHTML = "Range:  (" + ttD.toDateString() + ", " + ttD2.toDateString() + ")";
        swin_dates_label.innerHTML = "Range:  (" + init_walkData[0][4] + ", " + init_walkData[init_walkData.length-1][4] + ")";

        //---------------------------------------------
        // Down sample?
        //---------------------------------------------
        var step = 1; //Math.max( Math.round(init_walkData.length/500), 1);
        var pTk = Math.floor(init_walkData.length/step);

        //---------------------------------------------
        // Make array of points, downsample as needed
        //---------------------------------------------
        init_hs_data = new Array(pTk);
        init_st_data = new Array(pTk);
        init_sl_data = new Array(pTk);
        var cc = 0;
        for(var i = 0; i < init_walkData.length; i+=step, cc++){
           init_hs_data[cc] = new Array(3);
           init_st_data[cc] = new Array(3);
           init_sl_data[cc] = new Array(3);

           init_hs_data[cc][0] = init_walkData[i][0];  //Inches for all
           init_st_data[cc][0] = init_walkData[i][0];
           init_sl_data[cc][0] = init_walkData[i][0];

           init_hs_data[cc][1] = init_walkData[i][3];
           init_st_data[cc][1] = init_walkData[i][1];
           init_sl_data[cc][1] = init_walkData[i][2];
           
           init_hs_data[cc][2] = init_walkData[i][5];
           init_st_data[cc][2] = init_walkData[i][5];
           init_sl_data[cc][2] = init_walkData[i][5];
        }

        //---------------------------------------------
        // Compute point densities...
        //---------------------------------------------
        compute_point_density(init_hs_data,1,1,step);
        compute_point_density(init_sl_data,1,1,step);
        compute_point_density(init_st_data,1,30,step); 
    }

    //----------------------------------------
    // Store current state
    //----------------------------------------
    c_sid = sid_select.selectedIndex;
    c_swin = swin_select.selectedIndex;

    //-----------------------------------------------
    // Update initialization plot data
    //-----------------------------------------------
    if( new_init.length > 0 && init_K > 0){

        var mParams = new_init.split(/\[|,| |;|\]/);

        init_hs_mpd = new Array(init_K);
        init_st_mpd = new Array(init_K);
        init_sl_mpd = new Array(init_K);

        for(var k = 0; k < init_K; k++){
            init_hs_mpd[k] = new Array(4);
            init_st_mpd[k] = new Array(4);
            init_sl_mpd[k] = new Array(4);
            init_h_plot[k] = new Array(1);

            init_hs_mpd[k][0] = parseFloat(mParams[1 + k*5]);
            init_st_mpd[k][0] = init_hs_mpd[k][0];
            init_sl_mpd[k][0] = init_hs_mpd[k][0];

            init_hs_mpd[k][1] = parseFloat(mParams[4 + k*5]);
            init_st_mpd[k][1] = parseFloat(mParams[2 + k*5]);
            init_sl_mpd[k][1] = parseFloat(mParams[3 + k*5]);

            init_hs_mpd[k][2] = 1;   
            init_hs_mpd[k][3] = 3;   //Inches for all...

            init_st_mpd[k][2] = 1;
            init_st_mpd[k][3] = 0.15;

            init_sl_mpd[k][2] = 1;
            init_sl_mpd[k][3] = 3; 
        }

        new_init_label.innerHTML = "New: " + new_init;
    }
    else{
        new_init_label.innerHTML = "New: (click on graphs)";
    }

    if( init_K > 0){
        init_h_plot = new Array(init_K);
        for(var k = 0; k < init_K; k++){
            init_h_plot[k] = new Array(1);
            init_h_plot[k][0] = init_heights[k];
        }
    }


    //-------------------------------------------
    // Make plot data
    //-------------------------------------------
    var init_pD1 = [];
    var init_pD2 = [];
    var init_pD3 = [];
    if(init_walkData.length > 0){
        init_pD1.push( { data: init_hs_data, lines: { show: false }, points: { show: false}  });
        init_pD2.push( { data: init_sl_data, lines: { show: false }, points: { show: false}  }); 
        init_pD3.push( { data: init_st_data, lines: { show: false }, points: { show: false}  });
    }
    if( new_init.length > 0){
        init_pD1.push( { data: init_hs_mpd, lines: { show: false }, points: { show: false}  });
        init_pD2.push( { data: init_sl_mpd, lines: { show: false }, points: { show: false}  });
        init_pD3.push( { data: init_st_mpd, lines: { show: false }, points: { show: false}  });
    }
    if( init_K > 0){
        init_pD1.push( { data: init_h_plot, lines: { show: false }, points: { show: false}  });
        init_pD2.push( { data: init_h_plot, lines: { show: false }, points: { show: false}  });
        init_pD3.push( { data: init_h_plot, lines: { show: false }, points: { show: false}  });
    }

    //-----------------------------------------
    // Plot...
    //-----------------------------------------        
    $.plot($("#flotSid1"), init_pD1, {   grid: {color: "#000",borderWidth: 0,hoverable: false, clickable: true },                
                                    xaxis: { ticks: 10, min: 12, max: 84 },
                                    yaxis: { min:0, max: 64,ticks: 9 },
                                    hooks: { draw : [plot_walks_or_model] } });

    $.plot($("#flotSid2"), init_pD2, {   grid: {color: "#000",borderWidth: 0,hoverable: false, clickable: true },                
                                    xaxis: { ticks: 10, min: 12, max: 84 },
                                    yaxis: { min:8, max: 64,ticks: 8 },
                                    hooks: { draw : [plot_walks_or_model] } });

    $.plot($("#flotSid3"), init_pD3, {   grid: {color: "#000",borderWidth: 0,hoverable: false, clickable: true },                
                                    xaxis: { ticks: 10, min: 12, max: 84 },
                                    yaxis: { min:0.7, max: 3.1,ticks: 9 },
                                    hooks: { draw : [plot_walks_or_model] } });  

    sid_enable.disabled = false;

};

function initEnableUpdate(){
    if(c_sid > 0 && init_K > 0){

        var pwrd = prompt("Password please: ","password");
        $.ajax({
            type: "POST",
            url: "php/verifyUpdate.php",
            data:{pwrd: pwrd },
            async:   false,
            success:function(data){
                  mu_key = data;   
            },
            error: function (res){ alert("Error validating!");  }
        });

        if( mu_key === '0'){
            alert("Password incorrect!");
            sid_update.disabled = true;
            return;
        }

        sid_update.disabled = false;
        sid_enable.innerHTML = "Updating Enabaled";
        sid_enable.disabled = true;
    }
};

function initUpdate(){

    var mS = "Are you sure you want to update the initialization? " +
             "Any existing initialization will be cleared, and all gait " +
             "parameter and model information will be recomputed!";
    if(confirm(mS)){
        $.ajax({
            type: "POST",
            url: "php/updateInitQuery.php",
            data:{mu_key: mu_key, sid: sid_all[sid_select.selectedIndex], new_init: new_init },
            async:   false,
            success:function(data){
                if( data === '1'){
                  alert("Initialization update successful!"); 
                  init_label.innerHTML = "Current: " + new_init;
                  load_initializations();   //Reload initializations for later...
                }
                else
                  alert("Error (1) updating initialization! " + data);
            },
            error: function (res){ alert("Error (2) updating initialization!");  }
        });
    }

};

