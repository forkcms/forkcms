<?php

namespace App\Frontend\Modules\MediaLibrary\Widgets;

use App\Frontend\Modules\MediaLibrary\Widgets\Base\FrontendMediaWidget;

/**
 * This will show a MediaGroup (Custom Module) or a MediaGallery (Media Module) in a list.
 */
class Listing extends FrontendMediaWidget
{
    public function execute(): void
    {
        $this->loadData();

        // We need to have a MediaGroup to show this widget
        if (!$this->mediaGroup) {
            return;
        }

        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }
}
