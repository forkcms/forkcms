<?php

namespace Frontend\Modules\MediaLibrary\Widgets;

use Frontend\Modules\MediaLibrary\Widgets\Base\FrontendMediaWidget;

/**
 * This will show a MediaGroup (Custom Module) or a MediaGallery (Media Module) in a bootstrap carousel
 */
class Carousel extends FrontendMediaWidget
{
    /** @var string */
    private $imageImagineFilter = 'media_library_slider_pano';

    public function execute(): void
    {
        $this->loadData();

        // We need to have a MediaGroup to show this widget
        if (!$this->mediaGroup) {
            return;
        }

        parent::execute();
        $this->template->assign('mediaGroupId', $this->data['group_id']);
        $this->template->assign('imagineFilter', $this->imageImagineFilter);
        $this->loadTemplate();
        $this->parse();
    }

    public function setImageImagineFilter(string $filter): void
    {
        $this->imageImagineFilter = $filter;
    }
}
