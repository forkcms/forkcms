if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the events module
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.events =
{
	// init, something like a constructor
	init: function()
	{
		jsBackend.events.controls.init();
		jsBackend.events.category.init();

		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	},


	// end
	eoo: true
}


jsBackend.events.category =
{
	// init, something like a constructor
	init: function()
	{
		if($('.datagrid td.name').length > 0)
		{
			// buil ajax-url
			var url = '/backend/ajax.php?module='+ jsBackend.current.module +'&action=edit_category&language='+ jsBackend.current.language;

			// bind
			$('.datagrid td.name').inlineTextEdit({ saveUrl: url, tooltip: '{$msgClickToEdit}' });
		}
	},


	// end
	eoo: true
}


jsBackend.events.controls =
{
	// init, something like a constructor
	init: function()
	{
		$('#saveAsDraft').click(function(evt)
		{
			$('form').append('<input type="hidden" name="status" value="draft" />');
			$('form').submit();
		});
	},


	// end
	eoo: true
}


$(document).ready(function() { jsBackend.events.init(); });