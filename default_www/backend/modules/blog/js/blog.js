if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the blog module
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.blog =
{
	// init, something like a constructor
	init: function()
	{
		jsBackend.blog.controls.init();
		jsBackend.blog.category.init();

		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	},


	// end
	eoo: true
}


jsBackend.blog.category =
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


jsBackend.blog.controls =
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


$(document).ready(jsBackend.blog.init);