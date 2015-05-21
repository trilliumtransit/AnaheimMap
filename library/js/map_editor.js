// map editor


var editorMode = false;
var editorSelected;
var markerLookup = {};

var currentMarker = null;

var editedMarkers = new Array();

var savedEdits = new Array();


function updateEditedMarkers(marker, allZoom) { // object, bool
	var zoom = map.getZoom();
	// this is the function to call when saving the data
	
	
}

function bindNewMarkerToEditorData(landmark_id) {
	var zoom = map.getZoom();
	if(typeof editedMarkers[landmark_id] !== 'undefined') {
		console.log('icon has been edited before,  binding...');
		if(typeof editedMarkers[landmark_id][zoom] !== 'undefined') {
				console.log('binding new marker');
				editedMarkers[landmark_id][zoom].updateMarker();
		}
	}
}




$(document).ready(function(){


	$('#panel-start-stop-editing').click(function() {
		if(editorMode) {
			editorMode = false;
			//toggleDragableLandmarks();
			$(this).text('Start Editing');
			$(this).css('background','0');
			editorSelected.disable();
		} else {
			editorMode = true;
			//toggleDragableLandmarks();
			$(this).text('Stop Editing');
			$(this).css('background','red');
			if (editorSelected) {
				editorSelected.enable();
			}				
		}
	});

	
	
	
	map.on('zoomend', function(e) {
		var zoom = map.getZoom();	
		if(typeof editorSelected !== 'undefined') {
		 editorSelected.disable();
			$('#control-zoom-level').text('Zoom Level: '+zoom);
			// finds the new marker at new zoom level
		
			/*if(! editedMarkers[editorSelected.id]) {
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
		
				editorSelected.disable();
				editorSelected = editedMarkers[editorSelected.id][zoom];
				editorSelected.enable();
			
			}*/
		
		}
			
	});
	
	
	$('#panel-increase-icon-size').click(function () {
		
		editorSelected.increaseSize();
	});
	
	$('#panel-decrease-icon-size').click(function () {
		
		editorSelected.decreaseSize();
	});
	
	$('#panel-increase-font-size').click(function() {
	
	});
	
	$('#panel-decrease-font-size').click(function() {
	
	});
	
	$('#panel-move-label-left').click(function() {
		console.log('moving label left');
		editorSelected.moveLabelLeft();
	});
	$('#panel-move-label-right').click(function() {
		editorSelected.moveLabelRight();
	});
	$('#panel-move-label-up').click(function() {
		editorSelected.moveLabelUp();
	});
	$('#panel-move-label-down').click(function() {
		editorSelected.moveLabelDown();
	});
	$('#panel-save-current-marker-edits-current-zoom').click(function() {
		commitSelectedAtCurrentZoom();
	});
	$('#panel-save-current-marker-edits-current-zoom').click(function() {
		commitSelectedAtAllZooms();
	});
	
	
});

function commitSelectedAtCurrentZoom() {
	editorSelected.currentlySaved = true;
	saveCurrentMarker();
}

function commitSelectedAtAllZooms() {
	return 0;
}

function saveCurrentMarker() {
	var zoom = map.getZoom();
	if(! editedMarkers[editorSelected.id]) {
		// add entry
		//add an array at for this marker
			editedMarkers[editorSelected.id] = new Array();
			//
			
	}
	if(! editedMarkers[editorSelected.id][zoom]) {
		editedMarkers[editorSelected.id][zoom] = editorSelected;
	}
}

function revertCurrentMarker() {

}


function editorRespondToIconClick(marker) {
	if(editorMode) {
		marker[0].closePopup();
		//console.log(marker);
		// need to get current ref if marker already exists
		//currentMarker = new EditableMarker(marker[0]);	
		var zoom = map.getZoom();
		var foundExisting = false;	
		if(typeof editorSelected !== 'undefined') {
			if(typeof editedMarkers[marker[0].landmark_id] !== 'undefined') {
				if(typeof editedMarkers[marker[0].landmark_id][zoom] !== 'undefined') {
					if(editedMarkers[marker[0].landmark_id][zoom].uniqueID !== editorSelected.uniqueID) {
						editorSelected.disable();
						editorSelected = editedMarkers[marker[0].landmark_id][zoom];
						editorSelected.enable();
					} 
				} 
			}
		}
		if(typeof editorSelected === 'undefined'){
			var newEditableMarker = new EditableMarker(marker[0]);
			editorSelected = newEditableMarker;
			editorSelected.enable();
		} else if (!foundExisting && editorSelected.uniqueID !== map.getZoom() +'_'+marker[0].landmark_id) { 
			var newEditableMarker = new EditableMarker(marker[0]);
			editorSelected.disable();
			editorSelected = newEditableMarker;
			editorSelected.enable();
		}
		
	}
}

function EditableMarker(marker) {
	//
	
	this.zoom = map.getZoom();
	this.id = marker.landmark_id;
	this.marker = marker;
	this.uniqueID = this.zoom + '_' + this.id;
	
	this.initialLatLng = marker._latlng;
	this.currentLatLng = marker._latlng;
	this.latLngOffset = new L.LatLng(0,0);
	
	this.initialLabelOffsetX = -1;
	this.initialLabelOffsetY = -1;
	this.labelOffsetX = -1;
	this.labelOffsetY = -1;
	
	this.initialImgWidth = -1;
	this.initialImgHeight = -1;
	this.imgWidth = -1;
	this.imgHeight = -1;
	
	this.edited = false;
	this.currentlySaved = false;
	this.saved = false;
	
	var that = this;
	
	
	
	this.marker.on('dragend', function(event){
		console.log('dragend');
		that.updateAfterMove(event);
			
		
	});
	
	//markerLookup[this.id] = 
	
	this.getDomImg = function() {
		var $domImg;
		if( $(this.marker._icon).find('img').length > 0) { // if not an image marker
			$domImg = $(this.marker._icon).find('img');
		} else {
			$domImg = $(this.marker._icon);
		}
		return $domImg;
	}	
	
	var $freshDomImg = this.getDomImg();
	this.initialImgWidth = $freshDomImg.width();
	this.initialImgHeight = $freshDomImg.height();
	this.imgWidth = $freshDomImg.width();
	this.imgHeight = $freshDomImg.height();
	
	
	this.enable = function() {
	
		this.currentlySaved = false;
		$(this.marker._icon).addClass('editor-selected');
		$('.control-selected-landmark').attr('value',(this.marker.landmark_name));
		this.marker.dragging.enable();
		
		// get initial values
		$domImg = this.getDomImg();
		this.initialLatLng = this.marker._latlng;
		this.initialImgWidth = $domImg.width();
		this.initialImgHeight = $domImg.height();
		
		this.initialLabelOffsetX = this.labelOffsetX;
		this.initialLabelOffsetY = this.labelOffsetY;

		
	}
		
	this.disable = function() {
		$(this.marker._icon).removeClass('editor-selected');
		
		// revert unsaved changes
		if(! this.currentlySaved) {
		
			//this.marker._latlng = this.initialLatLng;
			
			this.imgWidth = this.initialImgWidth;
			this.imgHeight = this.initialImgHeight;
			
			this.setLabelOffset(this.initialLabelOffsetX,this.initialLabelOffsetY);
			
			this.marker.setLatLng(this.initialLatLng);
		} else {
			this.saved = true;
		}
		
		this.marker.dragging.disable();
	} 
	
	
	
	this.moveLabelLeft = function() {
		this.setLabelOffset(this.labelOffsetX - 1,
							this.labelOffsetY);
	}
	
	this.moveLabelRight = function() {
		this.setLabelOffset(this.labelOffsetX + 1,
							this.labelOffsetY);
	}
	
	this.moveLabelUp = function() {
		this.setLabelOffset(this.labelOffsetX,
							this.labelOffsetY - 1);
	}
	
	this.moveLabelDown = function() {
		this.setLabelOffset(this.labelOffsetX,
							this.labelOffsetY + 1 );
	}
	
	this.setLabelOffset = function(_x,_y) {
		
		this.labelOffsetX = _x;
		this.labelOffsetY = _y;
		
		var $domLabel = $(this.marker._icon).find('.icon-centered-label');
		console.log ('setting label position');
		$domLabel.css('transform','translate3d('+this.labelOffsetX+'px, '+ this.labelOffsetY+'px, 0 )');
	}
	
	this.setImgSize = function(_w,_h) {
		var $img = this.getDomImg();
		$img.width(_w);
		$img.height(_h);
		this.imgWidth = _w;
		this.imgHeight = _h; 
	}

	
	this.increaseSize = function() {
		var $img = this.getDomImg();
		var ratio = $img.width()/$img.height();
		$img.width($img.width() + 2);
		$img.height($img.width()/ratio);
		this.imgWidth = $img.width();
		this.imgHeight = $img.height(); 
		
		
		this.edited = true;
		
	}
	
	this.decreaseSize = function() {
		var $img = this.getDomImg();
		var ratio = $img.width()/$img.height();

		$img.width($img.width() - 2);
		$img.height($img.width()/ratio);
		this.imgWidth = $img.width();
		this.imgHeight = $img.height();
		
		this.edited = true;
		
	}
	
//this.increaseFontSize = function() {
		//$(this.marker._icon).css('font-size'
	//}
	
	this.updateAfterMove = function(event) {
		this.currentLatLng = this.marker._latlng;
		//console.log(this.marker);
		//console.log(event.target);
		//console.log(this.savedInitialLatLng);
		this.latLngOffset = new L.LatLng(this.currentLatLng.lat - this.initialLatLng.lat,
							 this.currentLatLng.lng - this.initialLatLng.lng);
		console.log(this.latLngOffset.lat);
		
		this.edited = true;
	}
	
	this.updateMarker = function() {
		//this.marker = newMarker;
		//img size
		this.setImgSize(this.imgWidth,this.imgHeight);
		//font size
		this.setLabelOffset(this.labelOffsetX,
							this.labelOffsetY);
		//label pos
		// latlng offset
	}
	
	//init
	
}