/**
 * Function related to cookies
 */

export class Cookies {
  /**
   * Are cookies enabled?
   *
   * @return bool
   */
  static isEnabled () {
    // try to grab the property
    let cookiesEnabled = !!(navigator.cookieEnabled)

    // unknown property?
    if (typeof navigator.cookieEnabled === 'undefined' && !cookiesEnabled) {
      // try to set a cookie
      document.cookie = 'testcookie'
      cookiesEnabled = ($.inArray('testcookie', document.cookie) !== -1)
    }

    // return
    return cookiesEnabled
  }

  /**
   * Read a cookie
   *
   * @return mixed
   */
  static readCookie (name) {
    // get cookies
    const cookies = document.cookie.split(';')
    name = name + '='

    for (let i = 0; i < cookies.length; i++) {
      let cookie = cookies[i]
      while (cookie.charAt(0) === ' ') cookie = cookie.substring(1, cookie.length)
      if (cookie.indexOf(name) === 0) return cookie.substring(name.length, cookie.length)
    }

    // fallback
    return null
  }

  static setCookie (name, value, days, secure) {
    if (typeof days === 'undefined') days = 7
    if (typeof secure === 'undefined') secure = window.location.protocol === 'https:'

    const expireDate = new Date()
    expireDate.setDate(expireDate.getDate() + days)
    document.cookie = name + '=' + escape(value) + ';expires=' + expireDate.toUTCString() + ';path=/' + (secure ? ';secure=true' : '')
  }
}
