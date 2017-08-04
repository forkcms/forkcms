/**
 * Interaction for the media module
 * global: jsBackend
 * global: utils
 */
jsBackend.mediaLibrary =
{
    init: function()
    {
        // load the tree
        jsBackend.mediaLibrary.tree.init();

        // add some extra controls
        jsBackend.mediaLibrary.controls.init();

        // adds functionalities for library
        jsBackend.mediaLibrary.library.init();
    }
};

/**
 * Add some extra controls
 * global: jsBackend
 */
jsBackend.mediaLibrary.controls =
{
    init: function()
    {
        // save and edit
        $('#saveAndEdit').on('click', function()
        {
            $('form').append('<input type="hidden" name="after_save" value="MediaItemEdit" />').submit();
        });

        // Bind dialog to "mediaItemCleanup" button
        $('a[data-role=media-item-cleanup]').on('click', function(e){
            e.preventDefault();

            $($(this).data('target')).modal('show');
        });
    }
};

/**
 * All methods related to the library overview
 * global: jsBackend
 */
jsBackend.mediaLibrary.library =
{
    currentType: null,
    init: function()
    {
        // start or not
        if ($('#library').length == 0) {
            return false;
        }

        // init edit folder dialog
        jsBackend.mediaLibrary.library.addEditFolderDialog();

        // init mass action hidden input fields
        jsBackend.mediaLibrary.library.dataGrids();
    },

    /**
     * Add edit folder dialog
     */
    addEditFolderDialog : function()
    {
        var $editMediaFolderDialog = $('#editMediaFolderDialog');
        var $editMediaFolderSubmit = $('#editMediaFolderSubmit');

        // stop here
        if ($editMediaFolderDialog.length == 0) {
            return false;
        }

        $editMediaFolderSubmit.on('click', function(){
            // Update folder using ajax
            $.ajax({
                data: {
                    fork: { action: 'MediaFolderEdit' },
                    folder_id: $('#mediaFolderId').val(),
                    name: $('#mediaFolderName').val()
                },
                success: function(json, textStatus) {
                    if (json.code != 200) {
                        // show error if needed
                        if (jsBackend.debug) {
                            alert(textStatus);
                        }

                        // show message
                        jsBackend.messages.error('success', textStatus);

                        return;
                    }

                    // show message
                    jsBackend.messages.add('success', json.message);

                    // close dialog
                    $('#editFolderDialog').modal('close');

                    // reload document
                    window.location.reload(true);
                }
            });
        });
    },

    /**
     * Move audio to another folder or connect audio to a gallery
     */
    dataGrids : function()
    {
        if (window.location.hash == '') {
            // select first tab
            $('#library .nav-tabs a:first').tab('show');
        }

        // When mass action button is clicked
        $('.jsMassActionSubmit').on('click', function(){
            // We remember the current type (image, file, movie, audio, ...)
            jsBackend.mediaLibrary.library.currentType = $(this).parent().find('select[name=action]').attr('id').replace('mass-action-', '');
        });

        // Submit form
        $('#confirmMassActionMediaItemMove').find('button[type=submit]').on('click', function(){
            $('#move-to-folder-id-for-type-' + jsBackend.mediaLibrary.library.currentType).val($('#moveToFolderId').val());
            $('#form-for-' + jsBackend.mediaLibrary.library.currentType).submit();
        });
    }
};

/**
 * All methods related to the tree
 * global: jsBackend
 * global: utils
 */
jsBackend.mediaLibrary.tree =
{
    pageID: null,
    // init, something like a constructor
    init: function()
    {
        var $treeHolder = $('#tree div');

        if ($treeHolder.length === 0) {
            return false;
        }

        // add "treeHidden"-class on leafs that are hidden, only for browsers that don't support opacity
        if (!jQuery.support.opacity) {
            $('#tree ul li[rel="hidden"]').addClass('treeHidden');
        }

        // set the item selected
        if (jsBackend.data.get('MediaLibrary.openedFolderId')) {
            $('#folder-' + jsBackend.data.get('MediaLibrary.openedFolderId')).addClass('selected');
            jsBackend.mediaLibrary.tree.pageID = jsBackend.data.get('MediaLibrary.openedFolderId');
        }

        var openedIds = [];
        if (typeof jsBackend.mediaLibrary.tree.pageID != 'undefined') {
            // get parents
            var parents = $('#folder-'+ jsBackend.mediaLibrary.tree.pageID).parents('li');

            // init var
            openedIds = ['folder-'+ jsBackend.mediaLibrary.tree.pageID];

            // add parents
            for(var i = 0; i < parents.length; i++) {
                openedIds.push($(parents[i]).prop('id'));
            }
        }

        // add home if needed
        if (!utils.array.inArray('folder-1', openedIds)) {
            openedIds.push('folder-1');
        }

        var options = {
            ui: { theme_name: 'fork' },
            opened: openedIds,
            rules: {
                multiple: false,
                multitree: 'all',
                drag_copy: false
            },
            lang: { loading: utils.string.ucfirst(jsBackend.locale.lbl('Loading')) },
            callback: {
                beforemove: jsBackend.mediaLibrary.tree.beforeMove,
                onselect: jsBackend.mediaLibrary.tree.onSelect,
                onmove: jsBackend.mediaLibrary.tree.onMove
            },
            plugins: {
                cookie: { prefix: 'jstree_', types: { selected: false }, options: { path: '/' } }
            }
        };

        // create tree
        $treeHolder.tree(options);

        // layout fix for the tree
        $('.tree li.open').each(function() {
            // if the so-called open-element doesn't have any childs we should replace the open-class.
            if ($(this).find('ul').length === 0) {
                $(this).removeClass('open').addClass('leaf');
            }
        });
    },

    // before an item will be moved we have to do some checks
    beforeMove: function(node, refNode, type, tree)
    {
        // get pageID that has to be moved
        var currentPageID = $(node).prop('id').replace('folder-', '');
        var parentPageID = (typeof refNode == 'undefined') ? 0 : $(refNode).prop('id').replace('folder-', '');

        // init var
        var result = false;

        // make the call
        $.ajax({
            async: false, // important that this isn't asynchronous
            data: {
                fork: { action: 'MediaFolderInfo' },
                id: currentPageID
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                if (jsBackend.debug) {
                    alert(textStatus);
                }
                result = false;
            },
            success: function(json, textStatus) {
                if (json.code != 200) {
                    if (jsBackend.debug) {
                        alert(textStatus);
                    }
                    result = false;

                    return;
                }

                if (json.data.allow_move) {
                    result = true;
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
        if (typeof newPageURL != 'undefined' && newPageURL != currentPageURL) {
            window.location = newPageURL;
        }
    },

    // when an item is moved
    onMove: function(node, refNode, type, tree, rollback)
    {
        // get the tree
        tree = tree.container.data('tree');

        // get pageID that has to be moved
        var currentPageID = $(node).prop('id').replace('folder-', '');

        // get pageID wheron the page has been dropped
        var droppedOnPageID = jsBackend.mediaLibrary.tree.getDroppedOnPageID(refNode);

        // make the call
        $.ajax({
            data: {
                fork: { action: 'MediaFolderMove' },
                id: currentPageID,
                dropped_on: droppedOnPageID,
                type: type,
                tree: tree
            },
            success: function(json, textStatus) {
                if (json.code == 200) {
                    // show message
                    jsBackend.messages.add('success', json.message);

                    return;
                }

                if (jsBackend.debug) {
                    alert(textStatus);
                }

                // show message
                jsBackend.messages.add('danger', jsBackend.locale.err('CantBeMoved'));

                // rollback
                $.tree.rollback(rollback);
            }
        });
    },

    getDroppedOnPageID: function(refNode)
    {
        if (typeof refNode === 'undefined') {
            return 0;
        }

        return $(refNode).prop('id').replace('folder-', '');
    }
};

/** global: jsBackend */
$(jsBackend.mediaLibrary.init);
