if(!jsBackend) { var jsBackend = new Object(); }


/**
 * All methods related to the search
 *
 * @author	Matthias Mullie <matthias@mullie.eu>
 */
jsBackend.search =
{
	// init, something like a constructor
	init: function()
	{
		// synonyms box
		if($('input.synonymBox').length > 0)
		{
			$('input.synonymBox').multipleTextbox(
			{
				emptyMessage: '{$msgNoSynonymsBox}',
				addLabel: '{$lblAdd|ucfirst}',
				removeLabel: '{$lblDeleteSynonym|ucfirst}'
			});
		}

		// settings enable/disable
		$('#searchModules input[type=checkbox]').change(function()
		{
			if($(this).is(':checked')) { $('#' + $(this).attr('id') + 'Weight').removeAttr('disabled').removeClass('disabled'); }
			else { $('#' + $(this).attr('id') + 'Weight').prop('disabled', true).addClass('disabled'); }
		});
	}
}

$(document).ready(jsBackend.search.init);