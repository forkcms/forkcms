/**
 * Interaction for the pages module
 */
/* global pageID, CKEDITOR, extrasById, extrasData, ss, templates, initDefaults, selectedId */

jsBackend.pages = {
  // init, something like a constructor
  init: function () {
    // load the tree
    jsBackend.pages.tree.init()

    // are we adding or editing?
    if ($('[data-role="template-switcher"]').length > 0) {
      // load stuff for editing and adding an page
      jsBackend.pages.extras.init()
      //jsBackend.pages.template.init()
    }

    if ($('[data-role="move-page-toggle"]').length > 0) {
      jsBackend.pages.move.init()
    }
  }
}

jsBackend.pages.move = {
  init: function () {
    jsBackend.pages.move.allowGodUsersToEnableTheMovePageSetting()
    jsBackend.pages.move.handleChangingPageTree()
    jsBackend.pages.move.handleChangingReferencePage()
  },

  handleChangingReferencePage: function () {
    $('[data-role="move-page-pages-select"]').on('change', function () {
      var $this = $(this)
      var $selectedOption = $this.find(':selected')
      var $typeSelect = $('[data-role="move-page-type-changer"]')
      var $typeSelectOptions = $typeSelect.find('option')
      var selectedValue = $typeSelect.find('option:selected').val()
      $typeSelectOptions.removeClass('disabled').prop('disabled', false).prop('selected', false)

      if ($selectedOption.data('allowInside') !== 1 && $this.val() !== '0') {
        $typeSelect.find('[value=inside]').addClass('disabled').prop('disabled', true)
      }
      if ($selectedOption.data('allowBefore') !== 1) {
        $typeSelect.find('[value=before]').addClass('disabled').prop('disabled', true)
      }
      if ($selectedOption.data('allowAfter') !== 1) {
        $typeSelect.find('[value=after]').addClass('disabled').prop('disabled', true)
      }

      if ($typeSelect.find('option[value="' + selectedValue + '"]').is(':disabled')) {
        selectedValue = $typeSelectOptions.not(':disabled').first().val()
      }

      $typeSelect.val(selectedValue)
      $typeSelect.trigger('change')
    }).trigger('change')
  },

  handleChangingPageTree: function () {
    $('[data-role="move-page-tree-changer"]').on('change', function () {
      var $this = $(this)
      var $pagesSelect = $('[data-role="move-page-pages-select"]')
      var selectedValue = $pagesSelect.find('option:selected').val()

      $pagesSelect.find('optgroup option').prop('selected', false).prop('disabled', true)

      // only show the pages of the selected tree
      $pagesSelect.find('optgroup').hide().children().hide()
      var $visiblePages = $pagesSelect.find('[data-tree-name="' + $this.val() + '"]')
      if ($visiblePages.length > 0) {
        $visiblePages.show().prop('disabled', false)
        $pagesSelect.find('optgroup[label="' + $visiblePages.first().attr('data-tree-label') + '"]').show()
      }

      if ($pagesSelect.find('option[value="' + selectedValue + '"]').is(':disabled')) {
        selectedValue = 0
      }

      $pagesSelect.val(selectedValue)
      $pagesSelect.trigger('change')
    }).trigger('change')
  },

  allowGodUsersToEnableTheMovePageSetting: function () {
    var $toggle = $('[data-role="allow-move-toggle"]')
    if ($toggle.length === 0) {
      return
    }

    $toggle.on('change', function () {
      var $movePageToggle = $('[data-role="move-page-toggle"]')
      if ($(this).is(':checked')) {
        $movePageToggle.removeClass('disabled').prop('disabled', false)

        return
      }

      $movePageToggle.addClass('disabled').prop('disabled', true).prop('checked', false).trigger('change')
    }).trigger('change')
  }
}

/**
 * All methods related to the controls (buttons, ...)
 */
jsBackend.pages.extras = {
  // init, something like a constructor
  init: function () {
    jsBackend.pages.extras.blockTypeSwitcher()
    jsBackend.pages.extras.saveBlock()
  },

  saveBlock: function () {
    $('[data-role="page-content-tab"]').on('click', '[data-role="page-block-save"]', function() {
      var $modal = $(this).closest('.modal')
      var separator = ' › '
      var $pageBlockTitle = $modal.closest('[data-role="page-block-wrapper"]').find('[data-role="page-block-title"]')
      var selectedBlockType = $modal.find('[data-role="select-block-type"]').val()
      var title = $modal.find('[data-role="select-block-type"] option:selected').text()

      if (selectedBlockType === 'block' ||selectedBlockType === 'widget') {
        title += separator + jsBackend.pages.extras.extractExtraTitle(
          separator,
          $modal.find('[data-role="page-block-content-type-wrapper"][data-type="' + selectedBlockType + '"] select option:selected')
        )
      }

      $pageBlockTitle.text(title)
      $modal.modal('hide')
    })

    $('[data-role="page-content-tab"] [data-role="page-block-save"]').trigger('click')
  },

  extractExtraTitle: function (separator, $selectedOption) {
    return $selectedOption.closest('optgroup').attr('label') + separator + $selectedOption.text()
  },

  blockTypeSwitcher: function () {
    $('[data-role="page-content-tab"]').on('change', '[data-role="select-block-type"]', function () {
      var $this = $(this)
      var $wrapper = $this.closest('[data-role="page-block-form-wrapper"]')
      $wrapper.find('[data-role="page-block-content-type-wrapper"]').hide()
      $wrapper.find('[data-role="page-block-content-type-wrapper"][data-type=' + $this.val() + ']').show()
    })
    $('[data-role="page-content-tab"] [data-role="select-block-type"]').trigger('change') // set the initial state
  }
}

/**
 * All methods related to the templates
 */
jsBackend.pages.template = {
  // indicates whether or not the page content is original or has been altered already
  original: true,
  userTemplates: {},

  // init, something like a constructor
  init: function () {
    // bind events
    jsBackend.pages.template.changeTemplateBindSubmit()

    // assign the global variable so we can use & modify it later on
    jsBackend.pages.template.initDefaults = initDefaults

    // load to initialize when adding a page
    jsBackend.pages.template.changeTemplate()
  },

  // method to change a template
  changeTemplate: function () {
    // get checked
    var selected = $('#templateList input:radio:checked').val()

    // get current & old template
    var old = templates[$('#templateId').val()]
    var current = templates[selected]
    var i = 0

    // show or hide the image tab
    if ('image' in current.data && current.data.image) {
      $('.js-page-image-tab').show()
    }
    else {
      $('.js-page-image-tab').hide()
    }

    // hide default (base) block
    $('#block-0').hide()

    // reset HTML for the visual representation of the template
    $('#templateVisual').html(current.html)
    $('#templateVisualLarge').html(current.htmlLarge)
    $('#templateVisualFallback .linkedBlocks').children().remove()
    $('#templateId').val(selected)
    $('#templateLabel, #tabTemplateLabel').html(current.label)

    // make new positions sortable
    jsBackend.pages.extras.sortable($('#templateVisualLarge div.linkedBlocks'))

    // hide fallback by default
    $('#templateVisualFallback').hide()

    // remove previous fallback blocks
    $('input[id^=blockPosition][value=fallback][id!=blockPosition0]').parent().remove()

    // check if we have already committed changes (if not, we can just ignore existing blocks and remove all of them)
    if (current !== old && jsBackend.pages.template.original) $('input[id^=blockPosition][id!=blockPosition0]').parent().remove()

    // loop existing blocks
    $('#editContent .contentBlock').each(function (i) {
      // fetch variables
      var index = parseInt($('input[id^=blockExtraId]', this).prop('id').replace('blockExtraId', ''))
      var extraId = parseInt($('input[id^=blockExtraId]', this).val())
      var position = $('input[id^=blockPosition]', this).val()
      var html = $('textarea[id^=blockHtml]', this).val()

      // skip default (base) block (= continue)
      if (index === 0) return true

      // blocks were present already = template was not original
      jsBackend.pages.template.original = false

      // check if this block is a default of the old template, in which case it'll go to the fallback position
      if (current !== old && typeof old.data.default_extras !== 'undefined' && $.inArray(extraId, old.data.default_extras[position]) >= 0 && html === '') $('input[id=blockPosition' + index + ']', this).val('fallback')
    })

    // init var
    var newDefaults = []

    // check if this default block has been changed
    if (current !== old || (typeof jsBackend.pages.template.initDefaults !== 'undefined' && jsBackend.pages.template.initDefaults)) {
      // this is a variable indicating that the add-action may initially set default blocks
      if (typeof jsBackend.pages.template.initDefaults !== 'undefined') jsBackend.pages.template.initDefaults = false

      // loop positions in new template
      for (var position in current.data.default_extras) {
        // loop default extra's on positions
        for (var block in current.data.default_extras[position]) {
          // grab extraId
          var extraId = current.data.default_extras[position][block]

          // find existing block sent to default
          var existingBlock = $('input[id^=blockPosition][value=fallback]:not(#blockPosition0)').parent().find('input[id^=blockExtraId][value=' + extraId + ']').parent()

          // if this block did net yet exist, add it
          if (existingBlock.length === 0) {
            newDefaults.push([extraId, position])
          }
          else {
            // if this block already existed, reset it to correct (new) position
            $('input[id^=blockPosition]', existingBlock).val(position)
          }
        }
      }
    }

    // loop existing blocks
    $('#editContent .contentBlock').each(function (i) {
      // fetch variables
      var index = parseInt($('input[id^=blockExtraId]', this).prop('id').replace('blockExtraId', ''))
      var extraId = parseInt($('input[id^=blockExtraId]', this).val())
      var extraType = $('input[id^=blockExtraType]', this).val()
      var extraData = $('input[id^=blockExtraData]', this).val()
      var position = $('input[id^=blockPosition]', this).val()
      var visible = $('input[id^=blockVisible]', this).attr('checked')

      // skip default (base) block (= continue)
      if (index === 0) return true

      // check if this position exists
      if ($.inArray(position, current.data.names) < 0) {
        // blocks in positions that do no longer exist should go to fallback
        position = 'fallback'

        // save position as fallback
        $('input[id=blockPosition' + index + ']', this).val(position)

        // show fallback
        $('#templateVisualFallback').show()
      }

      // add visual representation of block to template visualisation
      var added = jsBackend.pages.extras.addBlockVisual(position, index, extraId, visible, extraType, extraData)

      // if the visual could be not added, remove the content entirely
      if (!added) $(this).remove()
    })

    // reset block indexes
    jsBackend.pages.extras.resetIndexes()

    // add new defaults at last
    for (i in newDefaults) {
      jsBackend.pages.extras.addBlock(newDefaults[i][0], newDefaults[i][1])
    }
  },

  // bind template change submit click event
  changeTemplateBindSubmit: function (e) {
    // prevent the default action
    $('#changeTemplateSubmit').unbind('click').on('click', function(e) {
      e.preventDefault()
      if ($('#templateList input:radio:checked').val() !== $('#templateId').val()) {
        // change the template for real
        jsBackend.pages.template.changeTemplate()
      }

      // close modal
      $('#changeTemplate').modal('hide')
    })
  }
}

/**
 * All methods related to the pages tree
 */
jsBackend.pages.tree = {
  // init, something like a constructor
  init: function () {
    if ($('#tree').find('> [data-tree]').length === 0) return false

    var openedIds = []
    if (typeof pageID !== 'undefined') {
      // get parents
      var parents = $('#page-' + pageID).parents('li')

      // init var
      openedIds = ['page-' + pageID]

      // add parents
      for (var i = 0; i < parents.length; i++) {
        openedIds.push($(parents[i]).prop('id'))
      }
    }

    // add home if needed
    if (!utils.array.inArray('page-1', openedIds)) openedIds.push('page-1')

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
          icon: 'far fa-file'
        },
        home: {
          icon: 'fas fa-home'
        },
        anchor: {
          icon: 'far fa-file'
        },
        sitemap: {
          icon: 'fas fa-sitemap'
        },
        error: {
          icon: 'fas fa-exclamation-triangle'
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

    // Init page tree
    var jsTreeInstance = $('#tree').find('> [data-tree]').jstree(options)

    jsTreeInstance.on('move_node.jstree', function (event, data) {
      jsBackend.pages.tree.onMove(event, data, jsTreeInstance)
    })

    // Search through pages
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

    // set the item selected
    if (typeof selectedId !== 'undefined') $('#' + selectedId).addClass('selected')

    jsBackend.pages.tree.toggleJsTreeCollapse(jsTreeInstance)
  },

  // when an item is moved
  onMove: function (event, data, jsTreeInstance) {
    var tree = data.new_instance.element.first().data('tree')

    // get pageID that has to be moved
    var currentPageID = data.node.id.replace('page-', '')

    // set the default type of moving
    var type = 'before'

    // init allowMove
    var allowMove = false

    // get the position where the page is dropped
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

    // get pageID wheron the page has been dropped
    var droppedOnPageID = droppedOnElement.prop('id').replace('page-', '')

    // before an item will be moved we have to do some checks
    $.ajax({
      async: false, // important that this isn't asynchronous
      data: {
        fork: {action: 'GetInfo'},
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
        }
        else {
          // is page allowed to move
          if (json.data.move_allowed) {
            allowMove = true
          }
          else {
            jsTreeInstance.jstree('refresh')
            jsBackend.messages.add('danger', jsBackend.locale.lbl('PageNotAllowedToMove'))
            allowMove = false
          }

          // is parent allowed to have children
          if (json.data.children_allowed) {
            allowMove = true
          }
          else {
            jsTreeInstance.jstree('refresh')
            jsBackend.messages.add('danger', jsBackend.locale.lbl('PageNotAllowedToHaveChildren'))
            allowMove = false
          }
        }
      }
    })

    if (!allowMove) {
      return
    }

    // move the page
    $.ajax({
      data: {
        fork: {action: 'Move'},
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

          return
        }

        // show message
        jsBackend.messages.add('success', jsBackend.locale.msg('PageIsMoved').replace('%1$s', json.data.title))
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

$(jsBackend.pages.init)
