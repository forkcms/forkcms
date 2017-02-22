<?php

namespace Frontend\Modules\MediaLibrary\Widgets;

use Frontend\Modules\MediaLibrary\Widgets\Base\FrontendMediaWidget;

/**
 * This will show a MediaGroup (Custom Module) or a MediaGallery (Media Module) in a lightbox using PhotoSwipe.
 */
class Lightbox extends FrontendMediaWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        $this->loadData();

        // We need to have a MediaGroup to show this widget
        if ($this->mediaGroup) {
            /**
             * Note: if you also want to support <figure> and <figcaption> in older browsers (< IE9),
             * you should add the following html to your <head>.
             * <!--[if lt IE 9]>
             *  <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
             * <![endif]-->
             */
            // Add CSS
            $this->addCSS('Plugins/photoswipe/photoswipe.css');
            $this->addCSS('Plugins/photoswipe/default-skin.css');

            // Add custom CSS
            $this->addCSS('lightbox.css');

            // Add JS
            $this->addJS('Plugins/photoswipe/photoswipe.min.js');
            $this->addJS('Plugins/photoswipe/photoswipe-ui-default.min.js');

            /**
             * Attention:
             * PhotoSwipe only works properly when you define the image data-size="widthxheight" to the large image
             * because it requires this to show transition/zoom/... properly.
             *
             * More info about this:
             *     - FAQ: http://photoswipe.com/documentation/faq.html#image-size
             *     - GitHub Issue: https://github.com/dimsemenov/PhotoSwipe/issues/741
             */
            // We define the resolutions
            $this->setResolutions([
                $this->get('media_library.factory.frontend.resolution')->create(
                    'small', // Give a custom name for your image size which will create "small" + "small_source" variables in frontend
                    800, // Optional: width
                    null, // Optional: height
                    'resize', // Use "resize" or "crop"
                    100 // Optional: this is the quality, value between 0 - 100
                ),
            ]);

            parent::execute();
            $this->loadTemplate();
            $this->parse();
        }
    }
}
