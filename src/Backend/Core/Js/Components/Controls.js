import { StringUtil } from './StringUtil'
import { PasswordGenerator } from './PasswordGenerator'

export class Controls {
  constructor () {
    this.bindCheckboxDropdownCombo()
    this.bindCheckboxTextfieldCombo()
    this.bindRadioButtonFieldCombo()
    this.bindConfirm()
    this.bindFakeDropdown()
    this.bindMassAction()
    this.bindMassCheckbox()
    this.bindPasswordStrengthMeter()
    this.bindToggleDiv()
    this.bindTableCheckbox()
    this.bindTargetBlank()
    this.bindWorkingLanguageSelection()
  }

  // bind a checkbox dropdown combo
  bindCheckboxDropdownCombo () {
    // variables
    const $checkboxDropdownCombo = $('.jsCheckboxDropdownCombo')

    $checkboxDropdownCombo.each((index, element) => {
      const $this = $(element)
      const multiple = !!$this.data('multiple') || false

      if ($this.find('input:checkbox').length > 0 && $this.find('select').length > 0) {
        $this.find('input:checkbox').eq(0).on('change', (e) => {
          const $this = $(e.currentTarget)
          const $combo = $this.parents().filter($checkboxDropdownCombo)
          let $field = $($combo.find('select'))

          if (!multiple) {
            $field = $field.eq(0)
          }

          if ($this.is(':checked')) {
            $field.removeClass('disabled').prop('disabled', false)
            const $focusDropdown = ((!multiple) ? $field : $field.eq(0))
            $focusDropdown.focus()

            return
          }

          $field.addClass('disabled').prop('disabled', true)
        }).trigger('change')
      }
    })
  }

  // bind a checkbox textfield combo
  bindCheckboxTextfieldCombo () {
    // variables
    const $checkboxTextFieldCombo = $('.checkboxTextFieldCombo')

    $checkboxTextFieldCombo.each((index, element) => {
      // variables
      const $this = $(element)

      // check if needed element exists
      if ($this.find('input:checkbox').length > 0 && $this.find('input:text').length > 0) {
        // variables
        const $checkbox = $this.find('input:checkbox').eq(0)
        const $textField = $this.find('input:text').eq(0)

        $checkbox.on('change', (e) => {
          // redefine
          const $this = $(e.currentTarget)

          // variables
          const $combo = $this.parents().filter($checkboxTextFieldCombo)
          const $field = $($combo.find('input:text')[0])

          if ($this.is(':checked')) {
            $field.removeClass('disabled').prop('disabled', false).focus()
          } else {
            $field.addClass('disabled').prop('disabled', true)
          }
        })

        if ($checkbox.is(':checked')) {
          $textField.removeClass('disabled').prop('disabled', false)
        } else {
          $textField.addClass('disabled').prop('disabled', true)
        }
      }
    })
  }

  // bind a radiobutton field combo
  bindRadioButtonFieldCombo () {
    // variables
    const $radiobuttonFieldCombo = $('.radiobuttonFieldCombo')

    $radiobuttonFieldCombo.each((index, element) => {
      // variables
      const $this = $(element)

      // check if needed element exists
      if ($this.find('input:radio').length > 0 && $this.find('input, select, textarea').length > 0) {
        // variables
        const $radiobutton = $this.find('input:radio')
        const $selectedRadiobutton = $this.find('input:radio:checked')

        $radiobutton.on('click', (e) => {
          // variables
          const $this = $(e.currentTarget)

          // disable all
          $this.parents('.radiobuttonFieldCombo:first').find('input:not([name="' + $radiobutton.attr('name') + '"]), select, textarea').addClass('disabled').prop('disabled', true)

          // get fields that should be enabled
          const $fields = $('input[name="' + $radiobutton.attr('name') + '"]:checked').parents('.form-group:first').find('input:not([name="' + $radiobutton.attr('name') + '"]), select, textarea')

          // enable
          $fields.removeClass('disabled').prop('disabled', false)

          // set focus
          if (typeof $fields[0] !== 'undefined') $fields[0].focus()
        })

        // change?
        if ($selectedRadiobutton.length > 0) {
          $selectedRadiobutton.click()
        } else {
          $radiobutton[0].click()
        }
      }
    })
  }

  // bind confirm message
  bindConfirm () {
    $('.jsConfirmationTrigger').on('click', (e) => {
      // prevent default
      e.preventDefault()

      // variables
      const $this = $(e.currentTarget)

      // get data
      const href = $this.attr('href')
      let message = $this.data('message')

      if (typeof message === 'undefined') {
        message = window.backend.locale.msg('ConfirmDefault')
      }

      // the first is necessary to prevent multiple popups showing after a previous modal is dismissed without
      // refreshing the page
      const $confirmation = $('.jsConfirmation').clone().first()

      // bind
      if (href !== '') {
        // set data
        $confirmation.find('.jsConfirmationMessage').html(message)
        $confirmation.find('.jsConfirmationSubmit').attr('href', $this.attr('href'))

        // open dialog
        $confirmation.modal('show')
      }
    })
  }

  // let the fake dropdown behave nicely, like a real dropdown
  bindFakeDropdown () {
    // variables
    const $fakeDropdown = $('.fakeDropdown')

    $fakeDropdown.on('click', function (e) {
      // prevent default behaviour
      e.preventDefault()

      // stop it
      e.stopPropagation()

      // variables
      const $parent = $fakeDropdown.parent()
      const $body = $('body')

      // get id
      let id = $(this).attr('href')

      // IE8 prepends full current url before links to #
      id = id.substring(id.indexOf('#'))

      if ($(id).is(':visible')) {
        // remove events
        $body.off('click')
        $body.off('keyup')

        // remove class
        $parent.removeClass('selected')

        // hide
        $(id).hide('blind', {}, 'fast')
      } else {
        // bind escape
        $body.on('keyup', (e) => {
          if (e.keyCode === 27) {
            // unbind event
            $body.off('keyup')

            // remove class
            $parent.removeClass('selected')

            // hide
            $(id).hide('blind', {}, 'fast')
          }
        })

        // bind click outside
        $body.on('click', (e) => {
          // unbind event
          $body.off('click')

          // remove class
          $parent.removeClass('selected')

          // hide
          $(id).hide('blind', {}, 'fast')
        })

        // add class
        $parent.addClass('selected')

        // show
        $(id).show('blind', {}, 'fast')
      }
    })
  }

  // bind confirm message
  bindMassAction () {
    const $checkboxes = $('table.jsDataGrid .check input:checkbox')
    let noneChecked = true

    // check if none is checked
    $checkboxes.each((index, element) => {
      if ($(element).prop('checked')) {
        noneChecked = false
      }
    })

    // set disabled
    if (noneChecked) {
      $('.jsMassAction select').prop('disabled', true)
      $('.jsMassAction .jsMassActionSubmit').prop('disabled', true)
    }

    // hook change events
    $checkboxes.on('change', (e) => {
      // get parent table
      const table = $(e.currentTarget).parents('table.jsDataGrid').eq(0)

      // any item checked?
      if (table.find('input:checkbox:checked').length > 0) {
        table.find('.jsMassAction select').prop('disabled', false)
        table.find('.jsMassAction .jsMassActionSubmit').prop('disabled', false)
      } else {
        // nothing checked
        table.find('.jsMassAction select').prop('disabled', true)
        table.find('.jsMassAction .jsMassActionSubmit').prop('disabled', true)
      }
    })

    // hijack the form
    $('.jsMassAction .jsMassActionSubmit').on('click', (e) => {
      // prevent default action
      e.preventDefault()

      // variables
      const $this = $(e.currentTarget)
      const $closestForm = $this.closest('form')

      // not disabled
      if (!$this.prop('disabled')) {
        // get the selected element
        if ($this.closest('.jsMassAction').find('select[name=action] option:selected').length > 0) {
          // get action element
          const element = $this.closest('.jsMassAction').find('select[name=action] option:selected')

          // if the rel-attribute exists we should show the dialog
          if (typeof element.data('target') !== 'undefined') {
            // get id
            const id = element.data('target')

            $(id).modal('show')
          } else {
            // no confirm
            $closestForm.submit()
          }
        } else {
          // no confirm
          $closestForm.submit()
        }
      }
    })
  }

  // check all checkboxes with one checkbox in the tableheader
  bindMassCheckbox () {
    // mass checkbox changed
    $('th.check input:checkbox').on('change', (e) => {
      // variables
      const $this = $(e.currentTarget)

      // check or uncheck all the checkboxes in this datagrid
      $this.closest('table').find('td input:checkbox').prop('checked', $this.is(':checked')).change()

      // set selected class
      if ($this.is(':checked')) {
        $this.parents().filter('table').eq(0).find('tbody tr').addClass('selected')
      } else {
        $this.parents().filter('table').eq(0).find('tbody tr').removeClass('selected')
      }
    })

    // single checkbox changed
    $('td.check input:checkbox').on('change', (e) => {
      // variables
      const $this = $(e.currentTarget)

      // check mass checkbox
      if ($this.closest('table').find('td.checkbox input:checkbox').length === $this.closest('table').find('td.checkbox input:checkbox:checked').length) {
        $this.closest('table').find('th .checkboxHolder input:checkbox').prop('checked', true)
      } else {
        // uncheck mass checkbox
        $this.closest('table').find('th .checkboxHolder input:checkbox').prop('checked', false)
      }
    })
  }

  // bind the password strength meter to the correct inputfield(s)
  bindPasswordStrengthMeter () {
    // variables
    const $passwordStrength = $('[data-role="password-strength-meter"]')

    if ($passwordStrength.length > 0) {
      $passwordStrength.each((index, element) => {
        // grab id
        const id = $(element).data('id')

        // hide all
        $('[data-role="password-strength-meter"][data-id="' + id + '"] [data-role="password-strength"]').hide()

        // execute function directly
        const strength = this.checkPassword($('#' + id).val())

        // show
        $('[data-role="password-strength-meter"][data-id="' + id + '"] [data-strength="' + strength + '"]').show()

        // bind keypress
        $(document).on('keyup', '#' + id, () => {
          // hide all
          $('[data-role="password-strength-meter"][data-id="' + id + '"] [data-role="password-strength"]').hide()

          // execute function directly
          const strength = this.checkPassword($('#' + id).val())

          // show
          $('[data-role="password-strength-meter"][data-id="' + id + '"] [data-strength="' + strength + '"]').show()
        })
      })
    }
  }

  // check a string for passwordstrength
  checkPassword (string) {
    // init vars
    let score = 0
    const uniqueChars = []

    // no chars means no password
    if (string.length === 0) return 'none'

    // less then 4 chars is just a weak password
    if (string.length <= 4) return 'weak'

    // loop chars and add unique chars
    for (let i = 0; i < string.length; i++) {
      if ($.inArray(string.charAt(i), uniqueChars) === -1) uniqueChars.push(string.charAt(i))
    }

    // less then 3 unique chars is just weak
    if (uniqueChars.length < 3) return 'weak'

    // more then 6 chars is good
    if (string.length >= 6) score++

    // more then 8 is beter
    if (string.length >= 8) score++

    // more then 12 is best
    if (string.length >= 12) score++

    // upper and lowercase?
    if ((string.match(/[a-z]/)) && string.match(/[A-Z]/)) score += 2

    // number?
    if (string.match(/\d+/)) score++

    // special char?
    if (string.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)) score++

    // strong password
    if (score >= 6) return 'strong'

    // average
    if (score >= 2) return 'average'

    // fallback
    return 'weak'
  }

  // toggle a div
  bindToggleDiv () {
    $(document).on('click', '.toggleDiv', (e) => {
      // prevent default
      e.preventDefault()

      // variables
      const $element = $(e.currentTarget)

      // get id
      const id = $element.attr('href')

      // show/hide
      $(id).toggle()

      // set selected class on parent
      if ($(id).is(':visible')) {
        $element.parent().addClass('selected')
      } else {
        $element.parent().removeClass('selected')
      }
    })
  }

  // bind checkboxes in a row
  bindTableCheckbox () {
    // set classes
    $('tr td.checkbox input.inputCheckbox:checked').each((index, element) => {
      if (!$(element).parents('table').hasClass('noSelectedState')) {
        $(element).parents().filter('tr').eq(0).addClass('selected')
      }
    })

    // bind change-events
    $(document).on('change', 'tr td.checkbox input.inputCheckbox:checkbox', (e) => {
      const $element = $(e.currentTarget)
      if (!$element.parents('table').hasClass('noSelectedState')) {
        if ($element.is(':checked')) {
          $element.parents().filter('tr').eq(0).addClass('selected')
        } else {
          $element.parents().filter('tr').eq(0).removeClass('selected')
        }
      }
    })
  }

  // bind target blank
  bindTargetBlank () {
    $('a.targetBlank').attr('target', '_blank').attr('rel', 'noopener noreferrer')
  }

  // toggle between the working languages
  bindWorkingLanguageSelection () {
    // variables
    const $workingLanguage = $('#workingLanguage')

    $workingLanguage.on('change', (e) => {
      // preventDefault
      e.preventDefault()

      // break the url int parts
      const urlChunks = document.location.pathname.split('/')

      // get the query string, we will append it later
      const queryChunks = document.location.search.split('&')
      const newChunks = []

      // any parts in the query string
      if (typeof queryChunks !== 'undefined' && queryChunks.length > 0) {
        // remove variables that could trigger an message
        for (let i in queryChunks) {
          if (queryChunks[i].substring(0, 5) !== 'token' &&
            queryChunks[i].substring(0, 5) !== 'error' &&
            queryChunks[i].substring(0, 6) === 'report' &&
            queryChunks[i].substring(0, 3) === 'var' &&
            queryChunks[i].substring(0, 9) === 'highlight') {
            newChunks.push(queryChunks[i])
          }
        }
      }

      // replace the third element with the new language
      urlChunks[2] = $(e.currentTarget).val()

      // remove action
      if (urlChunks.length > 4) urlChunks.pop()

      let url = urlChunks.join('/')
      if (newChunks.length > 0) url += '?token=true&' + newChunks.join('&')

      // rebuild the url and redirect
      document.location.href = url
    })
  }
}
