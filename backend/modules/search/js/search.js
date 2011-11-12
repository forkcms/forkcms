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
		$synonymBox = $('input.synonymBox');

		// synonyms box
		if($synonymBox.length > 0)
		{
			$synonymBox.multipleTextbox(
			{
				emptyMessage: '{$msgNoSynonymsBox}',
				addLabel: '{$lblAdd|ucfirst}',
				removeLabel: '{$lblDeleteSynonym|ucfirst}'
			});
		}

		// settings enable/disable
		$('#searchModules input[type=checkbox]').on('change', function()
		{
			$this = $(this);

			if($this.is(':checked')) { $('#' + $this.attr('id') + 'Weight').removeAttr('disabled').removeClass('disabled'); }
			else { $('#' + $this.attr('id') + 'Weight').prop('disabled', true).addClass('disabled'); }
		});
	}
}

$(jsBackend.search.init);