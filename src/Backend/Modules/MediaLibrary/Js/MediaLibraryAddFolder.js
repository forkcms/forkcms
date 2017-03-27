/**
 * Controls the adding of folders on the fly
 * global: jsBackend
 * global: utils
 */
jsBackend.mediaLibraryAddFolder =
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
            jsBackend.mediaLibraryAddFolder.updateFolders(selectedFolderId);
        }

        $addFolderSubmit.click(function () {
            // hide errors
            $folderTitleError.hide();

            // get selected folder
            selectedFolderId = ($('#uploadMediaFolderId').val()) ? $('#uploadMediaFolderId').val() : selectedFolderId;

            // update folders
            jsBackend.mediaLibraryAddFolder.updateFolders(selectedFolderId, true);

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
                    jsBackend.mediaLibraryAddFolder.updateFolders(json.data.id);

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
     * @param int selectFolderId [optional]     Selects this folder
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

                // add empty element to html
                var html = '';

                // cache folders
                var folders = json2array(json.data).sort(sortByProperty('name'));

                // add folders to html
                $.each(folders, function(i, item) {
                    html += '<option value="' + item.id + '">' + item.name + '</option>';
                });

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
                if (selectFolderId) $('#addFolderParentId').val(selectFolderId);
            }
        });
    }
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
        if (a[property] < b[property]) {
            return -1;
        } else if (a[property] > b[property]) {
            return 1;
        }

        return 0;
    };
}

/** global: jsBackend */
$(jsBackend.mediaLibraryAddFolder.init);
