if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.pages = {
	// init, something like a constructor
	init: function() { 
		jsBackend.pages.changeTemplate();
		$('#templateList input:radio').bind('change', jsBackend.pages.changeTemplate);
	},
	
	changeTemplate: function() {
		// get checked
		var selected = $('#templateList input:checked').attr('value');

		// get current template
		var current = templates[selected];
		var i = 0;
		
		// hide unneeded blocks
		$('.contentBlock').each(function() {
			// hide if needed
			if(i >= current.number_of_blocks) $(this).hide();
			
			// process
			else {
				$(this).hide();
				$('.numbering', this).html(i);
				$('.blockName', this).html(current.data.names[i]);
			}
			
			// increment
			i++;
		});

		var i = 0;
		$('#templateDetails tr').each(function() {
			
			// hide if needed
			if(i >= current.number_of_blocks) $(this).hide();
			
			// process
			else {
				$(this).show();
				$('.numbering', this).html(i);
				$('.blockName', this).html(current.data.names[i]);
			}

			// increment
			i++;
		});
		
		$('#templateVisual').html(current.html);

		// show block
		if($('#templateVisual td.selected a').length == 0) $('#templateVisual td a:first').parent().addClass('selected');

		var showId = $('#templateVisual td.selected a').attr('href');

		$(showId).show();
	},
	
	// end
	_eoo: true
}

$(document).ready(function() { jsBackend.pages.init(); });