/**
 * Interaction for the mailmotor
 *
 * @author	Thomas Deceuninck <thomas@fronto.be>
 */
jsBackend.profiles =
{
	init: function()
	{
		jsBackend.profiles.addToGroup.init();
	},

	addToGroup:
	{
		init: function()
		{
			// update the hidden input for the new group's ID with the remembered value
			var $txtNewGroup = $('input[name="newGroup"]').val(window.name);

			// clone the groups SELECT into the "add to group" mass action dialog
			$('#massAddToGroupListPlaceholder').replaceWith(
				$('select[name="group"]')
					.clone(true)
					.removeAttr('id')
					.attr('name', 'newGroup')
					.css('width', '90%')
					.on('change', function()
					{
						// update the hidden input for the new group's ID with the current value
						$txtNewGroup.val(this.value);

						// remember the last selected value for the current window
						window.name = this.value;
					})
					.val(window.name)
			);
		}
	}
}

$(jsBackend.profiles.init);