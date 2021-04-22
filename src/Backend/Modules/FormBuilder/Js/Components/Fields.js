import { Messages } from '../../../../Core/Js/Components/Messages'
import { StringUtil } from '../../../../Core/Js/Components/StringUtil'
import { Config } from '../../../../Core/Js/Components/Config'
import { MultiTextBox } from '../../../../Core/Js/Components/MultiTextBox'
import Sortable from 'sortablejs'

export class Fields {
  constructor () {
    this.formId = $('#formId').val()
    this.lockEditRequest = false

    // set urls
    this.paramsDelete = {fork: {action: 'DeleteField'}}
    this.paramsGet = {fork: {action: 'GetField'}}
    this.paramsSave = {fork: {action: 'SaveField'}}
    this.paramsSequence = {fork: {action: 'Sequence'}}

    // init errors
    this.defaultErrorMessages = {}
    if (typeof window.defaultErrorMessages !== 'undefined') {
      this.defaultErrorMessages = window.defaultErrorMessages
    }

    // submit detection handler for the main form and modal field form
    this.bindFromSubmit()

    // bind
    this.bindDialogs()
    this.bindValidation()
    this.bindEdit()
    this.bindDelete()
    this.bindDragAndDrop()
  }

  /**
   * Bind the form submit action
   */
  bindFromSubmit () {
    $('#edit').submit(() => {
      // check if a modal window is already open
      $('.jsFieldDialog').each((index, fieldDialog) => {
        // if a modal window is open we prevent the event from propagating
        if ($(fieldDialog).css('display') !== 'none') {
          $(fieldDialog).find('.jsFieldDialogSubmit').trigger('click')

          return false
        }
      })
    })
  }

  /**
   * Bind delete actions
   */
  bindDelete () {
    // get all delete buttons
    $(document).on('click', '.jsFieldDelete', (e) => {
      // prevent default
      e.preventDefault()

      // get id
      const id = $(e.currentTarget).attr('data-field-id')

      // only when set
      if (id !== '') {
        // make the call
        $.ajax({
          data: $.extend({}, this.paramsDelete,
            {
              form_id: this.formId,
              field_id: id
            }),
          success: (data, textStatus) => {
            // success
            if (data.code === 200) {
              // delete from list
              $('#fieldHolder-' + id).fadeOut(200, () => {
                // remove item
                $('#fieldHolder-' + id).remove()

                // no items message
                this.toggleNoItems()
              })
            } else {
              // show error message
              Messages.add('danger', textStatus)
            }

            // alert the user
            if (data.code !== 200 && Config.isDebug()) {
              window.alert(data.message)
            }
          }
        })
      }
    })
  }

  /**
   * Bind the dialogs and bind click event to add links
   */
  bindDialogs () {
    // initialize
    $('.jsFieldDialog').each((index, fieldDialog) => {
      // get id
      const id = $(fieldDialog).attr('id')

      // only when set
      if (id !== '') {
        const $dialog = $('#' + id)

        $dialog.find('.jsFieldDialogSubmit').on('click', (e) => {
          e.preventDefault()

          // save/validate by type
          // @todo must be refactored
          switch (id) {
            case 'textboxDialog':
              this.saveTextbox()
              break
            case 'textareaDialog':
              this.saveTextarea()
              break
            case 'datetimeDialog':
              this.saveDatetime()
              break
            case 'headingDialog':
              this.saveHeading()
              break
            case 'paragraphDialog':
              this.saveParagraph()
              break
            case 'submitDialog':
              this.saveSubmit()
              break
            case 'dropdownDialog':
              this.saveDropdown()
              break
            case 'radiobuttonDialog':
              this.saveRadiobutton()
              break
            case 'mailmotorDialog':
              this.saveMailmotorbutton()
              break
            case 'checkboxDialog':
              this.saveCheckbox()
              break
          }
        })

        $dialog.on('shown.bs.modal', (e) => {
          // bind special boxes
          if (id === 'dropdownDialog') {
            const options = {
              splitChar: '|',
              emptyMessage: window.backend.locale.msg('NoValues'),
              errorMessage: StringUtil.ucfirst(window.backend.locale.err('AddTextBeforeSubmitting')),
              addLabel: StringUtil.ucfirst(window.backend.locale.lbl('Add')),
              removeLabel: StringUtil.ucfirst(window.backend.locale.lbl('Delete')),
              showIconOnly: false,
              afterBuild: this.multipleTextboxCallback
            }
            MultiTextBox.multipleTextbox(options, $('input#dropdownValues'))
          } else if (id === 'radiobuttonDialog') {
            const options = {
              splitChar: '|',
              emptyMessage: window.backend.locale.msg('NoValues'),
              addLabel: StringUtil.ucfirst(window.backend.locale.lbl('Add')),
              removeLabel: StringUtil.ucfirst(window.backend.locale.lbl('Delete')),
              errorMessage: StringUtil.ucfirst(window.backend.locale.err('AddTextBeforeSubmitting')),
              showIconOnly: false,
              afterBuild: this.multipleTextboxCallback
            }
            MultiTextBox.multipleTextbox(options, $('input#radiobuttonValues'))
          } else if (id === 'checkboxDialog') {
            const options = {
              splitChar: '|',
              emptyMessage: window.backend.locale.msg('NoValues'),
              addLabel: StringUtil.ucfirst(window.backend.locale.lbl('Add')),
              removeLabel: StringUtil.ucfirst(window.backend.locale.lbl('Delete')),
              errorMessage: StringUtil.ucfirst(window.backend.locale.err('AddTextBeforeSubmitting')),
              showIconOnly: false,
              afterBuild: this.multipleTextboxCallback
            }
            MultiTextBox.multipleTextbox(options, $('input#checkboxValues'))
          } else if (id === 'datetimeDialog') {
            $('#datetimeType').change((event) => {
              if ($(event.currentTarget).val() === 'time') {
                $('#datetimeDialog').find('.jsDefaultValue').hide()
                $('#datetimeValidation').val('time')
                $('.jsValidationErrorMessage').show()
                $('#datetimeErrorMessage').val(window.backend.locale.err('TimeIsInvalid'))
              } else {
                $('#datetimeDialog').find('.jsDefaultValue').show()
                $('.jsValidationErrorMessage').hide()
                $('#datetimeErrorMessage').val('')
                $('#datetimeValidation').val('')
              }
            }).trigger('change')

            $('#datetimeValueType').change((event) => {
              if ($(event.currentTarget).val() === 'today') {
                $('#datetimeValueAmount').prop('disabled', true).val('')
              } else {
                $('#datetimeValueAmount').prop('disabled', false)
              }
            }).trigger('change')
          }

          // focus on first input element
          if ($(e.currentTarget).find(':input:visible').length > 0) {
            $(e.currentTarget).find(':input:visible')[0].focus()
          }

          // toggle error messages
          this.toggleValidationErrors(id)
        })

        $dialog.on('hide.bs.modal', (e) => {
          // no items message
          this.toggleNoItems()

          // reset
          this.resetDialog(id)

          // toggle error messages
          this.toggleValidationErrors(id)
        })
      }
    })

    // bind clicks
    $('.jsFieldDialogTrigger').on('click', (e) => {
      // prevent default
      e.preventDefault()

      // get id
      const id = $(e.currentTarget).attr('data-field-id')

      // bind
      if (id !== '') {
        $('#' + id).modal('show')
      }
    })

    $('.jsRecaptchaTrigger').on('click', (e) => {
      e.preventDefault()

      this.saveRecaptcha()
    })
  }

  /**
   * Drag and drop fields
   */
  bindDragAndDrop () {
    if ($('[data-sequence-drag-and-drop="fields-formbuilder"]').length === 0) {
      return
    }

    const element = document.querySelector('[data-sequence-drag-and-drop="fields-formbuilder"]')

    /* eslint-disable no-new */
    // bind sortable
    new Sortable(element, {
      handle: '[data-role="drag-and-drop-handle"]',
      onEnd: () => {
        // init var
        const newIdSequence = this.getSequence($(element))

        // make ajax call
        $.ajax({
          data: $.extend({}, this.paramsSequence, {
            form_id: window.backend.formbuilder.formId,
            new_id_sequence: newIdSequence.join('|')
          }),
          success: (data, textStatus) => {
            // not a success so revert the changes
            if (data.code !== 200) {
              // refresh page
              window.location.reload()

              // show message
              Messages.add('danger', 'alter sequence failed.')
            }

            // alert the user
            if (data.code !== 200 && Config.isDebug()) {
              window.alert(data.message)
            }
          },
          error (XMLHttpRequest, textStatus, errorThrown) {
            // refresh page
            window.location.reload()

            // show message
            Messages.add('danger', 'alter sequence failed.')

            // alert the user
            if (Config.isDebug()) {
              window.alert(textStatus)
            }
          }
        })
      }
    })
  }

  getSequence (wrapper) {
    const sequence = []
    const rows = $(wrapper).find('[data-field-wrapper]')

    $.each(rows, function (index, element) {
      const id = $(element).attr('id').split('-')[1]
      sequence.push(id)
    })

    return sequence
  }

  /**
   * Bind edit actions
   */
  bindEdit () {
    // get all delete buttons
    $(document).on('click', '.jsFieldEdit', (e) => {
      // prevent default
      e.preventDefault()

      // checking if a request has been sent to load field that needs to be edited
      if (this.lockEditRequest) {
        return
      }

      // else we lock editing and continue processing the request
      this.lockEditRequest = true

      // get id
      const id = $(e.currentTarget).attr('data-field-id')

      // only when set
      if (id !== '') {
        // make the call
        $.ajax({
          data: $.extend({}, this.paramsGet, {
            form_id: this.formId,
            field_id: id
          }),
          success: (data, textStatus) => {
            // success
            if (data.code === 200) {
              // init default values
              if (data.data.field.settings === null) {
                data.data.field.settings = {}
              }
              if (data.data.field.settings.default_values === null) {
                data.data.field.settings.default_values = ''
              }

              let html = ''

              // textbox edit
              if (data.data.field.type === 'textbox') {
                // fill in form
                $('#textboxId').val(data.data.field.id)
                $('#textboxLabel').val(StringUtil.htmlDecode(data.data.field.settings.label))
                $('#textboxValue').val(StringUtil.htmlDecode(data.data.field.settings.default_values))
                $('#textboxPlaceholder').val(StringUtil.htmlDecode(data.data.field.settings.placeholder))
                $('#textboxClassname').val(StringUtil.htmlDecode(data.data.field.settings.classname))
                $('#textboxAutocomplete').val(StringUtil.htmlDecode(data.data.field.settings.autocomplete))
                if (data.data.field.settings.reply_to &&
                  data.data.field.settings.reply_to === true
                ) {
                  $('#textboxReplyTo').prop('checked', true)
                }
                if (data.data.field.settings.use_to_subscribe_with_mailmotor &&
                  data.data.field.settings.use_to_subscribe_with_mailmotor === true
                ) {
                  $('#textboxMailmotor').prop('checked', true)
                }
                if (data.data.field.settings.send_confirmation_mail_to &&
                  data.data.field.settings.send_confirmation_mail_to === true
                ) {
                  $('#textboxSendConfirmationMailTo').prop('checked', true)
                }
                $('#textboxConfirmationMailSubject').val(StringUtil.htmlDecode(data.data.field.settings.confirmation_mail_subject))
                $('#textboxConfirmationMailMessage').val(data.data.field.settings.confirmation_mail_message)
                $.each(
                  data.data.field.validations,
                  function (k, v) {
                    // required checkbox
                    if (k === 'required') {
                      $('#textboxRequired').prop('checked', true)
                      $('#textboxRequiredErrorMessage').val(StringUtil.htmlDecode(v.error_message))

                      return
                    }

                    // dropdown
                    $('#textboxValidation').val(v.type)
                    $('#textboxErrorMessage').val(StringUtil.htmlDecode(v.error_message))
                  }
                )

                // show dialog
                $('#textboxDialog').modal('show')
              } else if (data.data.field.type === 'textarea') {
                // textarea edit
                // fill in form
                $('#textareaId').val(data.data.field.id)
                $('#textareaLabel').val(StringUtil.htmlDecode(data.data.field.settings.label))
                $('#textareaValue').val(StringUtil.htmlDecode(data.data.field.settings.default_values))
                $('#textareaPlaceholder').val(StringUtil.htmlDecode(data.data.field.settings.placeholder))
                $('#textareaClassname').val(StringUtil.htmlDecode(data.data.field.settings.classname))
                $.each(
                  data.data.field.validations,
                  function (k, v) {
                    // required checkbox
                    if (k === 'required') {
                      $('#textareaRequired').prop('checked', true)
                      $('#textareaRequiredErrorMessage').val(StringUtil.htmlDecode(v.error_message))

                      return
                    }

                    // dropdown
                    $('#textareaValidation').val(v.type)
                    $('#textareaErrorMessage').val(StringUtil.htmlDecode(v.error_message))
                  }
                )

                // show dialog
                $('#textareaDialog').modal('show')
              } else if (data.data.field.type === 'datetime') {
                // datetime edit
                // fill in form
                $('#datetimeId').val(data.data.field.id)
                $('#datetimeLabel').val(StringUtil.htmlDecode(data.data.field.settings.label))
                $('#datetimeValueAmount').val(StringUtil.htmlDecode(data.data.field.settings.value_amount))
                $('#datetimeValueType').val(StringUtil.htmlDecode(data.data.field.settings.value_type))
                $('#datetimeType').val(StringUtil.htmlDecode(data.data.field.settings.input_type))
                $('#datetimeClassname').val(StringUtil.htmlDecode(data.data.field.settings.classname))
                $('#datetimeAutocomplete').val(StringUtil.htmlDecode(data.data.field.settings.autocomplete))
                $.each(
                  data.data.field.validations,
                  function (k, v) {
                    // required checkbox
                    if (k === 'required') {
                      $('#datetimeRequired').prop('checked', true)
                      $('#datetimeRequiredErrorMessage').val(StringUtil.htmlDecode(v.error_message))

                      return
                    }

                    // dropdown
                    $('#datetimeValidation').val(v.type)
                    $('#datetimeErrorMessage').val(StringUtil.htmlDecode(v.error_message))
                  }
                )

                // show dialog
                $('#datetimeDialog').modal('show')
              } else if (data.data.field.type === 'dropdown') {
                // dropdown edit
                // fill in form
                $('#dropdownId').val(data.data.field.id)
                $('#dropdownLabel').val(StringUtil.htmlDecode(data.data.field.settings.label))
                $('#dropdownValues').val(data.data.field.settings.values.join('|'))
                $('#dropdownClassname').val(StringUtil.htmlDecode(data.data.field.settings.classname))
                $.each(
                  data.data.field.validations,
                  function (k, v) {
                    // required checkbox
                    if (k === 'required') {
                      $('#dropdownRequired').prop('checked', true)
                      $('#dropdownRequiredErrorMessage').val(StringUtil.htmlDecode(v.error_message))

                      return
                    }

                    // dropdown
                    $('#dropdownValidation').val(v.type)
                    $('#dropdownErrorMessage').val(StringUtil.htmlDecode(v.error_message))
                  }
                )

                // dirty method to init the selected element
                if (typeof data.data.field.settings.default_values !== 'undefined') {
                  // build html
                  html = '<option value="' + data.data.field.settings.default_values + '"'
                  html += ' selected="selected">'
                  html += data.data.field.settings.default_values + '</option>'
                  $('#dropdownDefaultValue').append(html)
                }

                // show dialog
                $('#dropdownDialog').modal('show')
              } else if (data.data.field.type === 'radiobutton') {
                // radiobutton edit
                // fill in form
                $('#radiobuttonId').val(data.data.field.id)
                $('#radiobuttonLabel').val(StringUtil.htmlDecode(data.data.field.settings.label))
                $('#radiobuttonValues').val(data.data.field.settings.values.join('|'))
                $('#radiobuttonClassname').val(StringUtil.htmlDecode(data.data.field.settings.classname))
                $.each(
                  data.data.field.validations,
                  function (k, v) {
                    // required checkbox
                    if (k === 'required') {
                      $('#radiobuttonRequired').prop('checked', true)
                      $('#radiobuttonRequiredErrorMessage').val(StringUtil.htmlDecode(v.error_message))

                      return
                    }

                    // dropdown
                    $('#radiobuttonValidation').val(v.type)
                    $('#radiobuttonErrorMessage').val(StringUtil.htmlDecode(v.error_message))
                  }
                )

                // dirty method to init the selected element
                if (typeof data.data.field.settings.default_values !== 'undefined') {
                  // build html
                  html = '<option value="' + data.data.field.settings.default_values + '"'
                  html += ' selected="selected">'
                  html += data.data.field.settings.default_values + '</option>'
                  $('#radiobuttonDefaultValue').append(html)
                }

                // show dialog
                $('#radiobuttonDialog').modal('show')
              } else if (data.data.field.type === 'checkbox') {
                // checkbox edit
                // fill in form
                $('#checkboxId').val(data.data.field.id)
                $('#checkboxLabel').val(StringUtil.htmlDecode(data.data.field.settings.label))
                $('#checkboxValues').val(data.data.field.settings.values.join('|'))
                $('#checkboxClassname').val(StringUtil.htmlDecode(data.data.field.settings.classname))
                $.each(
                  data.data.field.validations,
                  function (k, v) {
                    // required checkbox
                    if (k === 'required') {
                      $('#checkboxRequired').prop('checked', true)
                      $('#checkboxRequiredErrorMessage').val(StringUtil.htmlDecode(v.error_message))

                      return
                    }

                    // dropdown
                    $('#checkboxValidation').val(v.type)
                    $('#checkboxErrorMessage').val(StringUtil.htmlDecode(v.error_message))
                  }
                )

                // dirty method to init the selected element
                if (typeof data.data.field.settings.default_values !== 'undefined') {
                  // build html
                  html = '<option value="' + data.data.field.settings.default_values + '"'
                  html += ' selected="selected">'
                  html += data.data.field.settings.default_values + '</option>'
                  $('#checkboxDefaultValue').append(html)
                }

                // show dialog
                $('#checkboxDialog').modal('show')
              } else if (data.data.field.type === 'mailmotor') {
                // mailmotor edit
                // fill in form
                $('#mailmotorId').val(data.data.field.id)
                $('#mailmotorLabel').val(StringUtil.htmlDecode(data.data.field.settings.label))
                $('#mailmotorListId').val(StringUtil.htmlDecode(data.data.field.settings.list_id))

                // show dialog
                $('#mailmotorDialog').modal('show')
              } else if (data.data.field.type === 'heading') {
                // heading edit
                // fill in form
                $('#headingId').val(data.data.field.id)
                $('#heading').val(StringUtil.htmlDecode(data.data.field.settings.values))

                // show dialog
                $('#headingDialog').modal('show')
              } else if (data.data.field.type === 'paragraph') {
                // paragraph edit
                // fill in form
                $('#paragraphId').val(data.data.field.id)
                $('#paragraph').val(data.data.field.settings.values)

                // show dialog
                $('#paragraphDialog').modal('show')
              } else if (data.data.field.type === 'submit') {
                // submit edit
                // fill in form
                $('#submitId').val(data.data.field.id)
                $('#submit').val(StringUtil.htmlDecode(data.data.field.settings.values))

                // show dialog
                $('#submitDialog').modal('show')
              }

              // validation form
              this.handleValidation('.jsValidation')
            } else {
              // show error message
              Messages.add('danger', textStatus)
            }

            // alert the user
            if (data.code !== 200 && Config.isDebug()) {
              window.alert(data.message)
            }

            // unlocks editing whatever server response is
            this.lockEditRequest = false
          }
        })
      }
    })
  }

  /**
   * Bind validation dropdown
   */
  bindValidation () {
    // loop all validation wrappers
    $('.jsValidation').each((index, element) => {
      // validation wrapper
      const wrapper = element

      // init
      this.handleValidation(wrapper)

      // on change @todo test me plz.
      $(wrapper).find('select:first').on('change', () => {
        this.handleValidation(wrapper)
      })
      $(wrapper).find('input:checkbox').on('change', () => {
        this.handleValidation(wrapper)
      })
    })
  }

  /**
   * Handle validation status
   */
  handleValidation (wrapper) {
    // get dropdown
    const required = $(wrapper).find('input:checkbox')
    const validation = $(wrapper).find('select').first()

    // toggle required error message
    if ($(required).is(':checked')) {
      // show errormessage
      $(wrapper).find('.jsValidationRequiredErrorMessage').slideDown()

      // error message empty so add default
      if ($(wrapper).find('.jsValidationRequiredErrorMessage input:visible:first').val() === '') {
        $(wrapper).find('.jsValidationRequiredErrorMessage input:visible:first').val(this.defaultErrorMessages.required)
      }
    } else {
      $(wrapper).find('.jsValidationRequiredErrorMessage').slideUp()
    }

    // toggle validation error message
    if ($(validation).val() !== '') {
      // show error message
      $(wrapper).find('.jsValidationErrorMessage').slideDown()

      // default error message
      $(wrapper).find('.jsValidationErrorMessage input:visible:first').val(this.defaultErrorMessages[$(validation).val()])
    } else {
      $(wrapper).find('.jsValidationErrorMessage').slideUp()
    }
  }

  /**
   * Fill up the default values dropdown after rebuilding the multipleTextbox
   */
  multipleTextboxCallback (id) {
    // init
    const items = $('#' + id).val().split('|')
    const defaultElement = $('select[rel=' + id + ']')
    const selectedValue = $(defaultElement).find(':selected').val()

    // clear values except the first empty one
    $(defaultElement).find('option[value!=""]').remove()

    // add items
    $(items).each((k, v) => {
      // values is not empty
      if (v !== '') {
        // build html
        let html = '<option value="' + v + '"'
        if (selectedValue === v) {
          html += ' selected="selected"'
        }
        html += '>' + v + '</option>'

        // append to dropdown
        $(defaultElement).append(html)
      }
    })
  }

  /**
   * Reset a dialog by emptying the form fields and removing errors
   */
  resetDialog (id) {
    // clear all form fields
    $('#' + id).find(':input').prop('checked', false).removeAttr('checked').removeAttr('selected').val('')

    // bind validation
    this.handleValidation('#' + id + ' .jsValidation')

    // clear form errors
    $('#' + id + ' .jsFieldError').html('')

    // reset hidden fields
    $('#datetimeDialog').find('.defaultValue').show()

    // select first tab
    $('#' + id + ' .nav-tabs .nav-link:first').tab('show')
  }

  /**
   * Handle checkbox save
   */
  saveCheckbox () {
    // init vars
    const fieldId = $('#checkboxId').val()
    const type = 'checkbox'
    const label = $('#checkboxLabel').val()
    const values = $('#checkboxValues').val()
    const defaultValue = $('#checkboxDefaultValue').val()
    const required = $('#checkboxRequired').is(':checked')
    const requiredErrorMessage = $('#checkboxRequiredErrorMessage').val()
    const classname = $('#checkboxClassname').val()

    // make the call
    $.ajax({
      data: $.extend({}, this.paramsSave, {
        form_id: this.formId,
        field_id: fieldId,
        type: type,
        label: label,
        values: values,
        default_values: defaultValue,
        required: required,
        required_error_message: requiredErrorMessage,
        classname: classname
      }),
      success: (data, textStatus) => {
        // success
        if (data.code === 200) {
          // clear errors
          $('.jsFieldError').html('')

          // form contains errors
          if (typeof data.data.errors !== 'undefined') {
            // assign errors
            if (typeof data.data.errors.label !== 'undefined') {
              $('#checkboxLabelError').html(data.data.errors.label)
            }
            if (typeof data.data.errors.values !== 'undefined') {
              $('#checkboxValuesError').html(data.data.errors.values)
            }
            if (typeof data.data.errors.required_error_message !== 'undefined') {
              $('#checkboxRequiredErrorMessageError').html(data.data.errors.required_error_message)
            }
            if (typeof data.data.errors.error_message !== 'undefined') {
              $('#checkboxErrorMessageError').html(data.data.errors.error_message)
            }

            // toggle error messages
            this.toggleValidationErrors('checkboxDialog')
          } else {
            // saved!
            // append field html
            this.setField(data.data.field_id, data.data.field_html)

            // close console box
            $('#checkboxDialog').modal('hide')
          }
        } else {
          // show error message
          Messages.add('danger', textStatus)
        }

        // alert the user
        if (data.code !== 200 && Config.isDebug()) {
          window.alert(data.message)
        }
      }
    })
  }

  /**
   * Handle text box save
   */
  saveDatetime () {
    // init vars
    const fieldId = $('#datetimeId').val()
    const type = 'datetime'
    const label = $('#datetimeLabel').val()
    const valueAmount = $('#datetimeValueAmount').val()
    const valueType = $('#datetimeValueType').val()
    const inputType = $('#datetimeType').val()
    const required = $('#datetimeRequired').is(':checked')
    const requiredErrorMessage = $('#datetimeRequiredErrorMessage').val()
    const validation = $('#datetimeValidation').val()
    const errorMessage = $('#datetimeErrorMessage').val()
    const classname = $('#datetimeClassname').val()
    const autocomplete = $('#datetimeAutocomplete').val()

    // make the call
    $.ajax(
      {
        data: $.extend({}, this.paramsSave, {
          form_id: this.formId,
          field_id: fieldId,
          type: type,
          label: label,
          value_amount: valueAmount,
          value_type: valueType,
          required: required,
          required_error_message: requiredErrorMessage,
          input_type: inputType,
          validation: validation,
          error_message: errorMessage,
          classname: classname,
          autocomplete: autocomplete
        }),
        success: (data, textStatus) => {
          // success
          if (data.code === 200) {
            // clear errors
            $('.invalid-feedback').html('')

            // form contains errors
            if (typeof data.data.errors !== 'undefined') {
              // assign errors
              if (typeof data.data.errors.label !== 'undefined') {
                $('#datetimeLabelError').html(data.data.errors.label)
              }
              if (typeof data.data.errors.default_value_error_message !== 'undefined') {
                $('#datetimeDefaultValueErrorMessageError').html(data.data.errors.default_value_error_message)
              }
              if (typeof data.data.errors.required_error_message !== 'undefined') {
                $('#datetimeRequiredErrorMessageError').html(data.data.errors.required_error_message)
              }
              if (typeof data.data.errors.error_message !== 'undefined') {
                $('#datetimeErrorMessageError').html(data.data.errors.error_message)
              }

              // toggle error messages
              this.toggleValidationErrors('datetimeDialog')
            } else {
              // saved!
              // append field html
              this.setField(data.data.field_id, data.data.field_html)

              // close console box
              $('#datetimeDialog').modal('hide')
            }
          } else {
            // show error message
            Messages.add('error', textStatus)
          }

          // alert the user
          if (data.code !== 200 && Config.isDebug()) {
            window.alert(data.message)
          }
        }
      })
  }

  /**
   * Handle dropdown save
   */
  saveDropdown () {
    // init vars
    const fieldId = $('#dropdownId').val()
    const type = 'dropdown'
    const label = $('#dropdownLabel').val()
    const values = $('#dropdownValues').val()
    const defaultValue = $('#dropdownDefaultValue').val()
    const required = $('#dropdownRequired').is(':checked')
    const requiredErrorMessage = $('#dropdownRequiredErrorMessage').val()
    const classname = $('#dropdownClassname').val()

    // make the call
    $.ajax({
      data: $.extend({}, this.paramsSave, {
        form_id: this.formId,
        field_id: fieldId,
        type: type,
        label: label,
        values: values,
        default_values: defaultValue,
        required: required,
        required_error_message: requiredErrorMessage,
        classname: classname
      }),
      success: (data, textStatus) => {
        // success
        if (data.code === 200) {
          // clear errors
          $('.jsFieldError').html('')

          // form contains errors
          if (typeof data.data.errors !== 'undefined') {
            // assign errors
            if (typeof data.data.errors.label !== 'undefined') {
              $('#dropdownLabelError').html(data.data.errors.label)
            }
            if (typeof data.data.errors.values !== 'undefined') {
              $('#dropdownValuesError').html(data.data.errors.values)
            }
            if (typeof data.data.errors.required_error_message !== 'undefined') {
              $('#dropdownRequiredErrorMessageError').html(data.data.errors.required_error_message)
            }
            if (typeof data.data.errors.error_message !== 'undefined') {
              $('#dropdownErrorMessageError').html(data.data.errors.error_message)
            }

            // toggle error messages
            this.toggleValidationErrors('dropdownDialog')
          } else {
            // saved!
            // append field html
            this.setField(data.data.field_id, data.data.field_html)

            // close console box
            $('#dropdownDialog').modal('hide')
          }
        } else {
          // show error message
          Messages.add('danger', textStatus)
        }

        // alert the user
        if (data.code !== 200 && Config.isDebug()) {
          window.alert(data.message)
        }
      }
    })
  }

  /**
   * Handle heading save
   */
  saveHeading () {
    // init vars
    const fieldId = $('#headingId').val()
    const type = 'heading'
    const value = $('#heading').val()

    // make the call
    $.ajax({
      data: $.extend({}, this.paramsSave, {
        form_id: this.formId,
        field_id: fieldId,
        type: type,
        values: value
      }),
      success: (data, textStatus) => {
        // success
        if (data.code === 200) {
          // clear errors
          $('.jsFieldError').html('')

          // form contains errors
          if (typeof data.data.errors !== 'undefined') {
            // assign errors
            if (typeof data.data.errors.values !== 'undefined') {
              $('#headingError').html(data.data.errors.values)
            }

            // toggle error messages
            this.toggleValidationErrors('headingDialog')
          } else {
            // saved!
            // append field html
            this.setField(data.data.field_id, data.data.field_html)

            // close console box
            $('#headingDialog').modal('hide')
          }
        } else {
          // show error message
          Messages.add('danger', textStatus)
        }

        // alert the user
        if (data.code !== 200 && Config.isDebug()) {
          window.alert(data.message)
        }
      }
    })
  }

  /**
   * Handle paragraph save
   */
  saveParagraph () {
    // init vars
    const fieldId = $('#paragraphId').val()
    const type = 'paragraph'
    const value = $('#paragraph').val()

    // make the call
    $.ajax({
      data: $.extend({}, this.paramsSave, {
        form_id: this.formId,
        field_id: fieldId,
        type: type,
        values: value
      }),
      success: (data, textStatus) => {
        // success
        if (data.code === 200) {
          // clear errors
          $('.jsFieldError').html('')

          // form contains errors
          if (typeof data.data.errors !== 'undefined') {
            // assign error
            if (typeof data.data.errors.values !== 'undefined') {
              $('#paragraphError').html(data.data.errors.values)
            }

            // toggle error messages
            this.toggleValidationErrors('paragraphDialog')
          } else {
            // saved!
            // append field html
            this.setField(data.data.field_id, data.data.field_html)

            // close console box
            $('#paragraphDialog').modal('hide')
          }
        } else {
          // show error message
          Messages.add('danger', textStatus)
        }

        // alert the user
        if (data.code !== 200 && Config.isDebug()) window.alert(data.message)
      }
    })
  }

  /**
   * Handle radiobutton save
   */
  saveRadiobutton () {
    // init vars
    const fieldId = $('#radiobuttonId').val()
    const type = 'radiobutton'
    const label = $('#radiobuttonLabel').val()
    const values = $('#radiobuttonValues').val()
    const defaultValue = $('#radiobuttonDefaultValue').val()
    const required = $('#radiobuttonRequired').is(':checked')
    const requiredErrorMessage = $('#radiobuttonRequiredErrorMessage').val()
    const classname = $('#radiobuttonClassname').val()

    // make the call
    $.ajax({
      data: $.extend({}, this.paramsSave, {
        form_id: this.formId,
        field_id: fieldId,
        type: type,
        label: label,
        values: values,
        default_values: defaultValue,
        required: required,
        required_error_message: requiredErrorMessage,
        classname: classname
      }),
      success: (data, textStatus) => {
        // success
        if (data.code === 200) {
          // clear errors
          $('.jsFieldError').html('')

          // form contains errors
          if (typeof data.data.errors !== 'undefined') {
            // assign errors
            if (typeof data.data.errors.label !== 'undefined') {
              $('#radiobuttonLabelError').html(data.data.errors.label)
            }

            if (typeof data.data.errors.values !== 'undefined') {
              $('#radiobuttonValuesError').html(data.data.errors.values)
            }

            if (typeof data.data.errors.required_error_message !== 'undefined') {
              $('#radiobuttonRequiredErrorMessageError').html(data.data.errors.required_error_message)
            }

            if (typeof data.data.errors.error_message !== 'undefined') {
              $('#radiobuttonErrorMessageError').html(data.data.errors.error_message)
            }

            // toggle error messages
            this.toggleValidationErrors('radiobuttonDialog')
          } else {
            // saved!
            // append field html
            this.setField(data.data.field_id, data.data.field_html)

            // close console box
            $('#radiobuttonDialog').modal('hide')
          }
        } else {
          // show error message
          Messages.add('danger', textStatus)
        }

        // alert the user
        if (data.code !== 200 && Config.isDebug()) {
          window.alert(data.message)
        }
      }
    })
  }

  /**
   * Handle mailmotorButton save
   */
  saveMailmotorbutton () {
    // init vars
    const fieldId = $('#mailmotorId').val()
    const type = 'mailmotor'
    const label = $('#mailmotorLabel').val()
    const listId = $('#mailmotorListId').val()

    // make the call
    $.ajax({
      data: $.extend({}, this.paramsSave, {
        form_id: this.formId,
        field_id: fieldId,
        type: type,
        label: label,
        list_id: listId
      }),
      success: (data, textStatus) => {
        // success
        if (data.code === 200) {
          // clear errors
          $('.jsFieldError').html('')

          // form contains errors
          if (typeof data.data.errors !== 'undefined') {
            // assign errors
            if (typeof data.data.errors.label !== 'undefined') {
              $('#mailmotorLabelError').html(data.data.errors.label)
            }

            if (typeof data.data.errors.list_id !== 'undefined') {
              $('#mailmotorListIdError').html(data.data.errors.list_id)
            }

            // toggle error messages
            this.toggleValidationErrors('mailmotorDialog')
          } else {
            // saved!
            // append field html
            this.setField(data.data.field_id, data.data.field_html)

            // close console box
            $('#mailmotorDialog').modal('hide')
          }
        } else {
          // show error message
          Messages.add('danger', textStatus)
        }

        // alert the user
        if (data.code !== 200 && Config.isDebug()) {
          window.alert(data.message)
        }
      }
    })
  }

  /**
   * Handle submit save
   */
  saveSubmit () {
    // init vars
    const fieldId = $('#submitId').val()
    const type = 'submit'
    const value = $('#submit').val()

    // make the call
    $.ajax({
      data: $.extend({}, this.paramsSave, {
        form_id: this.formId,
        field_id: fieldId,
        type: type,
        values: value
      }),
      success (data, textStatus) {
        // success
        if (data.code === 200) {
          // form contains errors
          if (typeof data.data.errors !== 'undefined') {
            // assign errors
            if (typeof data.data.errors.values !== 'undefined') {
              $('#submitError').html(data.data.errors.values)
            }

            // toggle error messages
            this.toggleValidationErrors('submitDialog')
          } else {
            // saved!
            // set value
            $('#submitField').val(value)

            // close console box
            $('#submitDialog').modal('hide')
          }

          // toggle error messages
          this.toggleValidationErrors('submitDialog')
        } else {
          // show error message
          Messages.add('danger', textStatus)
        }

        // alert the user
        if (data.code !== 200 && Config.isDebug()) {
          window.alert(data.message)
        }
      }
    })
  }

  /**
   * Handle recaptcha save
   */
  saveRecaptcha () {
    // make the call
    $.ajax({
      data: $.extend({}, this.paramsSave, {
        form_id: this.formId,
        type: 'recaptcha'
      }),
      success: (data, textStatus) => {
        // success
        if (data.code === 200) {
          // append field html
          this.setField(data.data.field_id, data.data.field_html)
        } else {
          // show error message
          Messages.add('danger', textStatus)
        }

        // alert the user
        if (data.code !== 200 && Config.isDebug()) {
          window.alert(data.message)
        }
      }
    })
  }

  /**
   * Handle textarea save
   */
  saveTextarea () {
    // init vars
    const fieldId = $('#textareaId').val()
    const type = 'textarea'
    const label = $('#textareaLabel').val()
    const value = $('#textareaValue').val()
    const placeholder = $('#textareaPlaceholder').val()
    const required = $('#textareaRequired').is(':checked')
    const requiredErrorMessage = $('#textareaRequiredErrorMessage').val()
    const validation = $('#textareaValidation').val()
    const validationParameter = $('#textareaValidationParameter').val()
    const errorMessage = $('#textareaErrorMessage').val()
    const classname = $('#textareaClassname').val()

    // make the call
    $.ajax({
      data: $.extend({}, this.paramsSave, {
        form_id: this.formId,
        field_id: fieldId,
        type: type,
        label: label,
        default_values: value,
        required: required,
        required_error_message: requiredErrorMessage,
        validation: validation,
        validation_parameter: validationParameter,
        error_message: errorMessage,
        placeholder: placeholder,
        classname: classname
      }),
      success: (data, textStatus) => {
        // success
        if (data.code === 200) {
          // clear errors
          $('.jsFieldError').html('')

          // form contains errors
          if (typeof data.data.errors !== 'undefined') {
            // assign errors
            if (typeof data.data.errors.label !== 'undefined') {
              $('#textareaLabelError').html(data.data.errors.label)
            }
            if (typeof data.data.errors.required_error_message !== 'undefined') {
              $('#textareaRequiredErrorMessageError').html(data.data.errors.required_error_message)
            }
            if (typeof data.data.errors.error_message !== 'undefined') {
              $('#textareaErrorMessageError').html(data.data.errors.error_message)
            }
            if (typeof data.data.errors.validation_parameter !== 'undefined') {
              $('#textareaValidationParameterError').html(data.data.errors.validation_parameter)
            }
            if (typeof data.data.errors.reply_to_error_message !== 'undefined') {
              $('#textboxReplyToErrorMessageError').html(data.data.errors.reply_to_error_message)
            }
            if (typeof data.data.errors.use_to_subscribe_with_mailmotor_error_message !== 'undefined') {
              $('#textboxMailmotorErrorMessageError').html(data.data.errors.use_to_subscribe_with_mailmotor_error_message)
            }

            // toggle error messages
            this.toggleValidationErrors('textareaDialog')
          } else {
            // saved!
            // append field html
            this.setField(data.data.field_id, data.data.field_html)

            // close console box
            $('#textareaDialog').modal('hide')
          }
        } else {
          // show error message
          Messages.add('danger', textStatus)
        }

        // alert the user
        if (data.code !== 200 && Config.isDebug()) {
          window.alert(data.message)
        }
      }
    })
  }

  /**
   * Handle text box save
   */
  saveTextbox () {
    // init vars
    const fieldId = $('#textboxId').val()
    const type = 'textbox'
    const label = $('#textboxLabel').val()
    const value = $('#textboxValue').val()
    const placeholder = $('#textboxPlaceholder').val()
    const replyTo = $('#textboxReplyTo').is(':checked')
    const sendConfirmationMailTo = $('#textboxSendConfirmationMailTo').is(':checked')
    const confirmationMailSubject = $('#textboxConfirmationMailSubject').val()
    const confirmationMailMessage = $('#textboxConfirmationMailMessage').val()
    const required = $('#textboxRequired').is(':checked')
    const requiredErrorMessage = $('#textboxRequiredErrorMessage').val()
    const validation = $('#textboxValidation').val()
    const validationParameter = $('#textboxValidationParameter').val()
    const errorMessage = $('#textboxErrorMessage').val()
    const classname = $('#textboxClassname').val()
    const mailmotor = $('#textboxMailmotor').is(':checked')
    const autocomplete = $('#textboxAutocomplete').val()

    // make the call
    $.ajax({
      data: $.extend({}, this.paramsSave, {
        form_id: this.formId,
        field_id: fieldId,
        type: type,
        label: label,
        default_values: value,
        reply_to: replyTo,
        use_to_subscribe_with_mailmotor: mailmotor,
        send_confirmation_mail_to: sendConfirmationMailTo,
        confirmation_mail_subject: confirmationMailSubject,
        confirmation_mail_message: confirmationMailMessage,
        required: required,
        required_error_message: requiredErrorMessage,
        validation: validation,
        validation_parameter: validationParameter,
        error_message: errorMessage,
        placeholder: placeholder,
        classname: classname,
        autocomplete: autocomplete
      }),
      success: (data, textStatus) => {
        // success
        if (data.code === 200) {
          // clear errors
          $('.jsFieldError').html('')

          // form contains errors
          if (typeof data.data.errors !== 'undefined') {
            // assign errors
            if (typeof data.data.errors.label !== 'undefined') {
              $('#textboxLabelError').html(data.data.errors.label)
            }
            if (typeof data.data.errors.required_error_message !== 'undefined') {
              $('#textboxRequiredErrorMessageError').html(data.data.errors.required_error_message)
            }
            if (typeof data.data.errors.error_message !== 'undefined') {
              $('#textboxErrorMessageError').html(data.data.errors.error_message)
            }
            if (typeof data.data.errors.validation_parameter !== 'undefined') {
              $('#textboxValidationParameterError').html(data.data.errors.validation_parameter)
            }
            if (typeof data.data.errors.reply_to_error_message !== 'undefined') {
              $('#textboxReplyToErrorMessageError').html(data.data.errors.reply_to_error_message)
            }
            if (typeof data.data.errors.use_to_subscribe_with_mailmotor_error_message !== 'undefined') {
              $('#textboxMailmotorErrorMessageError').html(data.data.errors.use_to_subscribe_with_mailmotor_error_message)
            }
            if (typeof data.data.errors.send_confirmation_mail_to_error_message !== 'undefined') {
              $('#textboxSendConfirmationMailToErrorMessageError').html(data.data.errors.send_confirmation_mail_to_error_message)
            }
            if (typeof data.data.errors.confirmation_mail_subject_error_message !== 'undefined') {
              $('#textboxConfirmationMailSubjectErrorMessageError').html(data.data.errors.confirmation_mail_subject_error_message)
            }
            if (typeof data.data.errors.confirmation_mail_message_error_message !== 'undefined') {
              $('#textboxConfirmationMailMessageErrorMessageError').html(data.data.errors.confirmation_mail_message_error_message)
            }

            // toggle error messages
            this.toggleValidationErrors('textboxDialog')
          } else {
            // saved!
            // append field html
            this.setField(data.data.field_id, data.data.field_html)

            // close console box
            $('#textboxDialog').modal('hide')
          }
        } else {
          // show error message
          Messages.add('danger', textStatus)
        }

        // alert the user
        if (data.code !== 200 && Config.isDebug()) {
          window.alert(data.message)
        }
      }
    })
  }

  /**
   * Append the field to the form or update it on its current location
   */
  setField (fieldId, fieldHTML) {
    // exist
    if ($('#fieldHolder-' + fieldId).length >= 1) {
      // add new one just before old one
      $('#fieldHolder-' + fieldId).after(fieldHTML)

      // remove old one
      $('#fieldHolder-' + fieldId + ':first').remove()
    } else {
      // new item
      // already field items so add after them
      if ($('#fieldsHolder .jsField').length >= 1) {
        $('#fieldsHolder .jsField:last').after(fieldHTML)
      } else {
        // first field so add in beginning
        $('#fieldsHolder').prepend(fieldHTML)
      }
    }

    // highlight
    $('#fieldHolder-' + fieldId).effect('highlight', {color: '#D9E5F3'}, 1500)
  }

  /**
   * Toggle the no items message based on the amount of rows
   */
  toggleNoItems () {
    // count the rows
    const rowCount = $('#fieldsHolder .jsField').length

    // got items (always 1 item in it)
    if (rowCount >= 1) {
      $('#noFields').hide()
    } else {
      // no items
      $('#noFields').show()
    }
  }

  /**
   * Toggle validation errors
   */
  toggleValidationErrors (id) {
    // remove highlights
    $('#' + id + ' .jsFieldTabsNav li').removeClass('danger')

    // loop tabs
    $('#' + id + ' .jsFieldTab').each((index, tab) => {
      // tab
      const tabId = $(tab).attr('id')

      // loop tab errors
      $(tab).find('.jsFieldError').each((index, error) => {
        // has a message so highlight tab
        if ($(error).html() !== '') {
          $('#' + id + ' .jsFieldTabsNav a[href="#' + tabId + '"]').closest('li').addClass('danger')
        }
      })
    })

    // loop error fields
    $('#' + id).find('.jsFieldError').each((index, error) => {
      // has a message
      if ($(error).html() !== '') {
        $(error).show()
      } else {
        // no message
        $(error).hide()
      }
    })
  }
}
