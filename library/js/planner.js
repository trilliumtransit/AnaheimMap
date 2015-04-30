$(document).ready(function(){

	$('.tab-holder').click(function() {
	
		$(this).parent().toggleClass('active');
		$(this).parent().siblings().removeClass('active');
		
		$('.items-container').each(function(ind, itemscontainer) {
			if($(itemscontainer).find('.active').length  < 1) {
				$(itemscontainer).removeClass('open');
			} else {
				$(itemscontainer).addClass('open');
			}
		});
		
		
	
	});
	
	
	$('li.menu-item').on( "click", function() {
	
		$(this).toggleClass('selected');
		$(this).siblings().removeClass('selected');
		//$(this).parent().parent().parent().parent().parent().parent().parent().find('.loc-input').attr('value', ($(this).text()).replace(/(\r\n|\n|\r|\t)/gm,"") + ", Anaheim, CA" );
		$(this).parent().parent().siblings().find('.selected').removeClass('selected');
		$(this).closest('.tab').siblings().find('.selected').removeClass('selected');
		$(this).closest('.single-slide').siblings().find('.selected').removeClass('selected');
		$(this).parent().parent().parent().parent().siblings().find('.selected').removeClass('selected');
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
		togglePlanner();
		// get lat long values from inputs
		$from = $('div#from-items-container').find('.menu-item.selected');
		var fromSelectedInfo = $from.attr('rel').split(';');
		//console.log(fromSelectedInfo);
		var fromLat = fromSelectedInfo[1];
		var fromLon = fromSelectedInfo[2];
		$to = $('div#to-items-container').find('.menu-item.selected');
		var toSelectedInfo = $to.attr('rel').split(';');
		var toLat = toSelectedInfo[1];
		var toLon = toSelectedInfo[2];
		$('#planner-results-title').html($from.text()+' <span>to</span> '+$to.text());
		//var itineraries = getItinerary([fromLat,fromLon],[toLat,toLon]));
		var itineraries = getItinerary([33.8046480634388,-117.915358543396],[33.82422318995612,-117.90390014648436]);
		var plannerHTML = "";
		
		itineraries.forEach(function(itinerary) {
			itinerary.forEach(function(leg) {
				console.log('leg');
				console.log(leg);
			
			});
		}); 
	});
	
	$('#return-to-input-link,.x-box').click(togglePlanner); 


	function togglePlanner() {
		$('#input-panel').toggleClass('hidden');
		$('#output-panel').toggleClass('hidden');
	}



});