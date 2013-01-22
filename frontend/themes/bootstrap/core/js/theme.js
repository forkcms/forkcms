/**
 * Theme related JS
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsFrontend.theme = {
	init: function() {
		jsFrontend.theme.hijackSubmit();

		// insert your code here
	},

	// add a loading class on the submit-button when a form is submitted
	hijackSubmit: function() {
		$('form').on('submit', function(e) {
			$(this).find('input:submit').addClass('loading');
		});
	}
}

$(jsFrontend.theme.init);