export class ScrollTo {
  constructor () {
    this.events()
  }

  events () {
    $(document).on('click', '[data-scroll]', $.proxy(this.scrollTo, this))
  }

  scrollTo (event) {
    let $anchor = $(event.currentTarget)
    let target = $anchor.attr('href')
    let offset = 0

    // If there is a custom offset
    if ($anchor.data('offset')) {
      offset = $anchor.data('offset')
    }

    /* check if we have an url, and if it is on the current page and the element exists disabled for nav-tabs */
    if ($(target).length > 0) {
      let $htmlBody = $('html, body')
      $htmlBody.stop()
      $htmlBody.animate({
        scrollTop: $(target).offset().top - offset
      }, 500)
    }
  }
}
