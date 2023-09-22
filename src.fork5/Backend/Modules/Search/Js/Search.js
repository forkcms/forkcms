import { MultiTextBox } from '../../../Core/Js/Components/MultiTextBox'
import { StringUtil } from '../../../Core/Js/Components/StringUtil'

export class Search {
  constructor () {
    const $synonymBox = $('input.synonymBox')

    // synonyms box
    if ($synonymBox.length > 0) {
      const options = {
        emptyMessage: window.backend.locale.msg('NoSynonymsBox'),
        errorMessage: StringUtil.ucfirst(window.backend.locale.err('AddTextBeforeSubmitting')),
        addLabel: StringUtil.ucfirst(window.backend.locale.lbl('Add')),
        removeLabel: StringUtil.ucfirst(window.backend.locale.lbl('DeleteSynonym'))
      }
      MultiTextBox.multipleTextbox(options, $synonymBox)
    }

    // settings enable/disable
    $('#searchModules').find('input[type=checkbox]').on('change', (event) => {
      const $this = $(event.currentTarget)
      const $weightElement = $('#' + $this.attr('id') + 'Weight')

      if ($this.is(':checked')) {
        $weightElement.removeAttr('disabled').removeClass('disabled')

        return
      }

      $weightElement.prop('disabled', true).addClass('disabled')
    })
  }
}
