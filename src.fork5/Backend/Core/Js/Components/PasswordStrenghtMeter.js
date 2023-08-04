const passwordStrength = require('check-password-strength')

export class PasswordStrenghtMeter {
  constructor (element) {
    // grab id
    const fieldId = $(element).data('id')

    // hide all
    $('[data-role="password-strength-meter"][data-id="' + fieldId + '"] [data-role="password-strength"]').hide()

    let strength = 'none'
    // execute function directly
    if ($('#' + fieldId).val().length > 0) {
      strength = passwordStrength($('#' + fieldId).val()).value
    }

    // show
    $('[data-role="password-strength-meter"][data-id="' + fieldId + '"] [data-strength="' + strength.toLowerCase() + '"]').show()

    // bind keypress
    $(document).on('keyup', '#' + fieldId, () => {
      // hide all
      $('[data-role="password-strength-meter"][data-id="' + fieldId + '"] [data-role="password-strength"]').hide()

      let strength = 'none'
      // execute function directly
      if ($('#' + fieldId).val().length > 0) {
        // execute function directly
        strength = passwordStrength($('#' + fieldId).val()).value
      }

      // show
      $('[data-role="password-strength-meter"][data-id="' + fieldId + '"] [data-strength="' + strength.toLowerCase() + '"]').show()
    })
  }
}
