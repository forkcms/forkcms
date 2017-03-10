/**
 * global: jsFrontend
 */
(function($)
{
    /**
     * Create responsive media slider, which uses bxSlider
     */
    $.fn.mediaSlider = function()
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
            var multipleItems = ($slider.find('ul li').length > 1);

            // we only have one item, hide controls
            if (!multipleItems) {
                showControls = false;
                showPager = false;
            }

            // init bxslider
            $slider.find('ul').show().bxSlider({
                adaptiveHeight: true,
                auto: multipleItems,
                autoStart: true,
                controls: showControls,
                mode: 'fade',
                pager: showPager,
                preloadImages: 'all',
                speed: 800,
                swipeThreshold: 100,
                touchEnabled: multipleItems
            });
        });
    };
})(jQuery);

/**
 * Media slider
 */
jsFrontend.mediaSlider =
{
    init: function()
    {
        var $sliders = $('.widgetMediaSlider');

        // when no items for slider found, stop here
        if ($sliders.length === 0) {
            return false;
        }

        // init sliders
        $sliders.mediaSlider();
    }
};

$(jsFrontend.mediaSlider.init);
