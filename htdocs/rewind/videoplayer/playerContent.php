<!-- Load the Visualization API and the piechart package. --> 


<script type="text/javascript">
	google.load('visualization', '1.0', {'packages' : [ 'corechart' ]});
	<?php
		$js_array = json_encode($GLOBALS['VIDEO_LIST']);
		//create a javascript array from php array
		echo "var video_list = ". $js_array . ";\n";
	?>
</script>

<div class='content'>
<div class="divTitle">
	<p style="height:10px;font-weight:bold">Depth Video Rewind Interface</p>
	<p id="search_message" style="font-size: 12px;font-weight: bold">
		Select a subject id and a date / time for videos you'd like to see. Clicking the Search button will then retrieve a list of videos. 
		Click on a listed video to select it, then use the player controls to navigate within that video. 
	</p>
</div>

<div class="cover">
<table cols="2" style="width:100%;height:670px;vertical-align:top;">
	<tr style="vertical-align:top;height: 30px">
		<td rowspan="2" style="width:25%;vertical-align:top;font-size: 11px;background-color: #fff;">
			<ul class="menu" id="theSelect">
				<li class="item2" style="width:100%;"><a style="text-align:left" href="">Videos <span>0</span></a>
				</li>
			</ul>	
		</td>
		<td class="searchDiv">
			<table style="width: 100%;height: 30px;">
				<tr style="width: 100%;">
				<form id="newSearchForm">
					<td style="min-width:20px;"><label style="font-size:12px;font-weight:bold;" for="roomNumber">ID:</label><select name="roomNumber" id="roomNumber"><?php echo patientIDSelector();?></select></td>
					<!-- <td style="min-width:90px;"><label style="font-size:12px;font-weight:bold;" for="startDate">Start: </label><input type="text" name="startDate" id="startDate" style="text-align:center;font-size: 11px;"></td> -->
					<td style="min-width:90px;"><label style="font-size:12px;font-weight:bold;" for="endDate">Before: </label><input type="text" name="endDate" id="endDate" style="text-align:center;font-size: 11px;width:100px"></td>
					<td style="min-width:90px;"><label style="font-size:12px;font-weight:bold;" for="onlyFalls">Only Fall Videos: </label><input type="checkbox" name="onlyFalls" id="onlyFalls" style="text-align:center;font-size: 11px; height:10px ;width:10px"></td>
				</form>
					<td style="min-width:40px;"><button id="searchButton" style="width:100%;height:22px; margin: 0;font-size: 12px;font-weight:bold">Search</button></td>
					<!-- <td style="min-width:40px;"><button id="helpButton" style="width:100%;height:22px; margin: 0;font-size: 12px;font-weight:bold" onclick="showHelp();">Help</button></td> -->
					<td style="width:auto"></td>
				</tr>
			</table>
		</td>	
	</tr>
	<tr style="height: 100%">
		<td style="width:auto">
			<table style="width: 100%;height: 100%">
			<tr style="height:600px">
				<td id="videoplayer" style="z-index:1;background:url(./res/images/videoBG.png) no-repeat center center; background-position:center; background-size:256px 256px;">
					<video id="MediaPlayer"  class="video-js vjs-default-skin vjs-big-play-centered" preload="auto" width="100%" height="600px" data-setup="{}">
				        <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
					</video>
				</td>
			</tr>
			<tr style="height:20px">
				<td>
					<table style="height: 26px; width: 100%; padding: 0px; margin: 0px;">
						<tr>
							<td style="width: 100%"><div id="chart_div" style="z-index:999; height:26px; padding-right:0px;"></div></td>
						</tr>
					</table>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td id="downloadLink" colspan="1" class="downloadLink" style="height:15px;text-align:left;vertical-align: top; font: bold 11px Arial;">
			<a style="cursor:pointer" title="download selected video here!">
				<img style="width:15px;height:15px;vertical-align: bottom; " src="./res/images/download.png" alt="ImageName">
				download selected video here!
			</a>
		</td>
		<td colspan="1" class="downloadLink" style="height:10px;font: 11px Arial;vertical-align:bottom;text-align:right">
			<button id="prev-button" class="prev-button" style="height:26px; width:76px;" title="Play Previous Video"> Prev. </button>
			<button id="next-button" class="next-button" style="height:26px; width:76px;" title="Play Next Video"> Next </button>
			<button id="replay-button" class="replay-button" style="height:26px; width:26px;" title="Rewind the last selected point"></button>
		</td>
	</tr>
</table>
</div><!--cover-->
<!-- Help -->
<div id="domHelp" style="display:none;"> 
<?php include('help.html');  ?>
</div> 
</div>
