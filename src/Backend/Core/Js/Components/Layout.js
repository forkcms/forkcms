export class Layout {
  constructor () {
    this.showBrowserWarning()
    this.dataGrid()
  }

  // data grid layout
  dataGrid () {
    if (jQuery.browser.msie) {
      $('.jsDataGrid tr td:last-child').addClass('lastChild')
      $('.jsDataGrid tr td:first-child').addClass('firstChild')
    }

    // dynamic striping
    $('.dynamicStriping.jsDataGrid tr:nth-child(2n)').addClass('even')
    $('.dynamicStriping.jsDataGrid tr:nth-child(2n+1)').addClass('odd')
  }

  // if the browser isn't supported, show a warning
  showBrowserWarning () {
    let showWarning = false
    let version = ''

    // check firefox
    if (jQuery.browser.mozilla) {
      // get version
      version = parseInt(jQuery.browser.version.substr(0, 3).replace(/\./g, ''))

      // lower than 19?
      if (version < 19) showWarning = true
    }

    // check opera
    if (jQuery.browser.opera) {
      // get version
      version = parseInt(jQuery.browser.version.substr(0, 1))

      // lower than 9?
      if (version < 9) showWarning = true
    }

    // check safari, should be webkit when using 1.4
    if (jQuery.browser.safari) {
      // get version
      version = parseInt(jQuery.browser.version.substr(0, 3))

      // lower than 1.4?
      if (version < 400) showWarning = true
    }

    // check IE
    if (jQuery.browser.msie) {
      // get version
      version = parseInt(jQuery.browser.version.substr(0, 1))

      // lower or equal than 6
      if (version <= 6) showWarning = true
    }

    // show warning if needed
    if (showWarning) $('#showBrowserWarning').show()
  }
}
