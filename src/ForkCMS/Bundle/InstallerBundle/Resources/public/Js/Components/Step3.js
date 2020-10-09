export class Step3 {
  constructor () {
    this.toggleDebugEmail()
  }

  toggleDebugEmail () {
    $('#debugEmailHolder').hide()

    if ($('#install_modules_different_debug_email').is(':checked')) {
      $('#debugEmailHolder').show()
    }

    // multiple languages
    $('#install_modules_different_debug_email').on('change', () => {
      if ($('#install_modules_different_debug_email').is(':checked')) {
        $('#debugEmailHolder').show()
        $('#install_modules_debug_email').focus()
      } else {
        $('#debugEmailHolder').hide()
      }
    })
  }
}
