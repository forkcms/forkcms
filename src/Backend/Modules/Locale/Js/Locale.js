/**
 * Interaction for the locale module
 *
 * @author	Thomas Deceuninck <thomas@fronto.be>
 */
jsBackend.translations =
{
	init: function()
	{
		jsBackend.translations.controls.init();
	}
}

jsBackend.translations.controls =
{
	init: function()
	{
		if($('select#application').length > 0 && $('select#module').length > 0)
		{
			// bind
			$('select#application').on('change', jsBackend.translations.controls.enableDisableModules);

			// call to start
			jsBackend.translations.controls.enableDisableModules();
		}

		if($('.dataGrid td.translationValue').length > 0)
		{
			// bind
			$('.dataGrid td.translationValue').inlineTextEdit(
			{
				params: { fork: { action: 'save_translation' } },
				tooltip: jsBackend.locale.msg('ClickToEdit'),
				afterSave: function(item)
				{
					if(item.find('span:empty').length == 1) item.addClass('highlighted');
					else item.removeClass('highlighted');
				}
			});

			// highlight all empty items
			$('.dataGrid td.translationValue span:empty').parents('td.translationValue').addClass('highlighted');
		}
	},

	enableDisableModules: function()
	{
		// frontend can't have specific module
		if($('select#application').val() == 'frontend')
		{
			// set all modules disabled
			$('select#module option').prop('disabled', true);

			// enable core
			$('select#module option[value=core]').prop('disabled', false).prop('selected', true);
		}

		// remove the disabled stuff
		else
		{
			$('select#module option').prop('disabled', false);
		}
	}
}

$(jsBackend.translations.init);