function onclkLogout() {
	var request_url = "login.php?request_by_ajax=1"
		+"&loader=1"
		+"&req_type=onclkLogout";

	$.ajax({
		type: "POST",
		url: request_url,
		cache: false,
		async : false,
		error: function(xhr){
			alert("[xxx] xmlHttp Failure!!");
		},
		complete: function(response){
			document.location = "login.php";
		}
	});
}