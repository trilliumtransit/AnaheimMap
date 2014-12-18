<?php
if (isset($_GET['system_map'])) {$system_map = $_GET['system_map'];}
if (isset($_GET['routes'])) {$routes = $_GET['routes'];}
if (isset($_GET['map_files_base'])) {$map_files_base = $_GET['map_files_base'];}
 
?>
//



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
var route_layers = Array();
var topPane;
var topLayer;
var zoom_icon_scale = Array();
var landmarks = Array();
var landmark_icons = Array();

var tile_layer = new Array();
// tile_layer[1] = new L.tileLayer('http://{s}.tiles.mapbox.com/v3/'+map_id_labels+'/{z}/{x}/{y}.png');

var landmark_markers = Array();

// define which routes
var route_ids_array = [<?php echo $routes ?>];
var system_map = <?php echo $system_map ?>;
// define other variables
var map_files_base = <?php echo $map_files_base ?>;
var api_base_url = 'http://archive.oregon-gtfs.com/gtfs-api/';
var route_alignments_tiles = 'trilliumtransit.ca9f8a4a';
var road_label_tiles = 'trilliumtransit.b1c25bd2';
tile_layer[0] = new L.tileLayer('http://{s}.tiles.mapbox.com/v3/' + route_alignments_tiles + '/{z}/{x}/{y}.png');
tile_layer[1] = new L.tileLayer('http://{s}.tiles.mapbox.com/v3/' + road_label_tiles + '/{z}/{x}/{y}.png');
var default_icon_color = '575757';

var ZoomLevelThreshhold = 13;

// define the StopIcon
var StopIcon = L.Icon.extend({
    options: {
        iconSize: [12, 12],
        iconAnchor: [6, 6],
        popupAnchor: [0, 0]
    }
});

zoom_icon_scale[12] = .25;
zoom_icon_scale[13] = .3;
zoom_icon_scale[14] = .35;
zoom_icon_scale[15] = .6;
zoom_icon_scale[16] = 1;
zoom_icon_scale[17] = 1;
zoom_icon_scale[18] = 1;
zoom_icon_scale[19] = 1;

var unhighlighted_weight = 5;
var highlighted_weight = 10;


if (route_ids_array.length == 1) {route_ids_list = route_ids_array[0];}
else {var route_ids_list = route_ids_array.join();}

// mapbox token, basemap
L.mapbox.accessToken = 'pk.eyJ1IjoidHJpbGxpdW10cmFuc2l0IiwiYSI6ImVUQ2x0blUifQ.2-Z9TGHmyjRzy5GC1J9BTw';
var map = L.mapbox.map('interactive-map-holder', 'trilliumtransit.e8e8e512', { zoomControl: false });

// map controls
map.scrollWheelZoom.disable();
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
    //console.log(baseUrl + url);
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

	var load_data_url = generate_proxy_url(api_base_url+'routes/by-feed/anaheim-new-ca-us/route-id/'+route_ids_list);
	
	//console.log(load_data_url);
	//console.log('just before load_data (load routes)');

    routes = load_data(load_data_url);
	//console.log('routes: '+routes);
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
            // console.log(index);
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
            // console.log(index);
            break;
        }
    }

    return index;
}

function get_routes_for_stop_id(stop_id) {
	
	var stops_array_index = get_stops_array_index_from_id(stop_id);
	var specific_routes = stops[stops_array_index].routes;
	var route_ids_array = Array();
	
    for (var i = 0, len = specific_routes.length; i < len; i++) {
       route_ids_array.push(specific_routes[i].route_id);
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

			var geojson = routes[index].shared_arcs_geojson || routes[index].geojson;

					route_styles[id] = [];
					
					
					
					route_styles[id][0] = {
						"color": '#' + routes[index].route_color,
						"weight": unhighlighted_weight,
						"opacity": 1,
						// "dashArray": [10,10],
						"clickable": true
					};
					
					
					
					route_styles[id][1] = {
						"color": '#' + routes[index].route_color,
						"weight": highlighted_weight,
						"opacity": 1,
						
						// "dashArray": [10,10],
						"clickable": true
					};
					

					route_layers[id] = L.geoJson(geojson, {
						style: route_styles[id][0]
					});
					
					
					



	    }
	        route_layers[id].addTo(map);
	        

	    if (routes_active.indexOf(parseInt(id)) == -1) {
	        routes_active.push(parseInt(id));
	        // console.log('adding route_id '+id+' to routes_active');
	    }
	}
}

function stop_icons() {
		
    for (var i = 0; i < routes.length; i++) {
    	if (!isInArray(routes[i].route_color,route_colors)) {
    		route_colors.push(routes[i].route_color);
		    }
        }

		StopIcons[default_icon_color] = new StopIcon({iconUrl:map_files_base+"create_image.php?r=13&bw=3&&bc=ffffff&fg="+default_icon_color});

        for (var i = 0; i < route_colors.length; i++) {
                StopIcons[route_colors[i]] = new StopIcon({iconUrl:map_files_base+"create_image.php?r=13&bc=ffffff&fg="+route_colors[i]});
            }
		
	}



function generate_proxy_url(url) {
    return url;
	} 


/*pushing items into array each by each and then add markers*/
// console.log('just before defining load_stop_markers');
function load_stop_markers() {

    // if the map has the stops_layer_group, get rid of it
    if (map.hasLayer(stops_layer_group)) {
        map.removeLayer(stops_layer_group);
    }

    // clear out the current stops array
    // stops_layer_group = L.layerGroup();

		
	var load_data_url = generate_proxy_url(api_base_url+'stops/by-feed/anaheim-new-ca-us/route-id/'+route_ids_list);

	//console.log('just before load_data');

    //  async approach
    load_data_async(load_data_url, null,'', function(data){
    
    //console.log(data);
	
	stop_icons();
	
		//console.log('load_data_was_fired');
        stops = data;
        //console.log(stops);
        if (stops !== null) {

            for (var i = 0; i < stops.length; i++) {

					
					if (stops[i].routes.length > 1) {
						stops[i].color ='575757';
					}
					else {
						//console.log(stops[i].routes[0].route_id);
						stops[i].color = get_route_color_for_id(stops[i].routes[0].route_id);
						//console.log('stops['+i+'].color: '+stops[i].color);
					}
                    
                
                var LamMarker = new L.marker([stops[i].geojson.coordinates[1], stops[i].geojson.coordinates[0]], {
                    icon: StopIcons[stops[i].color]
                }).bindPopup('', {maxWidth: 400});
                
                
                LamMarker.stop_id = stops[i].stop_id;
                LamMarker.marker_id = i;
                LamMarker.stop_name = stops[i].stop_name;
                LamMarker.stop_code = stops[i].stop_code;

                LamMarker.on('click', update_stop_info);
                LamMarker.on('popupclose', close_popup_update_map);
				
				//console.log(LamMarker);
				
				//console.log(LamMarker.getLatLng());

                stop_markers.push(LamMarker);
                stops_layer_group.addLayer(stop_markers[i]);
            }

        }
        map.addLayer(stops_layer_group);
        
        if (system_map) {
			 map.fitBounds([
				[33.797984, -117.924412],
				[33.813340, -117.909644]
			]);
        }
        else {
	        map.fitBounds(stops_layer_group.getBounds());
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


function create_landmark_marker(i,width,height,landmark_id,icon_index,landmark_lat,landmark_lon,filename) {

console.log('icon_index: '+icon_index);

var zoom_level_icon = landmark_icon(width,height,icon_index,filename);

		landmark_markers[i] = L.marker([landmark_lat, landmark_lon], {icon: zoom_level_icon});
		landmark_markers[i].landmark_id = landmark_id;
		landmark_markers[i].addTo(map);

}

function landmark_icon(width,height,icon_index,filename) {
	var current_zoom = map.getZoom();
	if(typeof current_zoom == 'undefined'){current_zoom = 15;}
	console.log('current_zoom: '+current_zoom);
	
	if (typeof landmark_icons[icon_index].icons == 'undefined') {
		landmark_icons[icon_index].icons = [];
		}
	
	if (typeof landmark_icons[icon_index].icons[current_zoom] == 'undefined') {
	
	landmark_icons[icon_index].icons[current_zoom] = {};
	
	console.log('height: '+height);
	console.log('width: '+width);

	var scaled_width = zoom_icon_scale[current_zoom] * width;
	var scaled_height = zoom_icon_scale[current_zoom] * height;
	console.log('scaled_width: '+scaled_width);
	console.log('scaled_height: '+scaled_height);

	landmark_icons[icon_index].icons[current_zoom] = new L.Icon({ 
		iconUrl: 'map_icons/'+filename,
		iconSize: [scaled_width, scaled_height],
		iconAnchor: [scaled_width/2, scaled_height/2]
		});
		
	}

	console.log(landmark_icons[icon_index].icons[current_zoom]);
	
	return landmark_icons[icon_index].icons[current_zoom];
}

function add_tile_layer(layer_id,z_index) {
	if (typeof (tile_layer[layer_id]) != "undefined") {
		//console.log(tile_layer[layer_id]);
		topLayer = tile_layer[layer_id].addTo(map);
//		topPane.appendChild(topLayer.getContainer());
		topLayer.setZIndex(z_index);
	}
}

// map interactivity

function update_stop_info(e) {

var popup_content = '<h3 class="stop_name">'+e.target.stop_name+'</h3>';

if (e.target.stop_code != '') {
	popup_content = popup_content+ '<p>text2go code: '+e.target.stop_code+'</p>';
}

var route_ids_array = get_routes_for_stop_id(e.target.stop_id);
highlight_route_alignment(route_ids_array);

console.log(routes);
console.log(route_ids_array);

for (var i = 0, len = route_ids_array.length; i < len; i++) {
	// console.log(route_ids_array[i]);
	
	var route_info = get_route_info_for_id(route_ids_array[i]);
	console.log(route_info);
	popup_content = popup_content + '<i id="icon-xsml-'+route_info.route_short_name+'" class="linked-div" rel="/route-and-schedules/" style="float: left;" ></i>'; // need to add link in the rel.
	
}

popup_content = popup_content + '<br style="clear: both;" />';

e.target.setPopupContent(popup_content);

}

function close_popup_update_map(e) {
var route_ids_array = get_routes_for_stop_id(e.target.stop_id);
unhighlight_route_alignment(route_ids_array);
}

function highlight_route_alignment(route_ids) {
		
		route_ids = encapsulate_in_array(route_ids);
		console.log('highlight_route_alignment. route_ids: '.route_ids);
		
		if (system_map) {add_route_alignment(route_ids);}

	    for (var i = 0, len = route_ids.length; i < len; i++) {
			console.log('highlight this alignment: '+route_ids[i]);
			var route_id = parseInt(route_ids[i]);
	    	if (routes_active.indexOf(route_id) > -1) {
				route_layers[route_id].bringToFront();
				route_layers[route_id].setStyle(route_styles[route_id][1]);
		    }

	    }
}

function unhighlight_route_alignment(route_ids) {

		route_ids = encapsulate_in_array(route_ids);
		
		if (system_map) {remove_route_alignment(route_ids);}

	    for (var i = 0, len = route_ids.length; i < len; i++) {
	    	var route_id = parseInt(route_ids[i]);
		    route_layers[route_id].setStyle(route_styles[route_id][0]);
	    }

}

function change_landmark_sizes() {
	
    for (var i = 0; i < landmark_markers.length; i++) {
    	if (typeof landmark_markers[i] !== 'undefined') {
			var landmark_id = landmark_markers[i].landmark_id;
			var icon_index = landmarks[landmark_id].icon_index;
			console.log ('icon_index: '+icon_index);
			var height = landmark_icons[icon_index].height;
			console.log ('height: '+height);
			var width = landmark_icons[icon_index].width;
			var filename = landmark_icons[icon_index].filename;
			landmark_markers[i].setIcon(landmark_icon(width,height,icon_index,filename));
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

// execute this to set up map

load_routes();
load_stop_markers();

if (system_map) {
	add_tile_layer(0,5);
	add_tile_layer(1,10);
}
else {add_route_alignment(route_ids_array);}

$.ajax({
    url: map_files_base+"icons.csv",
    async: false,
    success: function (csvd) {
        landmark_icons =  $.csv.toObjects(csvd);
    },
    dataType: "text"
});

$.ajax({
    url: map_files_base+ "landmarks.csv",
    async: true,
    success: function (csvd) {
        
        var landmarks_array_temp =  $.csv.toObjects(csvd);
        
        console.log(landmarks_array_temp);
        
        for (var i = 0, len = landmarks_array_temp.length; i < len; i++) {
        
			console.log(landmarks_array_temp[i].landmark_id);
		
			landmarks[landmarks_array_temp[i].landmark_id] = {};
		
			landmarks[landmarks_array_temp[i].landmark_id].landmark_name = landmarks_array_temp[i].landmark_name;
			landmarks[landmarks_array_temp[i].landmark_id].category_name = landmarks_array_temp[i].category_name;
			landmarks[landmarks_array_temp[i].landmark_id].landmark_url = landmarks_array_temp[i].landmark_url;
			var landmark_lat_temp = landmarks_array_temp[i].lat;
			landmarks[landmarks_array_temp[i].landmark_id].lat = landmark_lat_temp;
			var landmark_lon_temp = landmarks_array_temp[i].lon;
			landmarks[landmarks_array_temp[i].landmark_id].lon = landmark_lon_temp;
			landmarks[landmarks_array_temp[i].landmark_id].icon_id = landmarks_array_temp[i].icon_id;
		
			var icon_index = get_icon_index_for_icon(landmarks_array_temp[i].icon_id);
		
			if (typeof icon_index !== 'undefined') {
		
				landmarks[landmarks_array_temp[i].landmark_id].icon_index = icon_index;

				console.log(icon_index);

				var width = landmark_icons[icon_index].width;
				var height = landmark_icons[icon_index].height;
				var filename = landmark_icons[icon_index].filename;
				var landmark_id = landmarks_array_temp[i].landmark_id;     
		
				// var current_zoom = map.getZoom();
				console.log('current_zoom: '+map.getZoom());
		
				create_landmark_marker(i,width,height,landmark_id,icon_index,landmark_lat_temp,landmark_lon_temp,filename);
		
        	}
        

		
		}
		
		},
    dataType: "text"
		
});

// executable code
map.on('load',  function() {

	map.on('zoomend', function() {
		console.log('zoomend happened.');
		change_landmark_sizes();
		toggle_stop_visibility();

	});

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


