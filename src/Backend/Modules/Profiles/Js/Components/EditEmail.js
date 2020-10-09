export class EditEmail {
  constructor () {
    if ($('#newEmailBox').length === 0) return false

    $('#newEmail').on('change', () => {
      this.toggleBox()
    })

    this.toggleBox()
  }

  toggleBox () {
    const $item = $('#newEmail')
    const checked = ($item.prop('checked') === true)
    const $box = $('#newEmailBox')
    const $input = $box.find('input')

    if (checked) {
      $input.removeClass('disabled').removeAttr('disabled').focus()
    } else {
      $input.addClass('disabled').prop('disabled', 'disabled')
    }
  }
}
