export class Controls {
  constructor () {
    this.bindPasswordStrengthMeter()
  }

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

  // check a string for password strength
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
}
