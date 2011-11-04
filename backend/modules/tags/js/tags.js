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
			$('.dataGrid td.tag').inlineTextEdit({ saveParams: 'module=' + jsBackend.current.module + '&action=edit&language=' + jsBackend.current.language, tooltip: '{$msgClickToEdit}' });
		}
	}
}

$(jsBackend.tags.init);