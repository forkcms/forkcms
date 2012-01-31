/**
 * Interaction for the pages module
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Matthias Mullie <matthias@mullie.eu>
 * @author	Dieter Vanden Eynde <dieter@netlash.com>
 */
jsBackend.pages =
{
	// init, something like a constructor
	init: function()
	{
		// load the tree
		jsBackend.pages.tree.init();

		// are we adding or editing?
		if(typeof templates != 'undefined')
		{
			// load stuff for the page
			jsBackend.pages.template.init();
			jsBackend.pages.extras.init();
		}

		// button to save to draft
		$('#saveAsDraft').on('click', function(e)
		{
			$('form').append('<input type="hidden" name="status" value="draft" />');
			$('form').submit();
		});

		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	}
}

/**
 * All methods related to the controls (buttons, ...)
 *
 * @author	Matthias Mullie <matthias@mullie.eu>
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Dieter Vanden Eynde <dieter@netlash.com>
 */
jsBackend.pages.extras =
{
	// init, something like a constructor
	init: function()
	{
		// bind events
		$('#extraType').on('change', jsBackend.pages.extras.populateExtraModules);
		$('#extraModule').on('change', jsBackend.pages.extras.populateExtraIds);

		// bind buttons
		$(document).on('click', 'a.addBlock', jsBackend.pages.extras.showAddDialog);
		$(document).on('click', 'a.deleteBlock', jsBackend.pages.extras.showDeleteDialog);
		$(document).on('click', '.showEditor', jsBackend.pages.extras.editContent);
		$(document).on('click', '.toggleVisibility', jsBackend.pages.extras.toggleVisibility);

		// make the blocks sortable
		jsBackend.pages.extras.sortable();
	},

	// store the extra for real
	addBlock: function(selectedExtraId, selectedPosition)
	{
		// clone prototype block
		var block = $('.contentBlock:first').clone();

		// fetch amount of blocks already on page, it'll be the index of the newly added block
		var index = $('.contentBlock').length;

		// update index occurences in the hidden data
		var blockHtml = $('textarea[id^=blockHtml]', block);
		var blockExtraId = $('input[id^=blockExtraId]', block);
		var blockPosition = $('input[id^=blockPosition]', block);
		var blockVisibility = $('input[id^=blockVisible]', block);

		// update id & name to new index
		blockHtml.prop('id', blockHtml.prop('id').replace('0', index)).prop('name', blockHtml.prop('name').replace('0', index));
		blockExtraId.prop('id', blockExtraId.prop('id').replace('0', index)).prop('name', blockExtraId.prop('name').replace('0', index));
		blockPosition.prop('id', blockPosition.prop('id').replace('0', index)).prop('name', blockPosition.prop('name').replace('0', index));
		blockVisibility.prop('id', blockVisibility.prop('id').replace('0', index)).prop('name', blockVisibility.prop('name').replace('0', index));

		// save position
		blockPosition.val(selectedPosition);

		// save extra id
		blockExtraId.val(selectedExtraId);

		// add block to dom
		block.appendTo($('#editContent'));

		// get block visibility
		var visible = blockVisibility.attr('checked');

		// add visual representation of block to template visualisation
		var addedVisual = jsBackend.pages.extras.addBlockVisual(selectedPosition, index, selectedExtraId, visible);

		// block/widget = don't show editor
		if(typeof extrasById != 'undefined' && typeof extrasById[selectedExtraId] != 'undefined') $('.blockContentHTML', block).hide();

		// editor
		else $('.blockContentHTML', block).show();

		// reset block indexes
//		jsBackend.pages.extras.resetIndexes();

		return addedVisual ? index : false;
	},

	// add block visual on template
	addBlockVisual: function(position, index, extraId, visible)
	{
		// check if the extra is valid
		if(extraId != 0 && typeof extrasById[extraId] == 'undefined') return false;

		// block
		if(extraId != 0)
		{
			// link to edit this block/widget
			var editLink = '';
			if(extrasById[extraId].type == 'block' && extrasById[extraId].data.url) editLink = extrasById[extraId].data.url;
			if(extrasById[extraId].type == 'widget' && typeof extrasById[extraId].data.edit_url != 'undefined' && extrasById[extraId].data.edit_url) editLink = extrasById[extraId].data.edit_url;

			// title, description & visibility
			var title = extrasById[extraId].human_name;
			var description = extrasById[extraId].path;
		}

		// editor
		else
		{
			// link to edit this content, title, description & visibility
			var editLink = '';
			var title = '{$lblEditor|ucfirst}';
			var description = utils.string.stripTags($('#blockHtml' + index).val()).substr(0, 200);
		}

		// create html to be appended in template-view
		var blockHTML = '<div class="templatePositionCurrentType' + (visible ? ' ' : ' templateDisabled') + '" data-block-id="' + index + '">' +
							'<span class="templateTitle">' + title + '</span>' +
							'<span class="templateDescription">' + description + '</span>' +
							'<div class="buttonHolder">' +
								'<a href="' + (editLink ? editLink : '#') + '" class="' + (extraId == 0 ? 'showEditor ' : '') + 'button icon iconOnly iconEdit' + '"' + (extraId != 0 && editLink ? ' target="_blank"' : '') + (extraId != 0 && editLink ? '' : ' onclick="return false;"') + ((extraId != 0 && editLink) || extraId == 0 ? '' : 'style="display: none;" ') + '><span>{$lblEdit|ucfirst}</span></a>' +
								'<a href="#" class="button icon iconOnly ' + (visible ? 'iconVisible ' : 'iconInvisible ') + 'toggleVisibility"><span>&nbsp;</span></a>' +
								'<a href="#" class="deleteBlock button icon iconOnly iconDelete"><span>{$lblDeleteBlock|ucfirst}</span></a>' +
							'</div>' +
						'</div>';

		// set block description in template-view
		$('#templatePosition-' + position + ' .linkedBlocks').append(blockHTML);

		// mark as updated
		jsBackend.pages.extras.updatedBlock($('.templatePositionCurrentType[data-block-id=' + index + ']'));

		return true;
	},

	// delete a linked block
	deleteBlock: function(index)
	{
		// remove block from template overview
		$('.templatePositionCurrentType[data-block-id=' + index + ']').remove();

		// remove block
		$('[name=block_extra_id_' + index + ']').parent('.contentBlock').remove();

		// after removing all from fallback; hide fallback
		jsBackend.pages.extras.hideFallback();

		// reset indexes (sequence)
		jsBackend.pages.extras.resetIndexes();
	},

	// edit content
	editContent: function(e)
	{
		// prevent default event action
		e.preventDefault();

		// fetch block index
		var index = $(this).parent().parent().attr('data-block-id');

		// save unaltered content
		var previousContent = $('#blockHtml' + index).val();

		// placeholder for block node that will be moved by the jQuery dialog
		$('#blockHtml' + index).parent().parent().parent().after('<div id="blockPlaceholder"></div>');

		// show dialog
		$('#blockHtml').dialog(
		{
			closeOnEscape: false,
			draggable: false,
			resizable: false,
			modal: true,
			width: 940,
			title: '{$lblEditor|ucfirst}',
			position: 'center',
			buttons:
			{
				'{$lblOK|ucfirst}': function()
				{
					// grab the content
					var content = $('#html').val();

					// save content
					jsBackend.pages.extras.setContent(index, content);

					// edit content = template is no longer original
					jsBackend.pages.template.original = false;

					// close dialog
					$(this).dialog('close');
				},
				'{$lblCancel|ucfirst}': function()
				{
					// reset content
					jsBackend.pages.extras.setContent(index, previousContent);

					// close the dialog
					$(this).dialog('close');
				}
			},
			// jQuery's dialog is so nice to move this node to display it well, but does not put it back where it belonged
			close: function(e, ui)
			{
				// destroy dialog (to get rid of html order problems)
				$(this).dialog('destroy');

				// find block placeholder
				var blockPlaceholder = $('#blockPlaceholder');

				// move node back to the original position
				$(this).insertBefore(blockPlaceholder);

				// remove placeholder
				blockPlaceholder.remove();
			},
			// jQuery's dialog & CKEditor don't play nicely!
			open: function()
			{
				// reload the editors
				jsBackend.ckeditor.destroy();
				jsBackend.ckeditor.load();

				// resize the editor, so we have space to edit the content
				CKEDITOR.instances['html'].resize('100%', 375);

				// set content in editor
				$('#html').val(previousContent);
			}
		});
	},

	// hide fallback
	hideFallback: function()
	{
		// after removing all from fallback; hide fallback
		if($('#templateVisualFallback .templatePositionCurrentType').length == 0) $('#templateVisualFallback').hide();
	},

	// populate the dropdown with the modules
	populateExtraModules: function()
	{
		// get selected value
		var selectedType = $('#extraType').val();

		// hide
		$('#extraModuleHolder').hide();
		$('#extraExtraIdHolder').hide();
		$('#extraModule').html('<option value="0">-</option>');
		$('#extraExtraId').html('<option value="0">-</option>');

		// only widgets and block need the module dropdown
		if(selectedType == 'widget' || selectedType == 'block')
		{
			// loop modules
			for(var i in extrasData)
			{
				// add option if needed
				if(typeof extrasData[i]['items'][selectedType] != 'undefined') $('#extraModule').append('<option value="'+ extrasData[i].value +'">'+ extrasData[i].name +'</option>');
			}

			// show
			$('#extraModuleHolder').show();
		}
	},

	// populates the dropdown with the extra's
	populateExtraIds: function()
	{
		// get selected value
		var selectedType = $('#extraType').val();
		var selectedModule = $('#extraModule').val();

		// hide and clear previous items
		$('#extraExtraIdHolder').hide();
		$('#extraExtraId').html('');

		// any items?
		if(typeof extrasData[selectedModule] != 'undefined' && typeof extrasData[selectedModule]['items'][selectedType] != 'undefined')
		{
			if(extrasData[selectedModule]['items'][selectedType].length == 1 && selectedType == 'block')
			{
				$('#extraExtraId').append('<option selected="selected" value="'+ extrasData[selectedModule]['items'][selectedType][0].id +'">'+ extrasData[selectedModule]['items'][selectedType][0].label +'</option>');
			}

			else
			{
				// loop items
				for(var i in extrasData[selectedModule]['items'][selectedType])
				{
					// add option
					$('#extraExtraId').append('<option value="'+ extrasData[selectedModule]['items'][selectedType][i].id +'">'+ extrasData[selectedModule]['items'][selectedType][i].label +'</option>');
				}

				// show
				$('#extraExtraIdHolder').show();
			}
		}
	},

	// reset all indexes to keep all items in proper order
	resetIndexes: function()
	{
		// mark content to be reset
		$('.contentBlock').addClass('reset');

		// reorder indexes of existing blocks:
		// is doesn't really matter if a certain block at a certain position has a certain index; the important part
		// is that they're all sequential without gaps and that the sequence of blocks inside a position is correct
		$('.templatePositionCurrentType').each(function(i)
		{
			// fetch block id
			var oldIndex = $(this).attr('data-block-id');
			var newIndex = i + 1;

			// update index of entry in template-view
			$(this).attr('data-block-id', newIndex);

			// update index occurences in the hidden data
			var blockHtml = $('.reset [name=block_html_' + oldIndex + ']');
			var blockExtraId = $('.reset [name=block_extra_id_' + oldIndex + ']');
			var blockPosition = $('.reset [name=block_position_' + oldIndex + ']');
			var blockVisible = $('.reset [name=block_visible_' + oldIndex + ']');

			blockHtml.prop('id', blockHtml.prop('id').replace(oldIndex, newIndex)).prop('name', blockHtml.prop('name').replace(oldIndex, newIndex));
			blockExtraId.prop('id', blockExtraId.prop('id').replace(oldIndex, newIndex)).prop('name', blockExtraId.prop('name').replace(oldIndex, newIndex));
			blockPosition.prop('id', blockPosition.prop('id').replace(oldIndex, newIndex)).prop('name', blockPosition.prop('name').replace(oldIndex, newIndex));
			blockVisible.prop('id', blockVisible.prop('id').replace(oldIndex, newIndex)).prop('name', blockVisible.prop('name').replace(oldIndex, newIndex));

			// no longer mark as needing to be reset
			blockExtraId.parent('.contentBlock').removeClass('reset');

			// while we're at it, make sure the position is also correct
			blockPosition.val($(this).parent().parent().attr('data-position'));
		});

		// mark all as having been reset
		$('.contentBlock').removeClass('reset');
	},

	// save/reset the content
	setContent: function(index, content)
	{
		// the content to set
		if(content != null) $('#blockHtml' + index).val(content);

		// add short description to visual representation of block
		var description = utils.string.stripTags($('#blockHtml' + index).val()).substr(0, 200);
		$('.templatePositionCurrentType[data-block-id=' + index + '] .templateDescription').html(description);

		// mark as updated
		jsBackend.pages.extras.updatedBlock($('.templatePositionCurrentType[data-block-id=' + index + ']'));
	},

	// add a block
	showAddDialog: function(e)
	{
		// prevent the default action
		e.preventDefault();

		// save the position wherefor we will change the extra
		position = $(this).parent().parent().attr('data-position');

		// init var
		var hasModules = false;

		// check if there already blocks linked
		$('input[id^=blockExtraId]').each(function()
		{
			// get id
			var id = $(this).val();

			// check if a block is already linked
			if(id != '' && typeof extrasById[id] != 'undefined' && extrasById[id].type == 'block') hasModules = true;
		});

		// hide warnings
		$('#extraWarningAlreadyBlock').hide();
		$('#extraWarningHomeNoBlock').hide();

		// init var
		var enabled = true;

		// blocks linked?
		if(hasModules)
		{
			// disable module selection
			enabled = false;

			// show warning
			$('#extraWarningAlreadyBlock').show();
		}

		// home can't have any modules linked!
		if(typeof pageID != 'undefined' && pageID == 1)
		{
			// disable module selection
			enabled = false;

			// show warning
			$('#extraWarningHomeNoBlock').show();
		}

		// enable/disable blocks
		$('#extraType option[value=block]').attr('disabled', !enabled);

		// set type
		$('#extraType').val('html');
		$('#extraExtraId').val(0);

		// populate the modules
		jsBackend.pages.extras.populateExtraModules();

		// initialize the modal for choosing an extra
		if($('#addBlock').length > 0)
		{
			$('#addBlock').dialog(
			{
				draggable: false,
				resizable: false,
				modal: true,
				width: 500,
				buttons:
				{
					'{$lblOK|ucfirst}': function()
					{
						// fetch the selected extra id
						var selectedExtraId = $('#extraExtraId').val();

						// add the extra
						var index = jsBackend.pages.extras.addBlock(selectedExtraId, position);

						// add a block = template is no longer original
						jsBackend.pages.template.original = false;

						// close dialog
						$(this).dialog('close');

						// if the added block was an editor, show the editor immediately
						if(index && !(typeof extrasById != 'undefined' && typeof extrasById[selectedExtraId] != 'undefined'))
						{
							$('.templatePositionCurrentType[data-block-id=' + index + '] .showEditor').click();
						}
					},
					'{$lblCancel|ucfirst}': function()
					{
						// close the dialog
						$(this).dialog('close');
					}
				}
			 });
		}
	},

	// delete a block
	showDeleteDialog: function(e)
	{
		// prevent the default action
		e.preventDefault();

		// save element to variable
		var element = $(this);

		// initialize the modal for deleting a block
		if($('#confirmDeleteBlock').length > 0)
		{
			$('#confirmDeleteBlock').dialog(
			{
				draggable: false,
				resizable: false,
				modal: true,
				buttons:
				{
					'{$lblOK|ucfirst}': function()
					{
						// delete this block
						jsBackend.pages.extras.deleteBlock(element.parent().parent('.templatePositionCurrentType').attr('data-block-id'));

						// delete a block = template is no longer original
						jsBackend.pages.template.original = false;

						// close dialog
						$(this).dialog('close');
					},
					'{$lblCancel|ucfirst}': function()
					{
						// close the dialog
						$(this).dialog('close');
					}
				 }
			 });
		}
	},

	// re-order blocks
	sortable: function()
	{
		// make blocks sortable
		$('div.linkedBlocks').sortable(
		{
			items: '.templatePositionCurrentType',
			tolerance: 'pointer',
			placeholder: 'dragAndDropPlaceholder',
			forcePlaceholderSize: true,
			connectWith: 'div.linkedBlocks',
			opacity: 0.7,
			delay: 300,
			stop: function(e, ui)
			{
				// reorder indexes of existing blocks:
				jsBackend.pages.extras.resetIndexes();

				// mark as updated
				jsBackend.pages.extras.updatedBlock(ui.item);

				// after removing all from fallback; hide fallback
				jsBackend.pages.extras.hideFallback();

				// reorder blocks = template is no longer original
				jsBackend.pages.template.original = false;
			},
			start: function(e, ui)
			{
				// check if we're moving from template
				if($(this).parents('#templateVisualLarge').length > 0)
				{
					// disable dropping to fallback
					$('div.linkedBlocks').sortable('option', 'connectWith', '#templateVisualLarge div.linkedBlocks');
				}
				else
				{
					// enable dropping on fallback
					$('div.linkedBlocks').sortable('option', 'connectWith', 'div.linkedBlocks');
				}

				// refresh sortable to reflect altered dropping
				$('div.linkedBlocks').sortable('refresh');
			}
		});
	},

	// toggle block visibility
	toggleVisibility: function(e)
	{
		// prevent default event action
		e.preventDefault();

		// toggle visibility = template is no longer original
		jsBackend.pages.template.original = false;

		// get index of block
		var index = $(this).parent().parent().attr('data-block-id');

		// get visibility checbox
		var checkbox = $('#blockVisible' + index);

		// get current visibility state
		var visible = checkbox.is(':checked');

		// invert visibility
		visible = !visible;

		// change visibility state
		checkbox.attr('checked', visible);

		// remove current visibility indicators
		$(this).removeClass('iconVisible').removeClass('iconInvisible');
		$(this).parent().parent().removeClass('templateDisabled');

		// toggle visibility indicators
		if(visible) $(this).addClass('iconVisible');
		else
		{
			$(this).addClass('iconInvisible');
			$(this).parent().parent().addClass('templateDisabled');
		}
	},

	// display an effect on updated items
	updatedBlock: function(element)
	{
		element.effect('highlight');
	}
}

/**
 * All methods related to the templates
 *
 * @author	Matthias Mullie <matthias@mullie.eu>
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.pages.template =
{
	// indicates whether or not the page content is original or has been altered already
	original: true,

	// init, something like a constructor
	init: function()
	{
		// bind events
		$('#changeTemplate').on('click', jsBackend.pages.template.showTemplateDialog);

		// load to initialize when adding a page
		jsBackend.pages.template.changeTemplate();
	},

	// method to change a template
	changeTemplate: function()
	{
		// destroy sortable blocks
		$('div.linkedBlocks').sortable('destroy');

		// get checked
		var selected = $('#templateList input:radio:checked').val();

		// get current & old template
		var old = templates[$('#templateId').val()];
		var current = templates[selected];
		var i = 0;

		// hide default (base) block
		$('#block-0').hide();

		// reset HTML for the visual representation of the template
		$('#templateVisual').html(current.html);
		$('#templateVisualLarge').html(current.htmlLarge);
		$('#templateVisualFallback .linkedBlocks').html('');
		$('#templateId').val(selected);
		$('#templateLabel, #tabTemplateLabel').html(current.label);

		// hide fallback by default
		$('#templateVisualFallback').hide();

		// remove previous fallback blocks
		$('input[id^=blockPosition][value=fallback][id!=blockPosition0]').parent().remove();

		// check if we have already committed changes (if not, we can just ignore existing blocks and remove all of them)
		if(current != old && jsBackend.pages.template.original) $('input[id^=blockPosition][id!=blockPosition0]').parent().remove();

		// loop existing blocks
		$('#editContent .contentBlock').each(function(i)
		{
			// fetch variables
			var index = $('input[id^=blockExtraId]', this).prop('id').replace('blockExtraId', '');
			var extraId = parseInt($('input[id^=blockExtraId]', this).val());
			var position = $('input[id^=blockPosition]', this).val();
			var html = $('textarea[id^=blockHtml]', this).val();

			// skip default (base) block (= continue)
			if(index == 0) return true;

			// blocks were present already = template was not original
			jsBackend.pages.template.original = false;

			// check if this block is a default of the old template, in which case it'll go to the fallback position
			if(current != old && $.inArray(extraId, old.data.default_extras[position]) >= 0 && html == '') $('input[id=blockPosition' + index + ']', this).val('fallback');
		});

		// init var
		newDefaults = new Array();

		// check if this default block has been changed
		if(current != old || (typeof initDefaults != 'undefined' && initDefaults))
		{
			// this is a variable indicating that the add-action may initially set default blocks
			if(typeof initDefaults != 'undefined') initDefaults = false;

			// loop positions in new template
			for(var position in current.data.default_extras)
			{
				// loop default extra's on positions
				for(var block in current.data.default_extras[position])
				{
					// grab extraId
					extraId = current.data.default_extras[position][block];

					// init var
					var existingBlock = null;

					// find existing block sent to default
					var existingBlock = $('input[id^=blockPosition][value=fallback]:not(#blockPosition0)').parent().find('input[id^=blockExtraId][value=' + extraId + ']').parent();

					// if this block did net yet exist, add it
					if(existingBlock.length == 0) newDefaults.push(new Array(extraId, position));

					// if this block already existed, reset it to correct (new) position
					else $('input[id^=blockPosition]', existingBlock).val(position);
				}
			}
		}

		// loop existing blocks
		$('#editContent .contentBlock').each(function(i)
		{
			// fetch variables
			var index = $('input[id^=blockExtraId]', this).prop('id').replace('blockExtraId', '');
			var extraId = parseInt($('input[id^=blockExtraId]', this).val());
			var position = $('input[id^=blockPosition]', this).val();
			var visible = $('input[id^=blockVisible]', this).attr('checked');

			// skip default (base) block (= continue)
			if(index == 0) return true;

			// check if this position exists
			if($.inArray(position, current.data.names) < 0)
			{
				// blocks in positions that do no longer exist should go to fallback
				position = 'fallback';

				// save position as fallback
				$('input[id=blockPosition' + index + ']', this).val(position);

				// show fallback
				$('#templateVisualFallback').show();
			}

			// add visual representation of block to template visualisation
			added = jsBackend.pages.extras.addBlockVisual(position, index, extraId, visible);

			// if the visual could be not added, remove the content entirely
			if(!added) $(this).remove();
		});

		// reset block indexes
		jsBackend.pages.extras.resetIndexes();

		// add new defaults at last
		for(var i in newDefaults) jsBackend.pages.extras.addBlock(newDefaults[i][0], newDefaults[i][1]);

		// make the blocks sortable (again)
		jsBackend.pages.extras.sortable();
	},

	// show the dialog to alter the selected template
	showTemplateDialog: function(e)
	{
		// prevent the default action
		e.preventDefault();

		$('#chooseTemplate').dialog(
		{
			draggable: false,
			resizable: false,
			modal: true,
			width: 940,
			buttons:
			{
				'{$lblOK|ucfirst}': function()
				{
					if($('#templateList input:radio:checked').val() != $('#templateId').val())
					{
						// change the template for real
						jsBackend.pages.template.changeTemplate();
					}

					// close dialog
					$(this).dialog('close');
				},
				'{$lblCancel|ucfirst}': function()
				{
					// close the dialog
					$(this).dialog('close');
				}
			 }
		 });
	}
}

/**
 * All methods related to the tree
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.pages.tree =
{
	// init, something like a constructor
	init: function()
	{
		if($('#tree div').length == 0) return false;

		// add "treeHidden"-class on leafs that are hidden, only for browsers that don't support opacity
		if(!jQuery.support.opacity) $('#tree ul li[rel="hidden"]').addClass('treeHidden');

		var openedIds = [];
		if(typeof pageID != 'undefined')
		{
			// get parents
			var parents = $('#page-'+ pageID).parents('li');

			// init var
			var openedIds = ['page-'+ pageID];

			// add parents
			for(var i = 0; i < parents.length; i++) openedIds.push($(parents[i]).prop('id'));
		}

		// add home if needed
		if(!utils.array.inArray('page-1', openedIds)) openedIds.push('page-1');

		var options =
		{
			ui: { theme_name: 'fork' },
			opened: openedIds,
			rules:
			{
				multiple: false,
				multitree: 'all',
				drag_copy: false
			},
			lang: { loading: '{$lblLoading|ucfirst}' },
			callback:
			{
				beforemove: jsBackend.pages.tree.beforeMove,
				onselect: jsBackend.pages.tree.onSelect,
				onmove: jsBackend.pages.tree.onMove
			},
			types:
			{
				'default': { renameable: false, deletable: false, creatable: false, icon: { image: '/backend/modules/pages/js/jstree/themes/fork/icons.gif' } },
				'page': { icon: { position: '0 -80px' } },
				'folder': { icon: { position: false } },
				'hidden': { icon: { position: false } },
				'home': { draggable: false, icon: { position: '0 -112px' } },
				'pages': { icon: { position: false } },
				'error': { draggable: false, max_children: 0, icon: { position: '0 -160px' } },
				'sitemap': { max_children: 0, icon: { position: '0 -176px' } },
				'redirect': { icon: { position: '0 -264px' } },
				'direct_action': { max_children: 0, icon: { position: '0 -280px' } }
			},
			plugins:
			{
				cookie: { prefix: 'jstree_', types: { selected: false }, options: { path: '/' } }
			}
		};

		// create tree
		$('#tree div').tree(options);

		// layout fix for the tree
		$('.tree li.open').each(function()
		{
			// if the so-called open-element doesn't have any childs we should replace the open-class.
			if($(this).find('ul').length == 0) $(this).removeClass('open').addClass('leaf');
		});

		// set the item selected
		if(typeof selectedId != 'undefined') $('#' + selectedId).addClass('selected');
	},

	// before an item will be moved we have to do some checks
	beforeMove: function(node, refNode, type, tree)
	{
		// get pageID that has to be moved
		var currentPageID = $(node).prop('id').replace('page-', '');
		if(typeof refNode == 'undefined') parentPageID = 0;
		else var parentPageID = $(refNode).prop('id').replace('page-', '')

		// home is a special item
		if(parentPageID == '1')
		{
			if(type == 'before') return false;
			if(type == 'after') return false;
		}

		// init var
		var result = false;

		// make the call
		$.ajax(
		{
			async: false, // important that this isn't asynchronous
			data:
			{
				fork: { action: 'get_info' },
				id: currentPageID
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				if(jsBackend.debug) alert(textStatus);
				result = false;
			},
			success: function(json, textStatus)
			{
				if(json.code != 200)
				{
					if(jsBackend.debug) alert(textStatus);
					result = false;
				}
				else
				{
					if(json.data.allow_move == 'Y') result = true;
				}
			}
		});

		// return
		return result;
	},

	// when an item is selected
	onSelect: function(node, tree)
	{
		// get current and new URL
		var currentPageURL = window.location.pathname + window.location.search;
		var newPageURL = $(node).find('a').prop('href');

		// only redirect if destination isn't the current one.
		if(typeof newPageURL != 'undefined' && newPageURL != currentPageURL) window.location = newPageURL;
	},

	// when an item is moved
	onMove: function(node, refNode, type, tree, rollback)
	{
		// get pageID that has to be moved
		var currentPageID = $(node).prop('id').replace('page-', '');

		// get pageID wheron the page has been dropped
		if(typeof refNode == 'undefined') droppedOnPageID = 0;
		else var droppedOnPageID = $(refNode).prop('id').replace('page-', '')

		// make the call
		$.ajax(
		{
			data:
			{
				fork: { action: 'move' },
				id: currentPageID,
				dropped_on: droppedOnPageID,
				type: type
			},
			success: function(json, textStatus)
			{
				if(json.code != 200)
				{
					if(jsBackend.debug) alert(textStatus);

					// show message
					jsBackend.messages.add('error', '{$errCantBeMoved}');

					// rollback
					$.tree.rollback(rollback);
				}
				else
				{
					// show message
					jsBackend.messages.add('success', '{$msgPageIsMoved}'.replace('%1$s', json.data.title));
				}
			}
		});
	}
}

$(jsBackend.pages.init);