/**
 * Interaction for the connection of media to the media module.
 * global: jsBackend
 */
jsBackend.mediaLibraryHelper =
{
    init: function()
    {
        // controls the editing of a group
        jsBackend.mediaLibraryHelper.group.init();

        // bind upload events
        jsBackend.mediaLibraryHelper.upload.bindEvents();

        // initialize uploader
        jsBackend.mediaLibraryHelper.upload.init();
    }
};

/**
 * Edit group
 * global: jsBackend
 */
var media = {};
var mediaFolders = false;
var mediaGalleries = {};
var mediaGroupI = 0;
var mediaFolderId = 0;
var mediaCurrentGroup = [];
jsBackend.mediaLibraryHelper.group =
{
    init: function()
    {
        // start or not
        if ($('#addMediaDialog').length == 0) return false;

        // get galleries
        jsBackend.mediaLibraryHelper.group.getGalleries();

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
                prevSequence = newSequence = $('#group-' + mediaGroupI + ' .mediaIds').first().val();

                // don't prevent the click
                ui.item.removeClass('preventClick');
            },
            update: function(e, ui) {
                // set group i
                mediaGroupI = $(this).parent().parent().attr('id').replace('group-', '');

                // prepare correct new sequence value for hidden input
                newSequence = $(this).sortable("serialize").replace(/media\-/g, '').replace(/\[\]\=/g, '-').replace(/&/g, ',');

                // add value to hidden input
                $('#group-' + mediaGroupI + ' .mediaIds').first().val(newSequence);
            },
            stop : function(e, ui) {
                // prevent click
                ui.item.addClass('preventClick');

                // new sequence: de-select this item + update sequence
                if (prevSequence != newSequence) {
                    // remove selected class
                    ui.item.removeClass('selected');

                    // update disconnect button
                    jsBackend.mediaLibraryHelper.group.updateDisconnectButton(mediaGroupI);
                // same sequence: select this item (accidently moved this media a few millimeters counts as a click)
                } else {
                    // don't prevent the click, click handler does the rest
                    ui.item.removeClass('preventClick');
                }
            }
        });

        // bind hover to media items so you see the edit button
        $('.mediaConnectedItems').on('hover', '.ui-state-default', function(e) {
            $(this).toggleClass('hover');
        });

        // bind click to media items so you can select them
        $('.mediaConnectedItems').on('click', '.mediaHolder', function(e) {
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
        $('.mediaEditBox').on('click', '.disconnectMediaItemsButton', function(e) {
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
        var $addMediaDialog = $('#addMediaDialog');
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
        $addMediaDialog.on('show.bs.modal', function () {
            jsBackend.mediaLibraryHelper.upload.init();
        });

        // bind click when opening "add media dialog"
        $('.addMediaButton').on('click', function(e) {
            // prevent default
            e.preventDefault();

            // redefine folderId when clicked on other group
            if ($(this).data('i') != mediaGroupI) {
                // clear folders cache
                jsBackend.mediaLibraryHelper.group.clearFoldersCache();
            }

            // define groupId
            mediaGroupI = $(this).data('i');

            // get current media for group
            mediaCurrentGroup = ($('#group-' + mediaGroupI + ' .mediaIds').first().val() != '')
                ? $.trim($('#group-' + mediaGroupI + ' .mediaIds').first().val()).split(',') : [];

            // set the group media
            mediaGalleries[mediaGroupI].media = mediaCurrentGroup;

            // load and add folders
            jsBackend.mediaLibraryHelper.group.getFolders();

            // load and get folder counts for group
            jsBackend.mediaLibraryHelper.group.getFolderCountsForGroup();

            // load and add media for group
            jsBackend.mediaLibraryHelper.group.getMedia();

            // toggle upload boxes
            jsBackend.mediaLibraryHelper.upload.toggleUploadBoxes();

            // select first tab
            $('#addMediaDialog').find('.nav-tabs a').first().tab('show');

            // open dialog
            $addMediaDialog.modal('show');
        });

        // bind change when selecting other folder
        $('#mediaFolders').on('change', function(e) {
            // cache current folder id
            mediaFolderId = $(this).val();

            // get media for this folder
            jsBackend.mediaLibraryHelper.group.getMedia();
        });
    },

    /**
     * Clear the media cache when necessary
     */
    clearMediaCache : function()
    {
        media = {};
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
     * @param int groupId       The group id we want to disconnect from.
     */
    disconnectMediaFromGroup : function(groupId)
    {
        // redefine current media group
        mediaCurrentGroup = [];

        // define mediaGroupI
        mediaGroupI = groupId;

        // current ids
        var currentIds = $.trim($('#group-' + mediaGroupI + ' .mediaIds').first().val()).split(',');

        // get selected items
        var $items = jsBackend.mediaLibraryHelper.group.getSelectedItems(mediaGroupI);

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
        mediaCurrentGroup = currentIds;

        // only get new folder counts on startup
        if (mediaGalleries[mediaGroupI].id != 0 && mediaGalleries[mediaGroupI].count == undefined) {
            // load folder counts for group using ajax
            $.ajax({
                data: {
                    fork: {
                        module: 'MediaLibrary',
                        action: 'GetMediaFolderCountsForGroup'
                    },
                    group_id : mediaGalleries[mediaGroupI].id
                },
                success: function(json, textStatus) {
                    if (json.code != 200) {
                        // show error if needed
                        if (jsBackend.debug) alert(textStatus);
                    } else {
                        // cache folder counts
                        mediaGalleries[mediaGroupI].count = json.data;

                        // update group media
                        jsBackend.mediaLibraryHelper.group.updateGroupMedia();

                        // update folder counts for items
                        jsBackend.mediaLibraryHelper.group.updateFolderCountsForItemsToDisconnect($items, mediaGroupI);

                        // update disconnect button
                        jsBackend.mediaLibraryHelper.group.updateDisconnectButton(groupId);
                    }
                }
            });
        } else {
            // update group media
            jsBackend.mediaLibraryHelper.group.updateGroupMedia();

            // update folder counts for items
            jsBackend.mediaLibraryHelper.group.updateFolderCountsForItemsToDisconnect($items, mediaGroupI);

            // update disconnect button
            jsBackend.mediaLibraryHelper.group.updateDisconnectButton(groupId);
        }
    },

    get : function(groupId)
    {
        return $('#group-' + groupId);
    },

    /**
     * Load in the folders count for a group
     *
     * @param int groupId
     */
    getFolderCountsForGroup : function()
    {
        // only get new folder counts on startup
        if (mediaGalleries[mediaGroupI].id != 0 && mediaGalleries[mediaGroupI].count == undefined) {
            // load folder counts for group using ajax
            $.ajax({
                data: {
                    fork: {
                        module: 'MediaLibrary',
                        action: 'GetMediaFolderCountsForGroup'
                    },
                    group_id : mediaGalleries[mediaGroupI].id
                },
                success: function(json, textStatus) {
                    if (json.code != 200) {
                        // show error if needed
                        if (jsBackend.debug) alert(textStatus);
                    } else {
                        // cache folder counts
                        mediaGalleries[mediaGroupI].count = json.data;

                        // update folders
                        jsBackend.mediaLibraryHelper.group.updateFolders();
                    }
                }
            });
        }
    },

    /**
     * Load in the folders and add numConnected from the group
     *
     * @param int groupId
     */
    getFolders : function()
    {
        if (!mediaFolders) {
            // load folders using ajax
            $.ajax({
                data: {
                    fork: {
                        module: 'MediaLibrary',
                        action: 'GetMediaFolders'
                    }
                },
                success: function(json, textStatus) {
                    if (json.code != 200) {
                        // show error if needed
                        if (jsBackend.debug) alert(textStatus);
                    } else {
                        // cache folders
                        mediaFolders = json2array(json.data).sort(sortByProperty('name'));

                        // update folders
                        jsBackend.mediaLibraryHelper.group.updateFolders();
                    }
                }
            });
        }
    },

    /**
     * Get galleries from looking at the DOM
     */
    getGalleries : function()
    {
        $('.mediaGroup').each(function() {
            var mediaGroupId = $(this).data('media-group-id');
            var type = $('#group-' + mediaGroupId + ' .type').first().val();
            // get current media for group
            var mediaIds = ($('#group-' + mediaGroupI + ' .mediaIds').first().val() != '')
                ? $.trim($('#group-' + mediaGroupI + ' .mediaIds').first().val()).split(',') : [];

            // Push ids to array
            mediaGalleries[mediaGroupId] = {
                'id' : mediaGroupId,
                'media' : mediaIds,
                'type' : type
            };
        });
    },

    /**
     * Get media items in a group
     *
     * @param int groupId [optional]
     */
    getItems : function(groupId)
    {
        var id = (groupId) ? groupId : mediaGroupI;
        return $('#group-' + id).find('.mediaConnectedItems');
    },

    /**
     * Load in the media for a group or in a folder
     *
     * @param int groupId
     * @param int folderId
     */
    getMedia : function()
    {
        /**
         * Not in cache - get media using ajax
         */
        if (mediaFolderId != null && !media[mediaFolderId]) {
            // init groupId
            var groupId = (mediaGalleries[mediaGroupI]) ? mediaGalleries[mediaGroupI].id : 0;

            // load media
            $.ajax( {
                data: {
                    fork: {
                        module: 'MediaLibrary',
                        action: 'GetMediaItems'
                    },
                    group_id: groupId,
                    folder_id: mediaFolderId
                },
                success: function(json, textStatus) {
                    if (json.code != 200) {
                        // show error if needed
                        if (jsBackend.debug) {
                            alert(textStatus);
                        }
                    } else {
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
                }
            });
        // Media already in cache, calling the update.
        } else {
            // update media
            jsBackend.mediaLibraryHelper.group.updateMedia();
        }
    },

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
     * @param int groupId
     */
    updateDisconnectButton : function(groupId)
    {
        // init variables
        var $group = jsBackend.mediaLibraryHelper.group.get(groupId);
        var $items = jsBackend.mediaLibraryHelper.group.getSelectedItems(groupId);

        // toggle disabled button
        $group.find('.mediaEditBox .disconnectMediaItemsButton').toggleClass('disabled', ($items.length > 0) ? false : true);
    },

    /**
     * Update the folder count
     *
     * @param int folderId          The folderId where you want the count to change.
     * @param string updateCount    Allowed: '+' or '-'
     * @param int updateWithValue   The value you want to add or substract.
     */
    updateFolderCount : function(folderId, updateCount, updateWithValue)
    {
        // count not found - add it
        if (mediaGalleries[mediaGroupI].count == undefined) {
            // define new object
            var obj = {};

            // redefine object
            obj[folderId] = 0;

            // add object to count
            mediaGalleries[mediaGroupI].count = obj;
        }

        // folder not found - add it to object
        if (mediaGalleries[mediaGroupI].count[folderId] == undefined) {
            // update object to count
            mediaGalleries[mediaGroupI].count[folderId] = 0;
        }

        // init count
        var count = parseInt(mediaGalleries[mediaGroupI].count[folderId]);

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
        mediaGalleries[mediaGroupI].count[folderId] = count;

        // update folders
        jsBackend.mediaLibraryHelper.group.updateFolders();
    },

    /**
     * Update folder counts for items
     *
     * @param array $items              The media items
     * @param int id [optional]         Overwrite value for the media group I
     */
    updateFolderCountsForItemsToDisconnect : function($items, id)
    {
        // define id
        if (id == null) {
            id = mediaGroupI;
        }

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
        // add empty element to html
        var html = '';

        // add folders to html
        $.each(mediaFolders, function(i, item) {
            // define count
            var count = 0;

            // redefine count
            if (mediaGalleries[mediaGroupI].count && mediaGalleries[mediaGroupI].count[item.id]) {
                count = mediaGalleries[mediaGroupI].count[item.id];
            }

            // add to html
            html += '<option value="' + item.id + '">';
            html += '   ' + item.name + ' (' + count + '/' + item.numMedia + ')';
            html += '</option>';
        });

        // add folders to dropdown
        $('#mediaFolders').html(html);

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
        var currentIds = ($('#group-' + mediaGroupI + ' .mediaIds').first().val() != '')
            ? $.trim($('#group-' + mediaGroupI + ' .mediaIds').first().val()).split(',') : [];

        // define empty
        var empty = (mediaCurrentGroup.length == 0) ? true : false;

        // check which items to add
        $(mediaCurrentGroup).each(function(i, id) {
            // add item
            if (!utils.array.inArray(id, currentIds)) {
                // loop media folders
                $.each(media, function(index, items) {
                    // loop media items in folder
                    $.each(items, function(index, item) {
                        // item found
                        if (id == item.id) {
                            html = '<li id="media-' + item.id + '" data-folder-id="' + item.folder_id + '" class="ui-state-default">';
                            html += '<div class="mediaHolder mediaHolder' + utils.string.ucfirst(item.type) + '">';

                            if (item.type == 'image') {
                                html += '<img src="' + item.preview_source + '" alt="' + item.title + '" title="' + item.title + '"/>';
                            } else {
                                html += '<div class="icon"></div>';
                                html += '<div class="url">' + item.url + '</div>';
                            }

                            html += '</div>';
                            html += '</li>';

                            // add to group
                            $currentItems.append(html);
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
        mediaGalleries[mediaGroupI].media = mediaCurrentGroup;

        // add empty media paragraph
        if (empty) {
            jsBackend.mediaLibraryHelper.group.getItems().after('<p class="mediaNoItems helpTxt">' + jsBackend.locale.msg('MediaNoItemsConnected') + '</p>');
        // delete empty media paragraph
        } else {
            $('#group-' + mediaGroupI).find('.mediaNoItems').remove();
        }

        // update the hidden group field for media
        $('#group-' + mediaGroupI).find('.mediaIds').first().val(mediaCurrentGroup.join(',').trim(','));

        // redefine
        mediaCurrentGroup = [];
    },

    /**
     * Update the media
     */
    updateMedia : function()
    {
        if (mediaFolderId == 0) {
            // hide library tabs on open
            $('#tabLibrary').find('.tabs').hide();

            // stop here
            return false;
        } else {
            // show library tabs on open
            $('#tabLibrary').find('.tabs').show();
        }

        // init variables
        var rowNoItems = '<tr><td>' + jsBackend.locale.msg('MediaNoItemsInFolder') + '</td></tr>';
        var htmlImages = '';
        var htmlFiles = '';
        var htmlMovies = '';
        var htmlAudio = '';
        var numImages = 0;
        var numFiles = 0;
        var numMovies = 0;
        var numAudio = 0;

        // loop media
        $.each(media[mediaFolderId], function(i, item) {
            // check if media is connected or not
            var connected = (typeof mediaGalleries[mediaGroupI] == 'undefined') ? false : utils.array.inArray(item.id, mediaGalleries[mediaGroupI].media);

            // item is an image
            if (item.type == 'image') {
                // add to html
                htmlImages += '<tr id="media-' + item.id + '" class="rowImage">';
                htmlImages += '  <td class="check">';
                htmlImages += '     <input type="checkbox" class="toggleConnectedCheckbox"';
                if (connected) {
                    htmlImages += ' checked="checked"';
                }
                htmlImages += ' />';
                htmlImages += '  </td>';
                htmlImages += '  <td class="fullUrl">';
                htmlImages += '     <img src="' + item.preview_source + '" alt="' + item.title + '" height="50" />';
                htmlImages += '  </td>';
                htmlImages += '  <td class="url">' + item.url + '</td>';
                htmlImages += '  <td class="title">' + item.title + '</td>';
                htmlImages += '</tr>';

                // add +1 to image counter
                numImages += 1;
            // item is a file
            } else if (item.type == 'file') {
                // add to html
                htmlFiles += '<tr id="media-' + item.id + '" class="rowFile">';
                htmlFiles += '  <td class="check">';
                htmlFiles += '     <input type="checkbox" class="toggleConnectedCheckbox"';
                if (connected) {
                    htmlFiles += ' checked="checked"';
                }
                htmlFiles += ' />';
                htmlFiles += '  </td>';
                htmlFiles += '  <td class="url">' + item.url + '</td>';
                htmlFiles += '  <td class="title">' + item.title + '</td>';
                htmlFiles += '</tr>';

                // add +1 to file counter
                numFiles += 1;
            // item is a movie
            } else if (item.type == 'movie') {
                // add to html
                htmlMovies += '<tr id="media-' + item.id + '" class="rowMovie">';
                htmlMovies += '  <td class="check">';
                htmlMovies += '    <input type="checkbox" class="toggleConnectedCheckbox"';
                if (connected) {
                    htmlMovies += ' checked="checked"';
                }
                htmlMovies += ' />';
                htmlMovies += '  </td>';
                htmlMovies += '  <td class="url">' + item.url + '</td>';
                htmlMovies += '  <td class="title">' + item.title + '</td>';
                htmlMovies += '</tr>';

                // add +1 to movies counter
                numMovies += 1;
            // item is an audio file
            } else if (item.type == 'audio') {
                // add to html
                htmlAudio += '<tr id="media-' + item.id + '" class="rowAudio">';
                htmlAudio += '  <td class="check">';
                htmlAudio += '    <input type="checkbox" class="toggleConnectedCheckbox"';
                if (connected) {
                    htmlAudio += ' checked="checked"';
                }
                htmlAudio += ' />';
                htmlAudio += '  </td>';
                htmlAudio += '  <td class="url">' + item.url + '</td>';
                htmlAudio += '  <td class="title">' + item.title + '</td>';
                htmlAudio += '</tr>';

                // add +1 to audio counter
                numAudio += 1;
            }
        });

        // update counter values
        $('#mediaCountImages').text('(' + numImages + ')');
        $('#mediaCountFiles').text('(' + numFiles + ')');
        $('#mediaCountMovies').text('(' + numMovies + ')');
        $('#mediaCountAudio').text('(' + numAudio + ')');

        // add html to correct items
        $('#mediaTableImages').html((htmlImages) ? htmlImages : rowNoItems);
        $('#mediaTableFiles').html((htmlFiles) ? htmlFiles : rowNoItems);
        $('#mediaTableMovies').html((htmlMovies) ? htmlMovies : rowNoItems);
        $('#mediaTableAudio').html((htmlAudio) ? htmlAudio : rowNoItems);

        // init $tabs
        var $tabs = $('#tabLibrary').find('.tabs');

        // remove selected
        $tabs.find('.ui-tabs-selected').removeClass('ui-tabs-selected');

        // not in connect-to-group modus (just uploading)
        if (typeof mediaGalleries[mediaGroupI] == 'undefined') {
            return false;
        }

        // we have an image group
        if (mediaGalleries[mediaGroupI].type == 'image') {
            $tabs.tabs({disabled: [1, 2, 3]});
            $tabs.tabs('select', 0);
        } else if (mediaGalleries[mediaGroupI].type == 'file') {
            $tabs.tabs({disabled: [0, 2, 3]});
            $tabs.tabs('select', 1);
        } else if (mediaGalleries[mediaGroupI].type == 'movie') {
            $tabs.tabs({disabled: [0, 1, 3]});
            $tabs.tabs('select', 2);
        } else if (mediaGalleries[mediaGroupI].type == 'audio') {
            $tabs.tabs({disabled: [0, 1, 2]});
            $tabs.tabs('select', 3);
        } else if (mediaGalleries[mediaGroupI].type == 'image-file') {
            $tabs.tabs({disabled: [2, 3]});
            $tabs.tabs('select', 0);
        } else if (mediaGalleries[mediaGroupI].type == 'image-movie') {
            $tabs.tabs({disabled: [1, 3]});
            $tabs.tabs('select', 0);
        } else {
            //$tabs.tabs('select', 3);
            $tabs.tabs({disabled: []});
        }

        // get table
        var $tables = $('.mediaTable');

        // redo odd-even
        $tables.find('tr').removeClass('odd').removeClass('even');
        $tables.find('tr:even').addClass('odd');
        $tables.find('tr:odd').addClass('even');

        // bind change when connecting/disconnecting media
        $tables.find('.toggleConnectedCheckbox').on('click', function(e) {
            // mediaId
            mediaId = $(this).parent().parent().attr('id').replace('media-', '');

            // was already connected?
            var connected = utils.array.inArray(mediaId, mediaCurrentGroup);

            // delete from array
            if (connected) {
                // loop all to find value and to delete it
                mediaCurrentGroup.splice(mediaCurrentGroup.indexOf(mediaId), 1);

                // update folder count
                jsBackend.mediaLibraryHelper.group.updateFolderCount(mediaFolderId, '-', 1);
            // add to array
            } else {
                mediaCurrentGroup.push(mediaId);

                // update folder count
                jsBackend.mediaLibraryHelper.group.updateFolderCount(mediaFolderId, '+', 1);
            }
        });

        // select the correct folder
        jsBackend.mediaLibraryHelper.group.updateFolderSelected();
    }
};

/**
 * All methods related to the upload
 * global: jsBackend
 */
jsBackend.mediaLibraryHelper.upload =
{
    init: function()
    {
        mediaFolderId = $('#uploadMediaFolderId').val();

        $('#fine-uploader-gallery').fineUploader({
            template: 'qq-template-gallery',
            thumbnails: {
                placeholders: {
                    waitingPath: '/css/vendors/fine-uploader/waiting-generic.png',
                    notAvailablePath: '/css/vendors/fine-uploader/not_available-generic.png'
                }
            },
            validation: {
                allowedExtensions: ['jpeg', 'jpg', 'gif', 'png', 'csv', 'doc', 'docx', 'pdf', 'rtf', 'txt', 'xls', 'xlsx', 'aiff', 'mp3', 'wav']
            },
            callbacks: {
                onUpload:  function() {
                    // redefine media folder id
                    mediaFolderId = $('#uploadMediaFolderId').val();

                    // We must set the endpoint dynamically, because "uploadMediaFolderId" is null at start and is async loaded using AJAX.
                    this.setEndpoint('/backend/ajax?fork[module]=MediaLibrary&fork[action]=UploadMediaItem&fork[language]=' + jsBackend.current.language + '&folder_id=' + mediaFolderId);
                },
                onComplete: function(id, name, responseJSON) {
                    // add file to uploaded box
                    jsBackend.mediaLibraryHelper.upload.addUploadedFile(responseJSON);

                    // update counter
                    jsBackend.mediaLibraryHelper.upload.uploadedCount += 1;

                    // toggle upload box
                    jsBackend.mediaLibraryHelper.upload.toggleUploadBoxes();
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
                        jsBackend.messages.add('error', utils.string.sprintf(jsBackend.locale.msg('MediaUploadedError'), (succeeded.length + '","' + failed.length)));
                    }
                }
            }
        });
    },

    /**
     * Add the uploaded file to the box
     *
     * @param item This is the media-item that ajax returned for us.
     */
    addUploadedFile : function(item)
    {
        // init html
        html = '';

        // create element
        html += '<li id="media-' + item.id + '" data-folder-id="' + item.folder.id + '" class="ui-state-default">';
        html += '    <div class="mediaHolder mediaHolder' + utils.string.ucfirst(item.type) + '">';

        // is image
        if (item.type == 'image') {
            html += '        <img src="' + item.preview_source + '" alt="' + item.title + '" title="' + item.title + '"/>';
        // is file, movie or audio
        } else {
            html += '        <div class="icon"></div>';
            html += '        <div class="url">' + item.url + '</div>';
        }

        html += '    </div>';
        html += '    <a href="#/" class="deleteMediaItem btn btn-default" ';
        html += 'title="' + utils.string.ucfirst(jsBackend.locale.msg('MediaDoNotConnectThisMedia')) + '">';
        html += '        <span>' + utils.string.ucfirst(jsBackend.locale.msg('MediaDoNotConnectThisMedia')) + '</span>';
        html += '    </a>';
        html += '</li>';

        // add element to box
        $('#uploadedMedia').append(html);
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
            mediaCurrentGroup.push(id);
        });

        // clear upload queue count
        jsBackend.mediaLibraryHelper.upload.uploadedCount = 0;

        // clear all elements
        $('#uploadedMedia').html('');
    },

    /**
     * Bind events
     */
    bindEvents: function()
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

    /**
     * Insert movie
     *
     * @param Event e
     */
    insertMovie : function(e)
    {
        // prevent other functions
        e.preventDefault();

        // update media for folder
        mediaFolderId = $('#uploadMediaFolderId').val();

        // define variables
        var mime = $('#mediaMovieSource').find(':checked').val();
        var $id = $('#mediaMovieId');
        var $title = $('#mediaMovieTitle');

        // insert movie using ajax
        $.ajax({
            data: {
                fork: {
                    module: 'MediaLibrary',
                    action: 'InsertMediaItemMovie'
                },
                folder_id: mediaFolderId,
                mime: mime,
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
                    jsBackend.mediaLibraryHelper.upload.addUploadedFile(json.data);

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
        var groupType = (mediaGalleries[mediaGroupI]) ? mediaGalleries[mediaGroupI].type : 'all';

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
        $('#mediaWillBeConnectedToMediaGroup').toggle((mediaGroupI !== 0));
    },

    /**
     * Needed to handle the visibility of the uploadedMediaBox
     *
     * @param int
     */
    uploadedCount : 0
};

function json2array(json){
    var result = [];
    var keys = Object.keys(json);
    keys.forEach(function(key){
        result.push(json[key]);
    });
    return result;
}

function sortByProperty(property) {
    'use strict';
    return function (a, b) {
        var sortStatus = 0;
        if (a[property] < b[property]) {
            sortStatus = -1;
        } else if (a[property] > b[property]) {
            sortStatus = 1;
        }

        return sortStatus;
    };
}

/** global: jsBackend */
$(jsBackend.mediaLibraryHelper.init);
