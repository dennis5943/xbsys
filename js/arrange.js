$(document).ready(function() {
	// page is now ready, initialize the calendar...
	$('.date').datepicker({
		// put your options and callbacks here
	});
	
	$('.timepicker-default').timepicker();
});

function onclkSubmitArrange() {
	$('#modal_loading').modal('toggle');
	
	var request_url = "arrange.php?request_by_ajax=1"
		+"&loader=1"
		+"&req_type=onclkSubmitArrange"
		+"&sel_game="+$('#sel_game').val()
		+"&txt_date="+$('#txt_date').val()
		+"&txt_time="+$('#txt_time').val()
		+"&txt_myMsg="+$('#txt_myMsg').val().replace(/\n/g, "<br/>");

	$.ajax({
		type: "POST",
		url: request_url,
		cache: false,
		async : false,
		error: function(xhr){
			alert("[xxx] xmlHttp Failure!!");
		},
		complete: function(response){
			$('#txt_myMsg').val('');
			$('#modal_loading').modal('toggle');
			$('#modal_alertBtn').modal('toggle');
		}
	});
}