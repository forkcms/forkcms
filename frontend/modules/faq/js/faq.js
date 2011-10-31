if(!jsFrontend) { var jsFrontend = new Object(); }


/**
 * Interaction for the faq module
 *
 * @author	Annelies Van Extergem <annelies@netlash.com>
 * @author	Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
jsFrontend.faq =
{
	// init, something like a constructor
	init: function()
	{
		if($('#faqFeedbackForm').length > 0) jsFrontend.faq.feedback.init();
	},

	// end
	eoo: true
}


// feedback form
jsFrontend.faq.feedback =
{
	init: function()
	{
		// usefull status has been changed
		$('#usefullY, #usefullN').bind('click', function()
		{
			// get usefull status
			var usefull = ($('#usefullY').attr('checked') ? true : false);
			
			// show or hide the form
			if(usefull) { $('form#feedback').submit(); }
			else { $('#feedbackNoInfo').show(); }
		});
	},

	// end
	eoo: true
}


$(document).ready(function() { jsFrontend.faq.init(); });