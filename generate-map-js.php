<?php
header( 'application/javascript' );
if(function_exists('imagepng')){
  echo  "//imagepng() -Exists-";
}else{
  echo "//imagepng() ==== DOES NOT ==== Exist";
}

if (isset($_GET['system_map'])) {$system_map = $_GET['system_map'];}
if (isset($_GET['is_mobile'])) {$system_map = $_GET['is_mobile'];}
if (isset($_GET['routes'])) {$routes = $_GET['routes'];}
if (isset($_GET['container_id'])) {$container_id = $_GET['container_id'];}
 

//

$map_files_base =  "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 
$naked_url_base = "$_SERVER[HTTP_HOST]"; 
$dragable_icons = "false";
if (strpos($map_files_base, 'localhost') !== FALSE) { // check if on mamp/apache localhost
//$dragable_icons = "true";
$naked_url_base .= "/art/";
}
$map_files_base_split = explode("generate-map-js.php", $map_files_base);
?>

// initialize global variables
var stops_layer_group = L.featureGroup();
var stops = Array();
var StopIcons = Array();
var route_colors = Array();
var stop_markers = Array();
var routes = Array();
var route_feature_group = new L.FeatureGroup();
var routes_active = Array();
var route_styles = Array();
var route_layers = [];
var route_shadows = Array();
var topPane;
var topLayer;
var zoom_icon_scale = Array();
var landmarks = Array();
var landmark_icons = Array();
var landmark_categories = Array();
var tile_layer = new Array();
var itineraryGroup = L.featureGroup();
var planner_itineraries_shown = new Array();

var landmark_markers = Array();
var major_landmark_markers_group = L.featureGroup();
var minor_landmark_markers_group = L.featureGroup();

// define which routes
var route_ids_array = [<?php echo $routes ?>];
var system_map = <?php echo $system_map ?>;
// define other variables
var map_files_base = '<?php echo $map_files_base_split[0] ?>';
var api_base_url = 'http://archive.oregon-gtfs.com/gtfs-api/';
var base_map_tiles = 'trilliumtransit.5434d913';
var route_alignments_tiles = 'trilliumtransit.ca9f8a4a';
var road_label_tiles = 'trilliumtransit.acea92f4';

var accessToken = 'pk.eyJ1IjoidHJpbGxpdW10cmFuc2l0IiwiYSI6ImVUQ2x0blUifQ.2-Z9TGHmyjRzy5GC1J9BTw';

var default_icon_color = '575757';

var ZoomLevelThreshhold = 15;
var minor_landmarks_zoom_threshhold = 16;

// define the StopIcon
var StopIcon = L.Icon.extend({
    options: {
        iconSize: [22, 22],
        iconAnchor: [6, 6],
        popupAnchor: [0, 0]
    }
});

zoom_icon_scale[12] = .25;
zoom_icon_scale[13] = .3;
zoom_icon_scale[14] = .35;
zoom_icon_scale[15] = .6;
zoom_icon_scale[16] = 1;
zoom_icon_scale[17] = 1.2;
zoom_icon_scale[18] = 1.2;
zoom_icon_scale[19] = 1.2;

var unhighlighted_weight = 5;
var highlighted_weight = 10;


if (route_ids_array.length == 1) {route_ids_list = route_ids_array[0];}
else {var route_ids_list = route_ids_array.join();}

var southWest = L.latLng(33.765528, -118.042018),
    northEast = L.latLng(33.863041, -117.803086),
    bounds = L.latLngBounds(southWest, northEast);

// mapbox token, basemap
//L.mapbox.accessToken = accessToken;
var map = L.map('<?php echo $container_id; ?>', 'trilliumtransit.5434d913', { zoomControl: false, zoomAnimation: false, maxBounds: bounds, minZoom: 13 });

// makes a map sandwich
//var topPane = L.DomUtil.create('div', 'leaflet-top-pane', map.getPanes().mapPane);
tile_layer[0] = new L.tileLayer('http://{s}.tiles.mapbox.com/v4/' + base_map_tiles + '/{z}/{x}/{y}.png?access_token=' + accessToken, {detectRetina: true,'zIndex': 10});
tile_layer[1] = new L.tileLayer('http://{s}.tiles.mapbox.com/v4/' + route_alignments_tiles + '/{z}/{x}/{y}.png?access_token=' + accessToken, {detectRetina: true,'zIndex': 500});
tile_layer[2] = new L.tileLayer('http://{s}.tiles.mapbox.com/v4/' + road_label_tiles + '/{z}/{x}/{y}.png?access_token=' + accessToken,
		{detectRetina: true,'clickable': 'false', 'zIndex': 1000, pane: 'overlayPane'}).addTo(map);
//topPane.appendChild(tile_layer[2].getContainer())


var imageUrl = 'http://<?php echo $naked_url_base; ?>wp-content/themes/art/library/images/map_blue_coverfade.png',
    imageBounds = [[33.63405913759068, -117.62203216552736], [33.97126744667272, -118.22628021240236]];

var overlay = L.imageOverlay(imageUrl, imageBounds).addTo(map);
//overlay.setOpacity(.5);
//alert(topPane);
//topPane.appendChild(tile_layer[1].getContainer());

// map controls
map.scrollWheelZoom.disable();
if (system_map) {
			 map.fitBounds([
				[33.76659033487751, -118.00518035888673],
				[33.85288250307444, -117.86304473876955]
			]);
        }
        
//new L.Control.Zoom({ position: 'topright' }).addTo(map);

// FUNCTIONS

map.on('click', function(e) {
   console.log("Lat, Lon : " + e.latlng.lat + ", " + e.latlng.lng)
});

// load data
function load_data(url, dataType) {
			dataType = typeof dataType !== 'undefined' ? dataType : "json";

			var returned_data = null;
			$.ajax({
				'async': false,
				'global': false,
				'url': url,
				'dataType': dataType,
				'success': function (data) {
					returned_data = data;
				}
			});
			return returned_data;
		}

function load_data_async(url, dataType, baseUrl, successResponse) {
    dataType = typeof dataType !== 'undefined' ? dataType : null;
    baseUrl = typeof baseUrl !== 'undefined' ? baseUrl : null;
    dataType = dataType !== null ? dataType : "json";
    baseUrl = baseUrl !== null ? baseUrl : map_app_base;
    var returned_data = null;
    successResponse = successResponse !== null ? successResponse : function(data){
        returned_data = data;
    };
    
    $.ajax({
        'global': false,
        'url': baseUrl + url,
        'dataType': dataType,
        'success': successResponse
    });
    return returned_data;
}

//utilities
function isInArray(value, array) {
  return array.indexOf(value) > -1;
}

function encapsulate_in_array(variable) {
	if (Array.isArray(variable)) {return variable;}
	else {
		var new_array = Array();
		new_array.push(variable);
		return new_array;
		}
	}


// Load an object with the routes
function load_routes() {
	var load_data_url = generate_proxy_url(api_base_url+'routes/by-feed/anaheim-ca-us');

    routes = load_data(load_data_url);
}

function get_index(value, array) {
	var index = array.indexOf(parseInt(value));
	return index;
}

function remove_from_array(value, array) {
    var index = get_index(value, array);
    if (index > -1) {
        array.splice(index, 1);
    }
}

function get_icon_index_for_icon(icon_id) {
var result;
for(var i = 0; i < landmark_icons.length; i++) {
    if( landmark_icons[i].id === icon_id ) {
        result = i;
        break;
    }
}
return result;
}

// old junk, but i am keeping this note around for now -- alternative way of getting an array with unique values
// from http://stackoverflow.com/questions/1960473/unique-values-in-an-array

// lookup functions
function get_routes_array_index_from_id(id) {
    var index = -1;
    for (var i = 0, len = routes.length; i < len; i++) {
        if (routes[i].route_id == id) {
            index = i;
            break;
        }
    }

    return index;
}

function get_stops_array_index_from_id(id) {
    var index = -1;
    for (var i = 0, len = stops.length; i < len; i++) {
        if (stops[i].stop_id == id) {
            index = i;
            break;
        }
    }

    return index;
}

function get_routes_for_stop_id(stop_id) {
	
	var stops_array_index = get_stops_array_index_from_id(stop_id);
	var specific_routes = stops[stops_array_index].routes;
	var routes_array_to_sort = Array();
	var route_ids_array = Array();
	
    for (var i = 0, len = specific_routes.length; i < len; i++) {
       routes_array_to_sort.push(Array(specific_routes[i].route_id, get_route_info_for_id(specific_routes[i].route_id).route_short_name));
    }

     routes_array_to_sort.sort(function(a,b) {
        return a[0]-b[0]
    });

	for (var i = 0, len = routes_array_to_sort.length; i < len; i++) {
       route_ids_array.push(routes_array_to_sort[i][0]);
    }
	
    return route_ids_array;
}

function get_route_color_for_id(route_id_lookup) {
var result = get_route_info_for_id(route_id_lookup).route_color;
return result;
}

function get_route_info_for_id(route_id_lookup) {
	var result;
	for(var route_i = 0; route_i < routes.length; route_i++) {
		if( routes[route_i].route_id === route_id_lookup ) {
			result = routes[route_i];
			break;
		}
	}
	return result;
}


// add map & remove map content
function add_route_alignment(ids) {

	    for (var i = 0, len = ids.length; i < len; i++) {
	    	var id = ids[i];

	    if (typeof route_layers[id] == 'undefined' || route_layers[id] == null) {
	    
			var index = get_routes_array_index_from_id(id);

			var geojson = routes[index].simple_00004_geojson || routes[index].geojson;
			// var geojson = routes[index].simple_u_geojson;
			
					route_styles[id] = [];
					
					
					
					route_styles[id][0] = {
						"color": '#' + routes[index].route_color,
						"weight": unhighlighted_weight,
						"opacity": 1,
						"clickable": true
					};
					
					
					
					route_styles[id][1] = {
						"color": '#' + routes[index].route_color,
						"weight": highlighted_weight,
						"opacity": 1,
						"clickable": true
					};
					

					route_layers[id] = L.geoJson(geojson, {
						style: route_styles[id][0]
					});
					
					
					



	    }

	    if (routes_active.indexOf(parseInt(id)) == -1) {
	        routes_active.push(parseInt(id));
	    }
	    

	}


// if(system_map) update_route_alignment_shadow(ids);
// activate_route_alignment(ids);

update_route_alignment_shadow(ids);
activate_route_alignment(ids);

	
}

function activate_route_alignment(ids) {
	ids = encapsulate_in_array(ids);
	for (var i = 0, len = ids.length; i < len; i++) {
	var id = ids[i];
	console.log('route_layers[id].addTo(map)');
	console.log('activate_route_alignment');
	console.log(route_layers[id]);
	console.log('route_layers['+id+'].addTo(map)');
	route_layers[id].addTo(map);
	// console.log('route_layers['+id+'].bringToFront();');
	// route_layers[id].bringToFront();
}
}

function update_route_alignment_shadow(ids) {
	
	route_shadows.forEach(function clearShadow(shadow) {
	 	map.removeLayer(shadow);
	});
		
	


	 for (var i = 0, len = routes_active.length; i < len; i++) {
			var id = routes_active[i];

		if (typeof route_shadows[id] == 'undefined' || route_shadows[id] == null) {
	    
			var index = get_routes_array_index_from_id(id);

			var geojson = routes[index].simple_00004_geojson || routes[index].geojson;
			// var geojson = routes[index].simple_u_geojson;

			route_shadows[id] = L.geoJson(geojson, {
				style: {
				"color": '#fff',
				"weight": highlighted_weight+4,
				"opacity": 1,
				
				// "dashArray": [10,10],
				// "clickable": true
			}
			});


	    }
	    	route_shadows[id].addTo(map);

	}
}

function stop_icons() {
		
		
		
    for (var i = 0; i < routes.length; i++) {
    	if (!isInArray(routes[i].route_color,route_colors)) {
    		route_colors.push(routes[i].route_color);
		    }
    }

	//StopIcons[default_icon_color] = new StopIcon({iconUrl:map_files_base+"create_image.php?r=13&bw=3&&bc=ffffff&fg="+default_icon_color});
	StopIcons['-1'] = new StopIcon({iconUrl:"http://<?php echo $naked_url_base; ?>/wp-content/themes/art/library/images/route-icons-individual/xsml-multi.png"});	
	for (var i = 0; i < 22; i++) {
			var route_info = get_route_info_for_id(route_ids_array[i]);
			StopIcons[""+i] = new StopIcon({iconUrl:"http://<?php echo $naked_url_base; ?>/wp-content/themes/art/library/images/route-icons-individual/xsml-"+i+".png"});
	}
		
}



function generate_proxy_url(url) {
    return url;
	} 


/*pushing items into array each by each and then add markers*/
function load_stop_markers() {

    // if the map has the stops_layer_group, get rid of it
    if (map.hasLayer(stops_layer_group)) {
        map.removeLayer(stops_layer_group);
    }

    // clear out the current stops array
    // stops_layer_group = L.layerGroup();

		
	var load_data_url = generate_proxy_url(api_base_url+'stops/by-feed/anaheim-ca-us/route-id/'+route_ids_list);

    //  async approach
    load_data_async(load_data_url, null,'', function(data){
    
	
	stop_icons();
	
        stops = data;
        if (stops !== null) {

            for (var i = 0; i < stops.length; i++) {

					
					if (stops[i].routes.length > 1) {
						stops[i].color ='575757';
						stops[i].route_short_name = '-1';
					}
					else {
						//console.log(stops[i].routes[0].route_id);
						stops[i].color = get_route_color_for_id(stops[i].routes[0].route_id);
						
						var route_info = get_route_info_for_id(stops[i].routes[0].route_id);
						stops[i].route_short_name = route_info.route_short_name;
					}
                    
                var LamMarker = new L.marker([stops[i].geojson.coordinates[1], stops[i].geojson.coordinates[0]], {
                   // icon: StopIcons[stops[i].color]
                   draggable: <?php echo $dragable_icons; ?>,
                   title: stops[i].stop_name,
                   zIndexOffset: 200,
                   icon: StopIcons[stops[i].route_short_name]
                }).bindPopup('', {maxWidth: 400});
                
                
                LamMarker.stop_id = stops[i].stop_id;
                LamMarker.marker_id = i;
                LamMarker.stop_name = stops[i].stop_name;
                LamMarker.stop_code = stops[i].stop_code;

                LamMarker.on('popupopen', update_stop_info);
                LamMarker.on('popupclose', close_popup_update_map);
				

                stop_markers.push(LamMarker);
                stops_layer_group.addLayer(stop_markers[i]);
            }

        }
        map.addLayer(stops_layer_group);
        
        if (!system_map) {
			 
	    	map.fitBounds(stops_layer_group.getBounds(), {animate: false});
	    	// add zoom stuff here.
	          
	        }
        
    });
}

function remove_route_alignment(ids) {
	ids = encapsulate_in_array(ids);

   for (var i = 0, len = ids.length; i < len; i++) {
	   	var id = ids[i];


    map.removeLayer(route_layers[id]);
    remove_from_array(id, routes_active);

}
}


function create_landmark_marker(i,width,height,landmark_id,icon_index,landmark_lat,landmark_lon,filename,category_name,landmark_name) {

	if (!isInArray(category_name,landmark_categories)) {
		landmark_categories.push(category_name);
	}
 
	var zoom_level_icon = landmark_icon(width,height,icon_index,filename);

	landmark_markers[landmark_id] = L.marker([landmark_lat, landmark_lon], {
	draggable: <?php echo $dragable_icons; ?>,
	icon: zoom_level_icon,
	title: landmark_name,
	zIndexOffset: 100
	}).bindPopup(landmark_name, {maxWidth: 400});
		

landmark_markers[landmark_id].landmark_id = landmark_id;
landmark_markers[landmark_id].landmark_name = landmark_name;
landmark_markers[landmark_id].category_name = category_name;
		
}

// LamMarker.on('popupopen', update_stop_info);
// LamMarker.on('popupclose', close_popup_update_map);
// 
// stop_markers.push(LamMarker);
// stops_layer_group.addLayer(stop_markers[i]);
// stops_layer_group.addLayer(stop_markers[i]);


	// this is to find the nearest stop
	// http://archive.oregon-gtfs.com/gtfs-api/stops/by-feed/anaheim-ca-us/nearest-to-lat-lon/33.803533/-117.913191

	// My thoughts
	// We need a way to limit the number of returned stops
	// Thoughts about how to make loading route details more efficient? -- right now the Javascript goes and loads this elsewhere, and has little choice but to load all the routes
	
// this is to load the stops -- borrowed from the load_stops function -- pruning now

function find_nearest_stop (lat,lon) {
	console.log('find_nearest_stop has run');

	var load_data_url = generate_proxy_url(api_base_url+'stops/by-feed/anaheim-ca-us/nearest-to-lat-lon/'+lat+'/'+lon);

    //  async approach
    
    var nearest_stops = load_data(load_data_url, 'json');

	if (nearest_stops !== null) {
		for (var i = 0; i < 1; i++) {
			// come back to this to consider how to show multiple routes
			if (nearest_stops[i].routes.length > 1) {
				nearest_stops[i].color ='575757';
				nearest_stops[i].route_short_name = '';
				var route_info = '';
			}
			else {
				nearest_stops[i].color = get_route_color_for_id(stops[i].routes[0].route_id);
				
				var route_info = get_route_info_for_id(stops[i].routes[0].route_id);
				nearest_stops[i].route_short_name = route_info.route_short_name;
				}			
			}
			var stop_name = nearest_stops[i].stop_name;
			var stop_code = nearest_stops[i].stop_code;
			var route_color = nearest_stops[i].color;
		}
    
//     load_data_async(load_data_url, null,'', function(data){
// 	console.log('load_data_aysnc has run for stops.../nearest-to-lat-lon/');
// 	stops = data;
// 	console.log(stops);
// 	if (stops !== null) {
// 		for (var i = 0; i < 1; i++) {
// 			if (stops[i].routes.length > 1) {
// 				stops[i].color ='575757';
// 				stops[i].route_short_name = '-1';
// 				var route_info = null;
// 			}
// 			else {
// 				stops[i].color = get_route_color_for_id(stops[i].routes[0].route_id);
// 				
// 				var route_info = get_route_info_for_id(stops[i].routes[0].route_id);
// 				stops[i].route_short_name = route_info.route_short_name;
// 				}			
// 			}
// 			var stop_name = stops[i].stop_name;
// 			var stop_code = stops[i].stop_code;
// 			var route_color = stops[i].color;
// 		}
// 	    });
	    
	    
	// var stop_info_to_return = new Array(stop_name,stop_code,route_color,route_info);
	return new Array(stop_name,stop_code,route_color,route_info);
}

// below is what is used to show the route bubbles in the stop information popup
// function update_stop_info(e) {
// 
// var popup_content = '<h3 class="stop_name">'+e.target.stop_name+'</h3>';
// 
// if (e.target.stop_code != '') {
// 	popup_content = popup_content+ '<p>text2go code: '+e.target.stop_code+'</p><p>Click a route to see the stop list:</p>';
// }
// 
// var route_ids_array = get_routes_for_stop_id(e.target.stop_id);
// 
// for (var i = 0, len = route_ids_array.length; i < len; i++) {
// 
// 	var route_info = get_route_info_for_id(route_ids_array[i]);
// 	popup_content = popup_content + '<a href="'+route_info.route_url+'"><i id="icon-xsml-'+route_info.route_short_name+'" class="linked-div" rel="/route-and-schedules/" style="float: left;" ></i></a>'; // need to add link in the rel.
// 
//}


	


function update_landmark_info(e) {

	console.log('update_landmark_info has fired.');
	console.log(e);

// > landmark_markers[1].getLatLng();
// < Object
// lat: 33.799298
// lng: -117.883872
// __proto__: Object

	// var nearest_stop = find_nearest_stop(e.target.getLatLng().lat,e.target.getLatLng().lng);
	
	var popup_content = '<h3 class="stop_name">'+e.target.landmark_name+'</h3>';
	
	// <p>Nearest ART stop: ' + nearest_stop[0] + '<br/>Served by: '+nearest_stop[3].route_short_name+'</p>';



// final action - set popup content
e.target.setPopupContent(popup_content);

//ga('send', 'event', 'map', 'click landmark', e.target.landmark_name);
	
}


function landmark_icon(width,height,icon_index,filename) {
	var current_zoom = map.getZoom();
	if(typeof current_zoom == 'undefined'){current_zoom = 15;}
	
	if (typeof landmark_icons[icon_index].icons == 'undefined') {
		landmark_icons[icon_index].icons = [];
		}
	
	if (typeof landmark_icons[icon_index].icons[current_zoom] == 'undefined') {
	
	landmark_icons[icon_index].icons[current_zoom] = {};
	

	var scaled_width = zoom_icon_scale[current_zoom] * width;
	var scaled_height = zoom_icon_scale[current_zoom] * height;

	landmark_icons[icon_index].icons[current_zoom] = new L.Icon({ 
		iconUrl: map_files_base+'landmark_icons/'+filename,
		iconSize: [scaled_width, scaled_height],
		iconAnchor: [scaled_width/2, scaled_height/2]
		});
		
	}
	
	return landmark_icons[icon_index].icons[current_zoom];
}

function add_tile_layer(layer_id,z_index) {
	if (typeof (tile_layer[layer_id]) != "undefined") {
		topLayer = tile_layer[layer_id].addTo(map);
//		topPane.appendChild(topLayer.getContainer());
		topLayer.setZIndex(z_index);
	}
}

// map interactivity

function update_stop_info(e) {

var popup_content = '<h3 class="stop_name">'+e.target.stop_name+'</h3>';

if (e.target.stop_code != '') {
	popup_content = popup_content+ '<p>text2go code: '+e.target.stop_code+'</p><p>Click a route to see the stop list:</p>';
}



var route_ids_array = get_routes_for_stop_id(e.target.stop_id);

for (var i = 0, len = route_ids_array.length; i < len; i++) {
	
	var route_info = get_route_info_for_id(route_ids_array[i]);
//	popup_content = popup_content + '<a href="'+route_info.route_url+'"><i id="icon-xsml-'+route_info.route_short_name+'" class="linked-div" rel="/route-and-schedules/" style="float: left;" ></i></a>'; // need to add link in the rel.

// <i id="icon-sml-14" class="route-icon route-icon-sml"> </i>

	popup_content = popup_content + '<a href="'+route_info.route_url+'"><i id="icon-sml-'+route_info.route_short_name+'" class="route-icon route-icon-sml linked-div" rel="/route-and-schedules/" style="float: left;" ></i></a>'; // need to add link in the rel.
	
}

popup_content = popup_content + '<br style="clear: both;" />';

e.target.setPopupContent(popup_content);

if (system_map) {
highlight_route_alignment(route_ids_array);}

//ga('send', 'event', 'map', 'click stop', e.target.stop_name+' (ID '+e.target.stop_id +')');

}

function close_popup_update_map(e) {
var route_ids_array = get_routes_for_stop_id(e.target.stop_id);
unhighlight_route_alignment(route_ids_array);
}

function highlight_route_alignment(route_ids) {
		
		console.log('route_ids');
		console.log(route_ids);
		route_ids = encapsulate_in_array(route_ids);
		console.log('route_ids');
		console.log(route_ids);
		
		 if (system_map) {
		
			add_route_alignment(route_ids);
			update_route_alignment_shadow(route_ids);
		}
		


	    for (var i = 0, len = route_ids.length; i < len; i++) {
			console.log('highlight this alignment: '+route_ids[i]);
			var route_id = parseInt(route_ids[i]);
	    	if (routes_active.indexOf(route_id) > -1) {
	    		console.log('routes_active.indexOf(route_id) > -1');
				route_layers[route_id].bringToFront();
				console.log('route_layers[route_id].bringToFront();');
				route_layers[route_id].setStyle(route_styles[route_id][1]);
		    }

	    }
	    
}

function unhighlight_route_alignment(route_ids) {

		route_ids = encapsulate_in_array(route_ids);
		
		if (system_map) {remove_route_alignment(route_ids);
		update_route_alignment_shadow(route_ids);}

	    for (var i = 0, len = route_ids.length; i < len; i++) {
	    	
	    	if (routes_active.indexOf(route_id) > -1) {
				var route_id = parseInt(route_ids[i]);
				route_layers[route_id].setStyle(route_styles[route_id][0]);
		    }
	    }

}
function refresh_landmark_view() {
	var marker_set = landmark_markers;
	
	for (var i = 0; i < marker_set.length; i++) {
		if (typeof marker_set[i] !== 'undefined') {
			var landmark_id = marker_set[i].landmark_id;

			var icon_index = landmarks[landmark_id].icon_index;
			var height = landmark_icons[icon_index].height;
			var width = landmark_icons[icon_index].width;
			var filename = landmark_icons[icon_index].filename;
			marker_set[i].setIcon(landmark_icon(width,height,icon_index,filename));
		}
	}
}


function toggle_stop_visibility() {
    if ( (map.getZoom() < ZoomLevelThreshhold && map.hasLayer(stops_layer_group)) || itinerary_up == true) {
        map.removeLayer(stops_layer_group);
    }
    if ( (map.getZoom() >= ZoomLevelThreshhold && map.hasLayer(stops_layer_group) == false) && !itinerary_up ) {
        load_stop_markers();
    }
}

function toggle_minor_landmark_visibility() {
    if ((map.getZoom() < minor_landmarks_zoom_threshhold && map.hasLayer(minor_landmark_markers_group)) || itinerary_up == true) {
        map.removeLayer(minor_landmark_markers_group);
    }
    if ((map.getZoom() >= minor_landmarks_zoom_threshhold && map.hasLayer(minor_landmark_markers_group) == false) && !itinerary_up) {
        add_landmarks_markers('minor');
//      map.addLayer(minor_landmark_markers_group);
    }
}

function add_object_property(property_name,object) {
	if (!object.hasOwnProperty(property_name)) {
		object[property_name] = Array();
	}
}

// execute this to set up map

load_routes();
//load_stop_markers();


add_tile_layer(0,5);

if (system_map) {
	
	//add_tile_layer(1,10);
}
else {
console.log("before add_route_alignment(route_ids_array)");
//update_route_alignment_shadow(route_ids_array);
add_route_alignment(route_ids_array);

console.log("add_route_alignment(route_ids_array)");
console.log("add_route_alignment("+route_ids_array+")");

}


//add_tile_layer(2,15);


// set up landmark_icons
$.ajax({
    url: map_files_base+"icons.csv",
    async: false,
    success: function (csvd) {
        landmark_icons =  $.csv.toObjects(csvd);
    },
    dataType: "text"
});


function load_landmarks_markers(significance_designation) {

	if (significance_designation == 'major') {
		var landmark_markers_group = major_landmark_markers_group;
		}
	if (significance_designation == 'minor') {
		var landmark_markers_group = minor_landmark_markers_group; 
		}

	if (landmark_markers_group.getLayers().length == 0) {

		$.ajax({
			url: map_files_base+ "landmarks_"+significance_designation+".csv",
			async: true,
			success: function (csvd) {
				
				var landmarks_array_temp =  $.csv.toObjects(csvd);
		
				console.log(landmarks_array_temp);
		
				for (var i = 0, len = landmarks_array_temp.length; i < len; i++) {
							
					landmarks[landmarks_array_temp[i].landmark_id] = {};
		
					landmarks[landmarks_array_temp[i].landmark_id].landmark_name = landmarks_array_temp[i].landmark_name;
					landmark_name = landmarks_array_temp[i].landmark_name;
					landmarks[landmarks_array_temp[i].landmark_id].category_name = landmarks_array_temp[i].category_name;
					landmarks[landmarks_array_temp[i].landmark_id].landmark_url = landmarks_array_temp[i].landmark_url;
					var landmark_lat_temp = landmarks_array_temp[i].lat;
					landmarks[landmarks_array_temp[i].landmark_id].lat = landmark_lat_temp;
					var landmark_lon_temp = landmarks_array_temp[i].lon;
					landmarks[landmarks_array_temp[i].landmark_id].lon = landmark_lon_temp;
					landmarks[landmarks_array_temp[i].landmark_id].major = landmarks_array_temp[i].major;
					landmarks[landmarks_array_temp[i].landmark_id].category_name = landmarks_array_temp[i].category_name;
					landmarks[landmarks_array_temp[i].landmark_id].icon_id = landmarks_array_temp[i].icon_id;
		
					var icon_index = get_icon_index_for_icon(landmarks_array_temp[i].icon_id);
		
					if (typeof icon_index !== 'undefined') {
		
						landmarks[landmarks_array_temp[i].landmark_id].icon_index = icon_index;


						var width = landmark_icons[icon_index].width;
						var height = landmark_icons[icon_index].height;
						var filename = landmark_icons[icon_index].filename;
						var landmark_id = landmarks_array_temp[i].landmark_id;
		
						// var current_zoom = map.getZoom();
		
						create_landmark_marker(i,width,height,landmark_id,icon_index,landmark_lat_temp,landmark_lon_temp,filename,landmarks_array_temp[i].category_name,landmark_name);
				
						var LamMarker = landmark_markers[landmark_id];
						LamMarker.on('popupopen', update_landmark_info);
						
						// landmark_markers[landmark_id] = LamMarker;
						landmark_markers_group.addLayer(landmark_markers[landmark_id]);
						
						//var new_array_length = landmark_markers.push(LamMarker) - 1;
						//landmark_markers_group.addLayer(landmark_markers[new_array_length]);
						
						}
		
					}
				
				},
			dataType: "text"
		
		});
	}
}

function add_landmarks_markers(significance_designation) {
	
	if (significance_designation == 'major') {
	var landmark_markers_group = major_landmark_markers_group;
	}
if (significance_designation == 'minor') {
	var landmark_markers_group = minor_landmark_markers_group; 
	}
	
	map.addLayer(landmark_markers_group);
	}


// load_landmarks_markers('minor');


// executable code
map.on('load',  function() {

	
	if (system_map) {
		add_tile_layer(0,5);
		add_tile_layer(1,15);
	}
	else {
		// add_route_alignment(route_ids_array);
		// update_route_alignment_shadow(route_ids_array);
		console.log(routes);
		console.log('highlight_route_alignment(route_ids_array);');
		// highlight_route_alignment(route_ids_array);
		setTimeout(function() {  highlight_route_alignment(route_ids_array); },5);

	}


});

load_landmarks_markers('major');
load_landmarks_markers('minor');

add_landmarks_markers('major');


map.on('zoomend', function(e) {

		
		refresh_landmark_view();
		toggle_stop_visibility();
		toggle_minor_landmark_visibility();

});


// adding events for Google Analytics here

map.on('zoomend', function() {
	//ga('send', 'event', 'map', 'zoomend', 'Zoom level', map.getZoom());
});


// http://jsfiddle.net/7go98fe4/
     	   

// Object
// height: "184"
// id: "downtown_disney_district"
// landmark_id: ""
// width: "190"
// x: "248"
// y: "16"
// __proto__: Object

var planner_url = 'http://gtfs-api.ed-groth.com/trip-planner/anaheim-ca-us/plan-then-merge-by-route-sequence';
var default_plan_time = '1%3A29pm';
var planner_response;


  // decodePolyLine from otp-leaflet-client/src/main/webapp/js/otp/util/Geo.js
  /* This program is free software: you can redistribute it and/or
     modify it under the terms of the GNU Lesser General Public License
     as published by the Free Software Foundation, either version 3 of
     the License, or (at your option) any later version.
     
     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.
     
     You should have received a copy of the GNU General Public License
     along with this program.  If not, see <http://www.gnu.org/licenses/>. 
   */
	var decodePolyline = function(polyline) {
		
		  var currentPosition = 0;

		  var currentLat = 0;
		  var currentLng = 0;
	
		  var dataLength  = polyline.length;
		  
		  var polylineLatLngs = new Array();
		  
		  while (currentPosition < dataLength) {
			  
			  var shift = 0;
			  var result = 0;
			  
			  var byte;
			  
			  do {
				  byte = polyline.charCodeAt(currentPosition++) - 63;
				  result |= (byte & 0x1f) << shift;
				  shift += 5;
			  } while (byte >= 0x20);
			  
			  var deltaLat = ((result & 1) ? ~(result >> 1) : (result >> 1));
			  currentLat += deltaLat;
	
			  shift = 0;
			  result = 0;
			
			  do {
				  byte = polyline.charCodeAt(currentPosition++) - 63;
				  result |= (byte & 0x1f) << shift;
				  shift += 5;
			  } while (byte >= 0x20);
			  
			  var deltLng = ((result & 1) ? ~(result >> 1) : (result >> 1));
			  
			  currentLng += deltLng;
	
			  polylineLatLngs.push(new L.LatLng(currentLat * 0.00001, currentLng * 0.00001));
		  }	
		  
		  return polylineLatLngs;
	};
  

var itineraries_for_display;

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}



function getItinerary(start,end) { // must pass data to allow for ajax success funtions
	
	if (typeof start[0] == "undefined") {
		var start_coords = new Array(landmarks[start].lat,landmarks[start].lon);
		var start_landmark_id = start;
		//ga('send', 'event', 'map', 'Plan trip - landmarks', 'From: '+landmarks[start].landmark_name + 'To: '+landmarks[end].landmark_name);
		}
	else {
		var start_coords = new Array(start[0],start[1]);
		var start_landmark_id = null;
		//ga('send', 'event', 'map', 'Plan trip - lat/lon', 'From: '+start[0]+','+start[1]+' To: '+end[0]+','+end[1]);
		}
		
	if (typeof end[0] == "undefined") {
		var end_coords = new Array(landmarks[end].lat,landmarks[end].lon);
		var end_landmark_id = end;
		}
	else {
		var end_coords = new Array(end[0],end[1]);
		var end_landmark_id = null;
		}
	
	var date = new Date();
	console.log(date);
	var month = date.getMonth()+1;
	var current_date_formatted = month + '-' + date.getDate() + '-' + date.getFullYear();

	var query_url = planner_url + '?fromPlace=' + start_coords[0] + '%2C' + start_coords[1] + '&toPlace=' + end_coords[0] + '%2C' + end_coords[1] + '&time='+default_plan_time+'&date='+current_date_formatted;
	console.log('query_url: '+query_url);

	planner_response = load_data(query_url);

	console.log('planner_response: '+planner_response);

	var start_location = planner_response.from;
	var end_location = planner_response.to;
	
	var leg_counter;
	var itinerary;
	var itinerary_with_bus_counter = 0;	
	var itineraries = [];

	
	for(var itinerary_i = 0; itinerary_i < planner_response.itineraries.length; itinerary_i++) {
		
		console.log('itinerary_i: '+itinerary_i);
	
		itinerary = planner_response.itineraries[itinerary_i];

		console.log('itinerary (planner response)');
		console.log(itinerary);

		leg_counter = 0;
		
		var has_bus = 0;
		for(var leg_i = 0; leg_i < itinerary.legs.length; leg_i++) {
			if (itinerary.legs[leg_i].mode == 'BUS') {has_bus = 1;}
			}
		
		
			for(var leg_i = 0; leg_i < itinerary.legs.length; leg_i++) {
		
					var current_leg = itinerary.legs[leg_i];
		
					console.log('current_leg');
					console.log(current_leg);
					console.log(current_leg.mode);
				
					if (current_leg.mode == 'BUS') {
					
						if (isNumeric(current_leg.routeHumanFrequency)) {var display_frequency = 'Every '+current_leg.routeHumanFrequency+' minutes';} else {var display_frequency = current_leg.routeHumanFrequency;}
			
						// route information
						var route_info_object = {mode: 'BUS',
						shape: decodePolyline(current_leg.legGeometry.points),
						route_short_name: current_leg.route, // route_short_name
						route_long_name: current_leg.routeLongName,
						route_color: current_leg.routeColor,
						frequency: display_frequency,
						route_url: current_leg.routeUrl,
						first_bus: current_leg.routeSpan.early.departure_time, // in UTC format -- come back to this
						last_bus: current_leg.routeSpan.late.departure_time // in UTC format -- come back to this
							};
				
		//				itineraries[itinerary_i][leg_counter].route_info = route_info_object;
			
		//				console.log(itineraries[itinerary_i][leg_counter].route_info);
			
						// start stop
						var start_stop_object = {name: current_leg.from.name,
							stop_code: current_leg.from.stopCode,
							stop_id: current_leg.from.stopId.id,
							lat: current_leg.from.lat,
							lon: current_leg.from.lon
							};

		//				itineraries[itinerary_i][leg_counter].start_stop = start_stop_object;

						var end_stop_object = {name: current_leg.to.name,
							stop_code: current_leg.to.stopCode,
							stop_id: current_leg.to.stopId.id,
							lat: current_leg.to.lat,
							lon: current_leg.to.lon
							};
				
		//				itineraries[itinerary_i][leg_counter].end_stop = end_stop_object;
			
						var leg_to_add = {
							route_info: route_info_object,
							start_stop_object: start_stop_object,
							end_stop_object: end_stop_object
						};
			
						console.log('itineraries['+itinerary_with_bus_counter+']['+leg_counter+']');
						
						if (typeof itineraries[itinerary_with_bus_counter] == 'undefined') {
							itineraries[itinerary_with_bus_counter] = new Array();
						}
						
						itineraries[itinerary_with_bus_counter][leg_counter] = leg_to_add;
							leg_counter++;
						}
						
					if (current_leg.mode == 'WALK' && itinerary.legs.length == 1 && planner_response.itineraries.length == 1) {
					
					
			
						// route information
						var route_info_object = {shape: decodePolyline(current_leg.legGeometry.points),
						mode: 'WALK'
							};
				
		//				itineraries[itinerary_i][leg_counter].route_info = route_info_object;
			
		//				console.log(itineraries[itinerary_i][leg_counter].route_info);
			
						// start stop
						var start_stop_object = {name: current_leg.from.name,
							lat: current_leg.from.lat,
							lon: current_leg.from.lon
							};

		//				itineraries[itinerary_i][leg_counter].start_stop = start_stop_object;

						var end_stop_object = {name: current_leg.to.name,
							lat: current_leg.to.lat,
							lon: current_leg.to.lon
							};
				
		//				itineraries[itinerary_i][leg_counter].end_stop = end_stop_object;
			
						var leg_to_add = {
							route_info: route_info_object,
							start_stop_object: start_stop_object,
							end_stop_object: end_stop_object
						};
			
						console.log('itineraries['+itinerary_with_bus_counter+']['+leg_counter+']');
						
						if (typeof itineraries[itinerary_with_bus_counter] == 'undefined') {
							itineraries[itinerary_with_bus_counter] = new Array();
						}
						
						itineraries[itinerary_with_bus_counter][leg_counter] = leg_to_add;
							leg_counter++;
						
					
					}

					}
				itinerary_with_bus_counter++;
			
		}
	

	

//	What do I want in itinerary objects?
//  mode = "walk" or "transit" <-- for now do not show any walking
//  transit details
	// start stop -- stop object: name, stop_id, stop_code
	// end stop -- stop object: name, stop_id, stop_code
	// polyline
	// route info: short name, long name, color, first bus, last bus, route_url

var return_object = {
	start_landmark_id: start_landmark_id,
	end_landmark_id: end_landmark_id,
	itineraries: itineraries
};

console.log(return_object);

itineraries_for_display = return_object;

}

var itinerary_up = 0;
var showing_itinerary;
var start_icon = new L.Icon({ 
		iconUrl: map_files_base+'map_ui_images/marker-flag-start-shadowed.png',
		iconSize: [48,49],
		iconAnchor: [48,49]
		});
var end_icon = new L.Icon({ 
		iconUrl: map_files_base+'map_ui_images/marker-flag-end-shadowed.png',
		iconSize: [48,49],
		iconAnchor: [48,49]
		});
var tripplan_polylines = new Array();
var tripplan_markers = new Array();
var start_marker;
var end_marker;
var start_landmark_marker;
var end_landmark_marker;

function map_itinerary(itinerary_i) {

	var itinerary = itineraries_for_display.itineraries[itinerary_i];

	map.removeLayer(tile_layer[1]);

	if (map.hasLayer(start_marker)) {map.removeLayer(start_marker);}
	if (map.hasLayer(end_marker)) {map.removeLayer(end_marker);}

	// clear the showing_itinerary_object
	showing_itinerary = itinerary;

	remove_tripplan();

	// set to true because an itinerary is being shown
	itinerary_up = true;

	toggle_stop_visibility();

	var line_offset = 0;

	if (itineraries_for_display.start_landmark_id != null) {
			start_landmark_marker = landmark_markers[itineraries_for_display.start_landmark_id];
			start_landmark_marker.addTo(map);}
			
	if (itineraries_for_display.end_landmark_id != null) {
			end_landmark_marker = landmark_markers[itineraries_for_display.end_landmark_id];
			end_landmark_marker.addTo(map);}

	
	
	for(var leg_i = 0; leg_i < itinerary.length; leg_i++) {
	
		if (leg_i == 0) {start_marker = L.marker([itinerary[leg_i].start_stop_object.lat, itinerary[leg_i].start_stop_object.lon], {icon: start_icon,zIndexOffset: 388});
				start_marker.addTo(map);}
				
		if (leg_i == itinerary.length-1) {end_marker = L.marker([itinerary[leg_i].end_stop_object.lat, itinerary[leg_i].end_stop_object.lon], {icon: end_icon,zIndexOffset: 388});
				end_marker.addTo(map);}
		
		if (itinerary[leg_i].route_info.mode == 'BUS') {
		
					tripplan_markers[leg_i] = new L.marker( [ itinerary[leg_i].start_stop_object.lat , itinerary[leg_i].start_stop_object.lon], {
							   zIndexOffset: 400,
							   icon: StopIcons[itinerary[leg_i].route_info.route_short_name]
							});

					tripplan_markers[leg_i].addTo(map);
		
				}

		if(itinerary[leg_i].route_info.hasOwnProperty('route_color')) {var polyline_color = itinerary[leg_i].route_info.route_color;}
		else {var polyline_color = '8642D2';}

		var line_points = itinerary[leg_i].route_info.shape;	
		//var offset = offsetPoints(line_points,.0001*offset,0);
		var outline = L.polyline(line_points, {offset: (line_offset-2), color: '#fff', weight: 12, opacity: 1});
		tripplan_polylines.push(outline);
		itineraryGroup.addLayer(outline);// outline
		var legPolyLine = L.polyline(line_points, {offset: line_offset, color: '#'+polyline_color, weight: 8, opacity: 1});
		tripplan_polylines.push(legPolyLine);
		itineraryGroup.addLayer(legPolyLine);
		
		line_offset ++;
		
	}
	
	itineraryGroup.addTo(map);
	map.fitBounds(itineraryGroup.getBounds());

	
}


function offsetPoints( points,  _xOffset,  _yOffset ) {
	var newPoints = Array();
	for(var i = 0; i<points.length; i++) {
		newPoints.push( L.latLng(points[i].lat + _xOffset, points[i].lng + _yOffset));
	}
	return newPoints;
}

function remove_tripplan() {

if (map.hasLayer(start_landmark_marker)) {map.removeLayer(start_landmark_marker);}
if (map.hasLayer(end_landmark_marker)) {map.removeLayer(end_landmark_marker);}
if (map.hasLayer(start_marker)) {map.removeLayer(start_marker);}
if (map.hasLayer(end_marker)) {map.removeLayer(end_marker);}



// I could consolidate the two below things into one function.

// removeLayer(polylines);
itineraryGroup.clearLayers();
map.removeLayer(itineraryGroup);

// reset the tripplan_polylines array
tripplan_polylines = [];


for(var i = 0; i < tripplan_markers.length; i++) {
	map.removeLayer(tripplan_markers[i]);
}

tripplan_markers = [];

}

function exit_tripplan_mode() {
	itinerary_up = false;
	add_tile_layer(0,5);
	add_tile_layer(1,10);
	toggle_stop_visibility();
	toggle_minor_landmark_visibility();
}



// http://gtfs-api.ed-groth.com/trip-planner/anaheim-ca-us/plan-then-merge-by-route-sequence?fromPlace=33.8046480634388%2C-117.915358543396&toPlace=33.82422318995612%2C-117.90390014648436&time=1%3A29pm&date=03-31-2015
