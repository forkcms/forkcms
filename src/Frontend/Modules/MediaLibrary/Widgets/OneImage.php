<?php

namespace Frontend\Modules\MediaLibrary\Widgets;

use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Frontend\Modules\MediaLibrary\Widgets\Base\FrontendMediaWidget;

class OneImage extends FrontendMediaWidget
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

    protected function parse(): void
    {
        /** @var MediaGroupMediaItem|bool $firstConnectedItem */
        $firstConnectedItem = $this->mediaGroup->getConnectedItems()->first();

        if ($firstConnectedItem === false) {
            return;
        }

        // Add OpenGraph image
        $this->header->addOpenGraphImage($firstConnectedItem->getItem()->getAbsoluteWebPath());

        // Assign item (and their source and other custom resolutions)
        $this->template->assign('mediaItem', $firstConnectedItem->getItem());
    }
}
