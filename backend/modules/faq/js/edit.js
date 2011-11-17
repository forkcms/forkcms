/**
 * Interaction for the faq categories
 *
 * @author	Lester Lievens <lester.lievens@netlash.com>
 * @author	Annelies Van Extergem <annelies@netlash.com>
 * @author	Davy Van Vooren <davy.vanvooren@netlash.com>
 */
jsBackend.faq.edit =
{
	// init, something like a constructor
	init: function()
	{
		// hide the data
		$('.longFeedback').hide();

		// add the click handler
		$(document).on('click', '.container', jsBackend.faq.edit.clickHandler);
	},

	clickHandler: function(e)
	{
		e.preventDefault();

		var link = $(this).find('a');

		// the action is currently closed, open it
		if(link.hasClass('iconCollapsed'))
		{
			// change css
			link.removeClass('iconCollapsed');
			link.addClass('iconExpanded');

			// show the feedback
			$(this).next('.longFeedback').show();
		}

		// the action is currently open, close it
		else
		{
			// change css
			link.addClass('iconCollapsed');
			link.removeClass('iconExpanded');

			// hide the feedback
			$(this).next('.longFeedback').hide();
		}
	}
}

$(jsBackend.faq.edit.init);