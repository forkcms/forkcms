if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.blog = {
	init: function() {
		jsBackend.blog.category.init();
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
	},
	// add new category
	add: function(evt) {
		// prevent default
		evt.preventDefault();
		
		// validate
		if($('#newCategoryValue').val().length == 0) return false;
		
		// split url to buil the ajax-url
		var chunks = document.location.pathname.split('/');
		// buil ajax-url
		var url = '/backend/ajax.php?module=' + chunks[3] + '&action=add_category&language=' + chunks[2];
		// init var
		var name = $('#newCategoryValue').val();
		
		// make the call
		$.ajax({cache: false, type: 'POST', dataType: 'json', 
			url: url,
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
				}
			
				// alert the user
				if(data.code != 200 && jsBackend.debug) { alert(data.message); }
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$('#newCategoryError').show();
				// alert the user
				if(jsBackend.debug) alert(textStatus);
			}
		});
	},
	// when a key is pressed
	keyPress: function(evt) {
		if($(this).val().length > 0) $('#newCategoryButton').removeClass('disabledButton');
		else $('#newCategoryButton').addClass('disabledButton');
	},
	
	// end
	eoo: true
}

$(document).ready(function() { jsBackend.blog.init(); });