import 'jstree/dist/jstree'
import { EventUtil } from '../../../../Core/Js/Components/EventUtil'
import { Messages } from '../../../../Core/Js/Components/Messages'
import { Config } from '../../../../Core/Js/Components/Config'

export class Tree {
  constructor () {
    if ($('[data-tree-media-libreary]').find('> [data-tree]').length === 0) return false

    // jsTree options
    const options = {
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
    const jsTreeInstance = $('[data-tree-media-libreary]').find('> [data-tree]').jstree(options)

    if ($('[data-clear-tree]').length > 0) {
      jsTreeInstance.jstree('clear_state')
    }

    jsTreeInstance.on('move_node.jstree', (event, data) => {
      this.onMove(event, data, jsTreeInstance)
    })

    // Search through folders
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

  onMove (event, data, jsTreeInstance) {
    const tree = data.new_instance.element.first().data('tree')

    // get folderID that has to be moved
    const currentPageID = data.node.id.replace('folder-', '')

    // set the default type of moving
    let type = 'before'

    // init allowMove
    let allowMove = false

    // get the position where the folder is dropped
    let droppedOnElement = $('#' + data.new_instance._model.data[data.parent].children[data.position + 1])

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
    const droppedOnPageID = droppedOnElement.prop('id').replace('folder-', '')

    // before an item will be moved we have to do some checks
    $.ajax({
      async: false, // important that this isn't asynchronous
      data: {
        fork: {action: 'MediaFolderInfo'},
        id: currentPageID,
        dropped_on: droppedOnPageID
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        if (Config.isDebug()) window.alert(textStatus)
        allowMove = false
      },
      success: function (json, textStatus) {
        if (json.code !== 200) {
          if (Config.isDebug()) window.alert(textStatus)
          allowMove = false
        } else {
          // is page allowed to move
          if (json.data.allow_move) {
            allowMove = true
          } else {
            jsTreeInstance.jstree('refresh')
            Messages.add('danger', window.backend.locale.err('CantBeMoved'))
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
      success: (json, textStatus) => {
        if (json.code !== 200) {
          if (Config.isDebug()) window.alert(textStatus)

          // show message
          Messages.add('danger', window.backend.locale.err('CantBeMoved'))

          // rollback
          jsTreeInstance.jstree('refresh')
        } else {
          // show message
          Messages.add('success', json.message)
        }
      }
    })
  }

  toggleJsTreeCollapse (jsTreeInstance) {
    $('[data-role="toggle-js-tree-collapse"]').on('click', (event) => {
      const $this = $(event.currentTarget)
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
