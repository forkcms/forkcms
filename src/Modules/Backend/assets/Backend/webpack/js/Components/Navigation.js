/**
 * Navigation controls
 */
import { Cookies } from '../../../../../../../Core/assets/js/Components/Cookies'

export class Navigation {
  constructor () {
    this.mobile()
    this.toggleCollapse()
    this.tooltip()
  }

  mobile () {
    const navbarWidth = this.calculateNavbarWidth()
    const $navbarNav = $('.navbar-dark .navbar-nav')

    $('.navbar-dark .navbar-nav').css('width', navbarWidth)

    $('.js-nav-prev').on('click', (e) => {
      e.preventDefault()
      $navbarNav.animate({'left': '+=85px'})
      this.setControls(85)
    })

    $('.js-nav-next').on('click', (e) => {
      e.preventDefault()
      $navbarNav.animate({'left': '-=85px'})
      this.setControls(-85)
    })
  }

  resize () {
    const $navbarNav = $('.navbar-dark .navbar-nav')
    const navbarWidth = this.calculateNavbarWidth()
    const windowWidth = this.calculateWindowWidth()

    if (navbarWidth < windowWidth) {
      $navbarNav.css('left', '0')
      $('.js-nav-next').hide()
    }
    this.setControls(0)
  }

  toggleCollapse () {
    const $wrapper = $('.main-wrapper')
    const $navCollapse = $('.js-toggle-nav')
    let collapsed = $wrapper.hasClass('navigation-collapsed')

    if ($wrapper.hasClass('navigation-collapsed')) {
      $('.js-nav-screen-text').html(window.backend.locale.lbl('OpenNavigation'))
    } else {
      $('.js-nav-screen-text').html(window.backend.locale.lbl('CloseNavigation'))
    }

    $navCollapse.on('click', (e) => {
      e.preventDefault()
      $wrapper.toggleClass('navigation-collapsed')
      if ($wrapper.hasClass('navigation-collapsed')) {
        $('.js-nav-screen-text').html(window.backend.locale.lbl('OpenNavigation'))
      } else {
        $('.js-nav-screen-text').html(window.backend.locale.lbl('CloseNavigation'))
      }
      collapsed = !collapsed
      Cookies.setCookie('navigation-collapse', collapsed)
      setTimeout(() => {
        this.resize()
      }, 250)
    })
  }

  tooltip () {
    const $wrapper = $('.main-wrapper')
    const tooltipElements = document.querySelectorAll('[data-toggle="tooltip-nav"]')

    for (let i = 0; i < tooltipElements.length; i++) {
      const tooltip = tooltipElements[i]
      const tooltipObject = new bootstrap.Tooltip(tooltip, {
        boundary: 'window',
        trigger: 'manual',
        placement: 'right'
      })

      tooltip.addEventListener('mouseenter', () => {
        if ($wrapper.hasClass('navigation-collapsed') && $(window).width() > 787) {
          tooltipObject.show()
        }
      })

      tooltip.addEventListener('mouseleave', () => {
        tooltipObject.hide()
      })
    }
  }

  setControls (offset) {
    const $navbarNav = $('.navbar-dark .navbar-nav')
    const rightOffset = this.calculateOffset(offset)

    if ((parseInt($navbarNav.css('left')) + offset) >= 0) {
      $('.js-nav-prev').hide()
    } else {
      $('.js-nav-prev').show()
    }

    if (rightOffset < 0) {
      $('.js-nav-next').show()
    } else {
      $('.js-nav-next').hide()
    }
  }

  calculateWindowWidth () {
    return $(window).width()
  }

  calculateNavbarWidth () {
    const $navItem = $('.navbar-dark .nav-item')
    return $navItem.width() * $navItem.length
  }

  calculateOffset (offset) {
    const $navbarNav = $('.navbar-dark .navbar-nav')
    return this.calculateWindowWidth() - this.calculateNavbarWidth() - parseInt($navbarNav.css('left')) - offset
  }
}
