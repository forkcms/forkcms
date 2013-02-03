/**
 * Theme related JS
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsFrontend.theme = {
	init: function() {
		jsFrontend.theme.aria();
		jsFrontend.theme.hijackSubmit();
		jsFrontend.theme.scrollTo();

		// insert your code here
	},

	aria: function() {
		$('*[required]').attr('aria-required', 'true');
		$('.error input').attr('aria-invalid', 'true');
	},

	// add a loading class on the submit-button when a form is submitted
	hijackSubmit: function() {
		$('form').on('submit', function(e) {
			$(this).find('input:submit').addClass('loading disabled');
		});
	},

	scrollTo: function() {
		$('a.backToTop').on('click', function(e) {
			$('html, body').stop().animate({
				scrollTop: 0
			}, 600);
		});

		$(document).on('click', 'a[href*="#"]', function(e) {
			var $anchor = $(this),
				hash = $(this).attr('href'),
				url = hash.substr(0, hash.indexOf('#'));
			hash = hash.substr(hash.indexOf('#'));

			// check if we have an url, and if it is on the current page and the element exists
			if((url == '' || url.indexOf(document.location.pathname) >= 0) && $(hash).length > 0) {
				$('html, body').stop().animate({
					scrollTop: $(hash).offset().top
				}, 600);
			}
		});
	}
}

$(jsFrontend.theme.init);