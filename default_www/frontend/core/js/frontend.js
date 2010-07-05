if(!jsFrontend) { var jsFrontend = new Object(); }

jsFrontend = {
	// datamembers
	debug: false,
	
	// init, something like a constructor
	init: function() {
		// init stuff
		jsFrontend.initAjax();
		jsFrontend.gravatar.init();
	},
	
	// init
	initAjax: function() {
		// set defaults for AJAX
		$.ajaxSetup({ cache: false, type: 'POST', dataType: 'json', timeout: 10000 });
	},
	
	// end
	eof: true
}	

jsFrontend.gravatar = {
	// init, something like a constructor
	init: function() {
		$('.replaceWithGravatar').each(function() {
			var element = $(this);
			var gravatarId = element.attr('rel');
			var size = element.attr('height');
			// valid gravatar id
			if(gravatarId != '') {
				// build url
				var url = 'http://www.gravatar.com/avatar/'+ gravatarId + '?r=g&d=404';
				// add size if set before
				if(size != '') url += '&s=' + size;
				// create new image
				var gravatar = new Image();
				gravatar.src = url;
				// reset src
				gravatar.onload = function() { element.attr('src', url).addClass('gravatarLoaded'); }
			}
		});
	},
	
	// end
	eof: true
}

$(document).ready(function() { jsFrontend.init(); });