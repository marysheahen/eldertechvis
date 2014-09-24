<?php

  $SiteTitle = 'Eldertech Research Visualization Framework';

  require('includes/session.php');
?>
<html>
  <head>
    <title><?php echo $SiteTitle ?></title>
    <link href="/tabs.css" rel="stylesheet" />
  </head>
  <body>
    <div class="tabBox" style="clear:both;">
      <div class="tabArea">
        <a class="tab" href="/index.php">Home</a>
        <a class="tab" href="/health/index.php">Health</a>
        <?php echo '<a class="tab" href="/health/density2.php?resid='.$_SESSION['resid'].'">Density</a>';?>
        <a class="tab" href="/gait/index.html">Gait</a>
        <a class="tab" href="/rewind/index.php">Rewind</a>
		<?php echo '<a class="tab" href="/floorplan/index.php?resid='.$_SESSION['resid'].'">Floorplan</a>';?>
      </div>
      <div class="tabMain">
        <div class="saHome">
          <h1><?php echo $SiteTitle ?></h1>
          <h2>Integrated Web Interface for various sensor data:</h2>
          <ul>
            <li>Motion</li>
            <li>Pulse</li>
            <li>Respiration</li>
            <li>Bed Restlessness</li>
            <li>Bathroom Activity</li>
            <li>Gait</li>
            <li>Depth Video Rewind Player</li>
          </ul>
        </div>
      </div>
    </div>
  </body>
</html>


