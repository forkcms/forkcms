if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.locale =
{
	init: function()
	{
		jsBackend.locale.controls.init();
	},


	// end
	eoo: true
}


jsBackend.locale.controls =
{
	init: function()
	{
		if($('select#application').length > 0 && $('select#module').length > 0)
		{
			// bind
			$('select#application').bind('change', jsBackend.locale.controls.enableDisableModules);

			// call to start
			jsBackend.locale.controls.enableDisableModules();
		}

		if($('.dataGrid td.translationValue').length > 0)
		{
			// bind
			$('.dataGrid td.translationValue').inlineTextEdit(
			{
				params: { fork: { module: jsBackend.current.module, action: 'save_translation', language: jsBackend.current.language } },
				tooltip: '{$msgClickToEdit}',
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

		// remove the disbaled stuff
		else
		{
			$('select#module option').prop('disabled', false);
		}
	},


	// end
	eoo: true
}


$(document).ready(jsBackend.locale.init);