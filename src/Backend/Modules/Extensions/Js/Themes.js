/**
 * Interaction for the pages templates
 */
jsBackend.extensions =
{
	init: function()
	{
		jsBackend.extensions.themeSelection.init();
	}
};

jsBackend.extensions.themeSelection =
{
	init: function()
	{
		// store the list items
		var listItems = $('#installedThemes .js-theme');

		// one of the templates (ie. hidden radiobuttons) in the templateSelection <ul> are clicked
		listItems.on('click', function(e)
		{
			// store the object
			var radiobutton = $(this).find('input:radio:first');

			// set checked
			radiobutton.prop('checked', true);

			// if the radiobutton is checked
			if(radiobutton.is(':checked'))
			{
				// remove the selected state from all other templates
				listItems.find('.panel').removeClass('panel-primary').addClass('panel-default');

				// add a selected state to the parent
				radiobutton.closest('.panel').addClass('panel-primary');
			}
		});
	}
};

$(jsBackend.extensions.init);
