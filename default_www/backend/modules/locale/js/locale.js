if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.locale = {
	init: function() {
		jsBackend.locale.fixLayout.init();
	},
	// end
	eoo: true
}

jsBackend.locale.fixLayout = {
	init: function() {

		// add last child and first child for IE
		$('.datafilter tbody td:first-child').addClass('firstChild')
		$('.datafilter tbody td:last-child').addClass('lastChild')

		jsBackend.locale.fixLayout.equalHeight($(".datafilter tbody .options"));

	},
	equalHeight: function(group) {

		// make sure all options are the same height (eq height cols)
		var tallest = 0;
		group.each(function() {
			var thisHeight = $(this).height();
			if(thisHeight > tallest) {
				tallest = thisHeight;
			}
		});
		group.height(tallest);

	},
	
	// end
	eoo: true
}

$(document).ready(function() { jsBackend.locale.init(); });