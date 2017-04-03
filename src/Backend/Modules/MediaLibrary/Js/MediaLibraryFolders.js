/**
 * Controls the adding of folders on the fly
 * global: jsBackend
 * global: utils
 */
jsBackend.mediaLibraryFolders =
{
    init: function()
    {
        var $addFolderSubmit = $('#addFolderSubmit');
        var $addFolderDialog = $('#addFolderDialog');
        var $folderTitleError = $('#folderTitleError');

        // start or not
        if ($addFolderDialog.length === 0 || $addFolderSubmit.length === 0) {
            return false;
        }

        // get folder from id
        var selectedFolderId = (utils.url.getGetValue('folder')) ? utils.url.getGetValue('folder') : '';

        // add folders on startup
        if ($('#uploadMediaFolderId').length > 0) {
            jsBackend.mediaLibraryFolders.updateFolders(selectedFolderId);
        }

        $addFolderSubmit.click(function () {
            // hide errors
            $folderTitleError.hide();

            // get selected folder
            selectedFolderId = ($('#uploadMediaFolderId').val()) ? $('#uploadMediaFolderId').val() : selectedFolderId;

            // update folders
            jsBackend.mediaLibraryFolders.updateFolders(selectedFolderId, true);

            $.ajax({
                data: {
                    fork: {module: 'MediaLibrary', action: 'MediaFolderAdd'},
                    name: $('#addFolderTitle').val(),
                    parent_id: $('#addFolderParentId').val()
                },
                success: function (json, textStatus) {
                    if (json.code != 200) {
                        // show error if needed
                        if (jsBackend.debug) {
                            alert(textStatus);
                        }

                        // show message
                        $('#descriptionTitleError').show();

                        return;
                    }

                    // add and set selected
                    $('#mediaConnectedId').append('<option value="'+ json.data.id +'">'+ json.data.name +'</option>');

                    // show message
                    jsBackend.messages.add('success', jsBackend.locale.msg('FolderIsAdded'));

                    // update folders
                    jsBackend.mediaLibraryFolders.updateFolders(json.data.id);

                    $addFolderDialog.modal('hide');
                }
            });
        });

        // bind click
        $('#addFolder').on('click', function (e) {
            // prevent default
            e.preventDefault();

            // open dialog
            $addFolderDialog.modal('show');

            // Focus the text field
            $('#addFolderTitle').focus();
        });

        $addFolderDialog.on('hide.bs.modal', function () {
            $('#addFolderTitle').val('');
        });
    },

    /**
     * Get and update the folders using ajax
     *
     * @param {int} selectFolderId [optional] - Selects this folder
     */
    updateFolders : function(selectFolderId, dialog)
    {
        // define select folder id
        selectFolderId = (selectFolderId != null) ? selectFolderId : false;
        dialog = !!dialog;

        // get folders using ajax
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

                    // show message
                    $('#addFolderTitle').show();

                    return;
                }

                var html = jsBackend.mediaLibraryFolders.templates.getHTMLForMediaFolders(json.data);

                // update folders in media module
                if (!dialog) {
                    // add folders to dropdowns
                    $('#mediaFolders, #uploadMediaFolderId').html(html);

                    // select the new folder
                    if (selectFolderId) {
                        $('#uploadMediaFolderId').val(selectFolderId);
                    } else {
                        $('#uploadMediaFolderId option:eq(0)').attr("selected", "selected");
                    }

                    // update boxes
                    jsBackend.mediaLibraryHelper.upload.toggleUploadBoxes();
                }

                // add folders to dropdown
                $('#addFolderParentId').html('<option value="0" />' + html);

                // select the new folder
                if (selectFolderId) {
                    $('#addFolderParentId').val(selectFolderId);
                }
            }
        });
    }
};

jsBackend.mediaLibraryFolders.templates =
{
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
            html += jsBackend.mediaLibraryFolders.templates.getHTMLForMediaFolder(mediaFolder);
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
        var html = '<option value="' + mediaFolder.id + '">' + mediaFolder.slug + '</option>';

        if (mediaFolder.numberOfChildren > 0) {
            html += jsBackend.mediaLibraryFolders.templates.getHTMLForMediaFolders(mediaFolder.children);
        }

        return html;
    }
};

/** global: jsBackend */
$(jsBackend.mediaLibraryFolders.init);
