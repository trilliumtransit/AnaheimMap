<?php 
/*
Template Name: Standalone map 
*/
?>
<html>

<head>

<script src='https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.js'></script>
<script src="//cdn.maptiks.com/maptiks-leaflet.min.js"></script>
<script>maptiks.trackcode='699de837-cc69-42d0-b9a5-d8526e262965';</script>
<link href='https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.css' rel='stylesheet' />
<link href='http://rideart.org/wp-content/themes/art/library/css/route-icons.css' rel='stylesheet' />
<link href='<?php echo get_template_directory_uri(); ?>/library/css/style.css' rel='stylesheet' />
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/library/fonts/MyFontsWebfontsKit.css"> 
<link href='<?php echo get_template_directory_uri(); ?>/AnaheimMap/library/css/map_layout.css' rel='stylesheet' />
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/AnaheimMap/jquery.csv-0.71.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/library/js/art.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/AnaheimMap/library/js/layout.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/AnaheimMap/library/js/planner.js"></script>


</head>

<body style="background-color:#2068A7">
<div id="interactive-map-holder-wrap" style="width:100%;height:100%;position: relative;">
 <div id="planner-wrap" >
	<?php get_template_part( 'planner'); ?>  
</div><!-- end #planner-wrapper -->
<div id="interactive-map-holder" style="width:100%;height:100%;background-color:#2068A7;  ">

</div>
 <img id="draggingDisabled" src="<?php echo get_template_directory_uri(); ?>/AnaheimMap/images/map-gradient-overlay-copy.png" style="width: 100%; height: 100%; z-index: 509; position: absolute; top: 0; pointer-events: none;" /> 
</div>

<?php 
$link =  "//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 
if (strpos($link, 'localhost') !== FALSE) { // check if on mamp/apache localhost
//$link .= "/art";
}
$link = str_replace("map.php","", $link);
?>
<script src="<?php echo get_template_directory_uri(); ?>/AnaheimMap/generate-map-js.php?routes=1696,1697,1698,1699,1700,1701,1702,1703,1704,1705,1706,1707,1708,1709,1710,1711,1712,1713,1714,1716&system_map=true&container_id=interactive-map-holder"></script>


</body>
</html>

		