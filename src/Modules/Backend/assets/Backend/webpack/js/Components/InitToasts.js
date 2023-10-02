export class InitBsToasts {
  constructor () {
    this.initToasts()
  }

  // init toasts present on page load
  initToasts () {
    const messageNodes = document.querySelector('[data-messaging-wrapper]').querySelectorAll('[data-role="toast"]')
    const messageNodesList = Array.from(messageNodes)
    messageNodesList.forEach(message => {
      const toast = window.bootstrap.Toast.getOrCreateInstance(message)
      if (message.classList.contains('to-show')) toast.show()
    })
  }
}
