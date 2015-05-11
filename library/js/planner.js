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
		
		// calculate height stuff to keep all ui visible
		$item_container = $targ.closest('.items-container');
		if($item_container.hasClass('open')) {
			$item_container.height($targ.siblings('.place-pannel').height() + 45);
		}
		else {
			$item_container.height(40);
		}
		
}

$(document).ready(function(){


	$('.planner-results-option').click(function() {
		alert();
		//$(this).removeClass('closed');
		//$(this).addClass('open');
		//$open = $('.planner-results-option.open');
		//$open.removeClass('open');
		//$open.addClass('closed');
	});

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
		processPlannerInput()
	};

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
		
	});
	
	
	 
	

//	var slider = $("div.panel-slider-holder").sliderTabs({
// 		 panelArrows: true,
//		tabs: false
//	});
	
	//$('.menu-item').on("click mousedown mouseup focus blur keydown change",function(e){
   //  console.log(e);
//});


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
               	 $DisableDrag:true
                }
        };
        var jssor_slider1 = new $JssorSlider$('panel-slider-holder_0', options);
        var jssor_slider2 = new $JssorSlider$('panel-slider-holder_1', options);
        var jssor_slider3 = new $JssorSlider$('panel-slider-holder_2', options);
        var jssor_slider4 = new $JssorSlider$('panel-slider-holder_3', options);
        var jssor_slider5 = new $JssorSlider$('panel-slider-holder_4', options);
        var jssor_slider6 = new $JssorSlider$('panel-slider-holder_5', options);


	//Planner Data, trip stuff

	

	$('#main-planner-form').submit(function(event) {
		
		// trigger show the results panel
		
		event.preventDefault();
		
		processPlannerInput();
		
		
	});
	
	$('#return-to-input-link').click(togglePlanner); 
	$('.x-box').click(function() {
		togglePlanner();
		clearPlanner();
	});
	
	
	


	



});

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
		var fromLat = fromSelectedInfo[2];
		var fromLon = fromSelectedInfo[3];
		$to = $('div#to-items-container').find('.menu-item.selected');
		var toSelectedInfo = $to.attr('rel').split(';');
		var toLat = toSelectedInfo[2];
		var toLon = toSelectedInfo[3];
		$('#planner-results-title').html($from.text()+' <span>to</span> '+$to.text());
		getItinerary([fromLat,fromLon],[toLat,toLon]);
		//console.log(itineraries);
		//var itineraries = getItinerary([33.8046480634388,-117.915358543396],[33.82422318995612,-117.90390014648436]);
		var plannerHTML = "";
		$('.planner-results-option').remove();
		
		for(var i = 0; i<itineraries_for_display['itineraries'].length; i++) {

			var itenerary = itineraries_for_display['itineraries'][i];
			
				if(i==0) {plannerHTML += '<div id="planner-results-option-'+(i+1)+'" class="planner-results-option open">';}
				else {plannerHTML += '<div id="planner-results-option-'+(i+1)+'" class="planner-results-option closed">';} 
				plannerHTML += '<h3 class="option-title">Option '+(i+1)+'</h3>';
				for(var j = 0; j< itenerary.length; j++) {
					var leg = itenerary[j]
					console.log('leg');
					plannerHTML += '<ul class="leg">';
					if(j==0) {plannerHTML += '<li><i class="bullet"></i>Go to bus stop: '+leg.start_stop_object.name+' (text2go code: '+leg.start_stop_object.stop_code+').</li>';}
					else {plannerHTML += '<li><i class="bullet"></i>Transfer at '+leg.start_stop_object.name+' (text2go code: '+leg.start_stop_object.stop_code+').</li>';}
					plannerHTML += '<li class="bus-leg-item"><div class="bus-route-leg-item-title"><i></i><i id="icon-sml-'+leg.route_info.route_short_name+'" class="route-icon route-icon-sml"> </i>Board bus '+leg.route_info.route_short_name+".</div>";
						plannerHTML += '<div class="bus-leg-item-frequency bus-leg-item-info">Service '+leg.route_info.frequency+'</div>';
						plannerHTML += '<div class="bus-leg-item-first-last bus-leg-item-info"><div id="bus-leg-first"><strong>First bus today:</strong> '+moment(leg.route_info.first_bus).format('h:mm a').replace(/^0+/, '')+'</div>'+
																'<div id="bus-leg-last"> <strong>last bus today:</strong> '+moment(leg.route_info.last_bus).format('h:mm a').replace(/^0+/, '')+'</div></div>';
						
						plannerHTML += '<div class="bus-leg-item-link bus-leg-item-info"><a href="'+leg.route_info.route_url+'">See full route details</a></div>';
					plannerHTML += '</li>';
					if(j==(itenerary.length-1)) plannerHTML += '<li><i class="bullet"></i>Get off at '+leg.end_stop_object.name+".</li>";
					console.log(leg);
					plannerHTML += '</ul><br style="clear: both;" />';
					
				};
				plannerHTML += '</div> <!-- end #planner-results-option -->';
			
			
		}; 
	
		$('#output-panel #inner-planner').append(plannerHTML);
	
	}
	
	//map_itinerary(0);
		
}

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}