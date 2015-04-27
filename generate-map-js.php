<?php

if(function_exists('imagepng')){
  echo  "//imagepng() -Exists-";
}else{
  echo "//imagepng() ==== DOES NOT ==== Exist";
}

if (isset($_GET['system_map'])) {$system_map = $_GET['system_map'];}
if (isset($_GET['routes'])) {$routes = $_GET['routes'];}
if (isset($_GET['container_id'])) {$container_id = $_GET['container_id'];}
 

//

$map_files_base =  "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 
$naked_url_base = "$_SERVER[HTTP_HOST]"; 
$dragable_icons = "false";
if (strpos($map_files_base, 'localhost') !== FALSE) { // check if on mamp/apache localhost
$dragable_icons = "true";
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

var landmark_markers = Array();
var landmark_markers_group = L.featureGroup();

// define which routes
var route_ids_array = [<?php echo $routes ?>];
var system_map = <?php echo $system_map ?>;
// define other variables
var map_files_base = '<?php echo $map_files_base_split[0] ?>';
var api_base_url = 'http://archive.oregon-gtfs.com/gtfs-api/';
var route_alignments_tiles = 'trilliumtransit.ca9f8a4a';
var road_label_tiles = 'trilliumtransit.acea92f4';

var accessToken = 'pk.eyJ1IjoidHJpbGxpdW10cmFuc2l0IiwiYSI6ImVUQ2x0blUifQ.2-Z9TGHmyjRzy5GC1J9BTw';

tile_layer[0] = new L.tileLayer('http://{s}.tiles.mapbox.com/v4/' + route_alignments_tiles + '/{z}/{x}/{y}.png?access_token=' + accessToken, {detectRetina: true});
tile_layer[1] = new L.tileLayer('http://{s}.tiles.mapbox.com/v4/' + road_label_tiles + '/{z}/{x}/{y}.png?access_token=' + accessToken,{detectRetina: true});
var default_icon_color = '575757';

var ZoomLevelThreshhold = 15;

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
L.mapbox.accessToken = accessToken;
var map = L.mapbox.map('<?php echo $container_id; ?>', 'trilliumtransit.5434d913', { zoomControl: false, zoomAnimation: false, maxBounds: bounds, minZoom: 13 });


// map controls
map.scrollWheelZoom.disable();
if (system_map) {
			 map.fitBounds([
				[33.797984, -117.924412],
				[33.813340, -117.909644]
			]);
        }
        
new L.Control.Zoom({ position: 'bottomright' }).addTo(map);

// FUNCTIONS

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


route_urls = Array();

route_urls[1702] = 'http://rideart.org/routes-and-schedules/hotel-circle-clementine-line/';
route_urls[1705] = 'http://rideart.org/routes-and-schedules/downtown-packing-district-line/';
route_urls[1700] = 'http://rideart.org/routes-and-schedules/grand-plaza-line/';
route_urls[1711] = 'http://rideart.org/routes-and-schedules/toy-story-line/';
route_urls[1697] = 'http://rideart.org/routes-and-schedules/harbor-blvd-line/';
route_urls[1709] = 'http://rideart.org/routes-and-schedules/artic-sports-complex-line/';
route_urls[1716] = 'http://rideart.org/routes-and-schedules/canyon-line/';
route_urls[1708] = 'http://rideart.org/routes-and-schedules/artic-sports-complex-line/';
route_urls[1704] = 'http://rideart.org/routes-and-schedules/katella-line/';
route_urls[1710] = 'http://rideart.org/routes-and-schedules/orange-line/';
route_urls[1699] = 'http://rideart.org/routes-and-schedules/grand-plaza-line/';
route_urls[1703] = 'http://rideart.org/routes-and-schedules/hotel-circle-clementine-line/';
route_urls[1707] = 'http://rideart.org/routes-and-schedules/manchester-ave-line/';
route_urls[1714] = 'http://rideart.org/routes-and-schedules/canyon-line/';
route_urls[1712] = 'http://rideart.org/routes-and-schedules/buena-park-line/';
route_urls[1713] = 'http://rideart.org/routes-and-schedules/mainplace-line/';
route_urls[1696] = 'http://rideart.org/routes-and-schedules/harbor-blvd-line/';
route_urls[1698] = 'http://rideart.org/routes-and-schedules/grand-plaza-line/';
route_urls[1701] = 'http://rideart.org/routes-and-schedules/hotel-circle-clementine-line/';
route_urls[1706] = 'http://rideart.org/routes-and-schedules/ball-road-line/';

// Load an object with the routes
function load_routes() {
	var load_data_url = generate_proxy_url(api_base_url+'routes/by-feed/anaheim-ca-us');

    routes = load_data(load_data_url);
    
// 	for(var route_i = 0; route_i < routes.length; route_i++) {
// 		if(!routes[route_i].hasOwnProperty('route_url')) {       
// 		routes[route_i].route_url = 'http://rideart.org/routes-and-schedules/' + routes[route_i].route_long_name.toLowerCase().replace(/ /g,'-').replace(/\./g,'');	
// 		}
// 	}

	for(var route_i = 0; route_i < routes.length; route_i++) {
		if(!routes[route_i].hasOwnProperty('route_url')) {       
		routes[route_i].route_url = route_urls[routes[route_i].route_id];
		}
	}
	
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
 
	add_object_property(category_name,landmark_markers);

	var zoom_level_icon = landmark_icon(width,height,icon_index,filename);

	landmark_markers[category_name][i] = L.marker([landmark_lat, landmark_lon], {
	draggable: <?php echo $dragable_icons; ?>,
	icon: zoom_level_icon,
	title: landmark_name}).bindPopup(landmark_name, {maxWidth: 400});
		

landmark_markers[category_name][i].landmark_id = landmark_id;
landmark_markers[category_name][i].landmark_name = landmark_name;
		
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

	var nearest_stop = find_nearest_stop(e.target.getLatLng().lat,e.target.getLatLng().lng);
	
	var popup_content = '<h3 class="stop_name">'+e.target.landmark_name+'</h3><p>Nearest ART stop: ' + nearest_stop[0] + '<br/>Served by: '+nearest_stop[3].route_short_name+'</p>';



// final action - set popup content
e.target.setPopupContent(popup_content);

ga('send', 'event', 'map', 'click landmark', e.target.landmark_name);
	
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
		iconUrl: map_files_base+'map_icons/'+filename,
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

ga('send', 'event', 'map', 'click stop', e.target.stop_name+' (ID '+e.target.stop_id +')');

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

var minor_landmarks_zoom_threshhold = 16;

function refresh_landmark_view() {
		for (var category_i = 0; category_i < landmark_categories.length; category_i++) {
	
		var category_name = landmark_categories[category_i]
		var marker_set = landmark_markers[category_name];
		console.log(marker_set);
		
		for (var i = 0; i < marker_set.length; i++) {
			if (typeof marker_set[i] !== 'undefined') {
				var landmark_id = marker_set[i].landmark_id;
				console.log('landmark_id: '+landmark_id);
				console.log('landmarks['+category_name+']['+landmark_id+'].icon_index');
				var icon_index = landmarks[category_name][landmark_id].icon_index;
				var height = landmark_icons[icon_index].height;
				var width = landmark_icons[icon_index].width;
				var filename = landmark_icons[icon_index].filename;
				marker_set[i].setIcon(landmark_icon(width,height,icon_index,filename));
			}
		}
	}
}

function toggle_stop_visibility() {
    if (map.getZoom() < ZoomLevelThreshhold && map.hasLayer(stops_layer_group)) {
        map.removeLayer(stops_layer_group);
    }
    if (map.getZoom() >= ZoomLevelThreshhold && map.hasLayer(stops_layer_group) == false) {
        load_stop_markers();
    }
}

function add_object_property(property_name,object) {
	if (!object.hasOwnProperty(property_name)) {
		object[property_name] = Array();
	}
}

// execute this to set up map

load_routes();
load_stop_markers();


if (system_map) {
	add_tile_layer(0,5);
}
else {
console.log("before add_route_alignment(route_ids_array)");
//update_route_alignment_shadow(route_ids_array);
add_route_alignment(route_ids_array);

console.log("add_route_alignment(route_ids_array)");
console.log("add_route_alignment("+route_ids_array+")");

}

add_tile_layer(1,10);


$.ajax({
    url: map_files_base+"icons.csv",
    async: false,
    success: function (csvd) {
        landmark_icons =  $.csv.toObjects(csvd);
    },
    dataType: "text"
});


console.log("ajax URL: "+map_files_base+"icons.csv;");

$.ajax({
    url: map_files_base+ "landmarks_all.csv",
    async: true,
    success: function (csvd) {
        
        var landmarks_array_temp =  $.csv.toObjects(csvd);
        
        console.log(landmarks_array_temp);
        
        for (var i = 0, len = landmarks_array_temp.length; i < len; i++) {
        
        	var category_name = landmarks_array_temp[i].category_name;
        	
        	add_object_property(landmarks_array_temp[i].category_name,landmarks);
		
			landmarks[category_name][landmarks_array_temp[i].landmark_id] = {};
		
			landmarks[category_name][landmarks_array_temp[i].landmark_id].landmark_name = landmarks_array_temp[i].landmark_name;
			landmark_name = landmarks_array_temp[i].landmark_name;
			landmarks[category_name][landmarks_array_temp[i].landmark_id].category_name = landmarks_array_temp[i].category_name;
			landmarks[category_name][landmarks_array_temp[i].landmark_id].landmark_url = landmarks_array_temp[i].landmark_url;
			var landmark_lat_temp = landmarks_array_temp[i].lat;
			landmarks[category_name][landmarks_array_temp[i].landmark_id].lat = landmark_lat_temp;
			var landmark_lon_temp = landmarks_array_temp[i].lon;
			landmarks[category_name][landmarks_array_temp[i].landmark_id].lon = landmark_lon_temp;
			landmarks[category_name][landmarks_array_temp[i].landmark_id].major = landmarks_array_temp[i].major;
			landmarks[category_name][landmarks_array_temp[i].landmark_id].icon_id = landmarks_array_temp[i].icon_id;
		
			var icon_index = get_icon_index_for_icon(landmarks_array_temp[i].icon_id);
		
			if (typeof icon_index !== 'undefined') {
		
				landmarks[category_name][landmarks_array_temp[i].landmark_id].icon_index = icon_index;


				var width = landmark_icons[icon_index].width;
				var height = landmark_icons[icon_index].height;
				var filename = landmark_icons[icon_index].filename;
				var landmark_id = landmarks_array_temp[i].landmark_id;     
		
				// var current_zoom = map.getZoom();
		
				create_landmark_marker(i,width,height,landmark_id,icon_index,landmark_lat_temp,landmark_lon_temp,filename,category_name,landmark_name);
				
				var LamMarker = landmark_markers[category_name][i];
				LamMarker.on('popupopen', update_landmark_info);
				
				var new_array_length = landmark_markers.push(LamMarker) - 1;
				landmark_markers_group.addLayer(landmark_markers[new_array_length]);

		
        	}
        

		
		}
		
		map.addLayer(landmark_markers_group);
		
		},
    dataType: "text"
		
});


// executable code
map.on('load',  function() {

	
	if (system_map) {
add_tile_layer(0,5);
add_tile_layer(1,10);
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

map.on('zoomend', function(e) {
	
		refresh_landmark_view();
		toggle_stop_visibility();

});


// adding events for Google Analytics here

map.on('zoomend', function() {
	ga('send', 'event', 'map', 'zoomend', 'Zoom level '+map.getZoom());
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

