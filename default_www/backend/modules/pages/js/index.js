if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.pages = {
	init: function() {
		$('#tree ul').tree();
	},
	
	// end
	_eoo: true
}

$(document).ready(function() { jsBackend.pages.init(); });