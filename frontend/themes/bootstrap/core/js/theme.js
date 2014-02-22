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
		jsFrontend.theme.cookieBar();

		// insert your code here
		jsFrontend.theme.removeImageDimensions();
	},

	aria: function() {
		$('*[required]').attr('aria-required', 'true');
		$('.error input').attr('aria-invalid', 'true');
	},

	//
	cookieBar: function() {
		$cookieBar = $('#cookieBar');

		// hide the cookie if needed
		if(utils.cookies.readCookie('cookie_bar_hide') == 'b%3A1%3B') {
			$cookieBar.hide();
		}

		$cookieBar.on('click', '#cookieBarAgree', function(e) {
			utils.cookies.setCookie('cookie_bar_agree', 'b:1;');
			utils.cookies.setCookie('cookie_bar_hide', 'b:1;');
			$cookieBar.alert('close');
		});
		$cookieBar.on('click', '#cookieBarDisagree', function(e) {
			utils.cookies.setCookie('cookie_bar_agree', 'b:0;');
			utils.cookies.setCookie('cookie_bar_hide', 'b:1;');
			$cookieBar.alert('close');
		});
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
	},

	removeImageDimensions: function() {
    $('img').each(function() {
      $(this).removeAttr('style');
    });
	}
}

$(jsFrontend.theme.init);