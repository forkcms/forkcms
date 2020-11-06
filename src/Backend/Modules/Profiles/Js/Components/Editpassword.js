export class Editpassword {
  constructor () {
    if ($('#newPasswordBox').length === 0) return false

    $('#newPassword').on('change', () => {
      this.toggleBox()
    })

    this.toggleBox()
  }

  toggleBox () {
    const $item = $('#newPassword')
    const checked = ($item.prop('checked') === true)
    const $box = $('#newPasswordBox')

    $box.toggle(checked)
    $box.find('input#password').focus()
  }
}
