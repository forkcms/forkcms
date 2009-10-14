if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.pages = {
	// init, something like a constructor
	init: function() { 
//		jsBackend.pages.templateChange();
//		jsBackend.pages.bindOpenDialog();
	},
	
	// links with a class "openDialog" will open the dialog referenced in the href-attribute
	bindOpenDialog: function() {
		// create dialogs
		$('#templatePicker').dialog({ modal: true, autoOpen: false, draggable: false, resizable: false,
									 buttons: { '{$lblOK|ucfirst}': function() { 
																		jsBackend.pages.templateChange();
																		$(this).dialog('close'); 
																	}
												}	
									});
		
		$('#openTemplatePicker').bind('click', function(evt) {
			evt.preventDefault();
			$('#templatePicker').dialog('open');
		});
	},
	
	// change the template
	templateChange: function() {
		// get selected template
		var templateId = $('#templatePicker input:radio:checked').val();

		// number of blocks
		var numberOfBlocks = templates[templateId].numberOfBlocks;
		var names = templates[templateId].names;
		
		// remove unused blocks
		var tabs = $('#tabs li');
		var blocks = $('#tabs div');
		
		for(var i = 0; i < tabs.length; i++)
		{
			if(i <= (numberOfBlocks-1)) {
				$($(tabs[i]).children()[0]).html(names[i]);
			} else {
				$(tabs[i]).hide();
				$(blocks[i]).hide();
			}
		}
	},
		
	// end
	_eoo: true
}

$(document).ready(function() { jsBackend.pages.init(); });