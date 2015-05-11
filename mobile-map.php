<?php
/*
Template Name: mobile map
*/

//have the

//if (isset($_GET['routes'])) {$routes = $_GET['routes'];}



?>

<html>

<head>


<?php 
$link =  "$_SERVER[HTTP_HOST]"; 
if (strpos($link, 'localhost') !== FALSE) { // check if on mamp/apache localhost
$link .= "/art";
echo '';
}
$base_link = $link;
$link .= "/wp-content/themes/art/AnaheimMap/";

?>

<script src='https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.css' rel='stylesheet' />
<link href='<?php echo $base_link; ?>/wp-content/themes/art/library/css/route-icons.css' rel='stylesheet' />
<link href='<?php echo $link; ?>map_layout.css' rel='stylesheet' />
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?php echo $link; ?>/jquery.csv-0.71.min.js"></script>
<script src="<?php echo $link; ?>/AnaheimMap/layout.js"></script>

</head>

<body style="background-color:#2068A7">
<div id="interactive-map-holder-wrap" style="width:100%;height:100%;position: relative;">

<div id="mobile-map">

</div>
<div id="interactive-map-holder" style="width:100%;height:100%;background-color:#2068A7;  ">

</div>
 <img id="draggingDisabled" src="images/map-gradient-overlay-copy.png" style="width: 100%; height: 100%; z-index: 999; position: absolute; top: 0; pointer-events: none;" /> 
</div>


				<script src="http://<?php echo $link; ?>generate-map-js.php?routes=1696,1697,1698,1699,1700,1701,1702,1703,1704,1705,1706,1707,1708,1709,1710,1711,1712,1713,1714,1716&system_map=true&container_id=interactive-map-holder"></script>


</body>

</html>



