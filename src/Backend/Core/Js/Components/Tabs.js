/**
 * Apply tabs
 */

export class Tabs {
  constructor () {
    if ($('.nav-tabs').length > 0) {
      $('.tab-content .tab-pane').each((index, element) => {
        // check if there are invalid feedback classes, if they are visible or do not have display none style
        if ($(element).find('.invalid-feedback').length > 0 && ($(element).find('.invalid-feedback:visible').length > 0 || $(element).find('.invalid-feedback').css('display') !== 'none')) {
          $('.nav-tabs a[href="#' + $(element).attr('id') + '"]').addClass('bg-danger text-white')
        } else {
          $('.nav-tabs a[href="#' + $(element).attr('id') + '"]').removeClass('bg-danger text-white')
        }
      })
    }

    $('.nav-tabs a').click((e) => {
      // if the browser supports history.pushState(), use it to update the URL with the fragment identifier, without triggering a scroll/jump
      if (window.history && window.history.pushState) {
        // an empty state object for now — either we implement a proper pop state handler ourselves, or wait for jQuery UI upstream
        window.history.pushState({}, document.title, e.currentTarget.getAttribute('href'))
      } else {
        // for browsers that do not support pushState
        // save current scroll height
        const scrolled = $(window).scrollTop()

        // set location hash
        window.location.hash = '#' + e.currentTarget.getAttribute('href').split('#')[1]

        // reset scroll height
        $(window).scrollTop(scrolled)
      }
    })

    // Show tab if the hash is in the url
    const hash = window.location.hash
    if ($(hash).length > 0 && $(hash).hasClass('tab-pane')) {
      $('a[href="' + hash + '"]').tab('show')
    }
  }
}
