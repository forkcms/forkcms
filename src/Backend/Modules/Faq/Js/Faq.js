import { Messages } from '../../../Core/Js/Components/Messages'
import { Meta } from '../../../Core/Js/Components/Meta'

export class Faq {
  constructor () {
    // index stuff
    if ($('.jsDataGridQuestionsHolder').length > 0) {
      // destroy default drag and drop
      $('.sequenceByDragAndDrop tbody').sortable('destroy')

      // drag and drop
      this.bindDragAndDropQuestions()
      this.checkForEmptyCategories()
    }

    // do meta
    if ($('#title').length > 0) Meta.doMeta(('#title'))

    // hide the data
    $('.longFeedback').hide()

    $('[data-role=delete-feedback]').on('click', this.deleteFeedbackClick)
  }

  /**
   * Check for empty categories and make it still possible to drop questions
   */
  checkForEmptyCategories () {
    // reset initial empty grids
    $('table.emptyGrid').each((index, table) => {
      $(table).find('td').parent().remove()
      $(table).append(
        '<tr class="noQuestions">' +
        '<td colspan="' + $(this).find('th').length + '">' + window.backend.locale.msg('NoQuestionInCategory') + '</td>' +
        '</tr>'
      )
      $(table).removeClass('emptyGrid')
    })

    // when there are empty categories
    if ($('tr.noQuestions').length > 0) {
      // make dataGrid droppable
      $('table.jsDataGrid').droppable({
        // only accept table rows
        accept: 'table.jsDataGrid tr',
        drop (e, ui) {
          // remove the no questions in category message
          $(this).find('tr.noQuestions').remove()
        }
      })

      // cleanup remaining no questions
      $('table.jsDataGrid').each((index, table) => {
        if ($(table).find('tr').length > 2) $(table).find('tr.noQuestions').remove()
      })
    }
  }

  saveNewQuestionSequence ($wrapper, questionId, toCategoryId) {
    // vars we will need
    const fromCategoryId = $wrapper.attr('id').substring(9)
    const fromCategorySequence = $wrapper.sortable('toArray').join(',')
    const toCategorySequence = $('#dataGrid-' + toCategoryId).sortable('toArray').join(',')

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
          // change count in title (if any)
          $('div#dataGrid-' + fromCategoryId + ' .content-title h2').html($('div#dataGrid-' + fromCategoryId + ' .content-title h2').html().replace(/\(([0-9]*)\)$/, '(' + ($('div#dataGrid-' + fromCategoryId + ' table.jsDataGrid tr').length - 1) + ')'))

          // if there are no records -> show message
          if ($('div#dataGrid-' + fromCategoryId + ' table.jsDataGrid tr').length === 1) {
            $('div#dataGrid-' + fromCategoryId + ' table.jsDataGrid').append('<tr class="noQuestions">' +
              '<td colspan="3">' + window.backend.locale.msg('NoQuestionInCategory') + '</td>' +
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

          // change count in title (if any)
          $('div#dataGrid-' + toCategoryId + ' .content-title h2').html($('div#dataGrid-' + toCategoryId + ' .content-title h2').html().replace(/\(([0-9]*)\)$/, '(' + ($('div#dataGrid-' + toCategoryId + ' table.jsDataGrid tr').length - 1) + ')'))

          // show message
          Messages.add('success', data.message)
        } else {
          // not a success so revert the changes
          $(this).sortable('cancel')

          // show message
          Messages.add('danger', 'alter sequence failed.')
        }

        // alert the user
        if (data.code !== 200 && jsBackend.debug) { window.alert(data.message) }
      },
      error (XMLHttpRequest, textStatus, errorThrown) {
        // revert
        $(this).sortable('cancel')

        // show message
        Messages.add('danger', 'alter sequence failed.')

        // alert the user
        if (jsBackend.debug) { window.alert(textStatus) }
      }
    })
  }

  /**
   * Bind drag and dropping of a category
   */
  bindDragAndDropQuestions () {
    // go over every dataGrid
    $.each($('div.jsDataGridQuestionsHolder'), (index, element) => {
      const $this = $(element)
      // make them sortable
      $this.sortable({
        items: 'table.jsDataGrid tbody tr',        // set the elements that user can sort
        handle: 'td.dragAndDropHandle',            // set the element that user can grab
        tolerance: 'pointer',                    // give a more natural feeling
        connectWith: 'div.jsDataGridQuestionsHolder',        // this is what makes dragging between categories possible
        stop: (e, ui) => {
          this.saveNewQuestionSequence(
            $(element),
            ui.item.attr('id'),
            ui.item.parents('.jsDataGridQuestionsHolder').attr('id').substring(9)
          )
        }
      })
      $this.find('[data-role="order-move"]').off('click.fork.order-move').on('click.fork.order-move', (e) => {
        const $this = $(e.currentTarget)
        const $row = $this.closest('tr')
        const direction = $this.data('direction')
        const $holder = $row.closest('.jsDataGridQuestionsHolder')

        if (direction === 'up') {
          $row.prev().insertAfter($row)
        } else if (direction === 'down') {
          $row.next().insertBefore($row)
        }

        this.saveNewQuestionSequence($holder, $row.attr('id'), $holder.attr('id').substring(9))
      })
    })
  }

  deleteFeedbackClick (event) {
    event.preventDefault()

    const $modal = $('#confirmDeleteFeedback')
    $modal.siblings('#delete_id').val($(event.currentTarget).data('id'))
    $modal.modal('show')
  }
}
