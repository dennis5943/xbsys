$(document).ready(function() {
	$('#div_cal').fullCalendar({
		// put your options and callbacks here
	    events: {
	        url: './calendarFeed.php',
	        type: 'POST',
	        data: {
	            custom_param1: 'something',
	            custom_param2: 'somethingelse'
	        },
	        error: function() {
	            alert('there was an error while fetching events!');
	        },
	    },
		eventRender: function (event, element) {
			element.qtip({    
				content: {    
	                title: { text: "<h1 style='text-align:center'>" + ($.fullCalendar.formatDate(event.start, 'hh:mmtt')) + "</h1>" + event.title },
	                text: "<div style='text-align:right;font-style:italic'>By " + event.userName + '</div><span class="title">備註: </span>' + event.description
            	},
				position: { 
					corner: { 
						tooltip: 'leftTop',
					},
					target: 'mouse',
					adjust: { mouse: true }
				},
				show: { 
					effect: function() { 
						$(this).fadeTo(200, 0.7);
					}
				},
				style: { 
					name: 'dark',
					tip: {
						corner: 'leftTop',
						color: '#333333',
						size: {
							x: 16, // Be careful that the x and y values refer to coordinates on screen, not height or width.
							y : 9 // Depending on which corner your tooltip is at, x and y could mean either height or width!
						
						}
					}
				}
            })
        },
		loading: function(bool) {
			if (bool) $('#modal_loading').modal('toggle');
			else $('#modal_loading').modal('toggle');
		}
	})
});