if(!jsFrontend) { var jsFrontend = new Object(); }


/**
 * Interaction for the faq module
 *
 * @author	Annelies Van Extergem <annelies@netlash.com>
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
			if(usefull) { $('#feedbackNoInfo').hide(); }
			else { $('#feedbackNoInfo').show(); }

			// update the feedback
			$.ajax(
			{
				url: '/frontend/ajax.php?module=faq&action=update_feedback&language={$LANGUAGE}',
				data: 'question_id=' + $('#questionId').val() + '&usefull=' + (usefull ? 'Y' : 'N'),
				success: function(json, textStatus)
				{
					if(json.code != 200)
					{
						// show error if needed
						if(jsFrontend.debug) alert(textStatus);
					}
				}
			});
		});
	},

	_eoo: true
}


$(document).ready(function() { jsFrontend.faq.init(); });