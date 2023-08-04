import { InlineTextEdit } from '../../../Core/Js/Components/InlineTextEdit'

export class LocaleModule {
  constructor () {
    this.controls()

    if ($('select#application').length > 0 && $('select#module').length > 0) {
      // bind
      $('select#application').on('change', this.enableDisableModules)

      // call to start
      this.enableDisableModules()
    }
  }

  controls () {
    if ($('.jsDataGrid td.translationValue').length > 0) {
      // bind
      const options = {
        params: {fork: {action: 'SaveTranslation'}},
        tooltip: window.backend.locale.msg('ClickToEdit'),
        afterSave: (item) => {
          if (item.find('span:empty').length === 1) {
            item.addClass('highlighted')
          } else {
            item.removeClass('highlighted')
          }
        }
      }
      InlineTextEdit.inlineTextEdit(options, $('.jsDataGrid td.translationValue'))

      // highlight all empty items
      $('.jsDataGrid td.translationValue span:empty').parents('td.translationValue').addClass('highlighted')
    }

    // when clicking on the export-button which checkboxes are checked, add the id's of the translations to the querystring
    $('.jsButtonExport').click((e) => {
      e.preventDefault()

      const labels = []

      $('.jsDataGrid input[type="checkbox"]:checked').closest('tr').find('.translationValue').each((index, element) => {
        labels.push($(element).attr('data-numeric-id'))
      })

      window.location.href = $(e.currentTarget).attr('href') + '&ids=' + labels.join('|')
    })

    // When clicking on a sort-button (in the header of the table)
    // add the current filter to the url so we don't have to re-search everything,
    // and in the process loose the sorting.
    $('.jsDataGrid th a').click((e) => {
      e.preventDefault()

      let url = $(e.currentTarget).attr('href')

      const application = $('select#application').val()
      if (application !== '') {
        url += '&application=' + escape(application)
      }

      const module = $('select#module').val()
      if (module !== '') {
        url += '&module=' + escape(module)
      }

      const name = $('input#name').val()
      if (name !== '') {
        url += '&name=' + escape(name)
      }

      const value = $('input#value').val()
      if (value !== '') {
        url += '&value=' + escape(value)
      }

      $('input[name="language[]"]:checked').each((index, input) => {
        url += '&language[]=' + escape($(input).val())
      })

      $('input[name="type[]"]:checked').each((index, input) => {
        url += '&type[]=' + escape($(input).val())
      })

      window.location.href = url
    })
  }

  enableDisableModules () {
    // frontend can't have specific module
    if ($('select#application').val() === 'Frontend') {
      // set all modules disabled
      $('select#module option').prop('disabled', true)

      // enable core
      $('select#module option[value=Core]').prop('disabled', false).prop('selected', true)
    } else {
      // remove the disabled stuff
      $('select#module option').prop('disabled', false)
    }
  }
}
