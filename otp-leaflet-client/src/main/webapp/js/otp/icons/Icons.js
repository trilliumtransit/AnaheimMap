// Load Anaheim Icons from csv file
//


// every function uses this so we're making it a global.
var leaflet_map = null;

var map_icons_prefix = 'http://anaheim-otp.ed-groth.com/AnaheimMap/map_icons/'

// code from AnaheimMap
var zoom_icon_scale = Array();
zoom_icon_scale[10] = .125;
zoom_icon_scale[11] = .15;
zoom_icon_scale[12] = .25;
zoom_icon_scale[13] = .3;
zoom_icon_scale[14] = .35;
zoom_icon_scale[15] = .6;
zoom_icon_scale[16] = 1;
zoom_icon_scale[17] = 1;
zoom_icon_scale[18] = 1;
zoom_icon_scale[19] = 1;

var landmarks = Array();
var landmark_icons = Array();
var map_files_base = "http://anaheim-otp.ed-groth.com/AnaheimMap/";
var api_base_url = 'http://archive.oregon-gtfs.com/gtfs-api/';
var landmark_markers = Array();

var default_icon_color = '575757';

var ZoomLevelThreshhold = 13;

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

/*
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
*/

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


function generate_proxy_url(url) {
    return url;
} 

function create_landmark_marker(i,width,height,landmark_id,icon_index,landmark_lat,landmark_lon,filename) {

console.log('icon_index: '+icon_index);

var zoom_level_icon = landmark_icon(width,height,icon_index,filename);

        landmark_markers[i] = L.marker([landmark_lat, landmark_lon], {icon: zoom_level_icon});
        landmark_markers[i].landmark_id = landmark_id;
        landmark_markers[i].addTo(leaflet_map);

}

function landmark_icon(width,height,icon_index,filename) {
    var current_zoom = leaflet_map.getZoom();
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
        iconUrl: map_icons_prefix + filename,
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
        topLayer = tile_layer[layer_id].addTo(leaflet_map);
//        topPane.appendChild(topLayer.getContainer());
        topLayer.setZIndex(z_index);
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

function AnaheimMapMain (map) {
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
                    console.log('current_zoom: '+leaflet_map.getZoom());
            
                    create_landmark_marker(i,width,height,landmark_id,icon_index,landmark_lat_temp,landmark_lon_temp,filename);
            
                }
            

            
            }
            
            },
        dataType: "text"
            
    });

    // map should already be loaded here. we don't need to add under 'load'
    // handler.
    leaflet_map.on('zoomend', function() {
        console.log('zoomend happened.');
        change_landmark_sizes();
        // TODO. enable this.
        // toggle_stop_visibility();
    });

}

var LoadAnaheimIcons =  function (map) {

    console.log("LoadIcons called");
    console.log("leaflet object is: ", L );
    // var map = L.map('map');
    //var map = global_lmap;
    leaflet_map = map;
    console.log("map object is: ", map );

    // 33.809761,-117.91897,1
    var lat = 33.809761;
    var lng = -117.91897;
    var options = null;

    // L.marker( [ lat, lng ], options ).addTo(map);
    L.marker( [ lat, lng ]).addTo(leaflet_map);

    AnaheimMapMain() ;
}
