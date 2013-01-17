function onclkSubmit() {
	if($('#txt_passNewAgain').val() != $('#txt_passNew').val()) {
		alert("2次密碼不同啊");
		return false;
	}
	
	var request_url = "chgpass.php?request_by_ajax=1"
		+"&loader=1"
		+"&req_type=onclkSubmit"
		+"&txt_passNew="+encodeURIComponent($('#txt_passNew').val());

	$.ajax({
		type: "POST",
		url: request_url,
		cache: false,
		async : false,
		error: function(xhr){
			alert("[xxx] xmlHttp Failure!!");
		},
		complete: function(response){
			alert("改好囉! 請牢記");
		}
	});
}