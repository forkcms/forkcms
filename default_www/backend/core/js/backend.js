if(!jsBackend) { var jsBackend = new Object(); }

jsBackend = {
	// datamembers
	
	// functions
	bindConfirmation: function() {
		$('.askConfirmation').bind('click', function(evt) { return confirm($(this).attr('rel')); });
	},
		
	bindFadeOutAfterMouseMove: function() {
		// reports have to disappear after a mouse-move
		$(document.body).bind('mousemove', function(evt) { $('.fadeOutAfterMouseMove').fadeOut(2500); });		
	},
	
	bindHilight: function() {
		// if a hilightId was given, we should hilight it
		if(typeof hilightId != 'undefined') {
			var selector = hilightId;
			if($(hilightId)[0].tagName.toLowerCase == 'tr') { selector += ' td'; } 
			$(selector).highlightFade({color: '#FFFF88', speed: 2500});
		}		
	},
		
	// end
	_eoo: true
}

$(document).ready(function() {
	jsBackend.bindConfirmation();
	jsBackend.bindFadeOutAfterMouseMove();
	jsBackend.bindHilight();
});