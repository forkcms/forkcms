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
            $this->addCSS('Plugins/bxslider/jquery.bxslider.css');

            // Add custom CSS
            $this->addCSS('slider.css');

            // Add JS
            $this->addJS('Plugins/bxslider/jquery.bxslider.min.js');

            // We define the resolutions
            $this->setResolutions([
                $this->get('media_library.factory.frontend.resolution')->create(
                    'square', // Give a custom name for your image size which will create "square" + "square_source" variables in frontend
                    'crop', // Use "resize" or "crop"
                    1600, // Optional: width
                    350, // Optional: height
                    100 // Optional: this is the quality, value between 0 - 100
                ),
            ]);

            parent::execute();
            $this->loadTemplate();
            $this->parse();
        }
    }
}
