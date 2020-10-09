export class Html5Validation {
  static html5validation (options, element) {
    // define defaults
    const $input = $(element)
    let errorMessage = ''
    let type = ''
    const defaults = {
      required: window.frontend.components.locale.err('FieldIsRequired'),
      email: window.frontend.components.locale.err('EmailIsInvalid'),
      date: window.frontend.components.locale.err('DateIsInvalid'),
      number: window.frontend.components.locale.err('NumberIsInvalid'),
      value: window.frontend.components.locale.err('InvalidValue')
    }

    options = $.extend(defaults, options)

    $input.on('invalid', (e) => {
      if ($input[0].validity.valueMissing) {
        errorMessage = options.required
      } else if (!$input[0].validity.valid) {
        type = $input[0].type
        errorMessage = options.value

        if (options[type]) {
          errorMessage = options[type]
        }
      }

      e.target.setCustomValidity(errorMessage)
      $input.parents('.form-group').addClass('has-error')

      $input.on('input change', (e) => {
        e.target.setCustomValidity('')
      })
    })

    $input.on('blur', (e) => {
      $input.parents('.form-group').removeClass('has-error')
      e.target.checkValidity()
    })
  }
}
