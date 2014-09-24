//----------------------------------------------------
// Loads parameter data from database...
//----------------------------------------------------
function load_gait_data() {

    //----------------------------------------------------
    // Get array 
    //----------------------------------------------------
    $.ajax({
        type: "POST",
        url: "php/gaitQuery.php",
        data:{user_id: id_options[c_id], 
              mws: mw_options[c_mw], 
              gws: dw_options[c_dw]
          },
        async:   false,
        success:function(data){process_gait_data(data); },
        error: function (res)   {alert("Error loading data!");  }
    });

    if( gaitData.length === 0){
        return 0;
    }
    else{
        return 1;
    }
}

function load_gait_data_basic() {

    //----------------------------------------------------
    // Get array 
    //----------------------------------------------------
    $.ajax({
        type: "POST",
        url: "php/gaitQuery.php",
        data:{user_id: id_options[c_id], 
              mws: mw_options[c_sm], 
              gws: dw_options[1]
          },
        async:   false,
        success:function(data){process_gait_data(data); },
        error: function (res)   {alert("Error loading data!");  }
    });

    if( gaitData.length === 0){
        return 0;
    }
    else{
        return 1;
    }
}

function process_gait_data(allText) {

    //--------------------------------------
    // Parse
    //--------------------------------------
    var tR = jQuery.parseJSON(allText);
    var rows = tR.length;
    if(rows === 0){
        gaitData = [];
        return;
    }

    //--------------------------------------------
    // Convert data to array(s)
    //---------------------------------------------
    gaitData = new Array();
    for(var j=0; j<17; j++){           //Making 17 columns...
        gaitData[j] = new Array();
        for (var i=0; i<rows; i++) {
            gaitData[j][i] = new Array(2);
        }
    }


    var dps = 0;
    for (var i=0; i<rows; i++) {

            //-----------------------
            //Gait params in query
            //
            //   4 = Date
            //   5 = Height       (inches)
            //   6 = HeightSEM
            //   7 = Speed        (inches/sec)
            //   8 = SpeedSEM
            //   9 = StrideTime   (seconds)
            //   10 = StrideTimeSEM
            //   11 = StrideLength  (inches)
            //   12 = StrideLengthSEM
            //   13 = WalksPerDay
            //-----------------------
            
            //-----------------------
            //  in gaitData
            //
            //  0 Height
            //  1 StrideTime
            //  2 StrideLength
            //  3 Speed
            //  4 Speed + SEM*1.96
            //  5 Speed - SEM*1.96
            //  6 StrideLength + SEM*1.96
            //  7 StrideLength - SEM*1.96
            //  8 StrideTime + SEM*1.96
            //  9 StrideTime - SEM*1.96
            // 10 System Up/Down
            // 11 Fall Risk Mean
            // 12 Fall Risk + SEM
            // 13 Fall Risk - SEM
            // 14 WalksPerDay
            // 15 middle range (for basic interface)
            // 16 bad range (for basic interface)
            //-------------------------

            var date = new Date(tR[i][4]);
            var date_msec = date.getTime();
            var ctu = 0;


            gaitData[ctu][dps][0] = date_msec;
            if( tR[i][5] !== null)
                gaitData[ctu++][dps][1] = parseFloat(tR[i][5]);  //Leave as inches...
            else
                gaitData[ctu++][dps][1] = NaN;

            gaitData[ctu][dps][0] = date_msec;
            if( tR[i][9] !== null)
                gaitData[ctu++][dps][1] = parseFloat(tR[i][9]);  //Leave as seconds...
            else
                gaitData[ctu++][dps][1] = NaN;

            gaitData[ctu][dps][0] = date_msec;
            if( tR[i][11] !== null)
                gaitData[ctu++][dps][1] = parseFloat(tR[i][11])*2.54;  // Convert to cm
            else
                gaitData[ctu++][dps][1] = NaN;

            gaitData[ctu][dps][0] = date_msec;
            if( tR[i][7] !== null)
                gaitData[ctu++][dps][1] = parseFloat(tR[i][7])*2.54;  // Convert to cm/sec.
            else
                gaitData[ctu++][dps][1] = NaN;


            gaitData[ctu][dps][0] = date_msec;
            if (tR[i][8] !== null){
                gaitData[ctu++][dps][1] = (parseFloat(tR[i][7]) + (parseFloat(tR[i][8])*1.96))*2.54; 
                gaitData[ctu][dps][0] = date_msec;
                gaitData[ctu++][dps][1] = (parseFloat(tR[i][7]) - (parseFloat(tR[i][8])*1.96))*2.54; 
            }
            else{
                gaitData[ctu++][dps][1] = NaN; 
                gaitData[ctu][dps][0] = date_msec;
                gaitData[ctu++][dps][1] = NaN; 
            }

            gaitData[ctu][dps][0] = date_msec;
            if( tR[i][12] !== null){
                gaitData[ctu++][dps][1] = (parseFloat(tR[i][11]) + (parseFloat(tR[i][12])*1.96))*2.54;   
                gaitData[ctu][dps][0] = date_msec;
                gaitData[ctu++][dps][1] = (parseFloat(tR[i][11]) - (parseFloat(tR[i][12])*1.96))*2.54;   
            }
            else{
                gaitData[ctu++][dps][1] = NaN;  
                gaitData[ctu][dps][0] = date_msec;
                gaitData[ctu++][dps][1] = NaN;                     
            }

            gaitData[ctu][dps][0] = date_msec;
            if( tR[i][10] !== null){
                gaitData[ctu++][dps][1] = parseFloat(tR[i][9]) + (parseFloat(tR[i][10])*1.96); 
                gaitData[ctu][dps][0] = date_msec;
                gaitData[ctu++][dps][1] = parseFloat(tR[i][9]) - (parseFloat(tR[i][10])*1.96); 
            }
            else{
                gaitData[ctu++][dps][1] = NaN; 
                gaitData[ctu][dps][0] = date_msec;
                gaitData[ctu++][dps][1] = NaN; 
            }


            //----------------------
            // System up/down time
            //----------------------
            gaitData[ctu][dps][0] = date_msec;
            gaitData[ctu++][dps][1] = NaN;

            //-------------------
            //Fall Risk
            //-------------------
            gaitData[ctu][dps][0] = date_msec;         
            gaitData[ctu++][dps][1] = estimateTUG( gaitData[3][dps][1] );

            gaitData[ctu][dps][0] = date_msec;               
            gaitData[ctu++][dps][1] = estimateTUG( gaitData[4][dps][1] );

            gaitData[ctu][dps][0] = date_msec;               
            gaitData[ctu++][dps][1] = estimateTUG( gaitData[5][dps][1] );

            //----------------------
            // Walks per day
            //----------------------
            gaitData[ctu][dps][0] = date_msec;               
            gaitData[ctu++][dps][1] = tR[i][13];
            
            //----------------------
            // Range for basic
            //----------------------
            gaitData[ctu][dps][0] = date_msec;               
            gaitData[ctu++][dps][1] = 50;
            gaitData[ctu][dps][0] = date_msec;               
            gaitData[ctu++][dps][1] = 40;            
            
            //----------------------
            // Advance counter..
            //----------------------
            dps = dps + 1;
    }

    var tStart = gaitData[0][0][0];
    var tEnd = gaitData[0][dps-1][0];


    //-------------------------------------
    // Update date range if needed...
    //-------------------------------------
    if( dEnd === 0){

        dStart = tStart;
        dEnd = tEnd;

        dsEnd = dEnd;
        dsStart = dStart;

        mDate = gaitData[0][dps-1][0];   //Start model view on most recent available image...
    }
};


