if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.users = {
	init: function() {
		jsBackend.users.controls.init();
	},
	// end
	eoo: true
}

jsBackend.users.controls = {
	// somewhat like a constructor
	init: function() {
		jsBackend.users.controls.nick();
	},
	
	// set nick 
	nick: function() {
		// are all elements available
		if($('#nickname').length > 0 && $('#name').length > 0 && $('#surname').length > 0) {
			var change = true;
			
			// if the current value is the same as the one that would be generated then we bind the events
			if($('#nickname').val() != jsBackend.users.controls.calculateNick()) { change = false; }

			// bind events
			$('#name').keyup(function() { if(change) { $('#nickname').val(jsBackend.users.controls.calculateNick()); } });
			$('#surname').keyup(function() { if(change) { $('#nickname').val(jsBackend.users.controls.calculateNick()); } });
			
			// unbind events
			$('#nickname').keyup(function() { change = false; });
		}
	},
	
	// calculate the nickname
	calculateNick: function() {
		var maxLength = parseInt($('#nickname').attr('maxlength')); 
		if(maxLength == 0) maxLength = 255;
		
		return utils.string.trim(utils.string.trim($('#name').val()) +' '+ utils.string.trim($('#surname').val())).substring(0, maxLength);
	},
	
	// end
	eoo: true
}

$(document).ready(function() { jsBackend.users.init(); });