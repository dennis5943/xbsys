$(document).ready(function() {
	updateFriend();
});

function updateFriend() {
	$(".gt").each(function() {
		var obj = $(this);
		var gamerTag = $(this).html();
		var request_url = "friend.php?request_by_ajax=1"
			+"&loader=1"
			+"&req_type=updateFriend"
			+"&gamerTag="+gamerTag;
	
		$.ajax({
			type: "POST",
			url: request_url,
			cache: false,
			async : false,
			error: function(xhr){
				alert("[xxx] xmlHttp Failure!!");
			},
			complete: function(response){
				var strJ = JSON.parse( response.responseText );
				obj.siblings('.AvatarBody').html("<img src='"+strJ.AvatarBody+"'>");
				obj.siblings('.GamerScore').html(strJ.GamerScore);
				obj.siblings('.OnlineStatus').html(strJ.OnlineStatus);
				obj.siblings('.Bio').html(strJ.Bio);
			}
		});
	});
}