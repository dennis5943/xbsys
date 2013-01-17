$(document).ready(function() {
	$("tr[trigger=1][status=1]").live("click", function(){
		$("tr[inx="+$(this).attr('inx')+"][trigger!=1][status=1]").toggle();
	});
	$("tr[trigger=1][status=2]").live("click", function(){
		$("tr[inx="+$(this).attr('inx')+"][trigger!=1][status=2]").toggle();
	});
	$("[rel='tooltip']").tooltip();
});

// choose join or not
function onclickChoose(arrangeId) {
	var isJoin = 0;
	// get choose status
	var classBtn = $(".btn.active[inx="+arrangeId+"]").attr('class');
	if(classBtn.indexOf("success") > -1) {
		isJoin = 1;
	} else {
		isJoin = 0;
	}
	var request_url = "schedue.php?request_by_ajax=1"
		+"&loader=1"
		+"&req_type=onclickChoose"
		+"&arrangeId="+arrangeId
		+"&isJoin="+isJoin.toString();

	$.ajax({
		type: "POST",
		url: request_url,
		cache: false,
		async : false,
		error: function(xhr){
			alert("[xxx] xmlHttp Failure!!");
		},
		complete: function(response){
			document.location = "schedue.php";
		}
	});
}