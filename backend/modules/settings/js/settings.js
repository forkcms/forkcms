/**
 * Interaction for the settings index-action
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.settings =
{
	init: function()
	{
		$('#facebookAdminIds').multipleTextbox(
		{
			emptyMessage: '{$msgNoAdminIds}',
			addLabel: '{$lblAdd|ucfirst}',
			removeLabel: '{$lblDelete|ucfirst}',
			canAddNew: true
		});

		$('#testEmailConnection').on('click', jsBackend.settings.testEmailConnection);

		$('#activeLanguages input:checkbox').on('change', jsBackend.settings.changeActiveLanguage).change();
	},

	changeActiveLanguage: function(e)
	{
		var $this = $(this);

		// only go on if the item isn't disabled by default
		if(!$this.attr('disabled'))
		{
			// grab other element
			var $other = $('#' + $this.attr('id').replace('active_', 'redirect_'));

			if($this.is(':checked')) $other.attr('disabled', false);
			else $other.attr('checked', false).attr('disabled', true);
		}
	},

	testEmailConnection: function(e)
	{
		// prevent default
		e.preventDefault();

		$spinner = $('#testEmailConnectionSpinner');
		$error = $('#testEmailConnectionError');
		$success = $('#testEmailConnectionSuccess');
		$email = $('#settingsEmail');

		// show spinner
		$spinner.show();

		// hide previous results
		$error.hide();
		$success.hide();

		// fetch email parameters
		var settings = new Object();
		$.each($email.serializeArray(), function() { settings[this.name] = this.value; });

		// make the call
		$.ajax(
		{
			data: $.extend({ fork: { action: 'test_email_connection' } }, settings),
			success: function(data, textStatus)
			{
				// hide spinner
				$spinner.hide();

				// show success
				if(data.code == 200) $success.show();
				else $error.show();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				// hide spinner
				$spinner.hide();

				// show error
				$error.show();
			}
		});
	}
}

$(jsBackend.settings.init);