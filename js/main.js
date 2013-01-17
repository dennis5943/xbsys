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
		loading: function(bool) {
			if (bool) $('#modal_loading').modal('toggle');
			else $('#modal_loading').modal('toggle');
		}
	})
});