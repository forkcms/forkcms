<?php

namespace Frontend\Modules\MediaLibrary\Widgets;

use Frontend\Modules\MediaLibrary\Widgets\Base\FrontendMediaWidget;

/**
 * This will show a MediaGroup (Custom Module) or a MediaGallery (Media Module) in a slider using BxSlider.
 */
class Slider extends FrontendMediaWidget
{
    public function execute(): void
    {
        $this->loadData();

        // We need to have a MediaGroup to show this widget
        if (!$this->mediaGroup) {
            return;
        }

        $this->addJS('/js/vendors/slick.min.js', true);

        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }
}
