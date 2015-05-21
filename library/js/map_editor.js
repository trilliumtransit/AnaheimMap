// map editor


var editorMode = false;
var editorSelected = null;
var markerLookup = {};

var currentMarker = null;

var editedMarkers = new Array();




function updateEditedMarkers(marker, allZoom) { // object, bool
	var zoom = map.getZoom();
	// this is the function to call when saving the data
	

}




$(document).ready(function(){


	$('#panel-start-stop-editing').click(function() {
		if(editorMode) {
			editorMode = false;
			//toggleDragableLandmarks();
			$(this).text('Start Editing');
			$(this).css('background','0');
		} else {
			editorMode = true;
			//toggleDragableLandmarks();
			$(this).text('Stop Editing');
			$(this).css('background','red');
		}
	});

	
	
	
	map.on('zoomend', function(e) {
		var zoom = map.getZoom();	
		$('#control-zoom-level').text('Zoom Level: '+zoom);
		// finds the new marker at new zoom level
		
		if(! editedMarkers[editorSelected.id]) {
			// add entry
			//add an array at for this marker
			editedMarkers[editorSelected.id] = new Array();
		}
		
		if(! editedMarkers[editorSelected.id][zoom]) {
		
			var newMarkerEntry = new EditableMarker(landmark_markers[editorSelected.id]); 
			editedMarkers[editorSelected.id][zoom] = newMarkerEntry;
			editorSelected.disable();
			editorSelected = newMarkerEntry;
			editorSelected.enable();
			
		} else {
		
			editorSelect.disable();
			editorSelected = editedMarkers[marker.id][zoom];
			editorSelected.engable();
			
		}
			
	});
});

function saveCurrentMarker() {
	var zoom = map.getZoom();
	if(! editedMarkers[marker.id]) {
		// add entry
		//add an array at for this marker
			editedMarkers[marker.id] = new Array();
	}
	if(! editedMarkers[marker.id][zoom]) {
		editedMarkers[marker.id][zoom] = editorSelected;
	}
}

function revertCurrentMarker() {

}


function editorRespondToIconClick(marker) {
	if(editorMode) {
		marker[0].closePopup();
		console.log(marker);
		// need to get current ref if marker already exists
		currentMarker = new EditableMarker(marker[0]);		
	}
}

function EditableMarker(marker) {
	//
	this.id = marker.landmark_id;
	this.marker = marker;
	this.savedInitialLatLng = marker._latlng;
	
	this.currentLatLng = marker._latlng;
	this.latLngOffset = new L.LatLng(0,0);
	this.labelOffsetX = -1;
	this.labelOffsetY = -1;
	this.sizeMultiplier = -1;
	
	var that = this;
	
	this.marker.on('dragend', function(event){
		console.log('dragend');
		that.updateAfterMove(event);
	});
	
	//markerLookup[this.id] = 
	
	
	this.enable = function() {
		$(this.marker._icon).addClass('editor-selected');
		$('.control-selected-landmark').attr('value',(this.marker.landmark_name));
		this.marker.dragging.enable();
		
		
		
	}
		
	this.disable = function() {
		$(this.marker._icon).removeClass('editor-selected');
		this.marker.dragging.disable();
	} 
	
	this.updateAfterMove = function(event) {
		this.currentLatLng = this.marker._latlng;
		//console.log(this.marker);
		//console.log(event.target);
		//console.log(this.savedInitialLatLng);
		this.latLngOffset = new L.LatLng(this.currentLatLng.lat - this.savedInitialLatLng.lat,
							 this.currentLatLng.lng - this.savedInitialLatLng.lng);
		console.log(this.latLngOffset.lat);
	}
	
	if(! editorSelected ) {
		editorSelected = this;
		this.enable();
	} else {
		if(this.id !== editorSelected.id) {
			editorSelected.disable();
			editorSelected = this;
			this.enable();
		} 			
	} 
}