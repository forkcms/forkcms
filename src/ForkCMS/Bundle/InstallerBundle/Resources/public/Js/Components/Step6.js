export class Step6 {
  constructor () {
    $('#showPassword').on('change', (e) => {
      e.preventDefault()

      // show password
      if ($(e.currentTarget).is(':checked')) {
        $('#plainPassword').show()
        $('#fakePassword').hide()
      } else {
        $('#plainPassword').hide()
        $('#fakePassword').show()
      }
    })
  }
}
