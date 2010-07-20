if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.search = {
	init: function() {
		// synonyms box
		if($('input.synonymBox').length > 0) { $('input.synonymBox').tagBox({ emptyMessage: '{$msgNoSynonymsBox}', addLabel: '{$lblAdd|ucfirst}', removeLabel: '{$lblDeleteSynonym|ucfirst}' }); }
		
		// settings enable/disable
		$('#searchModules > td > input[type=checkbox]').change(function()
		{
			if($(this).is(':checked')) $('#' + $(this).attr('id') + 'Weight').removeAttr('disabled').removeClass('disabled');
			else $('#' + $(this).attr('id') + 'Weight').attr('disabled', 'disabled').addClass('disabled');
		});
	}
}

$(document).ready(function() { jsBackend.search.init(); });