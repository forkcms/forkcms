/**
 * Interaction for the blog module
 *
 * @author Jeroen Van den Bossche <jeroen.vandenbossche@wijs.be>
 */
jsFrontend.blog =
{
	init: function()
	{
		$('.markInappropriate').on('click', jsFrontend.blog.markInappropriateComment);
	},

	// mark a comment as inappropriate.
	markInappropriateComment: function()
	{
		var $this = $(this);

		$.ajax(
		{
			data:
			{
				fork: { module: 'blog', action: 'mark_inappropriate' },
				id: $this.data('id')
			},
			success: function(data, textStatus, jqXHR)
			{
				if(data.code == 200)
				{
					$this.replaceWith(data.message);
				}
			}
		});

		return false;
	}
}

$(jsFrontend.blog.init);
