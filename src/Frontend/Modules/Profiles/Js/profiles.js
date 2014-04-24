/**
 * Interaction for the profiles module
 *
 * @author	Thomas Deceuninck <thomas@fronto.be>
 */
jsFrontend.profiles = {
	/**
	 * Kind of constructor
	 */
	init: function()
	{
		jsFrontend.profiles.showPassword();
	},

	/**
	 * Make possible to show passwords in clear text
	 */
	showPassword: function()
	{
		// checkbox showPassword is clicked
		$('#showPassword').on('click', function()
		{
			// checkbox is checked
			if($(this).is(':checked'))
			{
				// clone password and change type
				$('.showPasswordInput').clone().attr('type', 'input').insertAfter($('.showPasswordInput'));

				// remove original
				$('.showPasswordInput:first').remove();
			}

			// checkbox not checked
			else
			{
				// clone password and change type
				$('.showPasswordInput').clone().attr('type', 'password').insertAfter($('.showPasswordInput'));

				// remove original
				$('.showPasswordInput:first').remove();
			}
		});
	}
}

$(jsFrontend.profiles.init);