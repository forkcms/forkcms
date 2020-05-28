/**
 * Interaction for the faq categories
 */
jsBackend.faq = {
  // init, something like a constructor
  init: function () {
    // index stuff
    if ($('[data-sequence-drag-and-drop="data-grid-faq"]').length > 0) {
      // destroy default drag and drop
      //$('.sequenceByDragAndDrop tbody').sortable('destroy')

      // drag and drop
      jsBackend.faq.bindDragAndDropQuestions()
      //jsBackend.faq.checkForEmptyCategories()
    }

    // do meta
    if ($('#title').length > 0) $('#title').doMeta()
  },

  /**
   * Check for empty categories and make it still possible to drop questions
   */
  checkForEmptyCategories: function () {
    // reset initial empty grids
    $('table.emptyGrid').each(function () {
      $(this).find('td').parent().remove()
      $(this).append(
        '<tr class="noQuestions">' +
        '<td colspan="' + $(this).find('th').length + '">' + jsBackend.locale.msg('NoQuestionInCategory') + '</td>' +
        '</tr>'
      )
      $(this).removeClass('emptyGrid')
    })

    // when there are empty categories
    if ($('tr.noQuestions').length > 0) {
      // make dataGrid droppable
      $('table.jsDataGrid').droppable({
        // only accept table rows
        accept: 'table.jsDataGrid tr',
        drop: function (e, ui) {
          // remove the no questions in category message
          $(this).find('tr.noQuestions').remove()
        }
      })

      // cleanup remaining no questions
      $('table.jsDataGrid').each(function () {
        if ($(this).find('tr').length > 2) $(this).find('tr.noQuestions').remove()
      })
    }
  },

  saveNewQuestionSequence: function (questionId, fromCategoryId, toCategoryId, fromCategorySequence, toCategorySequence) {
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
      success: function (data, textStatus) {
        // successfully saved reordering sequence
        if (data.code === 200) {
          // change count in title (if any)
          $('div#dataGrid-' + fromCategoryId + ' .content-title h2').html($('div#dataGrid-' + fromCategoryId + ' .content-title h2').html().replace(/\(([0-9]*)\)$/, '(' + ($('div#dataGrid-' + fromCategoryId + ' table.jsDataGrid tr').length - 1) + ')'))

          // if there are no records -> show message
          if ($('div#dataGrid-' + fromCategoryId + ' table.jsDataGrid tr').length === 1) {
            $('div#dataGrid-' + fromCategoryId + ' table.jsDataGrid').append('<tr class="noQuestions">' +
              '<td colspan="3">' + jsBackend.locale.msg('NoQuestionInCategory') + '</td>' +
              '</tr>'
            )
          }

          // check empty categories
          jsBackend.faq.checkForEmptyCategories()

          // redo odd-even
          var table = $('table.jsDataGrid')
          table.find('tr').removeClass('odd').removeClass('even')
          table.find('tr:even').addClass('even')
          table.find('tr:odd').addClass('odd')

          // change count in title (if any)
          $('div#dataGrid-' + toCategoryId + ' .content-title h2').html($('div#dataGrid-' + toCategoryId + ' .content-title h2').html().replace(/\(([0-9]*)\)$/, '(' + ($('div#dataGrid-' + toCategoryId + ' table.jsDataGrid tr').length - 1) + ')'))

          // show message
          jsBackend.messages.add('success', data.message)
        } else {
          // refresh page
          location.reload()

          // show message
          jsBackend.messages.add('danger', 'alter sequence failed.')
        }

        // alert the user
        if (data.code !== 200 && jsBackend.debug) { window.alert(data.message) }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        // refresh page
        location.reload()

        // show message
        jsBackend.messages.add('danger', 'alter sequence failed.')

        // alert the user
        if (jsBackend.debug) { window.alert(textStatus) }
      }
    })
  },

  /**
   * Bind drag and dropping of a category
   */
  bindDragAndDropQuestions: function () {
    // go over every dataGrid
    $.each($('[data-sequence-drag-and-drop="data-grid-faq"] tbody'), function (index, element) {

      // make them sortable
      new Sortable(element, {
        group: 'faqIndex', // this is what makes dragging between categories possible
        onEnd: function (event) {
          jsBackend.faq.saveNewQuestionSequence(
            $(event.item).attr('id'),
            $(event.from).parents('[data-questions-holder]').attr('id').substring(9),
            $(event.to).parents('[data-questions-holder]').attr('id').substring(9),
            jsBackend.faq.getSequence($(event.from)),
            jsBackend.faq.getSequence($(event.to))
          )
        }
      })

      // move with arrows
      $(element).find('[data-role="order-move"]').off('click.fork.order-move').on('click.fork.order-move', function () {
        // vars we will need
        var $this = $(this)
        var $row = $this.closest('tr')
        var direction = $this.data('direction')
        var questionId = $row.attr('id')
        var fromCategoryId = $this.parents('[data-questions-holder]').attr('id').substring(9)
        var toCategoryId = fromCategoryId
        var fromCategorySequence = jsBackend.faq.getSequence($(element))

        if (direction === 'up') {
          $row.prev().insertAfter($row)
        } else if (direction === 'down') {
          $row.next().insertBefore($row)
        }

        // set to category sequence after it's moved
        var toCategorySequence = jsBackend.faq.getSequence($(element))

        jsBackend.faq.saveNewQuestionSequence(
          questionId,
          fromCategoryId,
          toCategoryId,
          fromCategorySequence,
          toCategorySequence
        )
      })
    })
  },

  getSequence: function (wrapper) {
    var sequence = []
    var rows = $(wrapper).find('tr')

    $.each(rows, function (index, element) {
      var id = $(element).data('id')
      sequence.push(id)
    })

    return sequence.join(',')
  }
}

$(jsBackend.faq.init)
