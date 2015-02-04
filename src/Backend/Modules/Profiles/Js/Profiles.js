/**
 * Interaction for the profiles module
 *
 * @author Thomas Deceuninck <thomas@fronto.be>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
jsBackend.profiles =
{
	init: function()
	{
		jsBackend.profiles.addToGroup.init();
		jsBackend.profiles.settings.init();
		jsBackend.profiles.editEmail.init();
		jsBackend.profiles.editPassword.init();
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
	},

	editEmail:
	{
		init: function()
		{
			if ($('#newEmailBox').length == 0) return false;

			$('#newEmail').on('change', function() {
				jsBackend.profiles.editEmail.toggleBox();
			});

			jsBackend.profiles.editEmail.toggleBox();
		},

		toggleBox: function()
		{
			var $item = $('#newEmail');
			var checked = ($item.attr('checked') == 'checked');

			$('#newEmailBox').toggle(checked);
		}
	},

	editPassword:
	{
		init: function()
		{
			if ($('#newPasswordBox').length == 0) return false;

			$('#newPassword').on('change', function() {
				jsBackend.profiles.editPassword.toggleBox();
			});

			jsBackend.profiles.editPassword.toggleBox();
		},

		toggleBox: function()
		{
			var $item = $('#newPassword');
			var checked = ($item.attr('checked') == 'checked');

			$('#newPasswordBox').toggle(checked);
		}
	},

	settings:
	{
		init: function()
		{
			if ($('#sendNewProfileAdminMail').length == 0) return false;

			$('#sendNewProfileAdminMail').on('change', function() {
				jsBackend.profiles.settings.toggleAdminMail();
			});

			$('#overwriteProfileNotificationEmail').on('change', function() {
				jsBackend.profiles.settings.toggleProfileNotificationEmail();
			});

			jsBackend.profiles.settings.toggleAdminMail();
			jsBackend.profiles.settings.toggleProfileNotificationEmail();
		},

		toggleAdminMail: function()
		{
			var $item = $('#sendNewProfileAdminMail');
			var checked = ($item.attr('checked') == 'checked');

			$('#overwriteProfileNotificationEmailBox').toggle(checked);
		},

		toggleProfileNotificationEmail: function()
		{
			var $item = $('#overwriteProfileNotificationEmail');
			var checked = ($item.attr('checked') == 'checked');

			$('#profileNotificationEmailBox').toggle(checked);
		}
	}
};

$(jsBackend.profiles.init);
