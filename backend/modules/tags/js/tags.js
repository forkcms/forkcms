if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the tags module
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.tags =
{
	// init, something like a constructor
	init: function()
	{
		if($('.dataGrid td.tag').length > 0)
		{
			// bind
			$('.dataGrid td.tag').inlineTextEdit({ params: { fork: { action: 'edit' } }, tooltip: '{$msgClickToEdit}' });
		}
	},


	// end
	eoo: true
}


$(document).ready(jsBackend.tags.init);