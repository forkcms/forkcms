if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.extensions =
{
	init: function()
	{
		jsBackend.extensions.themeSelection.init();
	},


	// end
	eoo: true
}


jsBackend.extensions.themeSelection =
{
	init: function()
	{
		// store the list items
		var listItems = $('#themeSelection li');

		// one of the templates (ie. hidden radiobuttons) in the templateSelection <ul> are clicked
		listItems.click(function(evt)
		{
			// prevent default
			evt.preventDefault();
			
			// store the object
			var radiobutton = $(this).find('input:radio:first');
			
			// set checked
			radiobutton.prop('checked', true);

			// if the radiobutton is checked
			if(radiobutton.is(':checked'))
			{
				// remove the selected state from all other templates
				listItems.removeClass('selected');

				// add a selected state to the parent
				radiobutton.parent('li').addClass('selected');
			}
		});
	},


	// end
	eoo: true
}


$(document).ready(jsBackend.extensions.init);