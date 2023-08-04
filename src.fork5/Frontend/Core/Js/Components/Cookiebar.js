import { Cookies } from './Cookies'

export class Cookiebar {
  constructor () {
    // if there is no cookiebar we shouldn't do anything
    if ($('#cookie-bar').length === 0) return

    const $cookieBar = $('#cookie-bar')

    // @remark: as you can see we use PHP-serialized values so we can use them in PHP too.
    // hide the cookieBar if needed
    if (Cookies.readCookie('cookie_bar_hide') === 'b%3A1%3B') {
      $cookieBar.hide()
    }

    $cookieBar.on('click', '[data-role="cookie-bar-button"]', (e) => {
      e.preventDefault()

      if ($(e.currentTarget).data('action') === 'agree') {
        Cookies.setCookie('cookie_bar_agree', 'Y')
        Cookies.setCookie('cookie_bar_hide', 'Y')
      } else {
        Cookies.setCookie('cookie_bar_agree', 'N')
        Cookies.setCookie('cookie_bar_hide', 'Y')
      }

      $cookieBar.hide()
    })
  }
}
