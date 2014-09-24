//----------------------------------------------------
// Gets list of all RIDs from database
//----------------------------------------------------
function load_rid_sid(){

    //--------------------------------------------------
    // Loads RIDs in gait table
    //--------------------------------------------------
    $.ajax({
        type: "POST",
        url: "php/ridQuery.php",
        async:   false,
        success:function(data){ process_rid(data); },
        error: function (res)   {alert("Error getting RID list!");  }
    });   

    //--------------------------------------------------
    // Loads all SIDs in walk_table
    //--------------------------------------------------
    $.ajax({
        type: "POST",
        url: "php/sidQuery.php",
        async:   false,
        success:function(data){process_sid(data); },
        error: function (res)   {alert("Error getting SID list!");  }
    });
    
    //-------------------------------------------------
    // Loads all RIDs and heights from user table
    //-------------------------------------------------
    $.ajax({
        type: "POST",
        url: "php/ridAllQuery.php",
        async:   false,
        success:function(data){process_rid_all(data); },
        error: function (res)   {alert("Error getting RID(s)/Height(s) from user table!");  }
    });
}

function process_rid(data){

    var tR = jQuery.parseJSON(data);

    if(tR === null || tR.length < 1){
        id_options = [];
        sid_options = [];
        id_text = [];
        return;
    }

    id_options = new Array(tR.length + 1);
    id_options[0] = -1;
    sid_options[0] = -1;
    id_text = new Array(tR.length + 1);
    id_text[0] = "--------";

    for( var j =0; j < tR.length; j++){
        id_options[j+1] = parseInt(tR[j]);

        if( id_options[j+1] > 9999)
            sid_options[j+1] = Math.floor(id_options[j+1]/10);
        else
            sid_options[j+1] = id_options[j+1];

        id_text[j+1] = tR[j];
    }
}

function process_sid(data){

    var tR = jQuery.parseJSON(data);

    if(tR === null || tR.length < 1){
        sid_all = [];
        sid_text = [];
        return;
    }

    sid_all = new Array(tR.length + 1);
    sid_all[0] = -1;
    sid_text = new Array(tR.length + 1);
    sid_text[0] = "--------";

    for( var j =0; j < tR.length; j++){
        sid_all[j+1] = parseInt(tR[j]);
        sid_text[j+1] = tR[j];
    }
   
}

function process_rid_all(data){

    var tR = jQuery.parseJSON(data);

    if(tR === null || tR.length < 1){
        rid_all = [];
        height_all = [];
        return;
    }
    
    var temp_height_all = new Array(tR.length);
    var temp_rid_all = new Array(tR.length);
    var cc = 0;
    for( var j =0; j < tR.length; j++){
        var crid = parseInt(tR[j][0]);
        var sc = 1;
        
        if( crid <= 9999){
            //------------------------------------
            // Check for multiple residents
            //------------------------------------
            for( var k=0; k < tR.length; k++){
                if( Math.floor(parseInt(tR[k][0])/10) === crid){
                    sc = 0;
                }
            }
        }
        
        if( sc > 0){
            temp_rid_all[cc] = crid;
            temp_height_all[cc++] = parseFloat(tR[j][1]);
        }
    }
    
    height_all = new Array(cc);
    rid_all = new Array(cc);
    for(var j =0; j < cc; j++){
        height_all[j] = temp_height_all[j];
        rid_all[j] = temp_rid_all[j];
    }
    
}

//----------------------------------------------------
// Load init information from database
//----------------------------------------------------
function load_initializations(){

    $.ajax({
        type: "POST",
        url: "php/initQuery.php",
        async:   false,
        success:function(data){process_init_data(data); },
        error: function (res)   {alert("Error getting init info from DB!");  }
    });   

}

function process_init_data(data){

    var tR = jQuery.parseJSON(data);

    if(tR === null || tR.length < 1){
        init_data = [];
        return;
    }

    init_data = new Array(tR.length);

    for( var j =0; j < tR.length; j++){
        init_data[j] = new Array(2);
        init_data[j][0] = parseInt(tR[j][0]);
        init_data[j][1] = tR[j][1];
    }

}


