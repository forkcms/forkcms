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
        if (!$this->mediaGroup) {
            return;
        }

        $this->addCSSAndJS();
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    private function addCSSAndJS()
    {
        /**
         * Note: if you also want to support <figure> and <figcaption> in older browsers (< IE9),
         * you should add the following html to your <head>.
         * <!--[if lt IE 9]>
         *  <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
         * <![endif]-->
         */
        // Add CSS
        $this->addCSS('/css/vendors/photoswipe/photoswipe.css', true);
        $this->addCSS('/css/vendors/photoswipe/default-skin.css', true);

        // Add custom CSS
        $this->addCSS('lightbox.css');

        // Add JS
        $this->addJS('/js/vendors/photoswipe.min.js', true);
        $this->addJS('/js/vendors/photoswipe-ui-default.min.js', true);

        /**
         * Attention:
         * PhotoSwipe only works properly when you define the image data-size="widthxheight" to the large image
         * because it requires this to show transition/zoom/... properly.
         *
         * More info about this:
         *     - FAQ: http://photoswipe.com/documentation/faq.html#image-size
         *     - GitHub Issue: https://github.com/dimsemenov/PhotoSwipe/issues/741
         */
    }
}
