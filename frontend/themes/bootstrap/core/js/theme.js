/**
 * Theme related JS
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsFrontend.theme = {
	init: function() {
		jsFrontend.theme.aria();
		jsFrontend.theme.hijackSubmit();

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
	}
}

$(jsFrontend.theme.init);