$(document).ready(function() {
	$('.date').datepicker();
	$('.timepicker-default').timepicker();
});

function onclkSubmitArrange() {
	$('#modal_loading').modal('toggle');
	
	if($('#cbx_sendPtt').attr('checked')) { sendPtt(); }
	
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

function onclkSubmitNewGame() {
	if($('#txt_gameName').val().length == 0) { return false; }
	$('#modal_addNewGame').modal('toggle');
	$('#modal_loading').modal('toggle');

	var request_url = "arrange.php?request_by_ajax=1"
		+"&loader=1"
		+"&req_type=onclkSubmitNewGame"
		+"&txt_gameName="+encodeURI($('#txt_gameName').val());

	$.ajax({
		type: "POST",
		url: request_url,
		cache: false,
		async : false,
		error: function(xhr){
			alert("[xxx] xmlHttp Failure!!");
		},
		complete: function(response){
			$('#modal_loading').modal('toggle');
			$('#modal_alertBtn').modal('toggle');
		}
	});
}

function sendPtt() {
	window.open('inc/pttMailSender.php',
'Sending...','width=800,height=170');
}