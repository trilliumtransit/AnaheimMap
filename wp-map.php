<?php 
/*
Template Name: Standalone map 
*/
?>
<html>

<head>
<meta name="viewport" content="initial-scale=1, maximum-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">

<!--// this stuff needs to be updated on main site header-->
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/library/js/libs/leaflet-master/leaflet.css" />
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/js/libs/leaflet-master/leaflet.js"></script>

<!--<script src='https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.standalone.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.css' rel='stylesheet' />
///////-->

<link href='http://rideart.org/wp-content/themes/art/library/css/route-icons.css' rel='stylesheet' />
<link href='<?php echo get_template_directory_uri(); ?>/library/css/style.css' rel='stylesheet' />
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/library/fonts/MyFontsWebfontsKit.css"> 

<link href='<?php echo get_template_directory_uri(); ?>/library/css/planner.css' rel='stylesheet' />
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.min.js"></script>
<script src="http://momentjs.com/downloads/moment.min.js"></script>
 <script src="<?php echo get_template_directory_uri(); ?>/library/js/jssor.slider.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/AnaheimMap/jquery.csv-0.71.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/library/js/art.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/AnaheimMap/library/js/layout.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/AnaheimMap/library/js/planner.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/AnaheimMap/library/js/sliderTabs-1.1/jquery.sliderTabs.js"></script>
<link href='<?php echo get_template_directory_uri(); ?>/AnaheimMap/library/js/sliderTabs-1.1/styles/jquery.sliderTabs.css' rel='stylesheet' />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
<script src="<?php echo get_template_directory_uri(); ?>/AnaheimMap/library/js/leaflet.label/leaflet.label.js"></script>
<link href='<?php echo get_template_directory_uri(); ?>/AnaheimMap/library/js/leaflet.label/leaflet.label.css' rel='stylesheet' />
<link href='<?php echo get_template_directory_uri(); ?>/AnaheimMap/library/css/map_layout.css' rel='stylesheet' />

</head>

<body >
<div id="interactive-map-holder-wrap" style="width:100%;height:100%;position: relative;">
 <div id="planner-wrap" >
	<?php get_template_part( 'planner'); ?>  
</div><!-- end #planner-wrapper -->
<img id="draggingDisabled" src="<?php echo get_template_directory_uri(); ?>/library/images/home-map-bg.jpg" style="width: 100%; height: 100%; z-index: 0; position: absolute; top: 0; pointer-events: none;" /> 

<div id="interactive-map-holder" style="width:100%;height:100%;  ">

</div>
 </div>

<?php 
$link =  "//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 
if (strpos($link, 'localhost') !== FALSE) { // check if on mamp/apache localhost
//$link .= "/art";
}
$link = str_replace("map.php","", $link);
?>
<script src="<?php echo get_template_directory_uri(); ?>/AnaheimMap/generate-map-js.php?routes=1696,1697,1698,1699,1700,1701,1702,1703,1704,1705,1706,1707,1708,1709,1710,1711,1712,1713,1714,1716&system_map=true&container_id=interactive-map-holder&analytics=false"></script>


</body>
</html>

		