var obj_cache = new Object;

$(document).ready(function() {
	updateAvatar();
});

function onclkSubmitMsg() {
	$('#modal_loading').modal('toggle');
	var request_url = "msg.php?request_by_ajax=1"
		+"&loader=1"
		+"&req_type=onclkSubmitMsg"
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
			$('#div_msg').prepend(response.responseText);
			$('#div_msg blockquote').eq(0).hide().show('slide',{ direction: "left" });
			$('#txt_myMsg').val('');
			$('#modal_loading').modal('toggle');
		}
	});
}

function onclkDeleteMsg(msgId) {
	var request_url = "msg.php?request_by_ajax=1"
		+"&loader=1"
		+"&req_type=onclkDeleteMsg"
		+"&msgId="+msgId;

	$.ajax({
		type: "POST",
		url: request_url,
		cache: false,
		async : false,
		error: function(xhr){
			alert("[xxx] xmlHttp Failure!!");
		},
		complete: function(response){
			$('#div_msg').html(response.responseText);
		}
	});
}

function updateAvatar() {
	$(".cls_avatar").each(function() {
		var obj = $(this);
		var tmp_name = obj.attr('userName');
		if(obj_cache[tmp_name]) {
			obj.html(obj_cache[tmp_name]);
		} else {
			var request_url = "msg.php?request_by_ajax=1"
				+"&loader=1"
				+"&req_type=updateAvatar"
				+"&userName="+obj.attr('userName');
		
			$.ajax({
				type: "POST",
				url: request_url,
				cache: false,
				async : false,
				error: function(xhr){
					alert("[xxx] xmlHttp Failure!!");
				},
				complete: function(response){
					obj.html(response.responseText);
					obj_cache[tmp_name] = response.responseText;
				}
			});
		}
	});
}