var planner_first_load = true;

function updatePlannerPanels($targ) {
	$targ.parent().toggleClass('active');
		$targ.parent().siblings().removeClass('active');
		
	//	$items_container = $(
		
		$('.items-container').each(function(ind, itemscontainer) {
			if($(itemscontainer).find('.active').length  < 1) {
				$(itemscontainer).removeClass('open');
			} else {
				$(itemscontainer).addClass('open');
			}
		});
		
		updatePanelHeight($targ);
		
		
		
}

function updatePanelHeight($targ) {
// calculate height stuff to keep all ui visible
		$item_container = $targ.closest('.items-container');
		if($item_container.hasClass('open')) {
			$item_container.height($targ.siblings('.place-pannel').height() + 45);
		}
		else {
			$item_container.height(40);
		}
}




function setupResults() {
	$('.planner-results-option').click(function() {
	
		if($(this).hasClass('closed')) {
		$open = $('.planner-results-option.open');
		$open.removeClass('open');
		$open.addClass('closed');
		
		
		$(this).removeClass('closed');
		$(this).addClass('open');
		console.log(parseInt($(this).attr('rel')));
		map_itinerary(parseInt($(this).attr('rel')));
		}
	});
}

function processURL() {
	if(planner_first_load) {
		//check if url has locations provided
		var hash = window.location.hash;
		var urlTo = getParameterByName('to');
		var urlFrom = getParameterByName('from');
	
		if((urlTo != '') && (urlFrom != '')) {
			//select 
			console.log("auto poplulate planner");
			$('#from-items-container #attraction-id-'+urlFrom).addClass('selected');
			console.log('text:'+$('#from-items-container #attraction-id-'+urlFrom).text());
			$('#to-items-container #attraction-id-'+urlTo).addClass('selected');
			// get lat lons
		
			processPlannerInput()
		};
		planner_first_load = false;
	}
}

function clearSelectedStart() {
	$('#from-selection-preview').removeClass('active').text('');	
	$('#from-items-container .menu-item').removeClass('selected');
}

function clearSelectedEnd() {
	$('#to-selection-preview').removeClass('active').text('');	
	$('#to-items-container .menu-item').removeClass('selected');
}

function setStartFromID(attraction_id) {
	clearSelectedStart();
	//insertParam('from', attraction_id);
	$('#from-selection-preview').addClass('active').text($('#attraction-id-'+attraction_id).text());
	$('#from-items-container #attraction-id-'+attraction_id).addClass('selected');
	showStartLocation($('#from-items-container #attraction-id-'+attraction_id).attr('rel').split(';')[3],$('#from-items-container #attraction-id-'+attraction_id).attr('rel').split(';')[4]);
}

function setEndFromID(attraction_id) {
	clearSelectedEnd();
	//insertParam('to', attraction_id);
	$('#to-selection-preview').addClass('active').text($('#attraction-id-'+attraction_id).text());	
	$('#to-items-container #attraction-id-'+attraction_id).addClass('selected');
	showEndLocation($('#from-items-container #attraction-id-'+attraction_id).attr('rel').split(';')[3],$('#from-items-container #attraction-id-'+attraction_id).attr('rel').split(';')[4]);
}


$(document).ready(function(){


	$('#full-screen-desktop').click(function() {
		map.toggleFullscreen();
		if (map.isFullscreen()) {
		   $(this).find('a').html('<i></i>Go Full Screen');
		} else {
		   $(this).find('a').html('<i></i>Exit Full Screen');
		}
     
	});

	var editMarkers	 = getParameterByName('edit');
	if(editMarkers == 'true') {
			$('.leaflet-control-command-interior').css('display','inherit');
	}
	$( "#share-link-dialog" ).dialog({autoOpen: false});
	$('#share-this-map-link').click( function() {
		if($('#from-items-container .selected, #to-items-container .selected').length == 2) {
			$( "#share-link-dialog" ).text("http://www.rideart.org/standalone-map/" + 
									'?from='+$('#from-items-container .selected').attr('rel').split(';')[1]+
									'&to='+$('#to-items-container .selected').attr('rel').split(';')[1]);
		} else {
			$( "#share-link-dialog" ).text("http://www.rideart.org/standalone-map/");
		} 
		$( "#share-link-dialog" ).dialog( "open" );
	});

	$('body').on('click', '.plan-route-link-start', function(e) {
		
		var target = $(e.target);
		setStartFromID(target.attr('rel'));
	});
	$('body').on('click', '.plan-route-link-end', function(e) {
		
		var target = $(e.target);
		setEndFromID(target.attr('rel'));
		//if start selected 
		if($('#from-items-container .selected').length > 0) {
					processPlannerInput();	
		} 
	});
	
	
	
	$('li.menu-item').hover(function() {
		showPlace(
				  $(this).attr('rel').split(';')[3],
				  $(this).attr('rel').split(';')[4]
				  );
	}, function() {
	clearPreviews();
	});
	

	updatePlannerPanels($(this));

	$('.tab-holder').click(function() {
	
		updatePlannerPanels($(this));
			
	});
	
	
	$('li.menu-item').on( "click", function() {
	
		$(this).toggleClass('selected');
		$(this).siblings().removeClass('selected');
		//$(this).parent().parent().parent().parent().parent().parent().parent().find('.loc-input').attr('value', ($(this).text()).replace(/(\r\n|\n|\r|\t)/gm,"") + ", Anaheim, CA" );
		$(this).parent().parent().siblings().find('.selected').removeClass('selected');
		$(this).closest('.tab').siblings().find('.selected').removeClass('selected');
		$(this).closest('.single-slide').siblings().find('.selected').removeClass('selected');
		$(this).parent().parent().parent().parent().siblings().find('.selected').removeClass('selected');
		
		// update the preview field
		$(this).closest('.items-container').prev('.planner-selection-preview').text($(this).text()).addClass('active');
		if($(this).closest('.items-container').find('.selected').length < 1) {
			$(this).closest('.items-container').prev('.planner-selection-preview').text('').removeClass('active');
		}
		
		// close panel
		
		
		if($(this).hasClass('selected')){
			if($(this).closest('#from-items-container').length > 0) {
				$(this).closest('.tab').removeClass('active');
					updatePanelHeight($(this));
				//insertParam('from', $(this).attr('rel').split(';')[1]);
				showStartLocation($(this).attr('rel').split(';')[3], $(this).attr('rel').split(';')[4],$(this).text());
			} 
			else if ($(this).closest('#to-items-container').length > 0) {
				$(this).closest('.tab').removeClass('active');
				updatePanelHeight($(this));
				//	insertParam('to', $(this).attr('rel').split(';')[1]);
				showEndLocation($(this).attr('rel').split(';')[3], $(this).attr('rel').split(';')[4],$(this).text());
			}
		}
		
		
		
	});
	
	$('#mobile-bottom-hide-show-planner, #mobile-top-hide-show-planner').on('click', function(e) {
		$('#planner').toggleClass('hidden-planner');
		$('#input-panel').toggleClass('mobile-hidden');
		$('#output-panel').toggleClass('mobile-hidden');
		
		
		//if((this).hasClass('min')) {
	//		$(this).text('Show Planner');
	//	} else {
		//	$(this).text('Hide Planner');
		//}
	});
	
	
	 
	

//	var slider = $("div.panel-slider-holder").sliderTabs({
// 		 panelArrows: true,
//		tabs: false
//	});
	
	//$('.menu-item').on("click mousedown mouseup focus blur keydown change",function(e){
   //  console.log(e);
//});

if($( window ).width() > 600) {
  var options = {
            $DragOrientation: 0,
            $BulletNavigatorOptions: {                                //[Optional] Options to specify and enable navigator or not
                    $Class: $JssorBulletNavigator$,                       //[Required] Class to create navigator instance
                    $ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
                    $ActionMode: 1,                                 //[Optional] 0 None, 1 act by click, 2 act by mouse hover, 3 both, default value is 1
                    $AutoCenter: 1,                                 //[Optional] Auto center navigator in parent container, 0 None, 1 Horizontal, 2 Vertical, 3 Both, default value is 0
                    $Steps: 1,                                      //[Optional] Steps to go for each navigation request, default value is 1
                    $Lanes: 1,                                      //[Optional] Specify lanes to arrange items, default value is 1
                    $SpacingX: 5,                                   //[Optional] Horizontal space between each item in pixel, default value is 0
                    $SpacingY: 0,                                   //[Optional] Vertical space between each item in pixel, default value is 0
                    $Orientation: 1                                 //[Optional] The orientation of the navigator, 1 horizontal, 2 vertical, default value is 1
                },
                $ThumbnailNavigatorOptions: {
               	 $DisableDrag:false,
                }
        };
        var jssor_slider1 = new $JssorSlider$('panel-slider-holder_0', options);
        var jssor_slider2 = new $JssorSlider$('panel-slider-holder_1', options);
        var jssor_slider3 = new $JssorSlider$('panel-slider-holder_2', options);
        var jssor_slider4 = new $JssorSlider$('panel-slider-holder_3', options);
        var jssor_slider5 = new $JssorSlider$('panel-slider-holder_4', options);
        var jssor_slider6 = new $JssorSlider$('panel-slider-holder_5', options);

		

	//Planner Data, trip stuff

	}

	$('#main-planner-form').submit(function(event) {
		
		// trigger show the results panel
		
		event.preventDefault();
		
		processPlannerInput();
		
		
	});
	
	$('#return-to-input-link').click(function() {
			togglePlanner();
			refresh_landmark_view();
		toggle_stop_visibility();
		toggle_landmark_visibility();
		  // window.history.pushState({urlPath: ' '}, 'Title', ' ');
	}); 
	$('.x-box').click(function() {
		togglePlanner();
		clearPlanner();
		refresh_landmark_view();
		toggle_stop_visibility();
		toggle_landmark_visibility();
	});
	
	
	


	
//check if url has locations provided
	


});

function mobileTogglePlannerShow(e) {
	
}

function togglePlanner() {
		$('#input-panel').toggleClass('hidden');
		$('#output-panel').toggleClass('hidden');
		remove_tripplan();
		exit_tripplan_mode();
	}

function clearPlanner() {
	$('#planner .selected').removeClass('selected');
	$('.planner-selection-preview').text('').removeClass('active');
}

function processPlannerInput() {

// confirm that to/from have values
	if(($('#planner .selected').length < 2)) {
		alert('Please select both starting and ending locations');
	} else {
	
		togglePlanner();
		// get lat long values from inputs
		$from = $('div#from-items-container').find('.menu-item.selected');
		var fromSelectedInfo = $from.attr('rel').split(';');
		//console.log(fromSelectedInfo);
		var fromLat = fromSelectedInfo[3];
		var fromLon = fromSelectedInfo[4];
		$to = $('div#to-items-container').find('.menu-item.selected');
		var toSelectedInfo = $to.attr('rel').split(';');
		var toLat = toSelectedInfo[3];
		var toLon = toSelectedInfo[4];
		$('#planner-results-title').html($from.text()+' <span>to</span> '+$to.text());
		getItinerary([fromLat,fromLon],[toLat,toLon]);
		//console.log(itineraries);
		//var itineraries = getItinerary([33.8046480634388,-117.915358543396],[33.82422318995612,-117.90390014648436]);
		var plannerHTML = "";
		$('.planner-results-option').remove();
		
		for(var i = 0; i<itineraries_for_display['itineraries'].length && i<2; i++) {
			
			
			var itenerary = itineraries_for_display['itineraries'][i];
			// run through the legs and get route names
			var routeLegIds = new Array();
			for(var j = 0; j< itenerary.length; j++) {
				var leg = itenerary[j];
				routeLegIds.push(leg.route_info.route_short_name);
			}
			
			
			if(i==0) {plannerHTML += '<div id="planner-results-option-'+(i+1)+'" rel="'+i+'" class="planner-results-option open">';}
			else {plannerHTML += '<div id="planner-results-option-'+(i+1)+'" rel="'+i+'" class="planner-results-option closed">';} 
			if(itineraries_for_display['itineraries'].length >1) {
				plannerHTML += '<h3 class="option-title">Option '+(i+1);
				for(var id_ind = 0; id_ind<routeLegIds.length; id_ind ++) {
					plannerHTML += '<i id="icon-xsml-'+routeLegIds[id_ind]+'" class="route-icon"> </i>'; 	
				}
			
				plannerHTML +=' <span>(Click to expand)</span></h3>';
			}
			else {plannerHTML += '<h3 class="option-title">Trip details</h3>';}
			for(var j = 0; j< itenerary.length; j++) {
				var leg = itenerary[j]
				console.log('leg');
				plannerHTML += '<ul class="leg">';
				if(j==0) {plannerHTML += '<li><i class="bullet"></i>Go to bus stop: '+leg.start_stop_object.name+' (text2go code: '+leg.start_stop_object.stop_code+').</li>';}
				else {plannerHTML += '<li><i class="bullet"></i>Transfer at '+leg.start_stop_object.name+' (text2go code: '+leg.start_stop_object.stop_code+').</li>';}
				plannerHTML += '<li class="bus-leg-item"><div class="bus-route-leg-item-title"><i></i><i id="icon-sml-'+leg.route_info.route_short_name+'" class="route-icon route-icon-sml"> </i>Board bus '+leg.route_info.route_short_name+".</div>";
					plannerHTML += '<div class="bus-leg-item-frequency bus-leg-item-info"><strong>Service:</strong> '+leg.route_info.frequency+'</div>';
					plannerHTML += '<div class="bus-leg-item-first-last bus-leg-item-info"><div id="bus-leg-first"><strong>First bus today:</strong> '+moment(leg.route_info.first_bus).format('h:mm a').replace(/^0+/, '')+'</div>'+
															'<div id="bus-leg-last"> <strong>last bus today:</strong> '+moment(leg.route_info.last_bus).format('h:mm a').replace(/^0+/, '')+'</div></div>';
					
					plannerHTML += '<div class="bus-leg-item-link bus-leg-item-info"><a href="'+leg.route_info.route_url+'">See full route details</a></div>';
				plannerHTML += '</li>';
				if(j==(itenerary.length-1)) plannerHTML += '<li><i class="bullet"></i>Get off at '+leg.end_stop_object.name+".</li>";
				console.log(leg);
				plannerHTML += '</ul><br style="clear: both; margin-bottom: 10px;" />';
				
			};
			plannerHTML += '</div> <!-- end #planner-results-option -->';
			
			
		}; 
	
		$('#output-panel #inner-planner').append(plannerHTML);
		$('.plan-route-link-start').click( function(){
			clearSelectedStart();
		});
		$('.plan-route-link-end').click( function(){
			clearSelectedEnd();
		});
	
	}
	
	map_itinerary(0);
	setupResults();
	
		
}


// the next two funtions aid with the param url reading/writing for having linkable map urls.
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function insertParam(key, value) {
        key = escape(key); value = escape(value);

        var kvp = document.location.search.substr(1).split('&');
        if (kvp == '') {

            var newPath =  '?' + key + '=' + value;
            window.history.pushState({urlPath: newPath}, 'Title', newPath);
        }
        else {

            var i = kvp.length; var x; while (i--) {
                x = kvp[i].split('=');

                if (x[0] == key) {
                    x[1] = value;
                    kvp[i] = x.join('=');
                    break;
                }
            }

            if (i < 0) { kvp[kvp.length] = [key, value].join('='); }

           
            window.history.pushState({urlPath: '?'+kvp.join('&')}, 'Title', '?'+kvp.join('&'));
           
        }
    }