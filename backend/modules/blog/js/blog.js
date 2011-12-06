/**
 * Interaction for the blog module
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.blog =
{
	// init, something like a constructor
	init: function()
	{
		// variables
		$title = $('#title');

		jsBackend.blog.controls.init();

		// do meta
		if($title.length > 0) $title.doMeta();
	}
}

jsBackend.blog.controls =
{
	currentCategory: null,

	// init, something like a constructor
	init: function()
	{
		// variables
		$saveAsDraft = $('#saveAsDraft');
		$filter = $('#filter');
		$filterCategory = $('#filter #category');
		$addCategoryDialog = $('#addCategoryDialog');
		$categoryTitle = $('#categoryTitle');
		$categoryTitleError = $('#categoryTitleError');
		$categoryId = $('#categoryId');

		$saveAsDraft.on('click', function(e)
		{
			$('form').append('<input type="hidden" name="status" value="draft" />').submit();
		});

		$filterCategory.on('change', function(e)
		{
			$filter.submit();
		});

		if($addCategoryDialog.length > 0)
		{
			$addCategoryDialog.dialog(
			{
				autoOpen: false,
				draggable: false,
				resizable: false,
				modal: true,
				buttons:
				{
					'{$lblOK|ucfirst}': function()
					{
						// hide errors
						$categoryTitleError.hide();

						$.ajax(
						{
							data:
							{
								fork: { action: 'add_category' },
								value: $('#categoryTitle').val()
							},
							success: function(json, textStatus)
							{
								if(json.code != 200)
								{
									// show error if needed
									if(jsBackend.debug) alert(textStatus);

									// show message
									$categoryTitleError.show();
								}
								else
								{
									// add and set selected
									$categoryId.append('<option value="'+ json.data.id +'">'+ json.data.title +'</option>');

									// reset value
									jsBackend.blog.controls.currentCategory = json.data.id;

									// close dialog
									$addCategoryDialog.dialog('close');
								}
							}
						});
					},

					'{$lblCancel|ucfirst}': function()
					{
						// close the dialog
						$(this).dialog('close');
					}
				},
				close: function(e, ui)
				{
					// reset value to previous selected item
					$categoryId.val(jsBackend.blog.controls.currentCategory);
				}
			});

			// bind change
			$categoryId.on('change', function(e)
			{
				// new category?
				if($(this).val() == 'new_category')
				{
					// prevent default
					e.preventDefault();

					// open dialog
					$addCategoryDialog.dialog('open');
				}

				// reset current category
				else jsBackend.blog.controls.currentCategory = $categoryId.val();
			});
		}

		jsBackend.blog.controls.currentCategory = $categoryId.val();
	}
}

$(jsBackend.blog.init);