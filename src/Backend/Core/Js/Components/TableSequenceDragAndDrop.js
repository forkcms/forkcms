import { Config } from './Config'
import { Messages } from './Messages'
import Sortable from 'sortablejs'

export class TableSequenceDragAndDrop {
  constructor () {
    // constiables
    const $sequenceInstances = $('[data-sequence-drag-and-drop="data-grid"] tbody')

    if ($sequenceInstances.length === 0) {
      return
    }

    $.each($sequenceInstances, (index, element) => {
      /* eslint-disable no-new */
      new Sortable(element,
        {
          handle: '[data-role="drag-and-drop-handle"]',
          onEnd: (event) => {
            const $draggedItem = $(event.item)
            this.saveNewSequence($draggedItem.closest('table.jsDataGrid'))
          }
        }
      )

      $(element).find('[data-role="order-move"]').on('click.fork.order-move', (e) => {
        const $this = $(this)
        const $row = $this.closest('tr')
        const direction = $this.data('direction')

        e.preventDefault()

        if (direction === 'up') {
          $row.prev().insertAfter($row)
        } else if (direction === 'down') {
          $row.next().insertBefore($row)
        }

        this.saveNewSequence($row.closest('table'))
      })
    })
  }

  saveNewSequence ($table) {
    const action = (typeof $table.data('action') === 'undefined') ? 'Sequence' : $table.data('action').toString()
    const module = (typeof $table.data('module') === 'undefined') ? Config.getCurrentModule() : $table.data('module').toString()
    let extraParams = {}
    const $rows = $table.find('tr[id*=row-]')
    const newIdSequence = []

    // fetch extra params
    if (typeof $table.data('extra-params') !== 'undefined') {
      extraParams = $table.data('extra-params')

      // we convert the unvalid {'key':'value'} to the valid {"key":"value"}
      extraParams = extraParams.replace(/'/g, '"')

      // we parse it as an object
      extraParams = $.parseJSON(extraParams)
    }

    $rows.each((index, element) => {
      newIdSequence.push($(element).data('id'))
    })

    $.ajax({
      data: $.extend({
        fork: {module: module, action: action},
        new_id_sequence: newIdSequence.join(',')
      }, extraParams),
      success: (data) => {
        // not a success so revert the changes
        if (data.code !== 200) {
          // refresh page
          window.location.reload()

          Messages.add('danger', window.backend.locale.err('AlterSequenceFailed'))
        }

        // redo odd-even
        $table.find('tr').removeClass('odd').removeClass('even')
        $table.find('tr:even').addClass('odd')
        $table.find('tr:odd').addClass('even')

        if (data.code !== 200 && Config.isDebug()) {
          window.alert(data.message)
        }

        Messages.add('success', window.backend.locale.msg('ChangedOrderSuccessfully'))
      },
      error: (XMLHttpRequest) => {
        let textStatus = window.backend.locale.err('AlterSequenceFailed')

        // get real message
        if (typeof XMLHttpRequest.responseText !== 'undefined') {
          textStatus = $.parseJSON(XMLHttpRequest.responseText).message
        }

        Messages.add('danger', textStatus)

        // refresh page
        window.location.reload()

        if (Config.isDebug()) {
          window.alert(textStatus)
        }
      }
    })
  }
}
