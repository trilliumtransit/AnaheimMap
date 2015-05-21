// map editor


var editorMode = false;

$(document).ready(function(){


$('#panel-start-stop-editing').click(function() {
	if(editorMode) {
		editorMode = false;
		toggleDragableLandmarks();
	} else {
		editorMode = true;
		toggleDragableLandmarks();
	}
});



});