if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the settings index-action
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.settings =
{
	/**
	 * Kind of constructor
	 */
	init: function()
	{
		$('#facebookAdminIds').multipleTextbox(
		{
			emptyMessage: '{$msgNoAdminIds}',
			addLabel: '{$lblAdd|ucfirst}',
			removeLabel: '{$lblDelete|ucfirst}',
			canAddNew: true
		});

		if($('#testEmailConnection').length > 0) $('#testEmailConnection').bind('click', jsBackend.settings.testEmailConnection);

	},


	testEmailConnection: function(evt)
	{
		// prevent default
		evt.preventDefault();

		// show spinner
		$('#testEmailConnectionSpinner').show();
		$('#testEmailConnectionError').hide();
		$('#testEmailConnectionSuccess').hide();

		// fetch email parameters
		var settings = new Object();
		$.each($('#settingsEmail').serializeArray(), function() { settings[this.name] = this.value; });

		// make the call
		$.ajax(
		{
			data: $.extend({ fork: { action: 'test_email_connection' } }, settings),
			success: function(data, textStatus)
			{
				// hide spinner
				$('#testEmailConnectionSpinner').hide();

				// show success
				if(data.code == 200) $('#testEmailConnectionSuccess').show();
				else $('#testEmailConnectionsError').show();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				// hide spinner
				$('#testEmailConnectionSpinner').hide();

				// show error
				$('#testEmailConnectionError').show();
			}
		});
	}
}

$(document).ready(jsBackend.settings.init);