/**
 * All methods related to the search
 *
 * @author	Matthias Mullie <forkcms@mullie.eu>
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
				emptyMessage: jsBackend.locale.msg('NoSynonymsBox'),
				addLabel: utils.string.ucfirst(jsBackend.locale.lbl('Add')),
				removeLabel: utils.string.ucfirst(jsBackend.locale.lbl('DeleteSynonym'))
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
