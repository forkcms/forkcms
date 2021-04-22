import { EventUtil } from '../../../../Core/Js/Components/EventUtil'
import { Config } from '../../../../Core/Js/Components/Config'
import { Messages } from '../../../../Core/Js/Components/Messages'

export class Tree {
  constructor () {
    if ($('[data-tree-pages]').find('> [data-tree]').length === 0) return

    // jsTree options
    const options = {
      core: {
        animation: 0,
        themes: {
          name: 'proton',
          responsive: true
        },
        multiple: false,
        check_callback: this.checkCallback
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
    const jsTreeInstance = $('[data-tree-pages]').find('> [data-tree]').jstree(options)

    if ($('[data-clear-tree]').length > 0) {
      jsTreeInstance.jstree('clear_state')
    }

    jsTreeInstance.on('move_node.jstree', (event, data) => {
      this.onMove(event, data, jsTreeInstance)
    })
    jsTreeInstance.on('copy_node.jstree', (event, data) => {
      this.onMove(event, data, jsTreeInstance)
    })

    // Search through pages
    const searchThroughPages = () => {
      const v = $('.js-tree-search').val()
      $('#tree').find('> [data-tree]').each((index, element) => {
        $(element).jstree(true).search(v)
      })
    }
    $('.js-tree-search').bind('keyup input', EventUtil.debounce(searchThroughPages, 150))

    // To prevent FOUC, we only show the jsTree when it's done loading.
    jsTreeInstance.on('ready.jstree', (e, data) => {
      $('#tree').show()
    })

    // On selecting a node in the tree, visit the anchor.
    jsTreeInstance.on('select_node.jstree', (e, data, jsTreeInstance) => {
      if (data && data.node && data.event) {
        // Get current and new URL
        const node = data.node
        const currentPageURL = window.location.pathname + window.location.search
        const newPageURL = node.a_attr.href

        // Only redirect if destination isn't the current one.
        if (typeof newPageURL !== 'undefined' && newPageURL !== currentPageURL) {
          window.location = newPageURL
        }
      }
    })

    this.toggleJsTreeCollapse(jsTreeInstance)
  }

  checkCallback (operation, node, parent) {
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
  }

  // when an item is moved
  onMove (event, data, jsTreeInstance) {
    const tree = data.new_instance.element.first().data('tree')

    const node = event.type === 'copy_node' ? data.original : data.node
    // get pageID that has to be moved
    const currentPageID = node.id.replace('page-', '')

    // set the default type of moving
    let type = 'before'

    // get the position where the page is dropped
    let droppedOnElement = null
    if (data.new_instance._model.data[data.parent].children[data.position + 1] !== undefined) {
      droppedOnElement = $('#' + data.new_instance._model.data[data.parent].children[data.position + 1])
    }

    // when node is dropped on the last position, we can not add it before the last one -> we need after
    if (data.new_instance._model.data[data.parent].children[data.position + 1] === undefined &&
      data.new_instance._model.data[data.parent].children[data.position - 1] !== undefined) {
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
    let droppedOnPageID = 0
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
      success: (json, textStatus) => {
        if (json.code !== 200) {
          if (Config.isDebug()) window.alert(textStatus)

          // show message
          Messages.add('danger', window.backend.locale.err('CantBeMoved'))

          // rollback
          jsTreeInstance.jstree('refresh')

          return
        }

        // show message
        Messages.add('success', window.backend.locale.msg('PageIsMoved').replace('%1$s', json.data.title))
      },
      error: (XMLHttpRequest, textStatus, errorThrown) => {
        window.location.reload()
      }
    })
  }

  toggleJsTreeCollapse (jsTreeInstance) {
    $('[data-role="toggle-js-tree-collapse"]').on('click', (e) => {
      const $this = $(e.currentTarget)
      $this.toggleClass('tree-collapsed')
      const collapsed = $this.hasClass('tree-collapsed')
      const $buttonText = $('[data-role="toggle-js-tree-collapse-text"]')

      if (collapsed) {
        $buttonText.html(window.backend.locale.lbl('OpenTreeNavigation'))
        jsTreeInstance.jstree('close_all')

        return
      }

      $buttonText.html(window.backend.locale.lbl('CloseTreeNavigation'))
      jsTreeInstance.jstree('open_all')
    })
  }
}
