//-----------------------------------------
// Generates alerts..
//-----------------------------------------
function run_gait_alerts(){
  
    // Need one series for the parameter graph, and
    //  one for the risk graph
    alertsToPlot_RG = new Array(gaitData[0].length);
    alertsToPlot = new Array(gaitData[0].length);
    for(var i = 0; i < gaitData[0].length; i++){
        alertsToPlot[i] = [gaitData[0][i][0],0,0];  //Make this 3 for hook...
        alertsToPlot_RG[i] = [gaitData[0][i][0],0,0];  //Make this 3 for hook...
    }

    var r_fix = 1;
    var lA = 0;
    var doAlert = 0;
    var range = new Array();
    for(var f = 0; f < fTu.length; f++)
        range[f] = [0, 0];

    for(var d = nWin; d < gaitData[0].length; d++){

        //----------------------------------
        // Update baseline....
        //----------------------------------
        if(r_fix > 0){ 

            var si = d - nWin;
            var ei = d - 1;

            //-----------------------------------------------------
            // Make sure we have enough data...
            //-----------------------------------------------------
            var nGd = 0;  
            var wNp = 0;
            var uR = new Array(fTu.length);
            var nMv = new Array(fTu.length);
            for(var j = 0; j < fTu.length; j++){
                uR[j] = 0;
                nMv[j] = 0;
            }

            for(var td = si; td <= ei; td++){
                if( !isNaN(parseFloat(gaitData[1][td][1])) ){
                    nGd++;
                    wNp += parseFloat(gaitData[14][td][1]);

                    for(var j = 0; j < fTu.length; j++){
                        uR[j] += (parseFloat(gaitData[8 - j*2][td][1]) - parseFloat(gaitData[9 - j*2][td][1]));
                        nMv[j] += (parseFloat(gaitData[1+j][td][1]) * parseFloat(gaitData[14][td][1]));
                    }
                }
            }
            

            if ((nGd < rD*nWin) ||  (wNp < nWin*4)){
                doAlert = 0;
            }
            else{
                //-----------------------------------------------
                // Update the normal parameter cutoffs...
                //-----------------------------------------------  
                doAlert = 1;
                r_fix = 0;
                lA = d - 1;

                for(var j = 0; j < fTu.length; j++){
                    uR[j] = (uR[j]/nGd)/2;
                    nMv[j] /= wNp;

                    range[j][0] = nMv[j]*(1 + hY[0]) + uR[j];
                    range[j][1] = nMv[j]*(1 - hY[1]) - uR[j];
                }
            }
        }


        //----------------------------------------------
        // Run alert algorithm...
        //----------------------------------------------
        if( doAlert === 1){
            var ci = d - cWin + 1;
            var wCp = 0;
            var nGdC = 0;
            var uR = new Array(fTu.length);
            var cM = new Array(fTu.length);
            for(var j = 0; j < fTu.length; j++){
                uR[j] = 0;
                cM[j] = 0;
            }

            for(var td = ci; td <= d; td++){
                if( !isNaN(parseFloat(gaitData[1][td][1])) ){ 
                    wCp += parseFloat(gaitData[14][td][1]);
                    nGdC++;
                    for(var j = 0; j < fTu.length; j++){
                       uR[j] += parseFloat(gaitData[8 - j*2][td][1]) - parseFloat(gaitData[9 - j*2][td][1]);
                       cM[j] += parseFloat(gaitData[1+j][td][1]) * parseFloat(gaitData[14][td][1]);
                    }     
                }   
            }

            if ((d-lA >= 7) && (nGdC >= rD*cWin) && (wCp >= cWin*4) ){

                //----------------------------------------------------
                // Compute current value, threshold
                //----------------------------------------------------
                for(var j = 0; j < fTu.length; j++){
                    uR[j] = (uR[j]/nGdC)/2;
                    cM[j] /= wCp;

                    if( (cM[j] - uR[j] >= range[j][0]) ){
                        r_fix = 1;
                        lA = d;
                        alertsToPlot[d][2] |= (1<<j);
                        alertsToPlot[d][1] = 100;

                        alertsToPlot_RG[d][2] |= (1<<j);
                        alertsToPlot_RG[d][1] = 55;
                    }
                    else if(cM[j] + uR[j] <= range[j][1]){
                        r_fix = 1;
                        lA = d;
                        alertsToPlot[d][2] |= (1<<(j+fTu.length));
                        alertsToPlot[d][1] = 100;

                        alertsToPlot_RG[d][2] |= (1<<(j+fTu.length));
                        alertsToPlot_RG[d][1] = 55;
                    }
                }                
            }
        }
    }
}


