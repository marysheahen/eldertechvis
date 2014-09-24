var browserIsIE = false;
var browserIsSafari = false;

var chartLastSelectedSecond = 0;
var theRoomNumber = 0;
var MP4Player = true;
var currentPlayingVideoIndex = -1;

jQuery(window).load(function() {
	hideVideoPlayer();
});
$(function() {
	initGlobal();
});

function initGlobal(){
	hideVideoPlayer();
	//Disbable cache for all jQuery AJAX requests
	$.ajaxSetup({ cache: false });
	
	if (!window.location.origin) {
		window.location.origin = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port: '');
	}	
	sourcePath = window.location.origin+'/'+window.location.pathname.split('/')[1]+'/';

	browserIsIE = (/msie|trident/i).test(navigator.userAgent);
	browserIsSafari= !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);

	$("#replay-button").click(function() {
		seekVideoByTime(chartLastSelectedSecond);
	});
	
	$("#next-button").click(function() {
		PlayNextVideo();
	});
	
	$("#prev-button").click(function() {
		PlayPrevVideo();
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
		hideVideoPlayer();
		$('#videoplayer').css('background-image', 'url(./res/images/search-not-found.png)');
		$('#downloadLink').html('');
		$('#chart_div').hide();
	}else{
		//to show the background image
		$('#videoplayer').css('background-image', 'url(./res/images/videoBG.png)');
		hideVideoPlayer();
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
						.replace(".avi",".txt")
						.replace(".mp4",".txt");
		//console.log(theChartURL);
	    
		//Chart---------------------------------
		serviceURL = sourcePath+"videoplayer/file/file_service.php";
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

					downloadFilePath = generateDownloadFileURL(videPath);

					$.ajax({
					    url:downloadFilePath.replace(".avi",".mp4"),//'https://vis2.eldertech.missouri.edu/mcp//100/KinectData/04_01_2014/KDSmall-04_01_2014-19_21_04_637.mp4',
					    type:'HEAD',
					    error: function()
					    {
					        //file not exists
					    	console.log('no mp4');
					    	updateVideoObject(downloadFilePath.replace(".mp4",".avi"),false);
							MP4Player = false;
					    },
					    success: function()
					    {
					        //file exists
					    	console.log('has mp4');
					    	updateVideoObject(downloadFilePath.replace(".avi",".mp4"),true);
							MP4Player = true;
					    }
					});			
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
	
		function updateVideoObject(downloadFilePath,mp4Exists){
			$('#downloadLink').html(
					'<a href="'+downloadFilePath+'" style="cursor:pointer" title="download selected video here!">'+
					'<img style="width:15px;height:15px;vertical-align: bottom; " src="./res/images/download.png" alt="ImageName">'+
					' download selected video here!</a>');

			if(mp4Exists){
				MP4Player = true;
			}else{
				MP4Player = false;
				alert("No .mp4 video found for the selected time, you can download the avi version");
				return;
			}
			
			
			filePath = downloadFilePath;

			if(!MP4Player){//.avi
				$("#videoplayer").html('<div style="height:610px;width:100%">'+
						'<object style="height:100%;width:100%" classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" standby="Loading Microsoft® Windows® Media Player components..." type="application/x-oleobject" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsm p2inf.cab#Version=6,4,7,1112">'+ 
						'<param name="fileName" value="'+filePath+'"> '+
						'<param name="autoStart" value="false"> '+
						'<param name="showControls" value="true"> '+
						'<param name="AllowChangeDisplaySize" value="true">'+ 
						'<param name="ClickToPlay" value="true"> '+
						'<param name="stretchToFit" value="1"> '+						
						'<embed ID="MediaPlayer" style="height:100%;width:100%" type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" src="'+filePath+'" autoStart="false" stretchToFit=1></embed>'+
						'</object>'+
						'</div>');
			}else{//.mp4
				if(browserIsSafari){
					$("#videoplayer").html('<div style="height:610px;width:100%">'+
		 					'<video id="MediaPlayer" data-vid="video" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="100%" height="600px" data-setup="{}">'+
							'<source id="videoSrc" src="'+filePath+'" type="video/mp4" />'+
					        '<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>'+
						'</video>'+
					'</div>');
					myPlayer = document.getElementsByTagName('video')[0];
					myPlayer.load();
					myPlayer.play();

				}else{
					
					var myPlayer = _V_('MediaPlayer');
					// hide the video UI
					$("#MediaPlayer_html5_api").hide();
					// and stop it from playing
					myPlayer.pause();
					// assign the targeted videos to the source nodes
					myPlayer.src([{ type: "video/mp4", src: filePath }]);			
					myPlayer.controls('controls');			
					// load the new sources
					myPlayer.load();
					myPlayer.play();
					$("#MediaPlayer").attr('controls','controls');
					$("#MediaPlayer").show();
					$("#MediaPlayer_html5_api").show();
				}
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
	resultPath = resultPath.replace("///", "/").replace("../res","res").replace("/mnt/mcpweb","/mcp/");
	
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
	var vidIndex = -1;
	currentPlayingVideoIndex = -1;
	var output = [];
	if(sourceArray==null || sourceArray.length==0) {
		updateFilePath();
	}else{
		$.each(sourceArray, function(key, value){  
			vidIndex++;
			bgColorAtt = (value[3]=='1'? 'class="hasFall"':' ');
			output.push("<li id='vidItem_"+(vidIndex)+"' vidIndex='"+(vidIndex)+"' values0='"+value[0]+"' values1='"+value[1]+"'><a href='#'>"+value[1]+" <span "+bgColorAtt+">"+value[2]+"</span></a></li>");
		});
		updateFilePath(sourceArray[0][0],false);
	}
	resultList = '<li class="item2" style="width:100%;"><a style="text-align:left" href="">Videos <span>'+output.length+'</span></a>'
				+'<ul style="height:660px;overflow:auto;">'
				+ output.join('')
				+'</ul>'
				+'</li>';

	$('#theSelect').html("");
	$('#theSelect').html(resultList);

	$(".menu > li > ul li").click(function() {
		//Set selected---------------
		vidindex = $(this).attr('vidIndex');
		PlayVideoAtIndex(vidindex);
    });   
	
}

// Video List -----------------------

function PlayVideoAtIndex(vidIndex){
	if(video_list == undefined || video_list == null  || video_list == undefined || video_list.length == 0 || vidIndex >= video_list.length || vidIndex < 0)
		return;
	else{
		currentPlayingVideoIndex=parseInt(vidIndex);
        $(".menu > li > ul li").removeClass('active');
        $("#vidItem_"+vidIndex).addClass('active');
		vidPath = video_list[vidIndex][0];
		vidDate = video_list[vidIndex][1]; 	
		updateFilePath(vidPath, vidDate, true);
	}
}

function PlayPrevVideo(){
	if(video_list==undefined || video_list==null  || video_list==undefined || video_list.length == 0)
		return;
	else{
		if((currentPlayingVideoIndex+1)>=video_list.length){
			return;
		}else
			PlayVideoAtIndex(currentPlayingVideoIndex+1);
	}
}

function PlayNextVideo(){
	if(video_list==undefined || video_list==null  || video_list==undefined || video_list.length == 0)
		return;
	else{
		if((currentPlayingVideoIndex-1)<0){
			return;
		}else
			PlayVideoAtIndex(currentPlayingVideoIndex-1);
	}
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
	if(typeof google.visualization === 'undefined'){
		console.error('google.visualization is undefined!');
		return;
	}
	var data = google.visualization.arrayToDataTable(chartData);

	// Set chart options
	var options = {
		'chartArea':{left:MP4Player?0:browserIsIE?16:30,top:0,right:0,width:'100%',height:'100%'},
		'backgroundColor': { fill:'transparent' },
		'colors':['#0303F0','#FE2E2E'],
		'dataOpacity':.5,
		'enableInteractivity':true,
		'focusTarget':'datum',
		'curveType': 'function',//none-function
        'vAxis': {minValue:0,maxValue: 1,gridlines:{color: '#333',count:0}},
		'hAxis':{textPosition:'out', textStyle:{color: 'black', fontName: 'arial', fontSize:7}},
		//'tooltip': {isHtml: false,textStyle:{fill:'transparent', color: 'black', fontName: 'arial', fontSize:9}},
		'tooltip' : { trigger: 'none'}
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
			chartLastSelectedSecond = getTimeDiffInSeconds(data.Ad[0][0].Pe,data.Ad[selectedItem.row][0].Pe);
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
        	theEndDate=($('#endDate').val()=='____/__/__ __:__')?'':$('#endDate').val();
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
				try{
					this.setOptions({minDate:$('#startDate').val().substring(0,10)});
				}catch (e) {}
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
			var myPlayer = _V_("MediaPlayer");
			myPlayer.currentTime(seekToTime); 
	        myPlayer.play();
		}else{
			if(browserIsIE){
				MediaPlayer.CurrentPosition=seekToTime;
				MediaPlayer.play();
			}else{
				document.getElementById('MediaPlayer').controls.currentPosition = seekToTime;
				document.getElementById('MediaPlayer').controls.play();				
			}
		}
	}catch(e){console.error(e);}
}


//use this function to initialize page with url parameters
function simulateAjaxRequest(){
	startDateURL = getUrlParameter("startdate");
	endDateURL = getUrlParameter("enddate");
	theRoomNumberURL = getUrlParameter("residentid");
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
	serviceURL = sourcePath+(IS_DB_ORIENTED?"videoplayer/video_search_service_db.php":"videoplayer/video_search_service.php")+'?buster='+new Date().getTime();
	
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

