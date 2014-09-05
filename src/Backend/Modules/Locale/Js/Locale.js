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
				params: { fork: { action: 'SaveTranslation' } },
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

        // when clicking on the check which checkboxes are checked, and add the id's of the translations to the querystring
        $('.iconExport').click(function(e){

            e.preventDefault();

            var labels = new Array();

            $('.dataGridHolder .dataGridHolder input[type="checkbox"]:checked').closest('tr').find('.translationValue').each(function(e){
                labels.push($(this).attr('data-numeric-id'));
            });
            var url = $(this).attr('href') + '&ids=' + labels.join('|');

            window.location.href = url;
        });
	},

	enableDisableModules: function()
	{
		// frontend can't have specific module
		if($('select#application').val() == 'Frontend')
		{
			// set all modules disabled
			$('select#module option').prop('disabled', true);

			// enable core
			$('select#module option[value=Core]').prop('disabled', false).prop('selected', true);
		}

		// remove the disabled stuff
		else
		{
			$('select#module option').prop('disabled', false);
		}
	}
}

$(jsBackend.translations.init);
