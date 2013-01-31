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
			async : true,
			error: function(xhr){
				alert("[xxx] xmlHttp Failure!!");
			},
			complete: function(response){
				var strJ = JSON.parse( response.responseText );
				obj.siblings('.AvatarBody').html("<img src='"+strJ.AvatarBody+"'>");
				obj.siblings('.AvatarBody').find('img').qtip({    
					content: {    
		                title: { text: "<h1 style='text-align:center'>" + strJ.Motto +"</h1>"},
		                text: "<div style='text-align:right;font-style:italic'>" + strJ.Bio +"</div>"
	            	},
					position: { 
						corner: { 
							target: 'topRight',
							tooltip: 'leftMiddle'
						},
						target: false
					},
					show: { 
						ready: true,
						when: {
							event: false
						}
					},
					hide: {
						fixed: true,
						when: {
							event: false
						}
					},
					style: { 
						name: 'dark',
						tip: {
							corner: 'bottomLeft',
							color: '#333333',
							size: {
								x: 16, // Be careful that the x and y values refer to coordinates on screen, not height or width.
								y : 9 // Depending on which corner your tooltip is at, x and y could mean either height or width!
							
							}
						}
					}
            	})
				obj.siblings('.GamerScore').html(strJ.GamerScore);
				obj.siblings('.OnlineStatus').html(strJ.OnlineStatus);
			}
		});
	});
}