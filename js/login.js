function onclkLogin() {
	var request_url = "login.php?request_by_ajax=1"
		+"&loader=1"
		+"&req_type=onclkLogin"
		+"&txt_account="+$('#txt_account').val()
		+"&txt_pass="+$('#txt_pass').val();

	$.ajax({
		type: "POST",
		url: request_url,
		cache: false,
		async : false,
		error: function(xhr){
			alert("[xxx] xmlHttp Failure!!");
		},
		complete: function(response){
			var vardb = response.responseText;
			if(parseInt(vardb) == 0) {
				alert('password wrong...');
			} else {
				doLogin($('#txt_account').val(),$('#txt_pass').val());
			}
		}
	});
}

function doLogin(acc,pass) {
	var request_url = "login.php?request_by_ajax=1"
		+"&loader=1"
		+"&req_type=doLogin"
		+"&txt_account="+$('#txt_account').val()
		+"&txt_pass="+$('#txt_pass').val();

	$.ajax({
		type: "POST",
		url: request_url,
		cache: false,
		async : false,
		error: function(xhr){
			alert("[xxx] xmlHttp Failure!!");
		},
		complete: function(response){
			document.location = "main.php";
		}
	});
}