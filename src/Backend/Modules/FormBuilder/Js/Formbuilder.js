import { Fields } from './Components/Fields'
import { StringUtil } from '../../../Core/Js/Components/StringUtil'
import { MultiTextBox } from '../../../Core/Js/Components/MultiTextBox'

export class Formbuilder {
  constructor () {
    // variables
    const $selectMethod = $('select#method')
    const $formId = $('#formId')

    // fields handler
    this.fields = new Fields()

    // get form id
    this.formId = $formId.val()

    // hide or show the email based on the method
    if ($selectMethod.length > 0) {
      this.handleMethodField()
      $(document).on('change', 'select#method', this.handleMethodField)
    }

    const options = {
      emptyMessage: window.backend.locale.msg('NoEmailaddresses'),
      addLabel: StringUtil.ucfirst(window.backend.locale.lbl('Add', 'Core')),
      removeLabel: StringUtil.ucfirst(window.backend.locale.lbl('Delete')),
      errorMessage: StringUtil.ucfirst(window.backend.locale.err('AddTextBeforeSubmitting')),
      canAddNew: true
    }

    MultiTextBox.multipleTextbox(options, $('[data-role="formbuilder-recipient"]'))

    this.handleSuccessType()

    $('input[name="success_type"]').change(() => {
      this.handleSuccessType()
    })
  }

  handleSuccessType () {
    if ($('input[name="success_type"]:checked').val() === 'page') {
      $('[data-role="success-page"]').removeClass('d-none')
      $('[data-role="success-message"]').addClass('d-none')
    } else {
      $('[data-role="success-page"]').addClass('d-none')
      $('[data-role="success-message"]').removeClass('d-none')
    }
  }

  /**
   * Toggle email field based on the method value
   */
  handleMethodField () {
    // variables
    const $selectMethod = $('select#method')
    const $emailWrapper = $('#emailWrapper')

    if ($selectMethod.val() === 'database_email' || $selectMethod.val() === 'email') {
      // show email field
      $emailWrapper.slideDown()

      return
    }

    // hide email field
    $emailWrapper.slideUp()
  }
}
