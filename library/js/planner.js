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
	
	$('.place-pannel li').click(function() {
		
		$(this).toggleClass('selected');
		$(this).siblings().removeClass('selected');
		$(this).parent().parent().parent().parent().parent().parent().parent().find('.loc-input').attr('value', ($(this).text()).replace(/(\r\n|\n|\r|\t)/gm,"") + ", Anaheim, CA" );
		$(this).parent().parent().siblings().find('.selected').removeClass('selected');
		$(this).parent().parent().parent().parent().siblings().find('.selected').removeClass('selected');
	});
	
	
	$('.planner .next-button').click(function() {
		
		$panelNumber = $(this).parent().attr('rel');
		$(this).parent().find('.panel-tab.');
		
	});
	
	$('.planner .back-button').click(function() {
		
		$panelNumber = $(this).parent().attr('rel');
		$(this).parent().find('.panel-tab.');
		
	});
	



});