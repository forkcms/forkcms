if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.blog = {
	init: function() {
		jsBackend.blog.controls.init();
		jsBackend.blog.category.init();
		
		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	},
	// end
	eoo: true
}

jsBackend.blog.category = {
	init: function() {
		if($('#newCategory').length > 0) {
			// set disabled state
			$('#newCategoryButton').addClass('disabledButton');
			
			// hide errors
			$('#newCategoryError').hide();
			
			// bind events
			$('#newCategoryValue').bind('keyup', jsBackend.blog.category.keyPress);
			$('#newCategoryButton').bind('click', jsBackend.blog.category.add);
		}
		
		if($('.datagrid td.name').length > 0) {
			// buil ajax-url
			var url = '/backend/ajax.php?module=' + jsBackend.current.module + '&action=edit_category&language=' + jsBackend.current.language;

			// bind 
			$('.datagrid td.name').inlineTextEdit({ saveUrl: url, tooltip: '{$msgClickToEdit}' });
		}
		
	},
	// add new category
	add: function(evt) {
		// prevent default
		evt.preventDefault();
		
		// validate
		if($('#newCategoryValue').val().length == 0) return false;
		
		// buil ajax-url
		var url = '/backend/ajax.php?module=' + jsBackend.current.module + '&action=add_category&language=' + jsBackend.current.language;

		// init var
		var name = $('#newCategoryValue').val();
		
		// make the call
		$.ajax({ url: url,
			data: 'category_name=' + name,
			success: function(data, textStatus) {
				if(data.code == 200) {
					// existing categoyr
					if(typeof data.data.id != 'undefined') var id = data.data.id;
					
					// doesn't exist
					else
					{
						var id = data.data.new_id;
						$('#categoryId').append('<option selected="selected" value="'+ id +'">'+ name +'</option>');
					}

					// unselect all
					$('#categoryId option').attr('selected', '');
					
					// set selected
					$('#categoryId').val(id);
					
					// clear
					$('#newCategoryValue').val('');
					$('#newCategoryButton').addClass('disabledButton');
					$('#newCategory').slideUp();
					
					// show message
					jsBackend.messages.add('success', "{$msgAddedCategory|addslashes}".replace('%1$s', name));
				} else {
					// show message
					jsBackend.messages.add('error', textStatus);
				}
				
				// alert the user
				if(data.code != 200 && jsBackend.debug) { alert(data.message); }
			}
		});
	},
	// when a key is pressed
	keyPress: function(evt) {
		if(evt.which == 13) {
			// stop the default action
			evt.preventDefault();
			evt.stopPropagation();
			
			// add the category
			jsBackend.blog.category.add(evt); 
		}
		if($(this).val().length > 0) $('#newCategoryButton').removeClass('disabledButton');
		else $('#newCategoryButton').addClass('disabledButton');
	},
	
	// end
	eoo: true
}

jsBackend.blog.controls = {
	init: function() {
		$('#saveAsDraft').click(function(evt) {
			$('form').append('<input type="hidden" name="status" value="draft" />');
			$('form').submit();
		})
	},
	// end
	eoo: true
}

$(document).ready(function() { jsBackend.blog.init(); });