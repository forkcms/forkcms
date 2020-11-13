import { Messages } from '../../../Core/Js/Components/Messages'
import { Meta } from '../../../Core/Js/Components/Meta'
import Sortable from 'sortablejs'
import { Config } from '../../../Core/Js/Components/Config'

export class Faq {
  constructor () {
    // index stuff
    if ($('[data-sequence-drag-and-drop="data-grid-faq"]').length > 0) {
      // drag and drop
      this.bindDragAndDropQuestions()
      this.checkForEmptyCategories()
    }

    // do meta
    if ($('#title').length > 0) Meta.doMeta(('#title'))
  }

  /**
   * Check for empty categories and make it still possible to drop questions
   */
  checkForEmptyCategories () {
    // reset initial empty grids
    $('table.emptyGrid').each((index, grid) => {
      $(grid).find('td').parent().remove()
      $(grid).append(
        '<tr class="noQuestions">' +
        '<td colspan="' + $(grid).find('th').length + '">' + window.backend.locale.msg('NoQuestionInCategory') + '</td>' +
        '</tr>'
      )
      $(grid).removeClass('emptyGrid')
    })

    // when there are empty categories
    if ($('tr.noQuestions').length > 0) {
      // cleanup remaining no questions
      $('table.jsDataGrid').each((index, grid) => {
        if ($(grid).find('tr').length > 2) $(grid).find('tr.noQuestions').remove()
      })
    }
  }

  saveNewQuestionSequence (questionId, fromCategoryId, toCategoryId, fromCategorySequence, toCategorySequence) {
    // make ajax call
    $.ajax({
      data: {
        fork: {action: 'SequenceQuestions'},
        questionId: questionId,
        fromCategoryId: fromCategoryId,
        toCategoryId: toCategoryId,
        fromCategorySequence: fromCategorySequence,
        toCategorySequence: toCategorySequence
      },
      success: (data, textStatus) => {
        // successfully saved reordering sequence
        if (data.code === 200) {
          const $fromWrapper = $('div#dataGrid-' + fromCategoryId)
          const $fromWrapperTitle = $fromWrapper.find('.content-title h2')

          const $toWrapper = $('div#dataGrid-' + toCategoryId)
          const $toWrapperTitle = $toWrapper.find('.content-title h2')

          // change count in title of from wrapper (if any)
          $fromWrapperTitle.html($fromWrapperTitle.html().replace(/\(([0-9]*)\)$/, '(' + ($fromWrapper.find('table.jsDataGrid tr').length - 1) + ')'))

          // if there are no records -> show message
          if ($fromWrapper.find('table.jsDataGrid tr').length === 1) {
            $fromWrapper.find('table.jsDataGrid').append('' +
              '<tr class="noQuestions">' +
              '<td colspan="' + $fromWrapper.find('th').length + '">' + window.backend.locale.msg('NoQuestionInCategory') + '</td>' +
              '</tr>'
            )
          }

          // check empty categories
          this.checkForEmptyCategories()

          // redo odd-even
          const table = $('table.jsDataGrid')
          table.find('tr').removeClass('odd').removeClass('even')
          table.find('tr:even').addClass('even')
          table.find('tr:odd').addClass('odd')

          // change count in title of to wrapper (if any)
          $toWrapperTitle.html($toWrapperTitle.html().replace(/\(([0-9]*)\)$/, '(' + ($toWrapper.find('table.jsDataGrid tr').length - 1) + ')'))

          // show message
          Messages.add('success', data.message)
        } else {
          // refresh page
          window.location.reload()

          // show message
          Messages.add('danger', 'alter sequence failed.')
        }

        // alert the user
        if (data.code !== 200 && Config.isDebug()) { window.alert(data.message) }
      },
      error (XMLHttpRequest, textStatus, errorThrown) {
        // refresh page
        window.location.reload()

        // show message
        Messages.add('danger', 'alter sequence failed.')

        // alert the user
        if (Config.isDebug()) { window.alert(textStatus) }
      }
    })
  }

  /**
   * Bind drag and dropping of a category
   */
  bindDragAndDropQuestions () {
    // go over every dataGrid
    $.each($('[data-sequence-drag-and-drop="data-grid-faq"] tbody'), (index, element) => {
      /* eslint-disable no-new */
      // make them sortable
      new Sortable(element, {
        group: 'faqIndex', // this is what makes dragging between categories possible
        onEnd: (event) => {
          window.backend.faq.saveNewQuestionSequence(
            $(event.item).attr('id'),
            $(event.from).parents('[data-questions-holder]').attr('id').substring(9),
            $(event.to).parents('[data-questions-holder]').attr('id').substring(9),
            window.backend.faq.getSequence($(event.from)),
            window.backend.faq.getSequence($(event.to))
          )
        }
      })

      $(element).find('[data-role="order-move"]').off('click.fork.order-move').on('click.fork.order-move', (e) => {
        this.bindArrowMoveQuestions(e, element)
      })
    })
  }

  bindArrowMoveQuestions (e, element) {
    const $current = $(e.currentTarget)
    const $row = $current.closest('tr')
    const direction = $current.data('direction')
    const questionId = $row.attr('id')
    const fromCategoryId = $current.parents('[data-questions-holder]').attr('id').substring(9)
    const toCategoryId = fromCategoryId
    const fromCategorySequence = this.getSequence($(element))

    if (direction === 'up') {
      $row.prev().insertAfter($row)
    } else if (direction === 'down') {
      $row.next().insertBefore($row)
    }

    // set to category sequence after it's moved
    const toCategorySequence = this.getSequence($(element))

    this.saveNewQuestionSequence(
      questionId,
      fromCategoryId,
      toCategoryId,
      fromCategorySequence,
      toCategorySequence
    )
  }

  getSequence (wrapper) {
    const sequence = []
    const rows = $(wrapper).find('tr')

    $.each(rows, (index, element) => {
      const id = $(element).data('id')
      sequence.push(id)
    })

    return sequence.join(',')
  }

  deleteFeedbackClick (event) {
    event.preventDefault()

    const $modal = $('#confirmDeleteFeedback')
    $modal.siblings('#delete_id').val($(event.currentTarget).data('id'))
    $modal.modal('show')
  }
}
