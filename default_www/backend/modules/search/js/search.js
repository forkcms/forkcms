if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.search = 
{
	// init, something like a constructor
	init: function() 
	{
		// synonyms box
		if($('input.synonymBox').length > 0) 
		{ 
			$('input.synonymBox').multipleTextbox({ 
				emptyMessage: '{$msgNoSynonymsBox}', 
				addLabel: '{$lblAdd|ucfirst}', 
				removeLabel: '{$lblDeleteSynonym|ucfirst}' 
			}); 
		}

		// settings enable/disable
		$('#searchModules input[type=checkbox]').change(function() 
		{
			if($(this).is(':checked')) { $('#' + $(this).attr('id') + 'Weight').removeAttr('disabled').removeClass('disabled'); }
			else { $('#' + $(this).attr('id') + 'Weight').attr('disabled', 'disabled').addClass('disabled'); }
		});
	},


	// end
	eoo: true
}

$(document).ready(function() { jsBackend.search.init(); });