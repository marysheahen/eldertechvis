<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 4.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Video Rewind Interface</title>
<meta http-equiv="Content-Language" content="en-us" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./res/css/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="./res/css/jquery.datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="./res/css/video-js.css">


<?php 
  if (isset($_GET['wide'])&&$_GET['wide']==0) {
    echo("<link rel='stylesheet' type='text/css' href='./res/css/Main.css'/>");
  }else{
    echo("<link rel='stylesheet' type='text/css' href='./res/css/MainWide.css'/>");
  }
?>
<link rel='stylesheet' type='text/css' href='./res/css/listStyles.css'/>

<link href="/tabs.css" rel="stylesheet" />

<?php echo ('<script type="text/javascript"> var IS_REMOTE_PATH='.$GLOBALS['IS_REMOTE_PATH'].'</script>');?>
<?php echo ('<script type="text/javascript"> var IS_DB_ORIENTED='.$GLOBALS['IS_DB_ORIENTED'].'</script>');?>
<?php echo ('<script type="text/javascript"> var BASE_VIDEO_PATH="'.$GLOBALS['BASE_VIDEO_PATH'].'"</script>');?>

<script src="./res/js/jquery.js"></script>
<script src="./res/js/jquery.datetimepicker.js"></script>
<script src="./res/js/jquery-ui.js"></script>
<script src="./res/js/JavaScriptUtil.js"></script>
<script src="./res/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="res/js/jsapi.js"></script>
<script type="text/javascript" src="res/js/vidPlayer.js"></script>
<script src="res/js/video.js"></script>

</head>
<body>
  <div class='everything'>
    <div class="tabBox" style="clear:both;">
      <div class="tabArea">
        <a class="tab" href="/index.php">Home</a>
        <a class="tab" href="/health/index.php">Health</a>
        <a class="tab" href="/health/density2.php?resid=1001">Density</a>
        <a class="tab" href="/gait/index.html">Gait</a>
        <a class="tab" href="/rewind/index.php">Rewind</a>
      </div>
      <div class="tabMain">
