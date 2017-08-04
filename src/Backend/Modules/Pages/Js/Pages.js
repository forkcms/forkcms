/**
 * Interaction for the pages module
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
            jsBackend.pages.extras.init();
            jsBackend.pages.template.init();
        }

        // button to save to draft
        $('#saveAsDraft').on('click', function(e)
        {
            $('form').append('<input type="hidden" name="status" value="draft" />');
            $('form').submit();
        });

        // show / hide the remove from search index checkbox on change
        $('#authRequired').on('change', function(e)
        {
            if($(this).is(':checked')){
              $('[data-role="remove-from-search-index"]').removeClass('hidden');
            } else {
              $('[data-role="remove-from-search-index"]').addClass('hidden');
            }
        }).trigger('change');

        // do meta
        if($('#title').length > 0) $('#title').doMeta();
    }
};

/**
 * All methods related to the controls (buttons, ...)
 */
jsBackend.pages.extras = {
    uploaders: [],
    counter: 0,
    // init, something like a constructor
    init: function()
    {
        // get user templates
        jsBackend.pages.template.userTemplates = jsData.pages.userTemplates;

        // bind events
        $('#extraType').on('change', jsBackend.pages.extras.populateExtraModules);
        $('#extraModule').on('change', jsBackend.pages.extras.populateExtraIds);

        // bind buttons
        $(document).on('click', 'a.addBlock', jsBackend.pages.extras.showAddDialog);
        $(document).on('click', 'a.deleteBlock', jsBackend.pages.extras.showDeleteDialog);
        $(document).on('click', '.showEditor', jsBackend.pages.extras.editContent);
        $(document).on('click', '.editUserTemplate', jsBackend.pages.extras.editUserTemplate);
        $(document).on('click', '.toggleVisibility', jsBackend.pages.extras.toggleVisibility);
        $('.modal').on('scroll', jsBackend.pages.extras.modalScrolledHandler);

        // make the default position sortable
        jsBackend.pages.extras.sortable($('#templateVisualFallback div.linkedBlocks'));

        $('#authRequired').on('change', jsBackend.pages.extras.showGroups);
    },

    // handle stuff when scroll inside a modal
    modalScrolledHandler: function(event)
    {
        jsBackend.pages.extras.counter++;

        // skip 9 out of 10 scrolls
        if (jsBackend.pages.extras.counter % 10 !== 0) {
            return false;
        }

        // update the positions on each uploader
        $.each(jsBackend.pages.extras.uploaders, function(index, uploader) {
            uploader.updatePosition();
        });
    },

    // store the extra for real
    addBlock: function(selectedExtraId, selectedPosition, selectedExtraType, selectedExtraData)
    {
        selectedExtraType = selectedExtraType || 'rich_text';

        // clone prototype block
        var block = $('.contentBlock:first').clone();

        // fetch amount of blocks already on page, it'll be the index of the newly added block
        var index = $('.contentBlock').length;

        // update index occurences in the hidden data
        var blockHtml = $('textarea[id^=blockHtml]', block);
        var blockExtraId = $('input[id^=blockExtraId]', block);
        var blockExtraType = $('input[id^=blockExtraType]', block);
        var blockExtraData = $('input[id^=blockExtraData]', block);
        var blockPosition = $('input[id^=blockPosition]', block);
        var blockVisibility = $('input[id^=blockVisible]', block);

        // update id & name to new index
        blockHtml.prop('id', blockHtml.prop('id').replace('0', index)).prop('name', blockHtml.prop('name').replace('0', index));
        blockExtraId.prop('id', blockExtraId.prop('id').replace('0', index)).prop('name', blockExtraId.prop('name').replace('0', index));
        blockExtraType.prop('id', blockExtraType.prop('id').replace('0', index)).prop('name', blockExtraType.prop('name').replace('0', index));
        blockExtraData.prop('id', blockExtraData.prop('id').replace('0', index)).prop('name', blockExtraData.prop('name').replace('0', index));
        blockPosition.prop('id', blockPosition.prop('id').replace('0', index)).prop('name', blockPosition.prop('name').replace('0', index));
        blockVisibility.prop('id', blockVisibility.prop('id').replace('0', index)).prop('name', blockVisibility.prop('name').replace('0', index));

        // save position
        blockPosition.val(selectedPosition);

        // save extra id
        blockExtraId.val(selectedExtraId);

        // save extra type
        blockExtraType.val(selectedExtraType);

        // save extra data
        blockExtraData.val(JSON.stringify(selectedExtraData));

        // add block to dom
        block.appendTo($('#editContent'));

        // get block visibility
        var visible = blockVisibility.attr('checked');

        // add visual representation of block to template visualisation
        var addedVisual = jsBackend.pages.extras.addBlockVisual(selectedPosition, index, selectedExtraId, visible, selectedExtraType, selectedExtraData);

        // block/widget = don't show editor
        if (selectedExtraType !== 'usertemplate' && typeof extrasById != 'undefined' && typeof extrasById[selectedExtraId] != 'undefined') {
            $('.blockContentHTML', block).hide();
        }// editor or user template
        else {
            $('.blockContentHTML', block).show();
        }

        return addedVisual ? index : false;
    },

    // add block visual on template
    addBlockVisual: function(position, index, extraId, visible, extraType, extraData)
    {
        // check if the extra is valid
        if (extraType != 'usertemplate' && extraId != 0 && typeof extrasById[extraId] == 'undefined') return false;

        // block
        var editLink, title, description;
        if (extraType != 'usertemplate' && extraId != 0) {
            // link to edit this block/widget
            editLink = '';
            if (extrasById[extraId].type == 'block' && extrasById[extraId].data.url) editLink = extrasById[extraId].data.url;
            if (typeof extrasById[extraId].data.edit_url != 'undefined' && extrasById[extraId].data.edit_url) editLink = extrasById[extraId].data.edit_url;

            // title, description & visibility
            title = extrasById[extraId].human_name;
            description = extrasById[extraId].path;
        }

        // user template
        else if (extraType == 'usertemplate') {
            if (typeof(extraData) === "string" && extraData !== '') {
                extraData = JSON.parse(extraData);
            }

            editLink = '';
            title = utils.string.ucfirst(jsBackend.locale.lbl('UserTemplate'));
            if (extraData.title) {
                title += ': ' + extraData.title;
            }
            description = '';
            if (extraData.description) {
                description += extraData.description;
            }
        }

        // editor
        else {
            // link to edit this content, title, description & visibility
            editLink = '';
            title = utils.string.ucfirst(jsBackend.locale.lbl('Editor'));
            description = utils.string.stripTags($('#blockHtml' + index).val()).substr(0, 200);
        }

        var linkClass = '';
        if (extraType == 'usertemplate') {
            linkClass = 'editUserTemplate ';
        }
        else if (extraId == 0) {
            linkClass = 'showEditor ';
        }

        var showEditLink = (extraType === 'usertemplate' || (extraId != 0 && editLink));

        // create html to be appended in template-view
        var blockHTML = '<div class="templatePositionCurrentType' + (visible ? ' ' : ' templateDisabled') + '" data-block-id="' + index + '">' +
            '<span class="templateTitle">' + title + '</span>' +
            '<span class="templateDescription">' + description + '</span>' +
            '<div class="btn-group buttonHolder">' +
            '<a href="#" class="btn btn-default btn-icon-only btn-xs toggleVisibility"><span class="fa fa-' + (visible ? 'eye' : 'eye-slash') + '"></span></a>' +
            '<a href="' + (editLink ? editLink : '#') + '" class="' + linkClass + 'btn btn-primary btn-icon-only btn-xs' + '"' + (showEditLink ? ' target="_blank"' : '') + (showEditLink ? '' : ' onclick="return false;"') + ((showEditLink) || extraId == 0 ? '' : 'style="display: none;" ') + '><span class="fa fa-pencil"></span></a>' +
            '<a href="#" class="deleteBlock btn btn-danger btn-icon-only btn-xs"><span class="fa fa-trash-o"></span></a>' +
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
        var index = $(this).closest('*[data-block-id]').data('block-id');

        // save unaltered content
        var previousContent = $('#blockHtml' + index).val();

        // placeholder for block node that will be moved by the jQuery dialog
        $('#blockHtml' + index).parent().parent().parent().after('<div id="blockPlaceholder"></div>');

        // show dialog
        $('#blockHtmlSubmit').unbind('click').on('click', function(e) {
            e.preventDefault();

            // grab the content
            var content = $('#html').val();

            // save content
            jsBackend.pages.extras.setContent(index, content);

            // edit content = template is no longer original
            jsBackend.pages.template.original = false;

            // close dialog
            $('#blockHtml').modal('hide');
        });

        $('#blockHtmlCancel').unbind('click').on('click', function(e) {
            e.preventDefault();

            // reset content
            jsBackend.pages.extras.setContent(index, previousContent);

            // close the dialog
            $('#blockHtml').modal('hide');
        });

        $('#blockHtml').unbind('show.bs.modal').on('show.bs.modal', function(e) {
            // set content in editor
            CKEDITOR.instances['html'].setData(previousContent);
        }).modal('show');
    },

    // hide fallback
    hideFallback: function()
    {
        // after removing all from fallback; hide fallback
        if ($('#templateVisualFallback .templatePositionCurrentType').length === 0) $('#templateVisualFallback').hide();
    },

    // populate the dropdown with the modules
    populateExtraModules: function()
    {
        // get selected value
        var selectedType = $('#extraType').val();

        // hide
        $('#extraModuleHolder').hide();
        $('#extraExtraIdHolder').hide();
        $('#userTemplateHolder').hide();
        $('#extraModule').html('<option value="0">-</option>');
        $('#extraExtraId').html('<option value="0">-</option>');

        // only widgets and block need the module dropdown
        if (selectedType == 'widget' || selectedType == 'block') {
            // loop modules
            for (var i in extrasData) {
                // add option if needed
                if (typeof extrasData[i]['items'][selectedType] != 'undefined') $('#extraModule').append('<option value="' + extrasData[i].value + '">' + extrasData[i].name + '</option>');
            }

            // show
            $('#extraModuleHolder').show();
        }

        var userTemplates = jsBackend.pages.template.userTemplates;

        // show the defined user templates
        if (selectedType == 'usertemplate') {
            var $userTemplate = $('#userTemplate');
            $userTemplate.find('option').not('[value=-1]').remove();
            for (var j in userTemplates) {
                // add option if needed
                $userTemplate.append('<option value="' + j + '">' + userTemplates[j].title + '</option>');
            }
            $('#userTemplateHolder').show();
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
        if (typeof extrasData[selectedModule] != 'undefined' && typeof extrasData[selectedModule]['items'][selectedType] != 'undefined') {
            if (extrasData[selectedModule]['items'][selectedType].length == 1 && selectedType == 'block') {
                $('#extraExtraId').append('<option selected="selected" value="' + extrasData[selectedModule]['items'][selectedType][0].id + '">' + extrasData[selectedModule]['items'][selectedType][0].label + '</option>');
            }
            else {
                // loop items
                for (var i in extrasData[selectedModule]['items'][selectedType]) {
                    // add option
                    $('#extraExtraId').append('<option value="' + extrasData[selectedModule]['items'][selectedType][i].id + '">' + extrasData[selectedModule]['items'][selectedType][i].label + '</option>');
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
            var blockExtraType = $('.reset [name=block_extra_type_' + oldIndex + ']');
            var blockPosition = $('.reset [name=block_position_' + oldIndex + ']');
            var blockVisible = $('.reset [name=block_visible_' + oldIndex + ']');

            blockHtml.prop('id', blockHtml.prop('id').replace(oldIndex, newIndex)).prop('name', blockHtml.prop('name').replace(oldIndex, newIndex));
            blockExtraId.prop('id', blockExtraId.prop('id').replace(oldIndex, newIndex)).prop('name', blockExtraId.prop('name').replace(oldIndex, newIndex));
            blockExtraType.prop('id', blockExtraType.prop('id').replace(oldIndex, newIndex)).prop('name', blockExtraType.prop('name').replace(oldIndex, newIndex));
            blockPosition.prop('id', blockPosition.prop('id').replace(oldIndex, newIndex)).prop('name', blockPosition.prop('name').replace(oldIndex, newIndex));
            blockVisible.prop('id', blockVisible.prop('id').replace(oldIndex, newIndex)).prop('name', blockVisible.prop('name').replace(oldIndex, newIndex));

            // no longer mark as needing to be reset
            blockExtraId.parent('.contentBlock').removeClass('reset');

            // while we're at it, make sure the position is also correct
            blockPosition.val($(this).closest('*[data-position]').attr('data-position'));
        });

        // mark all as having been reset
        $('.contentBlock').removeClass('reset');
    },

    // save/reset the content
    setContent: function(index, content)
    {
        // don't set content if this is a usertemplate
        if ($('#blockExtraType' + index).val() === 'usertemplate') {
            return false;
        }

        // the content to set
        if (content != null) $('#blockHtml' + index).val(content);

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
        position = $(this).closest('*[data-position]').data('position');

        // init var
        var hasModules = false;

        // check if there already blocks linked
        $('input[id^=blockExtraId]').each(function()
        {
            // get id
            var id = $(this).val();

            // check if a block is already linked
            if (id !== '' && typeof extrasById[id] != 'undefined' && extrasById[id].type == 'block') hasModules = true;
        });

        // hide warnings
        $('#extraWarningAlreadyBlock').hide();
        $('#extraWarningHomeNoBlock').hide();

        // init var
        var enabled = true;

        // blocks linked?
        if (hasModules) {
            // disable module selection
            enabled = false;

            // show warning
            $('#extraWarningAlreadyBlock').show();
        }

        // home can't have any modules linked!
        if (typeof pageID != 'undefined' && pageID == 1) {
            // disable module selection
            enabled = false;

            // show warning
            $('#extraWarningHomeNoBlock').show();
        }

        // enable/disable blocks
        $('#extraType option[value=block]').attr('disabled', !enabled);

        // set type
        $('#extraType').val($('#extraType').val());
        $('#extraExtraId').val(0);

        // populate the modules
        jsBackend.pages.extras.populateExtraModules();

        // initialize the modal for choosing an extra
        if ($('#addBlock').length > 0) {
            $('#addBlockSubmit').unbind('click').on('click', function(e) {
                e.preventDefault();
                // fetch the selected extra type
                var selectedExtraType = $('#extraType').val();
                // fetch the selected extra id
                var selectedExtraId = $('#extraExtraId').val();
                // is user template?
                var isUserTemplate = (selectedExtraType == 'usertemplate');

                // fetch the selected extra data
                var selectedExtraData = $('#extraData').val();

                // fetch user template id
                if (isUserTemplate) {
                    selectedExtraId = $('#userTemplate').val();
                    selectedExtraData = jsData.pages.userTemplates[selectedExtraId];
                }

                // add the extra
                var index = jsBackend.pages.extras.addBlock(selectedExtraId, position, selectedExtraType, selectedExtraData);

                // add a block = template is no longer original
                jsBackend.pages.template.original = false;

                // close dialog
                $('#addBlock').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                    // if the added block was a user template, show the template popup immediately
                    if (isUserTemplate && index) {
                        $('.templatePositionCurrentType[data-block-id=' + index + '] .editUserTemplate').click();
                    }
                }).modal('hide');

                // if the added block was an editor, show the editor immediately
                if (!isUserTemplate && index && !(typeof extrasById != 'undefined' && typeof extrasById[selectedExtraId] != 'undefined')) {
                    $('.templatePositionCurrentType[data-block-id=' + index + '] .showEditor').click();
                }
            });

            $('#addBlock').modal('show');
        }
    },
    editUserTemplate: function(e)
    {
        // prevent default event action
        e.preventDefault();

        // fetch block index
        var index = $(this).parent().parent().attr('data-block-id');

        // fetch user template id
        var userTemplateId = $('#blockExtraId' + index).val();

        // fetch the current content
        var previousContent = $('#blockHtml' + index).val();

        var templateUrl, sourceHTML;

        // if there already was content, use this.
        if (previousContent !== '') {
            $('#userTemplateHiddenPlaceholder').html(previousContent);

            jsBackend.pages.extras.buildUserTemplateForm(
                $('#userTemplateHiddenPlaceholder'),
                $('#userTemplatePlaceholder')
            );
        }
        else {
            // if there was no content yet, take the default content
            templateUrl = String(jsBackend.pages.template.userTemplates[userTemplateId].file);

            $.ajax({
                url: templateUrl,
                dataType: 'html',
                success: function(data)
                {
                    $('#userTemplateHiddenPlaceholder').html(data);

                    jsBackend.pages.extras.buildUserTemplateForm(
                        $('#userTemplateHiddenPlaceholder'),
                        $('#userTemplatePlaceholder')
                    );
                }
            });
        }

        var $modal = $('#addUserTemplate');

        $modal.find('.js-submit-user-template').off('click').on('click', function(e) {
            jsBackend.pages.extras.saveUserTemplateForm(
                $('#userTemplateHiddenPlaceholder'),
                $('#userTemplatePlaceholder')
            );

            // grab content
            var content = $('#userTemplateHiddenPlaceholder').html();

            //save content
            jsBackend.pages.extras.setContent(index, content);

            // edit content = template is no longer original
            jsBackend.pages.template.original = false;

            $('#addUserTemplate').modal('hide');
        });
        $modal.off('hidden.bs.modal').on('hidden.bs.modal', function() {
            // the ajax file uploader inserts an input field in the body, remove it
            $('body > div > input[name="file"]').parent().remove();
            $('#userTemplatePlaceholder').html('');
        });
        $modal.modal('show');
    },

    /**
     * Builds a form containing all fields that should be replaced in the
     * hidden placeholder
     */
    buildUserTemplateForm: function($hiddenPlaceholder, $placeholder)
    {
        $placeholder.html('');

        $hiddenPlaceholder.find('*').each(function(key) {
            jsBackend.pages.extras.addCustomFieldInPlaceholderFor($(this), key, $placeholder);
        });
    },

    /**
     * Creates the html for a normal link
     */
    getLinkFieldHtml: function(text, url, label, key)
    {
        var html = '<div class="panel panel-default" id="user-template-link-' + key + '">';

        html += '<div class="panel-heading">';
        html += '<h3 class="panel-title">' + label + '</h3>';
        html += '</div>';

        html += '<div class="panel-body">';

        html += '<div class="form-group">';
        html += '<label>' + label + '</label>';
        html += '<input data-ft-label="' + label + '" type="text" class="form-control" value="' + text + '"/>';
        html += '</div>';

        html += '<div class="form-group last">';
        html += '<label>URL</label>';
        html += '<input data-ft-url="' + label + '" type="url" class="form-control" value="' + url + '"/>';
        html += '</div>';

        html += '</div>';

        html += '</div>';

        return html;
    },

    /**
     * Creates the html for a link without content
     */
    getLinkWithoutContentFieldHtml: function(url, label, key)
    {
        var html = '<div class="panel panel-default" id="user-template-link-without-content-' + key + '">';

        html += '<div class="panel-heading">';
        html += '<h3 class="panel-title">' + label + '</h3>';
        html += '</div>';

        html += '<div class="panel-body">';

        html += '<div class="form-group last">';
        html += '<input data-ft-url="' + label + '" type="url" class="form-control" value="' + url + '"/>';
        html += '</div>';

        html += '</div>';

        html += '</div>';

        return html;
    },

    /**
     * Creates the html for a text field
     */
    getTextFieldHtml: function(text, label, key)
    {
        var html = '<div class="panel panel-default" id="user-template-text-' + key + '">';

        html += '<div class="panel-heading">';
        html += '<h3 class="panel-title">' + label + '</h3>';
        html += '</div>';

        html += '<div class="panel-body">';

        html += '<div class="form-group last">';
        html += '<input data-ft-label="' + label + '" type="text" class="form-control" value="' + text + '" />';
        html += '</div>';

        html += '</div>';

        html += '</div>';

        return html;
    },

    /**
     * Creates the html for an editor
     */
    getTextAreaFieldHtml: function(text, label, key)
    {
        var html = '<div class="panel panel-default panel-editor" id="user-template-textarea-' + key + '">';

        html += '<div class="panel-heading">';
        html += '<h3 class="panel-title">' + label + '</h3>';
        html += '</div>';

        html += '<div class="panel-body">';

        html += '<div class="form-group last">';
        html += '<textarea class="form-control" data-ft-label="' + label + '" cols="83" rows="15">' + text + '</textarea>';
        html += '</div>';

        html += '</div>';

        html += '</div>';

        return html;
    },

    /**
     * Creates the html for an editor
     */
    getEditorFieldHtml: function(text, label, key)
    {
        var html = '<div class="panel panel-default panel-editor" id="user-template-editor-' + key + '">';

        html += '<div class="panel-heading">';
        html += '<h3 class="panel-title">' + label + '</h3>';
        html += '</div>';

        html += '<div class="panel-body">';

        html += '<div class="form-group last">';
        html += '<textarea id="user-template-cke-' + key + '" data-ft-label="' + label + '" cols="83" rows="15" class="inputEditor">' + text + '</textarea>';
        html += '</div>';

        html += '</div>';

        html += '</div>';

        return html;
    },

    /**
     * Creates the html for an image field
     */
    getImageFieldHtml: function(src, alt, label, isVisible, key)
    {
        var html = '<div class="panel panel-default" id="user-template-image-' + key + '">';

        html += '<div class="panel-heading">';
        html += '<h3 class="panel-title">' + label + '</h3>';
        html += '</div>';

        html += '<div class="panel-body clearfix">';

        html += '<div class="form-group thumbnail">';
        html += '<img class="img-responsive"' + (isVisible ? '' : ' style="display: none;"') + ' src="' + src + '" />';
        html += '<div class="caption" id="ajax-upload-' + key + '">';
        html += '<label>' + label + '</label>';
        // this will be replaced by the ajax uploader
        html += '<input data-ft-label="' + label + '" type="file" accept="image/*" />';
        html += '</div>';
        html += '</div>';

        html += '<div class="form-group">';
        html += '<label for="alt' + key + '">Alt attribute</label>';
        html += '<input class="form-control" type="text" id="alt' + key + '" value="' + alt + '" />';
        html += '</div>';

        html += '<div class="checkbox">';
        html += '<label><input type="checkbox"' + (isVisible ? 'checked' : '') + '/> ' + jsBackend.locale.lbl('ShowImage') + '</label>'
        html += '</div>';

        html += '</div>';

        html += '</div>';

        return html;
    },

    /**
     * Creates the html for an image background field
     */
    getImageBackgroundFieldHtml: function(src, label, key)
    {
        var html = '<div class="panel panel-default" id="user-template-image-background-' + key + '">';

        html += '<div class="panel-heading">';
        html += '<h3 class="panel-title">' + label + '</h3>';
        html += '</div>';

        html += '<div class="panel-body clearfix">';

        html += '<div class="form-group thumbnail">';
        html += '<img class="img-responsive"' + ' src="' + src + '" />';
        html += '<div class="caption" id="ajax-upload-' + key + '">';
        html += '<label>' + label + '</label>';
        // this will be replaced by the ajax uploader
        html += '<input data-ft-label="' + label + '" type="file" accept="image/*" />';
        html += '</div>';
        html += '</div>';

        html += '</div>';

        html += '</div>';

        return html;
    },

    /**
     * Checks if an element is some kind of special field that should have form
     * fields and builds the html for it
     */
    addCustomFieldInPlaceholderFor: function($element, key, $placeholder)
    {
        // replace links
        if (($element).is('[data-ft-type="link"]')) {
            $placeholder.append(jsBackend.pages.extras.getLinkFieldHtml($element.text(), $element.attr('href'), $element.data('ft-label'), key));

            return;
        }

        // replace links without content
        if ($element.is('[data-ft-type="link-without-content"]')) {
            $placeholder.append(jsBackend.pages.extras.getLinkWithoutContentFieldHtml($element.attr('href'), $element.data('ft-label'), key));

            return;
        }

        // replace text
        if ($element.is('[data-ft-type="text"]')) {
            $placeholder.append(jsBackend.pages.extras.getTextFieldHtml($element.text(), $element.data('ft-label'), key));

            return;
        }

        if ($element.is('[data-ft-type="textarea"]')) {
            $placeholder.append(jsBackend.pages.extras.getTextAreaFieldHtml($element.text(), $element.data('ft-label'), key));

            return;
        }

        // replace image
        if ($element.is('[data-ft-type="image"]')) {
            $placeholder.append(
                jsBackend.pages.extras.getImageFieldHtml(
                    $element.attr('src'),
                    $element.attr('alt'),
                    $element.data('ft-label'),
                    $element.attr('style') !== 'display: none;',
                    key
                )
            );

            // attach an ajax uploader to the field
            jsBackend.pages.extras.uploaders.push(new ss.SimpleUpload({
                button: 'ajax-upload-' + key,
                url: '/backend/ajax?fork[module]=Pages&fork[action]=UploadFile&type=UserTemplate',
                name: 'file',
                accept: 'image/*',
                responseType: 'json',
                onComplete: function(filename, response) {
                    if (!response) {
                        alert(filename + 'upload failed');
                        return false;
                    }

                    // cache the old image variable, we'll want to remove it to keep our filesystem clean
                    var oldImage = $('#user-template-image-' + key + ' img').attr('src');

                    $('#user-template-image-' + key + ' img').attr(
                        'src',
                        '/src/Frontend/Files/Pages/UserTemplate/' + response.data
                    );

                    // send a request to remove the old image if the old image doesn't have the same name
                    if (oldImage !== response.data) {
                        $.ajax({
                            data: {
                                fork: {module: 'Pages', action: 'RemoveUploadedFile'},
                                file: oldImage,
                                type: 'UserTemplate'
                            }
                        });
                    }
                }
            }));

            // handle the "show image" checkbox
            $('#user-template-image-' + key + ' input[type=checkbox]').on('click', function(e) {
                $('#user-template-image-' + key + ' img').toggle($(this).is(':checked'));
            });

            return;
        }

        // replace image background
        if ($element.is('[data-ft-type="image-background"]')) {
            $placeholder.append(
                jsBackend.pages.extras.getImageBackgroundFieldHtml(
                    $element.attr('data-src'),
                    $element.data('ft-label'),
                    key
                )
            );

            // attach an ajax uploader to the field
            jsBackend.pages.extras.uploaders.push(new ss.SimpleUpload({
                button: 'ajax-upload-' + key,
                url: '/backend/ajax?fork[module]=Pages&fork[action]=UploadFile&type=UserTemplate',
                name: 'file',
                accept: 'image/*',
                responseType: 'json',
                onComplete: function(filename, response) {
                    if (!response) {
                        alert(filename + 'upload failed');
                        return false;
                    }

                    // cache the old image variable, we'll want to remove it to keep our filesystem clean
                    var oldImage = $('#user-template-image-background-' + key + ' img').attr('src');

                    $('#user-template-image-background-' + key + ' img').attr(
                        'src',
                        '/src/Frontend/Files/Pages/UserTemplate/' + response.data
                    );

                    // send a request to remove the old image if the old image doesn't have the same name
                    if (oldImage !== response.data) {
                        $.ajax({
                            data: {
                                fork: {module: 'Pages', action: 'RemoveUploadedFile'},
                                file: oldImage,
                                type: 'UserTemplate'
                            }
                        });
                    }
                }
            }));

            return;
        }

        // replace editor
        if ($element.is('[data-ft-type="editor"]')) {
            $placeholder.append(jsBackend.pages.extras.getEditorFieldHtml($element.html(), $element.data('ft-label'), key));

            jsBackend.ckeditor.load();

            return;
        }
    },

    /**
     * Takes all the data out of the user template form and injects it again in
     * the original template html
     */
    saveUserTemplateForm: function($hiddenPlaceholder, $placeholder)
    {
        $hiddenPlaceholder.find('*').each(function(key) {
            jsBackend.pages.extras.saveCustomField($(this), key, $placeholder);
        });
    },

    saveCustomField: function($element, key, $placeholder)
    {
        if ($element.is('[data-ft-type="link"]')) {
            var $labelField = $placeholder.find('#user-template-link-' + key + ' input[data-ft-label]');
            var $urlField = $placeholder.find('#user-template-link-' + key + ' input[data-ft-url]');

            $element.attr('href', $urlField.val());
            $element.text($labelField.val());

            return;
        }

        if ($element.is('[data-ft-type="link-without-content"]')) {
            var $urlField = $placeholder.find('#user-template-link-without-content-' + key + ' input[data-ft-url]');

            $element.attr('href', $urlField.val());

            return;
        }

        if ($element.is('[data-ft-type="text"]')) {
            var $labelField = $placeholder.find('#user-template-text-' + key + ' input[data-ft-label]');

            $element.text($labelField.val());

            return;
        }

        if ($element.is('[data-ft-type="textarea"]')) {
            var $textarea = $placeholder.find('#user-template-textarea-' + key + ' textarea[data-ft-label]');

            $element.text($textarea.val());

            return;
        }

        if ($element.is('[data-ft-type="image"]')) {
            var $img = $placeholder.find('#user-template-image-' + key + ' img');
            var alt = $placeholder.find('#alt' + key).val();
            var $visible = $placeholder.find('#user-template-image-' + key + ' input[type=checkbox]');

            $element.attr('src', $img.attr('src'));
            $element.attr('alt', alt);
            if ($visible.is(':checked')) {
                $element.attr('style', 'display: block;');
            }
            else {
                $element.attr('style', 'display: none;');
            }

            return;
        }

        if ($element.is('[data-ft-type="image-background"]')) {
            var $img = $placeholder.find('#user-template-image-background-' + key + ' img');

            $element.attr('data-src', $img.attr('src'));
            $element.css('background-image', 'url("' + $img.attr('src') + '")');

            return;
        }

        if ($element.is('[data-ft-type="editor"]')) {
            var $textarea = $placeholder.find('#user-template-editor-' + key + ' textarea[data-ft-label]');

            $element.html($textarea.val());

            // destroy the editor
            var editor = CKEDITOR.instances['user-template-cke-' + key];
            if (editor) {
                editor.destroy(true);
            }

            return;
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
        if ($('#confirmDeleteBlock').length > 0) {
            $('#confirmDeleteBlockSubmit').unbind('click').on('click', function(e) {
                // delete this block
                jsBackend.pages.extras.deleteBlock(element.parent().parent('.templatePositionCurrentType').attr('data-block-id'));

                // delete a block = template is no longer original
                jsBackend.pages.template.original = false;

                // close dialog
                $('#confirmDeleteBlock').modal('hide');
            })

            $('#confirmDeleteBlock').modal('show');
        }
    },

    // show the groups for authentication
    showGroups: function(e)
    {
        // save element to variable
        $('.js-authentication-groups').toggle();
    },

    // re-order blocks
    sortable: function(element)
    {
        // make blocks sortable
        element.sortable({
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
                if ($(this).parents('#templateVisualLarge').length > 0) {
                    // disable dropping to fallback
                    $('div.linkedBlocks').sortable('option', 'connectWith', '#templateVisualLarge div.linkedBlocks');
                }
                else {
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
        var index = $(this).closest('*[data-block-id]').attr('data-block-id');

        // get visibility checbox
        var checkbox = $('#blockVisible' + index);

        // get current visibility state
        var visible = checkbox.is(':checked');

        // invert visibility
        visible = !visible;

        // change visibility state
        checkbox.attr('checked', visible);

        // remove current visibility indicators
        $(this).find('.fa').removeClass('fa-eye fa-eye-slash');
        $(this).closest('*[data-block-id]').removeClass('templateDisabled');

        // toggle visibility indicators
        if (visible) {
            $(this).find('.fa').addClass('fa-eye');
        }
        else {
            $(this).find('.fa').addClass('fa-eye-slash');
            $(this).closest('*[data-block-id]').addClass('templateDisabled');
        }
    },

    // display an effect on updated items
    updatedBlock: function(element)
    {
        element.effect('highlight', {color: '#D9E5F3'});
    }
};

/**
 * All methods related to the templates
 */
jsBackend.pages.template = {
    // indicates whether or not the page content is original or has been altered already
    original: true,
    userTemplates: {},

    // init, something like a constructor
    init: function()
    {
        // bind events
        jsBackend.pages.template.changeTemplateBindSubmit();

        // load to initialize when adding a page
        jsBackend.pages.template.changeTemplate();
    },

    // method to change a template
    changeTemplate: function()
    {
        // get checked
        var selected = $('#templateList input:radio:checked').val();

        // get current & old template
        var old = templates[$('#templateId').val()];
        var current = templates[selected];
        var i = 0;

        // show or hide the image tab
        if ('image' in current.data && current.data.image) {
            $('.js-page-image-tab').show();
        }
        else {
            $('.js-page-image-tab').hide();
        }

        // hide default (base) block
        $('#block-0').hide();

        // reset HTML for the visual representation of the template
        $('#templateVisual').html(current.html);
        $('#templateVisualLarge').html(current.htmlLarge);
        $('#templateVisualFallback .linkedBlocks').children().remove();
        $('#templateId').val(selected);
        $('#templateLabel, #tabTemplateLabel').html(current.label);

        // make new positions sortable
        jsBackend.pages.extras.sortable($('#templateVisualLarge div.linkedBlocks'));

        // hide fallback by default
        $('#templateVisualFallback').hide();

        // remove previous fallback blocks
        $('input[id^=blockPosition][value=fallback][id!=blockPosition0]').parent().remove();

        // check if we have already committed changes (if not, we can just ignore existing blocks and remove all of them)
        if (current != old && jsBackend.pages.template.original) $('input[id^=blockPosition][id!=blockPosition0]').parent().remove();

        // loop existing blocks
        $('#editContent .contentBlock').each(function(i)
        {
            // fetch variables
            var index = $('input[id^=blockExtraId]', this).prop('id').replace('blockExtraId', '');
            var extraId = parseInt($('input[id^=blockExtraId]', this).val());
            var position = $('input[id^=blockPosition]', this).val();
            var html = $('textarea[id^=blockHtml]', this).val();

            // skip default (base) block (= continue)
            if (index == 0) return true;

            // blocks were present already = template was not original
            jsBackend.pages.template.original = false;

            // check if this block is a default of the old template, in which case it'll go to the fallback position
            if (current != old && $.inArray(extraId, old.data.default_extras[position]) >= 0 && html === '') $('input[id=blockPosition' + index + ']', this).val('fallback');
        });

        // init var
        newDefaults = [];

        // check if this default block has been changed
        if (current != old || (typeof initDefaults != 'undefined' && initDefaults)) {
            // this is a variable indicating that the add-action may initially set default blocks
            if (typeof initDefaults != 'undefined') initDefaults = false;

            // loop positions in new template
            for (var position in current.data.default_extras) {
                // loop default extra's on positions
                for (var block in current.data.default_extras[position]) {
                    // grab extraId
                    extraId = current.data.default_extras[position][block];

                    // find existing block sent to default
                    var existingBlock = $('input[id^=blockPosition][value=fallback]:not(#blockPosition0)').parent().find('input[id^=blockExtraId][value=' + extraId + ']').parent();

                    // if this block did net yet exist, add it
                    if (existingBlock.length === 0) {
                        newDefaults.push(new Array(extraId, position));
                    }// if this block already existed, reset it to correct (new) position
                    else {
                        $('input[id^=blockPosition]', existingBlock).val(position);
                    }
                }
            }
        }

        // loop existing blocks
        $('#editContent .contentBlock').each(function(i)
        {
            // fetch variables
            var index = $('input[id^=blockExtraId]', this).prop('id').replace('blockExtraId', '');
            var extraId = parseInt($('input[id^=blockExtraId]', this).val());
            var extraType = $('input[id^=blockExtraType]', this).val();
            var extraData = $('input[id^=blockExtraData]', this).val();
            var position = $('input[id^=blockPosition]', this).val();
            var visible = $('input[id^=blockVisible]', this).attr('checked');

            // skip default (base) block (= continue)
            if (index == 0) return true;

            // check if this position exists
            if ($.inArray(position, current.data.names) < 0) {
                // blocks in positions that do no longer exist should go to fallback
                position = 'fallback';

                // save position as fallback
                $('input[id=blockPosition' + index + ']', this).val(position);

                // show fallback
                $('#templateVisualFallback').show();
            }

            // add visual representation of block to template visualisation
            added = jsBackend.pages.extras.addBlockVisual(position, index, extraId, visible, extraType, extraData);

            // if the visual could be not added, remove the content entirely
            if (!added) $(this).remove();
        });

        // reset block indexes
        jsBackend.pages.extras.resetIndexes();

        // add new defaults at last
        for (var i in newDefaults) jsBackend.pages.extras.addBlock(newDefaults[i][0], newDefaults[i][1]);
    },

    // bind template change submit click event
    changeTemplateBindSubmit: function(e)
    {
        // prevent the default action
        $('#changeTemplateSubmit').unbind('click').on('click', function(e) {
            e.preventDefault();
            if ($('#templateList input:radio:checked').val() != $('#templateId').val()) {
                // change the template for real
                jsBackend.pages.template.changeTemplate();
            }

            // close modal
            $('#changeTemplate').modal('hide');
        });
    }
};

/**
 * All methods related to the tree
 */
jsBackend.pages.tree = {
    // init, something like a constructor
    init: function()
    {
        if ($('#tree div').length === 0) return false;

        // add "treeHidden"-class on leafs that are hidden, only for browsers that don't support opacity
        if (!jQuery.support.opacity) $('#tree ul li[rel="hidden"]').addClass('treeHidden');

        var openedIds = [];
        if (typeof pageID != 'undefined') {
            // get parents
            var parents = $('#page-' + pageID).parents('li');

            // init var
            openedIds = ['page-' + pageID];

            // add parents
            for (var i = 0; i < parents.length; i++) openedIds.push($(parents[i]).prop('id'));
        }

        // add home if needed
        if (!utils.array.inArray('page-1', openedIds)) openedIds.push('page-1');

        var options = {
            ui: {theme_name: 'fork'},
            opened: openedIds,
            rules: {
                multiple: false,
                multitree: 'all',
                drag_copy: false
            },
            lang: {loading: utils.string.ucfirst(jsBackend.locale.lbl('Loading'))},
            callback: {
                beforemove: jsBackend.pages.tree.beforeMove,
                onselect: jsBackend.pages.tree.onSelect,
                onmove: jsBackend.pages.tree.onMove
            },
            plugins: {
                cookie: {prefix: 'jstree_', types: {selected: false}, options: {path: '/'}}
            }
        };

        // create tree
        $('#tree div').tree(options);

        // layout fix for the tree
        $('.tree li.open').each(function()
        {
            // if the so-called open-element doesn't have any childs we should replace the open-class.
            if ($(this).find('ul').length === 0) $(this).removeClass('open').addClass('leaf');
        });

        // set the item selected
        if (typeof selectedId != 'undefined') $('#' + selectedId).addClass('selected');
    },

    // before an item will be moved we have to do some checks
    beforeMove: function(node, refNode, type, tree)
    {
        // get pageID that has to be moved
        var parentPageID;
        var currentPageID = $(node).prop('id').replace('page-', '');
        if (typeof refNode == 'undefined') {
            parentPageID = 0;
        }
        else {
            parentPageID = $(refNode).prop('id').replace('page-', '');
        }

        // home is a special item
        if (parentPageID == '1') {
            if (type == 'before') return false;
            if (type == 'after') return false;
        }

        // init var
        var result = false;

        // make the call
        $.ajax({
            async: false, // important that this isn't asynchronous
            data: {
                fork: {action: 'GetInfo'},
                id: currentPageID
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                if (jsBackend.debug) alert(textStatus);
                result = false;
            },
            success: function(json, textStatus)
            {
                if (json.code != 200) {
                    if (jsBackend.debug) alert(textStatus);
                    result = false;
                }
                else {
                    if (json.data.allow_move) result = true;
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
        if (typeof newPageURL != 'undefined' && newPageURL != currentPageURL) window.location = newPageURL;
    },

    // when an item is moved
    onMove: function(node, refNode, type, tree, rollback)
    {
        // get the tree
        tree = tree.container.data('tree');

        // get pageID that has to be moved
        var currentPageID = $(node).prop('id').replace('page-', '');

        // get pageID wheron the page has been dropped
        var droppedOnPageID;
        if (typeof refNode == 'undefined') {
            droppedOnPageID = 0;
        }
        else {
            droppedOnPageID = $(refNode).prop('id').replace('page-', '');
        }

        // make the call
        $.ajax({
            data: {
                fork: {action: 'Move'},
                id: currentPageID,
                dropped_on: droppedOnPageID,
                type: type,
                tree: tree
            },
            success: function(json, textStatus)
            {
                if (json.code != 200) {
                    if (jsBackend.debug) alert(textStatus);

                    // show message
                    jsBackend.messages.add('danger', jsBackend.locale.err('CantBeMoved'));

                    // rollback
                    $.tree.rollback(rollback);
                }
                else {
                    // show message
                    jsBackend.messages.add('success', jsBackend.locale.msg('PageIsMoved').replace('%1$s', json.data.title));
                }
            }
        });
    }
};

$(jsBackend.pages.init);
