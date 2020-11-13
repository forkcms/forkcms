export class Modules {
  constructor () {
    $('.jsConfirmationTrigger').off().on('click', this.installModal)
  }

  installModal (e) {
    // prevent default
    e.preventDefault()

    // get data
    const href = $(e.currentTarget).attr('href')
    let message = $(e.currentTarget).data('message')

    if (typeof message === 'undefined') {
      message = window.backend.locale.msg('ConfirmModuleInstallDefault')
    }

    // the first is necessary to prevent multiple popups showing after a previous modal is dismissed without
    // refreshing the page
    const $confirmation = $('.jsConfirmation').clone().first()

    // bind
    if (href !== '') {
      // set data
      $confirmation.find('.jsConfirmationMessage').html(message)
      $confirmation.find('.jsConfirmationSubmit').attr('href', $(e.currentTarget).attr('href'))

      // open dialog
      $confirmation.modal('show')
    }
  }
}
