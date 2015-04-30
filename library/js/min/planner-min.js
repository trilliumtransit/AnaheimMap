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
		$(this).parent().parent().parent().siblings().find('.selected').removeClass('selected');
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

	 var jqxhr = $.getJSON( "http://gtfs-api.ed-groth.com/trip-planner/anaheim-ca-us/plan?fromPlace=33.8046480634388,-117.915358543396&toPlace=33.77272636987434,-117.8671646118164&time=1:29pm&date=03-31-2015", function() {
  		console.log( "success" );
		})
  .done(function(data) {
  console.log(data);
    console.log( "second success" );
  })
  .fail(function() { 
    console.log( "error" );
  })
  .always(function() {
    console.log( "complete" );
  });

	$('#main-planner-form').submit(function(event) {
		// get lat long values from inputs
		
		// trigger show the results panel
		
		event.preventDefault();
		togglePlanner();
		getItinerary(start_coords,end_coords);
	});
	
	$('#return-to-input-link,.x-box').click(togglePlanner); 


	function togglePlanner() {
		$('#input-panel').toggleClass('hidden');
		$('#output-panel').toggleClass('hidden');
	}



});

