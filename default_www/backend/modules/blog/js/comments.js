if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.blog = {
	init: function() {
		jsBackend.blog.comments.init();
	},
	
	// end
	eoo: true
}

jsBackend.blog.comments = {
	init: function() {
	},
	
	// end
	eoo: true
}

$(document).ready(function() { jsBackend.blog.init(); });