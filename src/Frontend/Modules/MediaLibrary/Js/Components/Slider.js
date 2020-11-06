import 'slick-carousel/slick/slick.min'

export class Slider {
  constructor () {
    const $sliders = $('.widget-media-library-slider')

    // when no items for slider found, stop here
    if ($sliders.length === 0) {
      return false
    }

    // init sliders
    $sliders.each((index, slider) => {
      // define slider
      const $slider = $(slider)

      // define show controls or not
      let showControls = $slider.data('show-controls')
      let showPager = $slider.data('show-pager')

      // we have multiple items
      const multipleItems = ($slider.find('div').length > 1)

      // we only have one item, hide controls
      if (!multipleItems) {
        showControls = false
        showPager = false
      }

      // init slick slider
      $slider.find('.widget-body').show().slick({
        arrows: showControls,
        autoplay: multipleItems,
        adaptiveHeight: true,
        dots: showPager,
        // fade: true, // this has side effects in bootstrap 4 because of the .fade class, if you enable this don't forget to add extra styling for this
        lazyLoad: 'ondemand',
        mobileFirst: true
      })
    })
  }
}
