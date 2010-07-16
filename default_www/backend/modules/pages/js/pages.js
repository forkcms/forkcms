if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.pages = {
	init: function() {
		// load the tree
		jsBackend.pages.tree.init();

		// are we adding or editing?
		if(typeof templates != 'undefined') {
			// load stuff for the page
			jsBackend.pages.template.init();
			jsBackend.pages.extras.init();
		}

		// manage templates
		jsBackend.pages.manageTemplates.init();
		
		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	},
	
	// end
	eoo: true
}

// all methods related to the controls (buttons, ...)
jsBackend.pages.extras = {
	init: function() {
		// bind events
		$('#extraType').change(jsBackend.pages.extras.populateExtraModules);
		$('#extraModule').change(jsBackend.pages.extras.populateExtraIds);
	
		// bind buttons
		$('a.chooseExtra').live('click', jsBackend.pages.extras.showExtraDialog);
		
		// load initial data, or initialize the dialogs
		jsBackend.pages.extras.load();
	},
	
	// load initial data, or initialize the dialogs
	load: function() {
		// set correct
		$('input.block_extra_id').each(function() { jsBackend.pages.extras.changeExtra($(this).val(), $(this).attr('id').replace('blockExtraId', '')); })
		
		// initialize the modal for choosing an extra
		if($('#chooseExtra').length > 0) {
			$('#chooseExtra').dialog({ autoOpen: false, draggable: false, resizable: false, modal: true, 
				buttons: { '{$lblOK|ucfirst}': function() {
											// change the extra for real
											jsBackend.pages.extras.changeExtra();
							
											// close dialog
											$(this).dialog('close');
										},
							'{$lblCancel|ucfirst}': function() {
												// empty the extraForBlock
												$('#extraForBlock').val('');
														
												// close the dialog
												$(this).dialog('close'); 
											}
							}
			 });
		}

		if($('#chooseTemplate').length > 0) {
			$('#chooseTemplate').dialog({ autoOpen: false, draggable: false, resizable: false, modal: true, width: 500,
				buttons: { '{$lblOK|ucfirst}': function() {
													if($('#templateList input:radio:checked').val() != $('#templateId').val())
													{
														// empty extra's
														$('.block_extra_id').val('');
														
														// change the template for real
														jsBackend.pages.template.changeTemplate();
													}
				
													// close dialog
													$(this).dialog('close');
												},
							'{$lblCancel|ucfirst}': function() {
														// close the dialog
														$(this).dialog('close'); 
													}
						 }
			 });
		}
	},
	
	// change the extra for a block
	showExtraDialog: function(evt) {
		// prevent the default action
		evt.preventDefault();
		
		// get the block wherefor we will change the extra
		var blockId = $(this).attr('rel');

		// get selected extra id
		var selectedExtraId = $('#blockExtraId'+ blockId).val();
		
		// populate the hidden field, so we know for which block we are changing
		$('#extraForBlock').val(blockId);

		// init var
		var hasModules = false;
		
		// check if there already blocks linked
		$('.linkedExtra input:hidden').each(function() {
			var id = $(this).val();
			if(id != '' && typeof extrasById[id] != 'undefined' && extrasById[id].type == 'block') hasModules = true;
		});

		// blocks linked?
		if(hasModules) {
			// show warning
			$('#extraWarningAlreadyBlock').show();
			
			// disable blocks
			$('#extraType option[value="block"]').attr('disabled', 'disabled');
		} else {
			// hide warning
			$('#extraWarningAlreadyBlock').hide();

			// enable blocks
			$('#extraType option[value="block"]').attr('disabled', '');
			
			// home can't have any modules linked!
			if(typeof pageID != 'undefined' && pageID == 1) $('#extraType option[value="block"]').attr('disabled', 'disabled');
		}
		
		// any extra selected before? And if so, does the extra still exists?
		if(typeof extrasById[selectedExtraId] != 'undefined') {
			// set type
			$('#extraType').val(extrasById[selectedExtraId].type);
			
			// populate the modules
			jsBackend.pages.extras.populateExtraModules();
			
			// set module
			$('#extraModule').val(extrasById[selectedExtraId].module);
			
			// populate the ids
			jsBackend.pages.extras.populateExtraIds();
			
			// set the extra id
			$('#extraExtraId').val(extrasById[selectedExtraId].id);
		} else {
			// set type
			$('#extraType').val('html');
			$('#extraExtraId').val('');
			
			// populate the modules
			jsBackend.pages.extras.populateExtraModules();
		}
		
		// open the modal
		$('#chooseExtra').dialog('open');
	},
	
	// store the extra for real
	changeExtra: function(selectedExtraId, selectedBlock) {
		// if their are no arguments passed we should grab them
		if(!selectedExtraId || !selectedBlock) {
			// init vars
			var selectedExtraId = $('#extraExtraId').val();
			var selectedBlock = $('#extraForBlock').val();
		}

		// something is really fucked up
		if(selectedBlock == '') return false;

		// store
		$('#blockExtraId'+ selectedBlock).val(selectedExtraId);

		// empty the extraForBlock
		$('#extraForBlock').val('');

		if($('#templateBlock-'+ selectedBlock).length > 0) {
			if(typeof extrasById != 'undefined' && typeof extrasById[selectedExtraId] != 'undefined') {
				// set description
				$('#templateBlock-'+ selectedBlock +' .templateBlockCurrentType').html(extrasById[selectedExtraId].human_name);
				
				// hide block
				$('#block-'+ selectedBlock).hide();
			} else {
				// set description
				$('#templateBlock-'+ selectedBlock +' .templateBlockCurrentType').html('{$lblEditor|ucfirst}');

				// show block
				$('#block-'+ selectedBlock).show();
			}
		}
	},
	
	// populate the dropdown with the modules
	populateExtraModules: function() {
		// get selected value
		var selectedType = $('#extraType').val();

		// hide
		$('#extraModuleHolder').hide();
		$('#extraExtraIdHolder').hide();
		$('#extraModule').html('<option value="-1">-</option>');
		$('#extraExtraId').html('<option value="-1">-</option>');

		// only widgets and block need the module dropdown
		if(selectedType == 'widget' || selectedType == 'block') {
			// loop modules
			for(var i in extrasData) {
				// add option if needed
				if(typeof extrasData[i]['items'][selectedType] != 'undefined') $('#extraModule').append('<option value="'+ extrasData[i].value +'">'+ extrasData[i].name +'</option>');
			}
			
			// show
			$('#extraModuleHolder').show();
		}
	},
	
	// populates the dropdown with the extra's
	populateExtraIds: function() {
		// get selected value
		var selectedType = $('#extraType').val();
		var selectedModule = $('#extraModule').val();

		// hide and clear previous items
		$('#extraExtraIdHolder').hide();
		$('#extraExtraId').html('');
			
		// any items?
		if(typeof extrasData[selectedModule] != 'undefined' && typeof extrasData[selectedModule]['items'][selectedType] != 'undefined') {
			
			if(extrasData[selectedModule]['items'][selectedType].length == 1) {
				$('#extraExtraId').append('<option selected="selected" value="'+ extrasData[selectedModule]['items'][selectedType][0].id +'">'+ extrasData[selectedModule]['items'][selectedType][0].label +'</option>');
			}
			
			else {
				// loop items
				for(var i in extrasData[selectedModule]['items'][selectedType]) {
					// add option
					$('#extraExtraId').append('<option value="'+ extrasData[selectedModule]['items'][selectedType][i].id +'">'+ extrasData[selectedModule]['items'][selectedType][i].label +'</option>');
				}

				// show
				$('#extraExtraIdHolder').show();
			}
		}
	},
	
	// end
	eoo: true
}

// all methods related to managing the templates
jsBackend.pages.manageTemplates = {
	init: function() {
		// check if we need to do something
		if($('#numBlocks').length > 0) {
			// bind event
			$('#numBlocks').change(jsBackend.pages.manageTemplates.showMetaData);
			
			// execute, to initialize
			jsBackend.pages.manageTemplates.showMetaData();
		}
	},

	// method to show the metadata about a specific block in a template
	showMetaData: function() {
		var itemsToShow = $('#numBlocks').val();
		var i = 0;
		
		// loop elements
		$('#metaData p').each(function() {
			// hide if needed
			if(i >= itemsToShow) $(this).hide();
			
			// show otherwise
			else $(this).show();
			
			// increment
			i++;
		});
	},
	// end
	eoo: true
}

// all methods related to the templates
jsBackend.pages.template = {
	init: function() {
		// bind events
		$('#changeTemplate').bind('click', jsBackend.pages.template.showTemplateDialog);
	
		// load to initialize
		jsBackend.pages.template.changeTemplate();
	},
	changeTemplate: function() {
		// get checked
		var selected = $('#templateList input:radio:checked').val();
		
		// get current template
		var current = templates[selected];
		var i = 0;
		
		// hide unneeded blocks
		$('.contentBlock').each(function() {
			// hide if needed
			if(i >= current.num_blocks) $(this).hide();
			
			// show the block and set the name
			else {
				$(this).show();
				$('.blockName', this).html(current.data.names[i]);
			}
			
			// increment
			i++;
		});

		// set HTML for the viual representation of the template
		$('#templateVisual').html(current.html);
		$('#templateVisualLarge').html(current.htmlLarge);
		$('#templateId').val(selected);
		$('#templateLabel, #tabTemplateLabel').html(current.label);
		
		// loop blocks and set extra's, to initialize the page
		$('.contentBlock').each(function() {
			var index = $(this).attr('id').replace('block-', '');
			var extraId = $('#block_extra_id_'+ index).val();
			jsBackend.pages.extras.changeExtra(extraId, index);
		});
	},
	
	// show the dialog to alter the selected template
	showTemplateDialog: function(evt) {
		// prevent the default action
		evt.preventDefault();
				
		// open the modal
		$('#chooseTemplate').dialog('open');
	},
	
	// end
	eoo: true
}

jsBackend.pages.tree = {
	init: function() {
		if($('#tree div').length == 0) return false;

		// add "treeHidden"-class on leafs that are hidden, only for browsers that don't support opacity
		if(!jQuery.support.opacity) $('#tree ul li[rel="hidden"]').addClass('treeHidden');
		
		var openedIds = [];
		if(typeof pageID != 'undefined') {
			// get parents
			var parents = $('#page-'+ pageID).parents('li');
			
			// init var
			var openedIds = ['page-'+ pageID];

			// add parents
			for(var i = 0; i < parents.length; i++) openedIds.push($(parents[i]).attr('id'));
		}

		// add home if needed
		if(!utils.array.inArray('page-1', openedIds)) openedIds.push('page-1');
		
		var options = { ui: { theme_name: 'fork' },
						opened: openedIds,
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
							cookie: { prefix: 'jstree_', types: { selected: false }, options: { path: '/' } }
						}
					};

		// create tree
		$('#tree div').tree(options);
		
		// layout fix for the tree
		$('.tree li.open').each(function() {
			// if the so-called open-element doesn't have any childs we should replace the open-class.
			if($(this).find('ul').length == 0) $(this).removeClass('open').addClass('leaf');
		});
	},
	
	// before an item will be moved we have to do some checks
	beforeMove: function(node, refNode, type, tree) {
		// get pageID that has to be moved
		var currentPageID = $(node).attr('id').replace('page-', '');

		// home is a special item
		if($(refNode).attr('id').replace('page-', '') == '1') {
			if(type == 'before') return false;
			if(type == 'after') return false;
		}
		
		// init var
		var result = false;
		
		// make the call
		$.ajax({ async: false, // important that this isn't asynchronous
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
		$.ajax({ url: '/backend/ajax.php?module=pages&action=move&language={$LANGUAGE}',
				 data: 'id=' + currentPageID + '&dropped_on='+ droppedOnPageID +'&type='+ type,
				 success: function(json, textStatus) {
					 if(json.code != 200) {
						 if(jsBackend.debug) alert(textStatus);

						 // show message
						 jsBackend.messages.add('error', '{$errCantBeMoved|addslashes}');

						 // rollback
						 $.tree.rollback(rollback);
					 } else {
						 // show message
						 jsBackend.messages.add('success', '{$msgPageIsMoved|addslashes}'.replace('%1$s', json.data.title));	
					 }
				 }
		});
	},
	// end
	eoo: true
}

$(document).ready(function() { jsBackend.pages.init(); });