function onclkSubmit() {
	var request_url = "admin.php?request_by_ajax=1"
		+"&loader=1"
		+"&req_type=onclkSubmit"
		+"&txt_myMsg="+encodeURIComponent($('#txt_myMsg').val().replace(/\n/g, "<br/>"));

	$.ajax({
		type: "POST",
		url: request_url,
		cache: false,
		async : false,
		error: function(xhr){
			alert("[xxx] xmlHttp Failure!!");
		},
		complete: function(response){
			alert("done!");
		}
	});
}