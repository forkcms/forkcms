/**
 * Interaction for the MailMotor module
 */
jsBackend.MailMotor =
{
	// init, something like a constructor
	init: function()
	{
		jsBackend.MailMotor.controls.init();
	}
};

jsBackend.MailMotor.controls =
{
	init: function()
	{
		// stop here because mail engine not found
		if ($('#settings_mailEngine').length === 0) {
			return;
		}

		// bind change to mailEngine dropdown
		$('#settings_mailEngine').on('change', function(){
			// define selected value
			var selectedValue = $(this).find('option:selected').val();

			// toggle api key and list id
			$('.mail-engine-selected').toggle(selectedValue !== 'not_implemented');
		}).trigger('change');
	}
};

$(jsBackend.MailMotor.init);
