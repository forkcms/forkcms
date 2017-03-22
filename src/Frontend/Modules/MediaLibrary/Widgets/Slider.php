<?php

namespace Frontend\Modules\MediaLibrary\Widgets;

use Frontend\Modules\MediaLibrary\Widgets\Base\FrontendMediaWidget;

/**
 * This will show a MediaGroup (Custom Module) or a MediaGallery (Media Module) in a slider using BxSlider.
 */
class Slider extends FrontendMediaWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        $this->loadData();

        // We need to have a MediaGroup to show this widget
        if ($this->mediaGroup) {
            // Add CSS
            $this->addCSS('/node_modules/bxslider/dist/jquery.bxslider.css', true);

            // Add custom CSS
            $this->addCSS('slider.css');

            // Add JS
            $this->addJS('/node_modules/bxslider/dist/jquery.bxslider.min.js', true);

            parent::execute();
            $this->loadTemplate();
            $this->parse();
        }
    }
}
