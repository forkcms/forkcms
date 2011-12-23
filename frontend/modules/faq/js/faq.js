/**
 * Interaction for the faq module
 *
 * @author	Annelies Van Extergem <annelies@netlash.com>
 * @author	Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsFrontend.faq =
{
	// init, something like a constructor
	init: function()
	{
		if($('#faqFeedbackForm').length > 0) jsFrontend.faq.feedback.init();
	}
}

// feedback form
jsFrontend.faq.feedback =
{
	init: function()
	{
		// useful status has been changed
		$('#usefulY, #usefulN').on('click', function()
		{
			// get useful status
			var useful = ($('#usefulY').attr('checked') ? true : false);

			// show or hide the form
			if(useful) { $('form#feedback').submit(); }
			else { $('#feedbackNoInfo').show(); }
		});
	}
}

$(jsFrontend.faq.init);