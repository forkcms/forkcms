export class Session {
  constructor () {
    this.sessionTimeoutPopup()
  }

  // Display a session timeout warning 1 minute before the session might actually expire
  sessionTimeoutPopup () {
    setInterval(() => {
      window.alert(window.backend.locale.msg('SessionTimeoutWarning'))
    }, (jsData.Core.session_timeout - 60) * 1000)
  }
}
