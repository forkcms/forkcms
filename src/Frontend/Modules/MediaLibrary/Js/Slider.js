/**
 * global: jsFrontend
 */
(function($)
{
    /**
     * Create responsive media slider, which uses "slick" slider
     */
    $.fn.mediaLibrarySlider = function()
    {
        // loop for all sliders
        return this.each(function()
        {
            // define slider
            var $slider = $(this);

            // define show controls or not
            var showControls = $slider.data('show-controls');
            var showPager = $slider.data('show-pager');

            // we have multiple items
            var multipleItems = ($slider.find('div').length > 1);

            // we only have one item, hide controls
            if (!multipleItems) {
                showControls = false;
                showPager = false;
            }

            // init slick slider
            $slider.find('.widget-body').show().slick({
                arrows: showControls,
                autoplay: multipleItems,
                adaptiveHeight: true,
                dots: showPager,
                fade: true,
                lazyLoad: 'ondemand',
                mobileFirst: true
            });
        });
    };
})(jQuery);

/**
 * MediaLibrary slider
 */
jsFrontend.mediaLibrarySlider =
{
    init: function()
    {
        var $sliders = $('.widget-media-library-slider');

        // when no items for slider found, stop here
        if ($sliders.length === 0) {
            return false;
        }

        // init sliders
        $sliders.mediaLibrarySlider();
    }
};

$(jsFrontend.mediaLibrarySlider.init);
