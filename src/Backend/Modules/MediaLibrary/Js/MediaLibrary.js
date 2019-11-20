/**
 * Interaction for the media module
 * global jsBackend
 * global utils
 */
jsBackend.mediaLibrary = {
  init: function () {
    // load the tree
    jsBackend.mediaLibrary.tree.init()

    // add some extra controls
    jsBackend.mediaLibrary.controls.init()

    // adds functionalities for library
    jsBackend.mediaLibrary.library.init()
  }
}

/**
 * Add some extra controls
 * global jsBackend
 */
jsBackend.mediaLibrary.controls = {
  init: function () {
    // save and edit
    $('#saveAndEdit').on('click', function () {
      $('form').append('<input type="hidden" name="after_save" value="MediaItemEdit" />').submit()
    })
  }
}

/**
 * All methods related to the library overview
 * global jsBackend
 */
jsBackend.mediaLibrary.library = {
  currentType: null,
  init: function () {
    // start or not
    if ($('#library').length === 0) {
      return false
    }

    // init edit folder dialog
    jsBackend.mediaLibrary.library.addEditFolderDialog()

    // init mass action hidden input fields
    jsBackend.mediaLibrary.library.dataGrids()
  },

  /**
   * Add edit folder dialog
   */
  addEditFolderDialog: function () {
    var $editMediaFolderDialog = $('#editMediaFolderDialog')
    var $editMediaFolderSubmit = $('#editMediaFolderSubmit')

    // stop here
    if ($editMediaFolderDialog.length === 0) {
      return false
    }

    $editMediaFolderSubmit.on('click', function () {
      // Update folder using ajax
      $.ajax({
        data: {
          fork: {action: 'MediaFolderEdit'},
          folder_id: $('#mediaFolderId').val(),
          name: $('#mediaFolderName').val()
        },
        success: function (json, textStatus) {
          if (json.code !== 200) {
            // show error if needed
            if (jsBackend.debug) {
              window.alert(textStatus)
            }

            // show message
            jsBackend.messages.error('success', textStatus)

            return
          }

          // show message
          jsBackend.messages.add('success', json.message)

          // close dialog
          $('#editFolderDialog').modal('close')

          // reload document
          window.location.reload(true)
        }
      })
    })
  },

  /**
   * Move audio to another folder or connect audio to a gallery
   */
  dataGrids: function () {
    if (window.location.hash === '') {
      // select first tab
      $('#library .nav-tabs .nav-item:first .nav-link').tab('show')
    }

    // When mass action button is clicked
    $('.jsMassActionSubmit').on('click', function () {
      // We remember the current type (image, file, movie, audio, ...)
      jsBackend.mediaLibrary.library.currentType = $(this).parent().find('select[name=action]').attr('id').replace('mass-action-', '')
    })

    // Submit form
    $('#confirmMassActionMediaItemMove').find('button[type=submit]').on('click', function () {
      $('#move-to-folder-id-for-type-' + jsBackend.mediaLibrary.library.currentType).val($('#moveToFolderId').val())
      $('#form-for-' + jsBackend.mediaLibrary.library.currentType).submit()
    })

    $('#confirmMassActionMediaItemDelete').find('button[type=submit]').on('click', function () {
      $('#form-for-' + jsBackend.mediaLibrary.library.currentType).submit()
    })
  }
}

/**
 * All methods related to the tree
 * global jsBackend
 * global utils
 */
jsBackend.mediaLibrary.tree = {
  pageID: null,
  // init, something like a constructor
  init: function () {
    if ($('#tree').find('> [data-tree]').length === 0) return false

    // jsTree options
    var options = {
      core: {
        animation: 0,
        themes: {
          name: 'proton',
          responsive: true
        },
        multiple: false,
        check_callback: true
      },
      types: {
        default: {
          icon: 'far fa-folder'
        },
        folder: {
          icon: 'far fa-folder'
        }
      },
      search: {
        show_only_matches: true
      },
      dnd: {
        inside_pos: 'last'
      },
      plugins: ['dnd', 'types', 'state', 'search']
    }

    // Init folder tree
    var jsTreeInstance = $('#tree').find('> [data-tree]').jstree(options)

    jsTreeInstance.on('move_node.jstree', function (event, data) {
      jsBackend.mediaLibrary.tree.onMove(event, data, jsTreeInstance)
    })

    // Search through folders
    var searchThroughPages = function () {
      var v = $('.js-tree-search').val()
      $('#tree').find('> [data-tree]').each(function () {
        $(this).jstree(true).search(v)
      })
    }
    $('.js-tree-search').bind('keyup input', utils.events.debounce(searchThroughPages, 150))

    // To prevent FOUC, we only show the jsTree when it's done loading.
    jsTreeInstance.on('ready.jstree', function (e, data) {
      $('#tree').show()
    })

    // On selecting a node in the tree, visit the anchor.
    jsTreeInstance.on('select_node.jstree', function (e, data, jsTreeInstance) {
      if (data && data.node && data.event) {
        // Get current and new URL
        var node = data.node
        var currentPageURL = window.location.pathname + window.location.search
        var newPageURL = node.a_attr.href

        // Only redirect if destination isn't the current one.
        if (typeof newPageURL !== 'undefined' && newPageURL !== currentPageURL) {
          window.location = newPageURL
        }
      }
    })

    jsBackend.mediaLibrary.tree.toggleJsTreeCollapse(jsTreeInstance)
  },

  // when an item is selected
  onSelect: function (node, tree) {
    // get current and new URL
    var currentPageURL = window.location.pathname + window.location.search
    var newPageURL = $(node).find('a').prop('href')

    // only redirect if destination isn't the current one.
    if (typeof newPageURL !== 'undefined' && newPageURL !== currentPageURL) {
      window.location = newPageURL
    }
  },

  // when an item is moved
  onMove: function (event, data, jsTreeInstance) {
    var tree = data.new_instance.element.first().data('tree')

    // get folderID that has to be moved
    var currentPageID = data.node.id.replace('folder-', '')

    // set the default type of moving
    var type = 'before'

    // init allowMove
    var allowMove = false

    // get the position where the folder is dropped
    var droppedOnElement = $('#' + data.new_instance._model.data[data.parent].children[data.position + 1])

    // when node is dropped on the last position, we can not add it before the last one -> we need after
    if (data.new_instance._model.data[data.parent].children[data.position + 1] === undefined) {
      droppedOnElement = $('#' + data.new_instance._model.data[data.parent].children[data.position - 1])
      type = 'after'
    }

    // when node is dropped on another node to move it inside
    if (data.new_instance._model.data[data.parent].children.length === 1 && data.new_instance._model.data[data.parent].children[0] === data.node.id) {
      type = 'inside'
      droppedOnElement = $('#' + data.parent)
    }

    // get folderID wheron the folder has been dropped
    var droppedOnPageID = droppedOnElement.prop('id').replace('folder-', '')

    // before an item will be moved we have to do some checks
    $.ajax({
      async: false, // important that this isn't asynchronous
      data: {
        fork: {action: 'MediaFolderInfo'},
        id: currentPageID,
        dropped_on: droppedOnPageID
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        if (jsBackend.debug) window.alert(textStatus)
        allowMove = false
      },
      success: function (json, textStatus) {
        if (json.code !== 200) {
          if (jsBackend.debug) window.alert(textStatus)
          allowMove = false
        } else {
          // is page allowed to move
          if (json.data.allow_move) {
            allowMove = true
          } else {
            jsTreeInstance.jstree('refresh')
            jsBackend.messages.add('danger', jsBackend.locale.err('CantBeMoved'))
            allowMove = false
          }
        }
      }
    })

    if (!allowMove) {
      return
    }

    // move the folder
    $.ajax({
      data: {
        fork: {action: 'MediaFolderMove'},
        id: currentPageID,
        dropped_on: droppedOnPageID,
        type: type,
        tree: tree
      },
      success: function (json, textStatus) {
        if (json.code !== 200) {
          if (jsBackend.debug) window.alert(textStatus)

          // show message
          jsBackend.messages.add('danger', jsBackend.locale.err('CantBeMoved'))

          // rollback
          jsTreeInstance.jstree('refresh')
        } else {
          // show message
          jsBackend.messages.add('success', json.message)
        }
      }
    })
  },

  toggleJsTreeCollapse: function (jsTreeInstance) {
    $('[data-role="toggle-js-tree-collapse"]').on('click', function () {
      var $this = $(this)
      $this.toggleClass('tree-collapsed')
      var collapsed = $this.hasClass('tree-collapsed')
      var $buttonText = $('[data-role="toggle-js-tree-collapse-text"]')

      if (collapsed) {
        $buttonText.html(jsBackend.locale.lbl('OpenTreeNavigation'))
        jsTreeInstance.jstree('close_all')

        return
      }

      $buttonText.html(jsBackend.locale.lbl('CloseTreeNavigation'))
      jsTreeInstance.jstree('open_all')
    })
  }
}

/** global jsBackend */
$(jsBackend.mediaLibrary.init)
