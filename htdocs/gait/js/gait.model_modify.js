
function modelEnableUpdate(){
    
    if(c_id > 0){

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

        raw_update.disabled = false;
        raw_enable.innerHTML = "Modification Enabled";
        raw_enable.disabled = true;
    }
};

function modelUpdate(){

    var mS = "Are you sure you want to save the model modification? " +
             "All existing model/gait data after this date will be cleared and recomputed!";
    if(confirm(mS)){
        
        $.ajax({
            type: "POST",
            url: "php/updateModelQuery.php",
            data:{mu_key: mu_key, sid: sid_options[c_id], date: modelDataString[0][0], obj: objStringToSave },
            async:   false,
            success:function(data){
                if( data === '1'){
                  alert("Model update successful!"); 
                  updateModelImage();
                }
                else
                  alert("Error (1) updating model! " + data);
            },
            error: function (res){ alert("Error (2) updating model!");  }
        });
       
    }

};

