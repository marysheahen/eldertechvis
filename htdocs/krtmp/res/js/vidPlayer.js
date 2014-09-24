var browserIsIE = false;
var chartLastSelectedSecond = 0;
var theRoomNumber = 0;
var MP4Player = true;

$(function() {
	initGlobal();
});

function initGlobal(){
	sourcePath = window.location.origin+'/'+window.location.pathname.split('/')[1]+'/';

	browserIsIE = (/msie|trident/i).test(navigator.userAgent);
	$('#replay-button').css('width', browserIsIE ? '17px' : '25px');
	$("#replay-button").click(function() {
		seekVideoByTime(chartLastSelectedSecond);
	});
	initChart();
	initDatePicker();
	initSpinner();	
	simulateAjaxRequest();
}
				
function initSpinner(){
    $( "#spinner" ).spinner({
      step: 0.01,
      numberFormat: "n"
    });
 
    $( "#culture" ).change(function() {
      var current = $( "#spinner" ).spinner( "value" );
      Globalize.culture( $(this).val() );
      $( "#spinner" ).spinner( "value", current );
    });
}	
function updateFilePath(newFilePath,videoDateTime,loadFirstVideo){
	//console.log(newFilePath+' >> '+videoDateTime);
	//show corresponding message
	if(newFilePath==null || newFilePath==""){
		$('#videoplayer').html('<div style="width:100%;height:100%;vertical-align:bottom; text-align:center;"><img  src="./res/images/search-not-found.png" alt="Smiley face"/></div>');
		$('#downloadLink').html('');
		$('#chart_div').hide();
	}else{
		//to show the background image
		$('#videoplayer').html('');
		if(loadFirstVideo){
			reloadVideoAndChart(newFilePath,videoDateTime);
		}
	}
}

function reloadVideoAndChart(videPath,videoDateTime){
    showLoading('<b>Loading video & chart data</b><br>Please wait...',loadVideoFunc);

    function loadVideoFunc() { 
		$('#chart_div').hide();
		
		theChartURL = generateChartFileURL(videPath);
		//theChartURL = "ftp:"+videPath;
		theChartURL = theChartURL
						.replace("Small","")
						.replace(".avi",".txt");
		//console.log(theChartURL);
	    
		//Chart---------------------------------
		serviceURL = sourcePath+"videoplayer/file/file_service.php";
		//console.log(theRoomNumber);
		
		$.ajax({
	        type: "POST",
	        url: serviceURL,
	        data:{
	        	file_path: theChartURL,
	        	//roomNumber: $('#roomNumber option:selected').val(),
	        	roomNumber:theRoomNumber,
	        	videoDateTime: videoDateTime
	        },
	        beforeSend : function (){
	        },		        
	        success: function(data) {
	        	//console.log("response: "+data);
	        	if(data){
		        	resultData = JSON.parse(data);
	        	}
	        	if(resultData==null){
		        	console.log("File not found or exception while reading: "+theChartURL);
		        }else{
		    		$('#chart_div').show();
		        	try{updateChartData(resultData);}catch (e) {console.error(e);};
		        	try{updateVideoObject(videPath);}catch (e) {console.error(e);};
		        }
			    $.unblockUI({
			    	onUnblock:function(){
			    	}
			    });
	        },
	        error:function(){
	        	$.unblockUI;
	        }
	     });
		
		//Video---------------------------------
		function updateVideoObject(filePath){
			downloadFilePath = generateDownloadFileURL(filePath);
			$('#downloadLink').html(
					'<a href="'+downloadFilePath+'" style="cursor:pointer" title="download selected video here!">'+
					'<img style="width:15px;height:15px;vertical-align: bottom; " src="./res/images/download.png" alt="ImageName">'+
					' download selected video here!</a>');
			filePath = downloadFilePath;
			
			if(!MP4Player){//.avi
				$("#videoplayer").html('<div style="height:610px;width:100%">'+
//						'<EMBED ID="MediaPlayer" style="height:100%;width:100%" TYPE="application/x-ms-wmp" SRC="'+filePath+'"'+
//						'ShowControls="1" ShowStatusBar="0" ShowDisplay="0" autostart="0" stretchToFit="1">'+
//						'</EMBED>'+
						
						'<object classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" standby="Loading Microsoft® Windows® Media Player components..." type="application/x-oleobject" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsm p2inf.cab#Version=6,4,7,1112">'+ 
						'<param name="fileName" value="'+filePath+'"> '+
						'<param name="autoStart" value="false"> '+
						'<param name="showControls" value="true"> '+
						'<param name="AllowChangeDisplaySize" value="true">'+ 
						'<param name="ClickToPlay" value="true"> '+
						'<embed  style="height:100%;width:100%" type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" src="'+filePath+'" autoStart="false" stretchToFit="1"></embed>'+
						'</object>'+
						
						'</div>');				
			}else{//.mp4
				$("#videoplayer").html('<div style="height:100%;width:100%">'+
						'<video id="MediaPlayer" class="video-js vjs-default-skin" controls preload="auto" width="100%" height="100%"'+
					      		'data-setup="{}">'+
				      		'<source src="'+filePath+'" type="video/mp4" />'+
					        '<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>'+
					      '</video>'+
						'</div>');
			}
		}    		
    }
}
function generateChartFileURL(videPath){
	theChartURL = videPath;
	if(!IS_REMOTE_PATH)
		theChartURL = '../'+theChartURL;
	
	theChartURL = theChartURL.replace("///", "/").replace("../res","res");
	return theChartURL;
}
function generateDownloadFileURL(sourcePath){
	resultPath = sourcePath;
	if(!IS_REMOTE_PATH)
		resultPath = '../'+resultPath;
	resultPath = resultPath.replace("///", "/").replace("../res","res").replace("/mnt/mcpweb/","/mcp/");
	
	if(MP4Player){
		resultPath = resultPath.replace(".avi",".mp4");
	}

	return resultPath;
}

function updateChartData(allText) {
	chartLastSelectedSecond = 0;
    //this is required for the google api
    chartData = allText;
	chartData.unshift(['col1','col2','col3']);
	drawChart();
}	

function hideVideoPlayer(){
	$("#MediaPlayer").hide();
}


function updateListByArray(sourceArray){
	var output = [];
	if(sourceArray==null || sourceArray.length==0) {
		updateFilePath();
	}else{
		$.each(sourceArray, function(key, value){  
			bgColorAtt = (value[3]=='1'? 'class="hasFall"':' ');
			output.push("<li values0='"+value[0]+"' values1='"+value[1]+"'><a href='#'>"+value[1]+" <span "+bgColorAtt+">"+value[2]+"</span></a></li>");
		});
		updateFilePath(sourceArray[0][0],false);
	}
	resultList = '<li class="item2" style="width:100%;"><a style="text-align:left" href="">Videos <span>'+output.length+'</span></a>'
				+'<ul style="height:660;overflow:auto;">'
				+ output.join('')
				+'</ul>'
				+'</li>';

	$('#theSelect').html("");
	$('#theSelect').html(resultList);

	$(".menu > li > ul li").click(function() {
		//Set selected---------------
        $(".menu > li > ul li").removeClass('active');
        $(this).addClass('active');
        //do action------------------
		values0 = $(this).attr('values0');
		values1 = $(this).attr('values1');
		updateFilePath(values0, values1, true);
    });   
	
}

// chart------------------------
function initChart(){	
	// Set a callback to run when the Google Visualization API is loaded.
	google.setOnLoadCallback(drawChart);	
}

// Callback that creates and populates a data table,
// instantiates the pie chart, passes in the data and draws it.
var chartData = [['col1', 'col2', 'col3'],["",0,0]];
function drawChart() {	
	// Create and populate the data table.
	var data = google.visualization.arrayToDataTable(chartData);

	// Set chart options
	var options = {
		//'animation.duration':5,
		//'axisTitlesPosition':'in',
		//'backgroundColor':{stroke:'white',strokeWidth:100,strokeColor:'yellow',fill:'gray'},
		'chartArea':{left:browserIsIE?16:30,top:0,right:0,width:'100%',height:'100%'},
		'backgroundColor': { fill:'transparent' },
		//'backgroundColor': { fill:'gray' },
		'colors':['#0303F0','#FE2E2E'],
		'dataOpacity':.5,
		'enableInteractivity':true,
		//'explorer':'{}',
		//'explorer': { 'actions': ['dragToZoom', 'rightClickToReset'] },
		'focusTarget':'datum',
		//'hAxis.gridlines':{'color': '#333', 'count': 2},
		
		'title' : '',
		'curveType': 'function',//none-function
        'vAxis': {minValue:0,maxValue: 1,gridlines:{color: '#333',count:0}},
		'hAxis':{textPosition:'out', textStyle:{color: 'black', fontName: 'arial', fontSize:7}},
		'tooltip': {isHtml: false,textStyle:{fill:'transparent', color: 'black', fontName: 'arial', fontSize:9}},
		//'tooltip' : { trigger: 'none'}
	};

	// Instantiate and draw our chart, passing in some options.
	var chart = new google.visualization.SteppedAreaChart(document.getElementById('chart_div'));
	
	// Listen for the 'select' event, and call my function selectHandler() when
	// the user selects something on the chart.
	google.visualization.events.addListener(chart, 'select', selectHandler);
	// The select handler. Call the chart's getSelection() method
	function selectHandler() {
		var selectedItem = chart.getSelection()[0];
		if (selectedItem) {
			//seekVideoByRow(selectedItem.row);
			chartLastSelectedSecond = getTimeDiffInSeconds(data.Gd[0][0].Ze,data.Gd[selectedItem.row][0].Ze);
			seekVideoByTime(chartLastSelectedSecond);
		}
	}
	function getTimeDiffInSeconds(timeStringFirst,timeStringSecond){
		s1 = convertTimeToMilliSeconds(timeStringFirst);
		s2 = convertTimeToMilliSeconds(timeStringSecond);
		timeDiff = s2-s1;
		//it is in the midnight
		if(timeDiff<0) timeDiff += convertTimeToMilliSeconds("24:00:00:00");
		
		return timeDiff;
	}
	function convertTimeToMilliSeconds(timeString){
		resultInSeconds = 0;
		$.each(timeString.split(":").slice(0,-1), function(index, item) {
			resultInSeconds += Math.pow(60,2-index)*item;
		});
		return resultInSeconds;
	}
	chart.draw(data, options);
}
// chart <<<<<<<<<<<<<<
function initSearchButton(){
	$("#searchButton").click(function() {
			theStartDate=($('#startDate').val()=='____/__/__ __:__')?'':$('#startDate').val();
        	theEndDate=($('#endDate').val()=='____/__/__ __:__')?'_':$('#endDate').val();
        	theRoomNumber=$('#roomNumber option:selected').val();
        	theOnlyFalls=$("#onlyFalls").is(':checked');
        	submitQuery(theRoomNumber,theStartDate,theEndDate,theOnlyFalls);
	});
}

function initDatePicker(){
	initSearchButton();
	//DatePicker
	$('#startDate').datetimepicker({
		mask:'9999/19/39 99:99',
		format:'Y/m/d H:i',
		step:5,
		onShow:function( ct ){
			hideVideoPlayer();
			if($('#endDate').val()!='____/__/__ __:__'){
				this.setOptions({maxDate:$('#endDate').val().substring(0,10)});
			}else{
				this.setOptions({maxDate:false});
			}				
		},
		timepicker:true
	});
	$('#endDate').datetimepicker({
		mask:'9999/19/39 99:99',
		format:'Y/m/d H:i',
		step:5,
		onShow:function( ct ){
			hideVideoPlayer();
			if($('#startDate').val()!='____/__/__ __:__'){
				this.setOptions({minDate:$('#startDate').val().substring(0,10)});
			}else{
				this.setOptions({minDate:false});
			}				
		},
		timepicker:true
	});	
}	
function showLoading(theMessage,onBlockCallback){
    $.blockUI({ 
    	onBlock:onBlockCallback,
    	message:theMessage,
        centerY: false, 
    	css: { 
	        border: 'none', 
	        padding: '15px', 
	        top: '25%',
	        backgroundColor: '#000', 
	        '-webkit-border-radius': '10px', 
	        '-moz-border-radius': '10px', 
	        'font-size': '24px', 
	        opacity: .7, 
	        color: 'white'
    	}
    }); 
    $(document).keyup(function(e) {
    	if (e.keyCode == 27) {$.unblockUI();}   // esc
    });
    setTimeout("", 10);
    
}
function showHelp(){
	basePath = window.location.pathname.replace("index.php","");
	window.open('videoplayer/help.html','_blank');
}
function seekVideoByRow(rowNum){
	if(rowNum==null || rowNum<0){
		rowNum=0;
	}
	var maxRowNum = (chartData==null)? 1 : chartData.length; 
	var maxVideoLength = 900;//seconds = 15Min
	$("#MediaPlayer").show();
	try{
		if(browserIsIE){
			MediaPlayer.CurrentPosition=maxVideoLength*(rowNum-3)/maxRowNum;
			MediaPlayer.play();
		}else{
			document.getElementById('MediaPlayer').controls.currentPosition = maxVideoLength*(rowNum-3)/maxRowNum;
			document.getElementById('MediaPlayer').controls.play();				
		}
	}catch(e){}
}
function seekVideoByTime(seconds){
	if(seconds==null || seconds<0){
		seconds=0;
	}
	$("#MediaPlayer").show();
	try{
		var seekToTime = seconds;
	    if( seekToTime < 0 || seekToTime > document.getElementById('MediaPlayer').duration ){
	        return;
	    }
		if(MP4Player){//MP4
		    document.getElementById('MediaPlayer').play();
		    document.getElementById('MediaPlayer').currentTime = seekToTime;
		}else{
			if(browserIsIE){
				MediaPlayer.CurrentPosition=seekToTime;
				MediaPlayer.play();
			}else{
				document.getElementById('MediaPlayer').controls.currentPosition = seekToTime;
				document.getElementById('MediaPlayer').controls.play();				
			}
		}
	}catch(e){}
}


//use this function to initialize page with url parameters
function simulateAjaxRequest(){
	startDateURL = getUrlParameter("startdate");
	endDateURL = getUrlParameter("enddate");
	theRoomNumberURL = getUrlParameter("roomnumber");
	onlyFallsURL = getUrlParameter("onlyfalls");
	//if all of them are null, do nothing
	if((typeof startDateURL === 'undefined')
		&&(typeof endDateURL === 'undefined')
		&&(typeof theRoomNumberURL === 'undefined')
		&&(typeof onlyFallsURL === 'undefined'))
	{return;}

	theRoomNumber=theRoomNumberURL;
	startDate=startDateURL;
	endDate=endDateURL;
	onlyFalls=onlyFallsURL;
	submitQuery(theRoomNumber,startDate,endDate,onlyFalls);
	//submitQuery('100','','',true);
}

function submitQuery(theRoomNumber,theStartDate,theEndDate,theOnlyFalls){
	if(typeof(theRoomNumber)==='undefined' && 
			typeof(theStartDate)==='undefined' && 
			typeof(theEndDate)==='undefined'){
		return;
	}
	serviceURL = sourcePath+(IS_DB_ORIENTED?"videoplayer/video_search_service_db.php":"videoplayer/video_search_service.php");
	$.ajax
    ({
    type: "POST",
    url:serviceURL ,
    data:{
    	startDate:(theStartDate=='____/__/__ __:__' || typeof(theStartDate)==='undefined' || theStartDate===null)?'':theStartDate,
    	endDate:(theEndDate=='____/__/__ __:__' || typeof(theEndDate)==='undefined' || theEndDate===null)?'':theEndDate,
    	roomNumber:theRoomNumber,
    	onlyFalls:(typeof(theOnlyFalls)==='undefined')?false:theOnlyFalls
    },
    beforeSend : function (){
        showLoading('<b>Searching videos.</b><br>Please wait...');
    },
    success: function(data){
    	//console.log(data);
	    $.unblockUI({
	    	onUnblock:function(){
	    		//console.log(data);
	            video_list = JSON.parse(data);
	    		updateListByArray(video_list);		    		
	    	}
	    }); 
	},
	error: function (request, status, error) {
	    $.unblockUI; 
		console.log(request);
		console.log(status);
		console.log(error);
		}
    });
}

function getUrlParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return sParameterName[1];
        }
    }
}
