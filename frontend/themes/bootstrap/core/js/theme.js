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
			$(this).find('input:submit').addClass('loading');
		});
	},

	scrollTo: function() {
		$(document).on('click', 'a[href*="#"]', function(e) {
			var $anchor = $(this),
				hash = $(this).attr('href'),
				url = hash.substr(0, hash.indexOf('#'));
			hash = hash.substr(hash.indexOf('#'));

			// if it is just the hash, we should reset it to body, which will make it scroll to the top of the page.
			if(hash == '#') hash = 'body';

			// check if we have an url, and if it is on the current page and the element exists
			if((url == '' || url.indexOf(document.location.pathname) >= 0) && $(hash).length > 0) {
				$('html, body').stop().animate({
					scrollTop: $(hash).offset().top
				}, 1000);
			}
		});
	}
}

$(jsFrontend.theme.init);