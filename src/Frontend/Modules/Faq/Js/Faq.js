/**
 * Interaction for the faq module
 *
 * @author	Annelies Van Extergem <annelies@netlash.com>
 * @author	Jelmer Snoeck <jelmer@siphoc.com>
 * @author	Thomas Deceuninck <thomas@fronto.be>
 * @author	Jeroen Desloovere <jeroen@siesqo.be>
 */
jsFrontend.faq =
{
	// init, something like a constructor
	init: function()
	{
		if($('#faqFeedbackForm').length > 0) jsFrontend.faq.feedback.init();
	}
};

// feedback form
jsFrontend.faq.feedback =
{
	init: function()
	{
		// useful status has been changed
		$('#usefulY, #usefulN').on('click', function()
		{
			// init useful status
			var useful = false;

			if ($(this).attr('id') == 'usefulY') {
				// get useful status
				useful = ($("#faqFeedbackForm input[type='radio']:checked").val() === 'Y');
			}

			// show or hide the form
			if (useful) {
				$('#message').prop('required', false);
				$('form#feedback').submit();
			} else {
				$('#feedbackNoInfo').show();
				$('#message').prop('required', true);
			}
		});
	}
};

$(jsFrontend.faq.init);
