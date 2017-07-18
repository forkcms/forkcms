/**
 * Interaction for the connection of media to the media module.
 * global: jsBackend
 * global: utils
 */
jsBackend.mediaLibraryHelper =
{
    init: function()
    {
        jsBackend.mediaLibraryHelper.buildMovieStorageTypeDropdown();
        jsBackend.mediaLibraryHelper.group.init();
        jsBackend.mediaLibraryHelper.upload.preInit();
        jsBackend.mediaLibraryHelper.upload.init();
    },

    buildMovieStorageTypeDropdown: function()
    {
        // Add movie storage type in MediaLibraryHelper
        var $movieStorageTypeDropdown = $('#mediaMovieStorageType');
        $(jsBackend.data.get('MediaLibrary.mediaAllowedMovieSource')).each(function(index, value){
            var html = '<option value="' + value + '"';

            if (index === 0) {
                html += ' selected="selected"';
            }

            html += '>' + value + '</option>';

            // Add to dropdown
            $movieStorageTypeDropdown.append(html);
        });
    }
};

/**
 * Edit group
 * global: jsBackend
 */
var media = {};
var mediaFolders = false;
var mediaGroups = {};
var currentMediaGroupId = 0;
var mediaFolderId = undefined;
var currentAspectRatio = false;
var currentMediaItemIds = [];
jsBackend.mediaLibraryHelper.group =
{
    init: function()
    {
        // start or not
        if ($('[data-role=media-library-add-dialog]').length == 0) {
            return false;
        }

        // get galleries
        jsBackend.mediaLibraryHelper.group.getGroups();

        // add media dialog
        jsBackend.mediaLibraryHelper.group.addMediaDialog();

        // init sequences
        var prevSequence = '';
        var newSequence = '';

        // bind drag'n drop to media
        $(".mediaConnectedBox .ui-sortable").sortable({
            opacity: 0.6,
            cursor: 'move',
            start : function(e, ui) {
                // redefine previous and new sequence
                prevSequence = newSequence = $('#group-' + currentMediaGroupId + ' .mediaIds').first().val();

                // don't prevent the click
                ui.item.removeClass('preventClick');
            },
            update: function() {
                // set group i
                currentMediaGroupId = $(this).parent().parent().attr('id').replace('group-', '');

                // prepare correct new sequence value for hidden input
                newSequence = $(this).sortable("serialize").replace(/media\-/g, '').replace(/\[\]\=/g, '-').replace(/&/g, ',');

                // add value to hidden input
                $('#group-' + currentMediaGroupId + ' .mediaIds').first().val(newSequence);
            },
            stop : function(e, ui) {
                // prevent click
                ui.item.addClass('preventClick');

                // new sequence: de-select this item + update sequence
                if (prevSequence != newSequence) {
                    // remove selected class
                    ui.item.removeClass('selected');

                    // update disconnect button
                    jsBackend.mediaLibraryHelper.group.updateDisconnectButton(currentMediaGroupId);
                // same sequence: select this item (accidently moved this media a few millimeters counts as a click)
                } else {
                    // don't prevent the click, click handler does the rest
                    ui.item.removeClass('preventClick');
                }
            }
        });

        // bind hover to media items so you see the edit button
        $('.mediaConnectedItems').on('hover', '.ui-state-default', function() {
            $(this).toggleClass('hover');
        });

        // bind click to media items so you can select them
        $('.mediaConnectedItems').on('click', '.mediaHolder', function() {
            // click handler executes
            if (!$(this).parent().hasClass('preventClick')) {
                // toggle class
                $(this).parent().toggleClass('selected');

                // define groupId (@todo: can this shorter?)
                var groupId = $(this).parent().parent().parent().parent().attr('id').replace('group-', '');

                // update disconnect button
                jsBackend.mediaLibraryHelper.group.updateDisconnectButton(groupId);
            // else remove prevent click
            } else {
                $(this).parent().removeClass('preventClick');
            }

            // external modules could use this
            $('body').trigger('mediaSelectedConnectedItemsChanged');
        });

        // bind click to disconnect button so you can disconnect media items
        $('.mediaEditBox').on('click', '.disconnectMediaItemsButton', function() {
            // button is not disabled
            if (!$(this).hasClass('disabled')) {
                // define groupId
                var groupId = $(this).data('i');

                // disconnect items
                jsBackend.mediaLibraryHelper.group.disconnectMediaFromGroup(groupId);

                // update disconnect button
                jsBackend.mediaLibraryHelper.group.updateDisconnectButton(groupId);
            }
        });
    },

    /**
     * Adds add media dialog, where you can connect/disconnect media to a group
     */
    addMediaDialog : function()
    {
        var $addMediaDialog = $('[data-role=media-library-add-dialog]');
        var $addMediaSubmit = $('#addMediaSubmit');

        $addMediaSubmit.on('click', function() {
            // add uploaded media to current group
            jsBackend.mediaLibraryHelper.upload.addUploadedMediaToGroup();

            // push media to group
            jsBackend.mediaLibraryHelper.group.updateGroupMedia();

            // show message
            jsBackend.messages.add('success', jsBackend.locale.msg('MediaGroupEdited'));

            // close the dialog
            $addMediaDialog.modal('hide');
        });

        // on show
        $addMediaDialog.on('show.bs.modal', jsBackend.mediaLibraryHelper.upload.init);

        // bind click when opening "add media dialog"
        $('.addMediaButton').on('click', function(e) {
            // prevent default
            e.preventDefault();

            // redefine folderId when clicked on other group
            if ($(this).data('i') != currentMediaGroupId || $(this).data('aspectRatio') != currentAspectRatio) {
                // clear folders cache
                jsBackend.mediaLibraryHelper.group.clearFoldersCache();
            }

            // define groupId
            currentMediaGroupId = $(this).data('i');
            currentAspectRatio = $(this).data('aspectRatio');
            if (currentAspectRatio === undefined) {
                currentAspectRatio = false;
            }

            // get current media for group
            currentMediaItemIds = ($('#group-' + currentMediaGroupId + ' .mediaIds').first().val() != '')
                ? $.trim($('#group-' + currentMediaGroupId + ' .mediaIds').first().val()).split(',') : [];

            // set the group media
            mediaGroups[currentMediaGroupId].media = currentMediaItemIds;

            // load and add folders
            jsBackend.mediaLibraryHelper.group.getFolders();

            // load and get folder counts for group
            jsBackend.mediaLibraryHelper.group.getFolderCountsForGroup();

            // load and add media for group
            jsBackend.mediaLibraryHelper.group.getMedia();

            // toggle upload boxes
            jsBackend.mediaLibraryHelper.upload.toggleUploadBoxes();

            // open dialog
            $addMediaDialog.modal('show');
        });

        // bind change when selecting other folder
        $('#mediaFolders').on('change', function() {
            // cache current folder id
            mediaFolderId = $(this).val();

            // get media for this folder
            jsBackend.mediaLibraryHelper.group.getMedia();
        });
    },

    /**
     * Clear the folders cache when necessary
     */
    clearFoldersCache : function()
    {
        mediaFolders = false;
    },

    /**
     * Disconnect media fast from this group
     *
     * @param {int} groupId The group id we want to disconnect from.
     */
    disconnectMediaFromGroup : function(groupId)
    {
        // define currentMediaGroupId
        currentMediaGroupId = groupId;

        // current ids
        var currentIds = $.trim($('#group-' + currentMediaGroupId + ' .mediaIds').first().val()).split(',');

        // get selected items
        var $items = jsBackend.mediaLibraryHelper.group.getSelectedItems(currentMediaGroupId);

        // get ids from selected items
        $items.each(function() {
            // get id
            var id = $(this).attr('id').replace('media-', '');

            // remove from array
            currentIds = jQuery.grep(currentIds, function(value) {
                return value != id;
            });
        });

        // redefine current media group
        currentMediaItemIds = currentIds;

        // only get new folder counts on startup
        if (mediaGroups[currentMediaGroupId].id != 0 && mediaGroups[currentMediaGroupId].count == undefined) {
            // load folder counts for group using ajax
            $.ajax({
                data: {
                    fork: {
                        module: 'MediaLibrary',
                        action: 'MediaFolderGetCountsForGroup'
                    },
                    group_id : mediaGroups[currentMediaGroupId].id
                },
                success: function(json, textStatus) {
                    if (json.code != 200) {
                        // show error if needed
                        if (jsBackend.debug) {
                            alert(textStatus);
                        }

                        return;
                    }

                    // cache folder counts
                    mediaGroups[currentMediaGroupId].count = json.data;

                    // update group media
                    jsBackend.mediaLibraryHelper.group.updateGroupMedia();

                    // update folder counts for items
                    jsBackend.mediaLibraryHelper.group.updateFolderCountsForItemsToDisconnect($items);

                    // update disconnect button
                    jsBackend.mediaLibraryHelper.group.updateDisconnectButton(groupId);
                }
            });
        } else {
            // update group media
            jsBackend.mediaLibraryHelper.group.updateGroupMedia();

            // update folder counts for items
            jsBackend.mediaLibraryHelper.group.updateFolderCountsForItemsToDisconnect($items);

            // update disconnect button
            jsBackend.mediaLibraryHelper.group.updateDisconnectButton(groupId);
        }
    },

    /**
     * @param {int} groupId
     * @returns {*|jQuery|HTMLElement}
     */
    get : function(groupId)
    {
        return $('#group-' + groupId);
    },

    /**
     * Load in the folders count for a group
     */
    getFolderCountsForGroup : function()
    {
        // only get new folder counts on startup
        if (mediaGroups[currentMediaGroupId].id != 0 && mediaGroups[currentMediaGroupId].count == undefined) {
            // load folder counts for group using ajax
            $.ajax({
                data: {
                    fork: {
                        module: 'MediaLibrary',
                        action: 'MediaFolderGetCountsForGroup'
                    },
                    group_id : mediaGroups[currentMediaGroupId].id
                },
                success: function(json, textStatus) {
                    if (json.code != 200) {
                        // show error if needed
                        if (jsBackend.debug) {
                            alert(textStatus);
                        }
                    } else {
                        // cache folder counts
                        mediaGroups[currentMediaGroupId].count = json.data;

                        // update folders
                        jsBackend.mediaLibraryHelper.group.updateFolders();
                    }
                }
            });
        }
    },

    /**
     * Load in the folders and add numConnected from the group
     */
    getFolders : function()
    {
        if (mediaFolders) {
            return;
        }

        // load folders using ajax
        $.ajax({
            data: {
                fork: {
                    module: 'MediaLibrary',
                    action: 'MediaFolderFindAll'
                }
            },
            success: function(json, textStatus) {
                if (json.code != 200) {
                    // show error if needed
                    if (jsBackend.debug) {
                        alert(textStatus);
                    }

                    return;
                }

                // cache folders
                mediaFolders = json.data;

                // update folders
                jsBackend.mediaLibraryHelper.group.updateFolders();
            }
        });
    },

    /**
     * Get groups from looking at the DOM
     */
    getGroups : function()
    {
        $('.mediaGroup').each(function() {
            var activateFallback = false;
            var mediaGroupId = $(this).data('media-group-id');

            // Fallback: we have no mediaGroupId
            // This means we tried saving the page, but an error was thrown, so the mediaGroup was empty again
            // But in the form field mediaIds, the ids are still present, so we need to use them and show the list items again
            if (mediaGroupId === '') {
                // redefine media group id
                mediaGroupId = $(this).find('.mediaGroupId').val();

                // Redefine wrong id (mediaGroupId was missing)
                $(this).attr('id', 'group-' + mediaGroupId);
                $(this).data('id', mediaGroupId);
                $(this).find('.addMediaButton').first().data('i', mediaGroupId);
                $(this).find('.disconnectMediaItemsButton').first().data('i', mediaGroupId);

                activateFallback = true;
            }

            var type = $('#group-' + mediaGroupId + ' .type').first().val();
            // get current media for group
            var mediaIds = ($('#group-' + mediaGroupId + ' .mediaIds').length > 0 && $('#group-' + mediaGroupId + ' .mediaIds').first().val() != '')
                ? $.trim($('#group-' + mediaGroupId + ' .mediaIds').first().val()).split(',') : [];

            // Push ids to array
            mediaGroups[mediaGroupId] = {
                'id' : mediaGroupId,
                'media' : mediaIds,
                'type' : type
            };

            if (activateFallback && mediaIds.length > 0) {
                // load media
                $.ajax( {
                    data: {
                        fork: {
                            module: 'MediaLibrary',
                            action: 'MediaItemGetAllById'
                        },
                        media_ids: mediaIds.join(',')
                    },
                    success: function(json, textStatus) {
                        if (json.code != 200) {
                            // show error if needed
                            if (jsBackend.debug) {
                                alert(textStatus);
                            }
                        } else {
                            // Define the variables
                            var $group = $('#group-' + mediaGroupId);
                            var $holder = $group.find('.mediaConnectedItems').first();

                            // Remove paragraph which says that we don't have any media connected
                            $group.find('.mediaNoItems').remove();

                            $(json.data.items).each(function(index, item) {
                                // add HTML for MediaItem to connect to holder
                                $holder.append(jsBackend.mediaLibraryHelper.templates.getHTMLForMediaItemToConnect(item));
                            });
                        }
                    }
                });
            }
        });
    },

    /**
     * Get media items in a group
     *
     * @param {int} groupId [optional]
     */
    getItems : function(groupId)
    {
        var id = (groupId) ? groupId : currentMediaGroupId;
        return $('#group-' + id).find('.mediaConnectedItems');
    },

    /**
     * Load in the media for a group or in a folder
     */
    getMedia : function()
    {
        // Load media from cache
        if (mediaFolderId === null || !media[mediaFolderId]) {
            jsBackend.mediaLibraryHelper.group.updateMedia();
        }

        // Load media using ajax
        $.ajax( {
            data: {
                fork: {
                    module: 'MediaLibrary',
                    action: 'MediaItemFindAll'
                },
                group_id: (mediaGroups[currentMediaGroupId]) ? mediaGroups[currentMediaGroupId].id : null,
                folder_id: mediaFolderId,
                aspect_ratio: currentAspectRatio
            },
            success: function(json, textStatus) {
                if (json.code != 200) {
                    // show error if needed
                    if (jsBackend.debug) {
                        alert(textStatus);
                    }

                    return;
                }

                // only do this when current folder is different
                if (json.data.folder != 0) {
                    // redefine folder id
                    mediaFolderId = json.data.folder;

                    // cache media
                    media[mediaFolderId] = json.data.media;
                // redefine current folder as none
                } else {
                    mediaFolderId = 0;
                }

                // update media
                jsBackend.mediaLibraryHelper.group.updateMedia();
            }
        });
    },

    /**
     * @param {int} groupId
     * @returns {*|jQuery|HTMLElement}
     */
    getSelectedItems : function(groupId)
    {
        return jsBackend.mediaLibraryHelper.group.get(groupId).find('.mediaConnectedItems li.selected');
    },

    updateFolderSelected : function()
    {
        // select the current media folder
        $('#mediaFolders').val(mediaFolderId);
    },

    /**
     * Enable/disable the disconnect button
     *
     * @param {int} groupId
     */
    updateDisconnectButton : function(groupId)
    {
        // init variables
        var $group = jsBackend.mediaLibraryHelper.group.get(groupId);
        var $items = jsBackend.mediaLibraryHelper.group.getSelectedItems(groupId);

        // toggle disabled button
        $group.find('.mediaEditBox .disconnectMediaItemsButton').toggleClass('disabled', ($items.length <= 0));
    },

    /**
     * Update the folder count
     *
     * @param {int} folderId - The folderId where you want the count to change.
     * @param {string} updateCount - Allowed: '+' or '-'
     * @param {int} updateWithValue - The value you want to add or substract.
     */
    updateFolderCount : function(folderId, updateCount, updateWithValue)
    {
        // count not found - add it
        if (mediaGroups[currentMediaGroupId].count == undefined) {
            // define new object
            var obj = {};

            // redefine object
            obj[folderId] = 0;

            // add object to count
            mediaGroups[currentMediaGroupId].count = obj;
        }

        // folder not found - add it to object
        if (mediaGroups[currentMediaGroupId].count[folderId] == undefined) {
            // update object to count
            mediaGroups[currentMediaGroupId].count[folderId] = 0;
        }

        // init count
        var count = parseInt(mediaGroups[currentMediaGroupId].count[folderId]);

        // subtract or add value
        count = (updateCount == '-') ? (count - updateWithValue) : (count + updateWithValue);

        // redefine count when under zero
        if (count < 0) {
            count = 0;
        // redefine amount when max has reached
        } else if (mediaFolders[folderId] != undefined && count > mediaFolders[folderId].numMedia) {
            count = mediaFolders[folderId].numMedia;
        }

        // update count
        mediaGroups[currentMediaGroupId].count[folderId] = count;

        // update folders
        jsBackend.mediaLibraryHelper.group.updateFolders();
    },

    /**
     * Update folder counts for items
     *
     * @param {array} $items - The media items
     */
    updateFolderCountsForItemsToDisconnect : function($items)
    {
        // update folder count
        $items.each(function() {
            // get id
            var thisFolderId = $(this).data('folderId');

            // update folder count
            jsBackend.mediaLibraryHelper.group.updateFolderCount(thisFolderId, '-', 1);
        });
    },

    updateFolders : function()
    {
        // add folders to dropdown
        $('#mediaFolders').html(jsBackend.mediaLibraryHelper.templates.getHTMLForMediaFolders(mediaFolders));

        // select the correct folder
        jsBackend.mediaLibraryHelper.group.updateFolderSelected();
    },

    /**
     * Update group media hidden field
     */
    updateGroupMedia : function()
    {
        // current connected items
        var $currentItems = jsBackend.mediaLibraryHelper.group.getItems();

        // current ids
        var currentIds = ($('#group-' + currentMediaGroupId + ' .mediaIds').first().val() != '')
            ? $.trim($('#group-' + currentMediaGroupId + ' .mediaIds').first().val()).split(',') : [];

        // define empty
        var empty = (currentMediaItemIds.length == 0);

        // check which items to add
        $(currentMediaItemIds).each(function(i, id) {
            // add item
            if (!utils.array.inArray(id, currentIds)) {
                // loop media folders
                $.each(media, function(index, items) {
                    // loop media items in folder
                    $.each(items, function(index, item) {
                        // item found
                        if (id == item.id) {
                            // Add HTML for MediaItem to Connect
                            $currentItems.append(jsBackend.mediaLibraryHelper.templates.getHTMLForMediaItemToConnect(item));
                        }
                    });
                });
            } else {
                // delete from array
                currentIds = jQuery.grep(currentIds, function(value) {
                    return value != id;
                });
            }
        });

        // check which items to delete
        $(currentIds).each(function(i, id) {
            // remove item
            $($currentItems).find('#media-' + id).remove();
        });

        // update the group media
        mediaGroups[currentMediaGroupId].media = currentMediaItemIds;

        // add empty media paragraph
        if (empty) {
            jsBackend.mediaLibraryHelper.group.getItems().after('<p class="mediaNoItems helpTxt">' + jsBackend.locale.msg('MediaNoItemsConnected') + '</p>');
        // delete empty media paragraph
        } else {
            $('#group-' + currentMediaGroupId).find('.mediaNoItems').remove();
            $('#group-' + currentMediaGroupId).find('.media-group-type-errors').remove();
        }

        // update the hidden group field for media
        $('#group-' + currentMediaGroupId).find('.mediaIds').first().val(currentMediaItemIds.join(','));

        // redefine
        currentMediaItemIds = [];
    },

    /**
     * Update the media
     */
    updateMedia : function()
    {
        // init variables
        var mediaItemTypes = jsBackend.data.get('MediaLibrary.mediaItemTypes');
        var html = {};
        var counts = {};
        var rowNoItems = jsBackend.mediaLibraryHelper.templates.getHTMLForEmptyTableRow();

        $(mediaItemTypes).each(function(index, type){
            html[type] = '';
            counts[type] = 0;
        });

        // loop media
        $.each(media[mediaFolderId], function(i, item) {
            // check if media is connected or not
            var connected = (typeof mediaGroups[currentMediaGroupId] == 'undefined') ? false : utils.array.inArray(item.id, mediaGroups[currentMediaGroupId].media);

            // Redefine
            html[item.type] += jsBackend.mediaLibraryHelper.templates.getHTMLForMediaItemTableRow(item, connected);
            counts[item.type] += 1;
        });

        $(mediaItemTypes).each(function(index, type) {
            $('#mediaTable' + utils.string.ucfirst(type)).html((html[type]) ? $(html[type]) : rowNoItems);
            $('#mediaCount' + utils.string.ucfirst(type)).text('(' + counts[type] + ')');
        });

        // init $tabs
        var $tabs = $('#tabLibrary').find('.nav-tabs');

        // remove selected
        $tabs.find('.active').removeClass('active');

        // not in connect-to-group modus (just uploading)
        if (typeof mediaGroups[currentMediaGroupId] === 'undefined') {
            return false;
        }

        // Enable all because we can switch between different groups on the same page
        $tabs.children('li').removeClass('disabled, active').children('a').attr('data-toggle', 'tab');

        var disabled = '';
        var enabled = 'li:eq(0)';

        // we have an image group
        if (mediaGroups[currentMediaGroupId].type === 'image') {
            disabled = 'li:gt(0)';
        } else if (mediaGroups[currentMediaGroupId].type === 'file') {
            disabled = 'li:eq(0), li:eq(2), li:eq(3)';
            enabled = 'li:eq(1)';
        } else if (mediaGroups[currentMediaGroupId].type === 'movie') {
            disabled = 'li:eq(0), li:eq(1), li:eq(3)';
            enabled = 'li:eq(2)';
        } else if (mediaGroups[currentMediaGroupId].type === 'audio') {
            disabled = 'li:lt(3)';
            enabled = 'li:eq(3)';
        } else if (mediaGroups[currentMediaGroupId].type === 'image-file') {
            disabled = 'li:eq(2), li:eq(3)';
        } else if (mediaGroups[currentMediaGroupId].type === 'image-movie') {
            disabled = 'li:eq(1), li:eq(3)';
        }

        if (disabled !== '') {
            $tabs.children(disabled).addClass('disabled').children('a').removeAttr("data-toggle");
        }
        $tabs.children(enabled).addClass('active').children('a').attr('data-toggle', 'tab');

        // get table
        var $tables = $('.mediaTable');

        // redo odd-even
        $tables.find('tr').removeClass('odd').removeClass('even');
        $tables.find('tr:even').addClass('odd');
        $tables.find('tr:odd').addClass('even');

        // bind change when connecting/disconnecting media
        $tables.find('.toggleConnectedCheckbox').on('click', function() {
            // mediaId
            var mediaId = $(this).parent().parent().attr('id').replace('media-', '');

            // was already connected?
            var connected = utils.array.inArray(mediaId, currentMediaItemIds);

            // delete from array
            if (connected) {
                // loop all to find value and to delete it
                currentMediaItemIds.splice(currentMediaItemIds.indexOf(mediaId), 1);

                // update folder count
                jsBackend.mediaLibraryHelper.group.updateFolderCount(mediaFolderId, '-', 1);
            // add to array
            } else {
                currentMediaItemIds.push(mediaId);

                // update folder count
                jsBackend.mediaLibraryHelper.group.updateFolderCount(mediaFolderId, '+', 1);
            }

            // If we did click something else then the checkbox, we should toggle the checkbox as well
            if (!$(this).parent().hasClass('check')) {
                var $input = $(this).parent().parent().find('.check input');
                var checked = $input.attr('checked');

                if (checked) {
                    $input.removeAttr('checked');
                } else {
                    $input.attr('checked', 'checked');
                }
            }
        });

        // select the correct folder
        jsBackend.mediaLibraryHelper.group.updateFolderSelected();
    }
};

jsBackend.mediaLibraryHelper.cropper =
{
    cropperQueue: [],
    isCropping: false,

    passToCropper: function(resizeInfo, resolve, reject) {
        jsBackend.mediaLibraryHelper.cropper.cropperQueue.push({
            'resizeInfo': resizeInfo,
            'resolve': resolve,
            'reject': reject,
        });

        // If the cropper is already handling the queue we don't need to start it a second time.
        if (jsBackend.mediaLibraryHelper.cropper.isCropping) {
            return;
        }

        jsBackend.mediaLibraryHelper.cropper.isCropping = true;
        var $dialog = jsBackend.mediaLibraryHelper.cropper.getDialog();

        if ($('[data-role="enable-cropper-checkbox"]').is(':checked')) {
            jsBackend.mediaLibraryHelper.cropper.switchToCropperModal($dialog);
        }

        jsBackend.mediaLibraryHelper.cropper.processNextImageInQueue($dialog);
    },

    getDialog: function() {
        var $dialog = $('[data-role=media-library-add-dialog]');

        if ($dialog.length > 0) {
            return $dialog.first();
        }

        return $('[data-role=media-library-cropper-dialog]').first();
    },

    processNextImageInQueue: function($dialog) {
        var nextQueuedImage = jsBackend.mediaLibraryHelper.cropper.cropperQueue.shift();
        jsBackend.mediaLibraryHelper.cropper.crop(
            $dialog,
            nextQueuedImage.resizeInfo,
            nextQueuedImage.resolve,
            nextQueuedImage.reject
        );
    },

    crop: function($dialog, resizeInfo, resolve, reject) {
        jsBackend.mediaLibraryHelper.cropper.attachEvents($dialog, resolve, reject, resizeInfo);
        jsBackend.mediaLibraryHelper.cropper.initSourceAndTargetCanvas(
            $dialog,
            resizeInfo.sourceCanvas,
            resizeInfo.targetCanvas
        );

        var readyCallback = undefined;
        // if we don't want to show the cropper we just crop without showing it
        if (!$('[data-role="enable-cropper-checkbox"]').is(':checked')) {
            readyCallback = jsBackend.mediaLibraryHelper.cropper.getCropEventFunction($dialog, resizeInfo, resolve);
        }

        jsBackend.mediaLibraryHelper.cropper.initCropper($dialog, resizeInfo, readyCallback);
    },

    initSourceAndTargetCanvas: function($dialog, sourceCanvas, targetCanvas) {
        // set the initial height and width on the target canvas
        targetCanvas.height = sourceCanvas.height;
        targetCanvas.width = sourceCanvas.width;

        $dialog.find('[data-role=media-library-cropper-dialog-canvas-wrapper]').empty().append(sourceCanvas);
    },

    initCropper: function($dialog, resizeInfo, readyCallback) {
        $(resizeInfo.sourceCanvas)
            .addClass('img-responsive')
            .cropper(jsBackend.mediaLibraryHelper.cropper.getCropperConfig(readyCallback));
    },

    getCropperConfig: function(readyCallback) {
        var config = {
            autoCropArea: 1,
            zoomOnWheel: false,
            zoomOnTouch: false,
        };

        if (readyCallback !== undefined) {
            config.ready = readyCallback;
        }

        if (currentAspectRatio !== false) {
            config.aspectRatio = currentAspectRatio;
        }

        return config;
    },

    hasNextImageInQueue: function() {
        return jsBackend.mediaLibraryHelper.cropper.cropperQueue.length > 0;
    },

    finish: function($dialog) {
        if (jsBackend.mediaLibraryHelper.cropper.hasNextImageInQueue()) {
            // handle the next item
            jsBackend.mediaLibraryHelper.cropper.processNextImageInQueue($dialog);

            jsBackend.mediaLibraryHelper.cropper.switchToCropperModal($dialog);

            return;
        }

        jsBackend.mediaLibraryHelper.cropper.isCropping = false;
        // check if it is a standalone dialog for the cropper
        if ($dialog.attr('data-role') === 'media-library-cropper-dialog') {
            $dialog.modal('hide');

            return;
        }

        $dialog.find('[data-role=media-library-select-modal]').removeClass('hidden');
        $dialog.find('[data-role=media-library-cropper-modal]').addClass('hidden');
    },

    switchToCropperModal: function($dialog) {
        if (!$dialog.hasClass('in')) {
            $dialog.modal('show');
        }

        $dialog.find('[data-role=media-library-select-modal]').addClass('hidden');
        $dialog.find('[data-role=media-library-cropper-modal]').removeClass('hidden');
    },

    getCloseEventFunction: function($dialog, resizeInfo, reject) {
        return function() {
            $dialog.off('hidden.bs.modal.media-library-cropper.close');

            $('[data-role=media-library-cropper-dialog-canvas-wrapper] > canvas').cropper('destroy');
            reject('Cancel');
            jsBackend.mediaLibraryHelper.cropper.finish($dialog);
        };
    },

    getCropEventFunction: function($dialog, resizeInfo, resolve) {
        return function() {
            var context = resizeInfo.targetCanvas.getContext('2d');
            var $cropper = $('[data-role=media-library-cropper-dialog-canvas-wrapper] > canvas');
            var cropBoxData = $cropper.cropper('getCroppedCanvas');
            var zoomTo = 1;

            // limit the width and height of the images to 3000 so they are not too big for the LiipImagine bundle
            if (cropBoxData.height > 3000 || cropBoxData.width > 3000) {
                zoomTo = 3000/((cropBoxData.height >= cropBoxData.width) ? cropBoxData.height : cropBoxData.width);
            }

            // set the correct height and width on the target canvas
            resizeInfo.targetCanvas.height = Math.round(cropBoxData.height * zoomTo);
            resizeInfo.targetCanvas.width = Math.round(cropBoxData.width * zoomTo);

            // make sure we start with a blank slate
            context.clearRect(0, 0, resizeInfo.targetCanvas.width, resizeInfo.targetCanvas.height);

            // add the new crop
            context.drawImage($cropper.cropper('getCroppedCanvas'), 0, 0, resizeInfo.targetCanvas.width, resizeInfo.targetCanvas.height);

            $dialog.off('hidden.bs.modal.media-library-cropper.close');
            resolve('Confirm');
            $dialog.find('[data-role=media-library-cropper-crop]').off('click.media-library-cropper.crop');
            $cropper.cropper('destroy');
            jsBackend.mediaLibraryHelper.cropper.finish($dialog);
        };
    },

    getRotateEventFunction: function() {
        return function() {
            var $cropper = $('[data-role=media-library-cropper-dialog-canvas-wrapper] > canvas');
            $cropper.cropper('rotate', $(this).data('degrees'));
            $cropper.cropper('crop');
        }
    },

    getZoomEventFunction: function() {
        return function() {
            var $cropper = $('[data-role=media-library-cropper-dialog-canvas-wrapper] > canvas');
            $cropper.cropper('zoom', $(this).data('zoom'));
            $cropper.cropper('crop');
        }
    },

    getResetEventFunction: function() {
        return function() {
            var $cropper = $('[data-role=media-library-cropper-dialog-canvas-wrapper] > canvas');
            $cropper.cropper('reset');
            $cropper.cropper('crop');
        }
    },

    attachEvents: function($dialog, resolve, reject, resizeInfo) {
        $dialog
            .off('hidden.bs.modal.media-library-cropper.close')
            .on(
                'hidden.bs.modal.media-library-cropper.close',
                jsBackend.mediaLibraryHelper.cropper.getCloseEventFunction(
                    $dialog,
                    resizeInfo,
                    reject
                )
            );

        $dialog.find('[data-role=media-library-cropper-crop]')
            .off('click.media-library-cropper.crop')
            .on(
                'click.media-library-cropper.crop',
                jsBackend.mediaLibraryHelper.cropper.getCropEventFunction(
                    $dialog,
                    resizeInfo,
                    resolve
                )
            );

        $dialog.find('[data-role=media-library-cropper-rotate]')
            .off('click.media-library-cropper.rotate')
            .on(
                'click.media-library-cropper.rotate',
                jsBackend.mediaLibraryHelper.cropper.getRotateEventFunction()
            );

        $dialog.find('[data-role=media-library-cropper-zoom]')
            .off('click.media-library-cropper.zoom')
            .on(
                'click.media-library-cropper.zoom',
                jsBackend.mediaLibraryHelper.cropper.getZoomEventFunction()
            );
        $dialog.find('[data-role=media-library-cropper-reset]')
            .off('click.media-library-cropper.zoom')
            .on(
                'click.media-library-cropper.zoom',
                jsBackend.mediaLibraryHelper.cropper.getResetEventFunction()
            );
    }
};

/**
 * All methods related to the upload
 * global: jsBackend
 */
jsBackend.mediaLibraryHelper.upload =
{
    preInit: function()
    {
        // bind change to upload_type
        $('#uploadMediaTypeBox').on('change', 'input[name=uploading_type]', jsBackend.mediaLibraryHelper.upload.toggleUploadBoxes);

        // bind click to add movie
        $('#addMediaMovie').on('click', jsBackend.mediaLibraryHelper.upload.insertMovie);

        // bind change to upload folder
        $('#uploadMediaFolderId').on('change', function() {
            // update upload button
            jsBackend.mediaLibraryHelper.upload.toggleUploadBoxes();
        }).trigger('change');

        // bind delete actions
        $('#uploadedMedia').on('click', '.deleteMediaItem', function() {
            $(this).parent().remove();
        });
    },

    toggleCropper: function() {
        // the cropper is mandatory
        var $formGroup = $('[data-role="cropper-is-mandatory-form-group"]');
        var $warning = $('[data-role="cropper-is-mandatory-message"]');
        var $checkbox = $('[data-role="enable-cropper-checkbox"]');

        if (currentAspectRatio === false) {
            $formGroup.removeClass('has-warning');
            $warning.addClass('hidden');
            $checkbox.removeClass('disabled').attr('disabled', false).attr('checked', false);

            return;
        }

        $formGroup.addClass('has-warning');
        $warning.removeClass('hidden');
        $checkbox.addClass('disabled').attr('disabled', true).attr('checked', true);
    },

    init: function()
    {
        // redefine media folder id
        mediaFolderId = $('#uploadMediaFolderId').val();

        // check if we need to cropper is mandatory
        jsBackend.mediaLibraryHelper.upload.toggleCropper();

        var $fineUploaderGallery = $('#fine-uploader-gallery');
        $fineUploaderGallery.fineUploader({
            template: 'qq-template-gallery',
            thumbnails: {
                placeholders: {
                    waitingPath: '/css/vendors/fine-uploader/waiting-generic.png',
                    notAvailablePath: '/css/vendors/fine-uploader/not_available-generic.png'
                }
            },
            validation: {
                allowedExtensions: jsBackend.data.get('MediaLibrary.mediaAllowedExtensions')
            },
            scaling: jsBackend.mediaLibraryHelper.upload.getScalingConfig(),
            callbacks: {
                onUpload: function(event) {
                    // redefine media folder id
                    mediaFolderId = $('#uploadMediaFolderId').val();

                    // We must set the endpoint dynamically, because "uploadMediaFolderId" is null at start and is async loaded using AJAX.
                    this.setEndpoint('/backend/ajax?fork[module]=MediaLibrary&fork[action]=MediaItemUpload&fork[language]=' + jsBackend.current.language + '&folder_id=' + mediaFolderId);
                },
                onComplete: function(id, name, responseJSON) {
                    // add file to uploaded box
                    $('#uploadedMedia').append(jsBackend.mediaLibraryHelper.templates.getHTMLForUploadedMediaItem(responseJSON));

                    // update counter
                    jsBackend.mediaLibraryHelper.upload.uploadedCount += 1;

                    // toggle upload box
                    jsBackend.mediaLibraryHelper.upload.toggleUploadBoxes();

                    $fineUploaderGallery.find('.qq-upload-success[qq-file-id=' + id + ']').hide();
                },
                onAllComplete: function(succeeded, failed) {
                    // clear if already exists
                    if (media[mediaFolderId]) {
                        // set folder to false so we can refresh items in the folder
                        media[mediaFolderId] = false;
                    }

                    // load and add media for group
                    jsBackend.mediaLibraryHelper.group.getMedia();

                    // everything uploaded, show success message
                    if (failed.length === 0) {
                        jsBackend.messages.add('success', utils.string.sprintf(jsBackend.locale.msg('MediaUploadedSuccess'), succeeded.length));
                    // not everything is uploaded successful, show error message
                    } else {
                        jsBackend.messages.add('danger', utils.string.sprintf(jsBackend.locale.msg('MediaUploadedError'), (succeeded.length + '","' + failed.length)));
                    }
                }
            }
        });
    },

    /**
     * Configure the uploader to trigger the cropper
     */
    getScalingConfig: function() {
        return {
            includeExif: false, // needs to be false to prevent issues during the cropping process, it also is good for privacy reasons
            sendOriginal: false,
            sizes: [
                {name: "", maxSize: 1} // used to trigger the cropper, this will set the maximum resulution to 1x1 px
                                       // It always trigger the cropper since it uses a hook for scaling pictures down
            ],
            customResizer: function(resizeInfo) {
                return new Promise(function(resolve, reject) {
                    jsBackend.mediaLibraryHelper.cropper.passToCropper(resizeInfo, resolve, reject);
                })
            }
        };
    },

    /**
     * Add uploaded media to group
     */
    addUploadedMediaToGroup : function()
    {
        // loop remaining items in uploaded media and push them to current group
        $('#uploadedMedia').find('li').each(function() {
            // get id
            var id = $(this).attr('id').replace('media-', '');

            // add each id to array
            currentMediaItemIds.push(id);
        });

        // clear upload queue count
        jsBackend.mediaLibraryHelper.upload.uploadedCount = 0;

        // clear all elements
        $('#uploadedMedia').html('');
    },

    /**
     * Insert movie
     *
     * @param {Event} e
     */
    insertMovie : function(e)
    {
        // prevent other functions
        e.preventDefault();

        // update media for folder
        mediaFolderId = $('#uploadMediaFolderId').val();

        // define variables
        var storageType = $('#mediaMovieStorageType').find(':checked').val();
        var $id = $('#mediaMovieId');
        var $title = $('#mediaMovieTitle');

        // insert movie using ajax
        $.ajax({
            data: {
                fork: {
                    module: 'MediaLibrary',
                    action: 'MediaItemAddMovie'
                },
                folder_id: mediaFolderId,
                storageType: storageType,
                id: $id.val(),
                title: $title.val()
            },
            success: function(json, textStatus) {
                if (json.code != 200) {
                    // show error if needed
                    if (jsBackend.debug) {
                        alert(textStatus);
                    }
                } else {
                    // add uploaded movie
                    $('#uploadedMedia').append(jsBackend.mediaLibraryHelper.templates.getHTMLForUploadedMediaItem(json.data));

                    // update counter
                    jsBackend.mediaLibraryHelper.upload.uploadedCount += 1;

                    // toggle upload boxes
                    jsBackend.mediaLibraryHelper.upload.toggleUploadBoxes();

                    // clear if already exists
                    if (media[mediaFolderId]) {
                        // set folder to false so we can refresh items in the folder
                        media[mediaFolderId] = false;
                    }

                    // load and add media for group
                    jsBackend.mediaLibraryHelper.group.getMedia();

                    // Clear the fields
                    $id.val('');
                    $title.val('');

                    // show message
                    jsBackend.messages.add('success', jsBackend.locale.msg('MediaMovieIsAdded'));
                }
            }
        });
    },

    /**
     * Toggle the upload box + uploaded box
     * Depending on the selected folder and the amount of files in the queue.
     */
    toggleUploadBoxes : function()
    {
        // init variables
        var $uploadingType = $('#uploadMediaTypeBox input[name=uploading_type]');
        var folderSelected = ($('#uploadMediaFolderId').val() != 0);
        var showMediaTypeBox = false; // step 2
        var showMediaBox = false; // step 2
        var showMovieBox = false; // step 2
        var showUploadedBox = false; // step 3

        // define group type
        var groupType = (mediaGroups[currentMediaGroupId]) ? mediaGroups[currentMediaGroupId].type : 'all';

        // does group accepts movies
        var moviesAllowed = (groupType == 'all' || groupType == 'image-movie' || groupType == 'movie');

        // movies not allowed
        if (!moviesAllowed) {
            // select first item (which is all, so we can upload regular media)
            $uploadingType.find(':first-child').attr('checked', 'checked');
        } else {
            if (groupType == 'movie') {
                // select first item (which is all, so we can upload regular media)
                $uploadingType.eq(1).attr('checked', 'checked');
            } else {
                showMediaTypeBox = true;
            }
        }

        // if we have media uploaded, show the uploaded box
        if (jsBackend.mediaLibraryHelper.upload.uploadedCount > 0) showUploadedBox = true;

        // we want to upload media
        if ($uploadingType.filter(':checked').val() == 'all') {
            // if we have selected a folder, show the upload media box
            if (folderSelected) showMediaBox = true;
        // we want to add movies (from youtube, ...)
        } else {
            // if we have selected a folder, show the upload media box
            if (folderSelected) showMovieBox = true;
        }

        // update show upload type choise
        $('#uploadMediaTypeBox').toggle(showMediaTypeBox);

        // toggle upload media box
        $('#uploadMediaBox').toggle(showMediaBox);

        // toggle upload movie box
        $('#addMovieBox').toggle(showMovieBox);

        // toggle uploaded box
        $('#uploadedMediaBox').toggle(showUploadedBox);
        $('#mediaWillBeConnectedToMediaGroup').toggle((currentMediaGroupId !== 0));
    },

    /**
     * Needed to handle the visibility of the uploadedMediaBox
     *
     * @param int
     */
    uploadedCount : 0
};

/**
 * Templates
 *
 * global: jsBackend
 * global: utils
 */
jsBackend.mediaLibraryHelper.templates =
{
    /**
     * Get HTML for empty table row
     *
     * @returns {string}
     */
    getHTMLForEmptyTableRow : function()
    {
        return '<tr><td>' + jsBackend.locale.msg('MediaNoItemsInFolder') + '</td></tr>';
    },

    /**
     * Get HTML for MediaFolders to show in dropdown
     *
     * @param {array} mediaFolders The mediaFolderCacheItem entity array.
     * @returns {string}
     */
    getHTMLForMediaFolders: function(mediaFolders)
    {
        var html = '';

        $(mediaFolders).each(function(i, mediaFolder){
            html += jsBackend.mediaLibraryHelper.templates.getHTMLForMediaFolder(mediaFolder);
        });

        return html;
    },

    /**
     * Get HTML for MediaFolder to show in dropdown
     *
     * @param {array} mediaFolder The mediaFolderCacheItem entity array.
     * @returns {string}
     */
    getHTMLForMediaFolder: function(mediaFolder)
    {
        var html = '';
        var count = 0;

        // redefine count
        if (mediaGroups[currentMediaGroupId].count && mediaGroups[currentMediaGroupId].count[mediaFolder.id]) {
            count = mediaGroups[currentMediaGroupId].count[mediaFolder.id];
        }

        // add to html
        html += '<option value="' + mediaFolder.id + '">';
        html += '   ' + mediaFolder.slug + ' (' + count + '/' + mediaFolder.numberOfMediaItems + ')';
        html += '</option>';

        if (mediaFolder.numberOfChildren > 0) {
            html += jsBackend.mediaLibraryHelper.templates.getHTMLForMediaFolders(mediaFolder.children);
        }

        return html;
    },

    /**
     * Get HTML for MediaItem to connect
     *
     * @param {array} mediaItem The mediaItem entity array.
     * @returns {string}
     */
    getHTMLForMediaItemToConnect : function(mediaItem)
    {
        var html = '<li id="media-' + mediaItem.id + '" data-folder-id="' + mediaItem.folder_id + '" class="ui-state-default">';
        html += '<div class="mediaHolder mediaHolder' + utils.string.ucfirst(mediaItem.type) + '">';

        if (mediaItem.type == 'image') {
            html += '<img src="' + mediaItem.preview_source + '" alt="' + mediaItem.title + '" title="' + mediaItem.title + '"/>';
        } else {
            html += '<div class="icon"></div>';
            html += '<div class="url">' + mediaItem.url + '</div>';
        }

        html += '</div>';
        html += '</li>';

        return html;
    },

    /**
     * Get HTML for MediaItem table row
     *
     * @param {array} mediaItem The mediaItem entity array.
     * @param {bool} connected
     * @returns {string}
     */
    getHTMLForMediaItemTableRow : function(mediaItem, connected)
    {
        var html = '<tr id="media-' + mediaItem.id + '" class="row' + utils.string.ucfirst(mediaItem.type) + '">';
        html += '<td class="check">';
        html += '<input type="checkbox" class="toggleConnectedCheckbox"';

        if (connected) {
            html += ' checked="checked"';
        }

        html += '/></td>';

        if (mediaItem.type === 'image') {
            html += '<td class="fullUrl">';
            html += '<img src="' + mediaItem.preview_source + '" alt="' + mediaItem.title + '" height="50" class="toggleConnectedCheckbox" />';
            html += '</td>';
        }

        html += '<td class="url">' + mediaItem.url + '</td>';
        html += '<td class="title">' + mediaItem.title + '</td>';
        html += '</tr>';

        return html;
    },

    /**
     * Get HTML for uploaded MediaItem
     *
     * @param {array} mediaItem - This is the media-item that ajax returned for us.
     * @return {string}
     */
    getHTMLForUploadedMediaItem : function(mediaItem)
    {
        // init html
        var html = '';

        // create element
        html += '<li id="media-' + mediaItem.id + '" data-folder-id="' + mediaItem.folder.id + '" class="ui-state-default">';
        html += '<div class="mediaHolder mediaHolder' + utils.string.ucfirst(mediaItem.type) + '">';

        // is image
        if (mediaItem.type == 'image') {
            html += '<img src="' + mediaItem.preview_source + '" alt="' + mediaItem.title + '" title="' + mediaItem.title + '"/>';
        // is file, movie or audio
        } else {
            html += '<div class="icon"></div>';
            html += '<div class="url">' + mediaItem.url + '</div>';
        }

        html += '</div>';
        html += '<button type="button" class="deleteMediaItem btn btn-default" ';
        html += 'title="' + utils.string.ucfirst(jsBackend.locale.msg('MediaDoNotConnectThisMedia')) + '">';
        html += '<span>' + utils.string.ucfirst(jsBackend.locale.msg('MediaDoNotConnectThisMedia')) + '</span>';
        html += '</button>';
        html += '</li>';

        return html;
    }
};

/** global: jsBackend */
$(jsBackend.mediaLibraryHelper.init);
