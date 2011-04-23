if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the blog module
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.blog =
{
	// init, something like a constructor
	init: function()
	{
		jsBackend.blog.controls.init();

		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	},


	// end
	eoo: true
}


jsBackend.blog.controls =
{
	currentCategory: null,
		
	// init, something like a constructor
	init: function()
	{
		$('#saveAsDraft').click(function(evt)
		{
			$('form').append('<input type="hidden" name="status" value="draft" />');
			$('form').submit();
		});
		
		$('#filter #category').change(function(evt)
		{
			$('#filter').submit();
		});
		
		if($('#addCategoryDialog').length > 0) {
			$('#addCategoryDialog').dialog(
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
							$('#categoryTitleError').hide();
							
							$.ajax(
							{
								url: '/backend/ajax.php?module='+ jsBackend.current.module +'&action=add_category&language={$LANGUAGE}',
								data: 'value=' + $('#categoryTitle').val(),
								success: function(json, textStatus)
								{
									if(json.code != 200)
									{
										// show error if needed
										if(jsBackend.debug) alert(textStatus);

										// show message
										$('#categoryTitleError').show();
									}
									else
									{
										// add and set selected
										$('#categoryId').append('<option value="'+ json.data.id +'">'+ json.data.title +'</option>');
										
										// reset value
										jsBackend.blog.controls.currentCategory = json.data.id;
										
										// close dialog
										$('#addCategoryDialog').dialog('close');
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
					close: function(event, ui) 
					{
						// reset value to previous selected item
						$('#categoryId').val(jsBackend.blog.controls.currentCategory);
					}
				});

			// bind change
			$('#categoryId').change(function(evt)
			{
				// new category?
				if($(this).val() == 'new_category')
				{
					// prevent default
					evt.preventDefault();
					
					// open dialog
					$('#addCategoryDialog').dialog('open');
				}
				
				// reset current category
				else jsBackend.blog.controls.currentCategory = $('#categoryId').val();
			});
		}
		
		jsBackend.blog.controls.currentCategory = $('#categoryId').val();
	},
	

	// end
	eoo: true
}


$(document).ready(jsBackend.blog.init);