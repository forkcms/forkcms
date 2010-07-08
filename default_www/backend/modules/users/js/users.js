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
			// if the current value is the same as the one that would be generated then we bind the events
			if($('#nickname').val() == jsBackend.users.controls.calculateNick()) {
				// bind events
				$('#name').keyup(function() { $('#nickname').val(jsBackend.users.controls.calculateNick()); });
				$('#surname').keyup(function() { $('#nickname').val(jsBackend.users.controls.calculateNick()); });
			}
		}
	},
	
	// calculate the nickname
	calculateNick: function() {
		return utils.string.trim(utils.string.trim($('#name').val()) +' '+ utils.string.trim($('#surname').val()));
	},
	
	// end
	eoo: true
}

$(document).ready(function() { jsBackend.users.init(); });