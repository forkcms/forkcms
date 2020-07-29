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
      jsBackend.pages.template.init()
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
    jsBackend.pages.extras.newBlock()

    // bind events
    $(document).on('click', '[data-role="page-block-visibility"]', jsBackend.pages.extras.toggleVisibilityBlock)
  },

  newBlock: function () {
    $('[data-role="page-content-tab"]').on('collection-field-added', '[data-addfield="collection"]', function (event, formCollectionItem) {
      var $formCollectionItem = $(formCollectionItem)
      $formCollectionItem.find('[data-role="select-block-type"]').trigger('change')
      $formCollectionItem.find('[data-role="page-block-edit"]').trigger('click')
    })
  },

  saveBlock: function () {
    $('[data-role="page-content-tab"]').on('click', '[data-role="page-block-save"]', function () {
      var $modal = $(this).closest('.modal')
      var separator = ' › '
      var $pageBlockWrapper = $modal.closest('[data-role="page-block-wrapper"]')
      var $pageBlockTitleWrapper = $pageBlockWrapper.find('[data-role="page-block-title-wrapper"]')
      var $pageBlockTitle = $pageBlockWrapper.find('[data-role="page-block-title"]')
      var $pageBlockType = $pageBlockWrapper.find('[data-role="page-block-type"]')
      var $pageBlockPreview = $pageBlockWrapper.find('[data-role="page-block-preview"]')
      var selectedBlockType = $modal.find('[data-role="select-block-type"]').val()

      if (selectedBlockType === 'block' || selectedBlockType === 'widget') {
        var title = jsBackend.pages.extras.extractExtraTitle(
          separator,
          $modal.find('[data-role="page-block-content-type-wrapper"][data-type="' + selectedBlockType + '"] select option:selected')
        )
        $pageBlockTitleWrapper.removeClass('d-none')
        $pageBlockTitle.text(title)
        $pageBlockType.text(selectedBlockType)
      } else {
        $pageBlockTitleWrapper.addClass('d-none')
      }

      if (selectedBlockType === 'rich_text') {
        $pageBlockPreview.removeClass('d-none')

        var text = jsBackend.pages.extras.getBlockPreview($.makeArray($modal.find('.ce-block [contenteditable]')))
        if (text !== '') {
          $pageBlockPreview.html(text)
        } else {
          $pageBlockPreview.html(jsBackend.locale.lbl('NoContentToShow'))
        }
      } else {
        $pageBlockPreview.addClass('d-none')
      }

      $modal.modal('hide')
    })
  },

  getBlockPreview: function ($elements) {
    var previewText = ''
    var prefix = '<p>'
    var affix = '</p>'
    var length = 75
    var addHellip = function(string) {
      if (string.length === 0) {
        return string
      }

      if (string.length > length) {
        --length
        affix = '…' + affix
      }

      return  prefix + string.substring(0, length) + affix
    }

    do {
      previewText += ' ' + $elements.shift().innerText
      previewText = previewText.trim()
    } while (previewText.length < length && $elements.length > 0)

    return addHellip(previewText)
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

      jsBackend.pages.extras.validateOnlyOneModuleIsConnected()
    })
    $('[data-role="page-content-tab"] [data-role="select-block-type"]').trigger('change') // set the initial state
  },

  validateOnlyOneModuleIsConnected: function () {
    var $selectedBlockOption = $('[data-role="select-block-type"] option:selected[value="block"]:first')
    var $allBlockOptions = $('[data-role="select-block-type"] option[value="block"]')

    if ($selectedBlockOption.length === 0) {
      $allBlockOptions.removeAttr('disabled')

      return
    }

    $allBlockOptions.not($selectedBlockOption).attr('disabled', 'disabled')
  },

  toggleVisibilityBlock: function (e) {
    // get checkbox
    var $checkbox = $(e.currentTarget)
    var $blackWrapper = $checkbox.parents('[data-role="page-block-wrapper"]')
    var $label = $blackWrapper.find('label[for="' + $checkbox.attr('id') + '"]')

    if ($blackWrapper.hasClass('block-not-visible')) {
      // make visible
      $blackWrapper.removeClass('block-not-visible')
      $label.find('[data-fa-i2svg]').attr('data-icon', 'eye')
      $label.find('.sr-only').html(jsBackend.locale.lbl('Hide'))
    } else {
      // make invisible
      $blackWrapper.addClass('block-not-visible')
      $label.find('[data-fa-i2svg]').attr('data-icon', 'eye-slash')
      $label.find('.sr-only').html(jsBackend.locale.lbl('Show'))
    }
  }
}

/**
 * Template switcher
 */
jsBackend.pages.template = {
  init: function () {
    $('[data-role="template-switcher"]').on('change', function() {
      window.location.replace(jsBackend.pages.template.getChangeTemplateUrl('template-id', $(this).val()))
    })
  },

  getChangeTemplateUrl: function (key, value, url) {
    if (!url) url = jsBackend.pages.template.getChangeTemplateUrl('report', null, window.location.href);
    var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
      hash

    if (re.test(url)) {
      if (typeof value !== 'undefined' && value !== null) {
        return url.replace(re, '$1' + key + "=" + value + '$2$3')
      }

      hash = url.split('#')
      url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '')
      if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
        url += '#' + hash[1]
      }
      return url
    }
    if (typeof value !== 'undefined' && value !== null) {
      var separator = url.indexOf('?') !== -1 ? '&' : '?'
      hash = url.split('#')
      url = hash[0] + separator + key + '=' + value
      if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
        url += '#' + hash[1]
      }
      return url
    }

    return url
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
        check_callback: jsBackend.pages.tree.checkCallback
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

    if ($('[data-clear-tree]').length > 0) {
      jsTreeInstance.jstree('clear_state')
    }

    jsTreeInstance.on('move_node.jstree', function (event, data) {
      jsBackend.pages.tree.onMove(event, data, jsTreeInstance)
    })
    jsTreeInstance.on('copy_node.jstree', function (event, data) {
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

  checkCallback: function (operation, node, parent) {
    if (operation !== 'move_node') {
      return true
    }

    if (!node.data.allowMove) {
      return false
    }

    if (typeof parent.data === 'undefined' || parent.data === null) {
      return typeof parent.children === 'undefined' || parent.children[0] !== 'page-1'
    }

    return parent.data.allowChildren
  },

  // when an item is moved
  onMove: function (event, data, jsTreeInstance) {
    var tree = data.new_instance.element.first().data('tree')

    var node = event.type === 'copy_node' ? data.original : data.node
    // get pageID that has to be moved
    var currentPageID = node.id.replace('page-', '')

    // set the default type of moving
    var type = 'before'

    // get the position where the page is dropped
    var droppedOnElement = null
    if (data.new_instance._model.data[data.parent].children[data.position + 1] !== undefined) {
      droppedOnElement = $('#' + data.new_instance._model.data[data.parent].children[data.position + 1])
    }

    // when node is dropped on the last position, we can not add it before the last one -> we need after
    if (data.new_instance._model.data[data.parent].children[data.position + 1] === undefined
      && data.new_instance._model.data[data.parent].children[data.position - 1] !== undefined) {
      droppedOnElement = $('#' + data.new_instance._model.data[data.parent].children[data.position - 1])
      type = 'after'
    }

    // when node is dropped on another node to move it inside
    if (data.new_instance._model.data[data.parent].children.length === 1 && data.new_instance._model.data[data.parent].children[0] === node.id) {
      type = 'inside'
      if (data.parent !== undefined && data.parent !== '#') {
        droppedOnElement = $('#' + data.parent)
      }
    }

    // get pageID wheron the page has been dropped
    var droppedOnPageID = 0
    if (droppedOnElement !== null) {
      droppedOnPageID = droppedOnElement.prop('id').replace('page-', '')
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
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        window.location.reload()
      },
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
