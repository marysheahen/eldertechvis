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
<div class="cover">
<table cols="2" style="width:100%;height:500px;vertical-align:top;">
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
					<td style="min-width:90px;"><label style="font-size:12px;font-weight:bold;" for="startDate">Start: </label><input type="text" name="startDate" id="startDate" style="text-align:center;font-size: 11px;"></td>
					<td style="min-width:90px;"><label style="font-size:12px;font-weight:bold;" for="endDate">End: </label><input type="text" name="endDate" id="endDate" style="text-align:center;font-size: 11px;width:100px"></td>
					<td style="min-width:90px;"><label style="font-size:12px;font-weight:bold;" for="onlyFalls">Only Fall Videos: </label><input type="checkbox" name="onlyFalls" id="onlyFalls" style="text-align:center;font-size: 11px; height:10px ;width:10px"></td>
				</form>
					<td style="min-width:40px;"><button id="searchButton" style="width:100%;height:22px; margin: 0;font-size: 12px;font-weight:bold">Search</button></td>
					<td style="min-width:40px;"><button id="helpButton" style="width:100%;height:22px; margin: 0;font-size: 12px;font-weight:bold" onclick="showHelp();">Help</button></td>
					<td style="width:auto"></td>
				</tr>
			</table>
	</tr>
	<tr style="height: 100%">
		<td style="width:auto">
			<table style="width: 100%;height: 100%">
			<tr style="height:100%px">
				<td id="videoplayer" style="z-index:1;background:url(./res/images/videoBG.png) no-repeat center center;background-size:256px 256px;"></td>
			</tr>
			<tr style="height:20px">
				<td>
					<table style="height: 26px; width: 100%; padding: 0px; margin: 0px;">
						<tr>
							<td style="width: 100%"><div id="chart_div" style="z-index:999; height:26px; padding-right:0px;"></div></td>
							<td><button id="replay-button" class="replay-button" style="height:26px; width:100%;" title="Rewind the last selected point"></button></td>
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
		<td colspan="1" class="downloadLink" style="height:10px;font: 11px Arial;vertical-align:bottom;text-align:center">
		</td>
	</tr>
</table>
</div><!--cover-->
<!-- Help -->
<div id="domHelp" style="display:none;"> 
<?php include('help.html');  ?>
</div> 
</div>
