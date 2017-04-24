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

        $this->addLightboxJS();
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    private function addLightboxJS()
    {
        $this->addJS('/js/vendors/photoswipe.min.js', true);
        $this->addJS('/js/vendors/photoswipe-ui-default.min.js', true);
    }
}
