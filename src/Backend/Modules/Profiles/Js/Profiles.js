/**
 * Interaction for the profiles module
 *
 * @author	Thomas Deceuninck <thomas@fronto.be>
 */
jsBackend.Profiles =
{
	init: function()
	{
		jsBackend.Profiles.massAddToGroup.init();
	},
	massAddToGroup:
	{
		init: function()
		{
			// update the hidden input for the new group's ID with the remembered value
			var $txtNewGroup = $('input[name="newGroup"]').val();

			// clone the groups SELECT into the "add to group" mass action dialog
			console.log($('select[name="group"]'));
			$('.jsMassActionAddToGroupSelectGroup').replaceWith(
				$('select[name="group"]')
					.clone(true)
					.removeAttr('id')
					.attr('name', 'newGroup')
					.on('change', function() {
						// update the hidden input for the new group's ID with the current value
						$txtNewGroup.val(this.value);
					})
			);
		}
	}
};

$(jsBackend.Profiles.init);
