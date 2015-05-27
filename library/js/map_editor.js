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
	// if there are edits for the icon refresh the icon
	var foundExistingIconEdit = false;
	if(typeof editedMarkers[landmark_id] !== 'undefined') {
		console.log('icon has been edited before,  binding...');
		if(typeof editedMarkers[landmark_id][zoom] !== 'undefined') {
				console.log('binding new marker');
				editedMarkers[landmark_id][zoom].updateMarker();
				foundExistingIconEdit = true;
		} 	
	} else {
		// make sure it loads default
		landmark_markers[landmark_id].setLatLng(new L.LatLng(landmarks[landmark_id].lat,landmarks[landmark_id].lon));
	
	}
	
	
	
	return foundExistingIconEdit;
	
}


var savedEdits;
function useSavedDataToRefreshIcons() {
	console.log('using saved edits: '+savedEdits);
	if(savedEdits.length > 4){
		var jsonArray = jQuery.parseJSON( savedEdits );
		jsonArray.forEach(function(value) {
			//console.log(value);
			var newID = value.id;
			var newZoom = value.zoom;
			if(! editedMarkers[newID]) {
				// add entry
				//add an array at for this marker
					editedMarkers[newID] = new Array();
					//

			}
			if(! editedMarkers[newID][newZoom]) {
				// find corresponding marker
				//console.log(landmark_markers);
				var newEditableMarker = new EditableMarker(landmark_markers[newID]);
				newEditableMarker.id = newID;
				newEditableMarker.zoom = value.zoom;
				newEditableMarker.imgHeight = value.imgHeight;
				newEditableMarker.imgWidth = value.imgWidth;
				newEditableMarker.initialImgHeight = value.imgHeight;
				newEditableMarker.initialImgWidth = value.imgWidth;
				newEditableMarker.labelOffsetX = value.labelOffsetX;
				newEditableMarker.labelOffsetY = value.labelOffsetY;
				newEditableMarker.initialLatLng = new L.LatLng(value.latlng.lat,value.latlng.lng);
				newEditableMarker.currentLatLng = new L.LatLng(value.latlng.lat,value.latlng.lng);
				editedMarkers[newID][newZoom] = newEditableMarker;
			
			}
		});
		refresh_landmark_view();
	}
}


	$.ajax({
		  dataType: "text",
		  url: map_files_base+'savedMapEdits.json',
		  async:false, 
		  success:  function(data) {
			 //
					//data = data.replace("[","");
		   			//data = data.replace("]","");
		   			//console.log(data); 
		   			savedEdits = data;
				   
		   
			}
	});



$(document).ready(function(){

	// find and load json
	//console.log(map_files_base+'savedMapEdits.json');
	
	


	

	$('#panel-save-to-server').click(function() {
		//save json
		var organizedArray = new Array();
		editedMarkers.forEach(function(zoomLevel) {
				//console.log(editedMarker);
			zoomLevel.forEach(function(editedMarker) {
				var infoToPush = {};
				infoToPush["id"] = editedMarker.id;
				infoToPush["landmark_name"] = editedMarker.marker.landmark_name;
				infoToPush["zoom"] = editedMarker.zoom;
				infoToPush["latlng"] = editedMarker.currentLatLng;
				infoToPush["labelOffsetX"] = editedMarker.labelOffsetX;
				infoToPush["labelOffsetY"] = editedMarker.labelOffsetY;
				infoToPush["imgWidth"] =  editedMarker.imgWidth;
				infoToPush["imgHeight"] = editedMarker.imgHeight;
				organizedArray.push(infoToPush);	
			});		  
		});
		//console.log(organizedArray);
		//console.log(JSON.stringify(organizedArray));
		
		
		$.ajax({
			url: map_files_base+'save_map_edits.php',
			type: 'POST',
			contentType: 'application/json',
			data: JSON.stringify(organizedArray),
			dataType: 'json'
		});
	
	});


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
		if(typeof editorSelected !== 'undefined' && editorSelected !== "null") {
		 editorSelected.disable();
		 editorSelected.updateMarker();
		 editorSelected = "null";
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
		refresh_landmark_view();
			
	});
	
	/*function safeButtonBind(buttonID, actionFunction) {
		var interval;
		$('#'+buttonID).mousedown(function() {
  		interval = setInterval(actionFunction(), 50);
		}).on('mouseout',function() {
			clearInterval(interval);
		}).on('mouseup',function() {
			clearInterval(interval);
		});
	}*/
	
	
	var increaseIconSizeInterval;
		$('#panel-increase-icon-size').mousedown(function() {
  		increaseIconSizeInterval = setInterval(function() {
  			editorSelected.increaseSize();
  		}, 50);
		}).on('mouseout',function() {
			clearInterval(increaseIconSizeInterval);
		}).on('mouseup',function() {
			clearInterval(increaseIconSizeInterval);
	});
	//safeButtonBind('panel-increase-icon-size', editorSelected.decreaseSize);
	
	var decreaseIconSizeInterval;
		$('#panel-decrease-icon-size').mousedown(function() {
  		decreaseIconSizeInterval = setInterval(function() {
  			editorSelected.decreaseSize();
  		}, 50);
		}).on('mouseout',function() {
			clearInterval(decreaseIconSizeInterval);
		}).on('mouseup',function() {
			clearInterval(decreaseIconSizeInterval);
	});
	
	$('#panel-increase-font-size').click(function() {
	
	});
	
	$('#panel-decrease-font-size').click(function() {
	
	});
	
	var moveLabelLeftInterval;
		$('#panel-move-label-left').mousedown(function() {
  		moveLabelLeftInterval = setInterval(function() {
  			editorSelected.moveLabelLeft();
  		}, 50);
		}).on('mouseout',function() {
			clearInterval(moveLabelLeftInterval);
		}).on('mouseup',function() {
			clearInterval(moveLabelLeftInterval);
	});
	
	var moveLabelRightInterval;
		$('#panel-move-label-right').mousedown(function() {
  		moveLabelRightInterval = setInterval(function() {
  			editorSelected.moveLabelRight();
  		}, 50);
		}).on('mouseout',function() {
			clearInterval(moveLabelRightInterval);
		}).on('mouseup',function() {
			clearInterval(moveLabelRightInterval);
	});
	
	var moveLabelUpInterval;
		$('#panel-move-label-up').mousedown(function() {
  		moveLabelUpInterval = setInterval(function() {
  			editorSelected.moveLabelUp();
  		}, 50);
		}).on('mouseout',function() {
			clearInterval(moveLabelUpInterval);
		}).on('mouseup',function() {
			clearInterval(moveLabelUpInterval);
	});
	
	var moveLabelDownInterval;
		$('#panel-move-label-down').mousedown(function() {
  		moveLabelDownInterval = setInterval(function() {
  			editorSelected.moveLabelDown();
  		}, 50);
		}).on('mouseout',function() {
			clearInterval(moveLabelDownInterval);
		}).on('mouseup',function() {
			clearInterval(moveLabelDownInterval);
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
		
		var editor = false;
		var needsLookup = true;
		
		if(typeof editorSelected !== 'undefined' && editorSelected !== "null") 
			editor = true;
		
		if(editor) {
			if(editorSelected.uniqueID === map.getZoom() +'_'+marker[0].landmark_id) {
	
				console.log('do nothing, selected icon was clicked again');
				needsLookup = false;
			} 
		} 
		
		if(needsLookup) {
		
			// new icon was clicked, look up if it has edits
			var found = false;
			if(typeof editedMarkers[marker[0].landmark_id] !== 'undefined') {
				if(typeof editedMarkers[marker[0].landmark_id][zoom] !== 'undefined') {
					
						if(editor) editorSelected.disable();
						editorSelected = editedMarkers[marker[0].landmark_id][zoom];
						editorSelected.enable();
						found = true;
					
				} 
			}
			if(!found) {
			// create a new one
				if(editor) editorSelected.disable();
				var newEditableMarker = new EditableMarker(marker[0]);
				editorSelected = newEditableMarker;
				editorSelected.enable();
			}
		
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
		if(that.zoom == map.getZoom())
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
		console.log('enable ' + this.marker.landmark_name);
		this.currentlySaved = false;
		$(this.marker._icon).addClass('editor-selected');
		$('.control-selected-landmark').attr('value',(this.marker.landmark_name));
		this.marker.dragging.enable();
		
		// get initial values
		$domImg = this.getDomImg();
		this.initialLatLng = this.marker._latlng;
		this.initialImgWidth = $domImg.width();
		this.initialImgHeight = $domImg.height();
		this.imgWidth = $domImg.width();
		this.imgHeight = $domImg.height();
		
		this.initialLabelOffsetX = this.labelOffsetX;
		this.initialLabelOffsetY = this.labelOffsetY;

		
	}
		
	this.disable = function() {
		console.log('disable ' + this.marker.landmark_name);
		$(this.marker._icon).removeClass('editor-selected');
		
		// revert unsaved changes
		if(! this.currentlySaved) {
		
			//this.marker._latlng = this.initialLatLng;
			
			this.setImgSize (this.initialImgWidth, this.initialImgHeight);
			this.setLabelOffset(this.initialLabelOffsetX,this.initialLabelOffsetY);
			this.marker.setLatLng(this.initialLatLng);
			// need to add label size
			
			
			
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
		//this.latLngOffset = new L.LatLng(this.currentLatLng.lat - this.initialLatLng.lat,
		//					 this.currentLatLng.lng - this.initialLatLng.lng);
		//console.log(this.latLngOffset.lat);
		
		this.edited = true;
	}
	
	this.updateMarker = function() {
		//this.marker = newMarker;
		//img size
		this.setImgSize(this.imgWidth,this.imgHeight);
		//font size
		this.marker.setLatLng(this.currentLatLng);
		this.setLabelOffset(this.labelOffsetX,
							this.labelOffsetY);
		//label pos
		// latlng offset
	}
	
	//init
	
}