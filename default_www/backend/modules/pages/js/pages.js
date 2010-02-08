if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.pages = {
	init: function() {
		jsBackend.pages.controls.init();
		jsBackend.pages.tree.init();
		jsBackend.pages.template.init();
	},
	
	// end
	eoo: true
}

// temp
jsBackend.pages.autosave = {
	init: function() {
	},

	save: function() {
		// save all TinyMCE instances
		if($('.inputEditor').length > 0) tinyMCE.triggerSave();

		// serialize data
		var data = $('form').serialize();

		// make the call
		$.ajax({ type: 'POST', dataType: 'json', 
				 url: '/backend/ajax.php?module=pages&action=save&language={$LANGUAGE}',
				 data: 'data=' + data +'&type=draft&id='+ pageID,
				 error: function(XMLHttpRequest, textStatus, errorThrown) {
					if(jsBackend.debug) alert(textStatus);
					
					// show messages
					jsBackend.messages.add('error', textStatus);
				 },
				 success: function(json, textStatus) {
						// show messages
						jsBackend.messages.add('success', textStatus);
				 }
		});
	},
	
	// end
	eoo: true
}

jsBackend.pages.controls = {
	init: function() {
		jsBackend.pages.controls.changeExtra();
		if($('.contentTitle .inputDropdown').length > 0) { $('.contentTitle .inputDropdown').bind('change', jsBackend.pages.controls.changeExtra); }
	},
	
	changeExtra: function(evt) {
		// find element with a block selected
		var element = $('.contentTitle option[rel=block]:selected');
		
		// nothing selected, so free all other elements
		if(element.length == 0) $('.contentTitle option').attr('disabled', '');
		
		// disable all other elements
		else $('.contentTitle option[rel=block]:not(:selected)').attr('disabled', 'disabled');
		
		// loop all
		$('.contentTitle .inputDropdown').each(function() {
			// get selected value
			var val = $(this).val();
			var id = $(this).attr('id');
			var index = id.replace('blockExtraId', '');
			var data = (val !== 'html') ? extraData[val] : null;

			// no real data, show editor
			if(data == null) {
				$('#blockContentHTML-'+ index).show();
				$('#blockContentExtra-'+ index).hide();
				$('#blockContentExtra-'+ index + ' p').html('&nbsp;');
			} else {
				if(data.type == 'block') {
					// build html
					var html = '{$msgModuleAttached}'.replace('{url}', data.data.url)
													 .replace('{name}', data.label);
				} else {
					// build html
					var html = '{$msgWidgetAttached}';
				}
				
				$('#blockContentHTML-'+ index).hide();
				$('#blockContentExtra-'+ index).show();
				$('#blockContentExtra-'+ index + ' p').html(html);
			}
			
		});
	},
	
	// end
	eoo: true
}

jsBackend.pages.template = {
	init: function() {
		if($('#templateList input:radio').length > 0) {
			jsBackend.pages.template.changeTemplate();
			$('#templateList input:radio').bind('change', jsBackend.pages.template.changeTemplate);
		}
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
			if(i >= current.num_blocks) $(this).hide();
			
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
			if(i >= current.num_blocks) $(this).hide();
			
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
	eoo: true
}

jsBackend.pages.tree = {
	init: function() {
		if($('#tree div').length == 0) return false;
	
		var options = { ui: { theme_name: 'fork' },
						rules: { multiple: false, multitree: 'all', drag_copy: false },
						lang: { loading: '{$lblLoading|ucfirst}' },
						callback: {
							beforemove: jsBackend.pages.tree.beforeMove,
							onselect: jsBackend.pages.tree.onSelect,
							onmove: jsBackend.pages.tree.onMove
						},
						types: {
							'default': { renameable: false, deletable: false, creatable: false, icon: { image: '/backend/modules/pages/js/jstree/themes/fork/icons.gif' } },
							'page': { icon: { position: '0 -80px' } },
							'folder': { icon: { position: false } },
							'hidden': { icon: { position: false } },
							'home': { draggable: false, icon: { position: '0 -112px' } },
							'pages': { icon: { position: false } },
							'error': { draggable: false, max_children: 0, icon: { position: '0 -160px' } },
							'sitemap': { max_children: 0, icon: { position: '0 -176px' } }
						},
						plugins: { 
							cookie: { prefix: 'jstree_', types: { selected: false } }
						}
					};

		// create tree
		$('#tree div').tree(options);
	},
	
	// before an item will be moved we have to do some checks
	beforeMove: function(node, refNode, type, tree) {
		// get pageID that has to be moved
		var currentPageID = $(node).attr('id').replace('page-', '');
		
		// init var
		var result = false;
		
		// make the call
		$.ajax({ type: 'POST', dataType: 'json', 
				 async: false, // important that this isn't asynchronous
				 url: '/backend/ajax.php?module=pages&action=get_info&language={$LANGUAGE}',
				 data: 'id=' + currentPageID,
				 error: function(XMLHttpRequest, textStatus, errorThrown) {
					if(jsBackend.debug) alert(textStatus);
					result = false;
				 },
				 success: function(json, textStatus) {
					 if(json.code != 200) {
						 if(jsBackend.debug) alert(textStatus);
						 result = false;
					 } else {
						 if(json.data.allow_move == 'Y') result = true;
					 }
				 }
		});

		// return
		return result;
	},
	// when an item is selected
	onSelect: function(node, tree) {
		// get current and new URL
		var currentPageURL = window.location.pathname + window.location.search; 
		var newPageURL = $(node).find('a').attr('href');
		
		// only redirect if destination isn't the current one.
		if(typeof newPageURL != 'undefined' && newPageURL != currentPageURL) window.location = newPageURL;
	},
	onMove: function(node, refNode, type, tree, rollback) {
		// get pageID that has to be moved
		var currentPageID = $(node).attr('id').replace('page-', '');
		
		// get pageID wheron the page has been dropped
		var droppedOnPageID = $(refNode).attr('id').replace('page-', '');

		// make the call
		$.ajax({ type: 'POST', dataType: 'json', 
				 url: '/backend/ajax.php?module=pages&action=move&language={$LANGUAGE}',
				 data: 'id=' + currentPageID + '&dropped_on='+ droppedOnPageID +'&type='+ type,
				 error: function(XMLHttpRequest, textStatus, errorThrown) {
					if(jsBackend.debug) alert(textStatus);
				 },
				 success: function(json, textStatus) {
					 if(json.code != 200) {
						 if(jsBackend.debug) alert(textStatus);
						 
						 // show message
						 jsBackend.messages.add('error', '{$errCantBeMoved}');
						 
						 // rollback
						 $.tree.rollback(rollback);
					 } else {
						 // show message
						 jsBackend.messages.add('success', '{$msgPageIsMoved}');
					 }
				 }
		});
	},
	
	// end
	eoo: true
}


$(document).ready(function() { jsBackend.pages.init(); });