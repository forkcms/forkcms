/**
 * Interaction for the MailMotor moduleJobCurrentState
 */
jsBackend.mailmotor =
{
	// init, something like a constructor
	init: function()
	{
		jsBackend.mailmotor.controls.init();
	}
};

jsBackend.mailmotor.controls =
{
	// init, something like a constructor
	init: function()
	{
		// stop here because mail engine not found
		if ($('#mailEngine').length == 0) {
			return false;
		}

		// bind change to mailEngine dropdown
		$('#mailEngine').on('change', function(){
			// define selected value
			var selectedValue = $(this).find('option:selected').val();

			// toggle api key and list id
			$('.mail-engine-selected').toggle(selectedValue !== '');
		}).trigger('change');
	}
};

$(jsBackend.mailmotor.init);
