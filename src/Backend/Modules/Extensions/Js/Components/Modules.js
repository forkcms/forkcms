export class Modules {
  constructor () {
    $('.jsConfirmationTrigger').off().on('click', this.installModal)
  }

  installModal (e) {
    // prevent default
    e.preventDefault()

    // get data
    const href = e.currentTarget.getAttribute('href')
    let message = e.currentTarget.dataset.message

    if (typeof message === 'undefined') {
      message = window.backend.locale.msg('ConfirmModuleInstallDefault')
    }

    // the first is necessary to prevent multiple popups showing after a previous modal is dismissed without
    // refreshing the page
    const exampleModal = document.querySelectorAll('.jsConfirmation')[0]
    const confirmationModalHtml = exampleModal.cloneNode(true)

    // bind
    if (href !== '') {
      // set data
      confirmationModalHtml.querySelectorAll('.jsConfirmationMessage')[0].innerHTML = message
      confirmationModalHtml.querySelectorAll('.jsConfirmationSubmit')[0].setAttribute('href', href)

      // open modal
      const confirmationModal = new window.bootstrap.Modal(confirmationModalHtml)
      confirmationModal.show()
    }
  }
}
