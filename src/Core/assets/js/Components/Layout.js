export class Layout {
  constructor () {
    this.showBrowserWarning()
  }

  // if the browser isn't supported, show a warning
  showBrowserWarning () {
    let showWarning = false

    // check firefox
    if (window.navigator.userAgent.indexOf('Firefox') !== -1) {
      const version = parseInt(window.navigator.appVersion.substr(0, 3).replace(/\./g, ''))

      if (version < 52) showWarning = true
    }

    // check opera
    if (window.navigator.userAgent.indexOf('OPR') !== -1) {
      const version = parseInt(window.navigator.appVersion.substr(0, 1))

      if (version < 44) showWarning = true
    }

    // check safari, should be webkit when using 1.4
    if (window.navigator.userAgent.indexOf('Safari') !== -1 && window.navigator.userAgent.indexOf('Chrome') === -1) {
      const version = parseInt(window.navigator.appVersion.substr(0, 3))

      // lower than 1.4?
      if (version < 400) showWarning = true
    }

    // check IE
    if (window.navigator.userAgent.indexOf('MSIE') === -1) {
      showWarning = true
    }

    // show warning if needed
    if (showWarning) $('#showBrowserWarning').show()
  }
}
