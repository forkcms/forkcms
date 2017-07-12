<?php

namespace Frontend\Modules\MediaLibrary\Widgets;

use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Frontend\Modules\MediaLibrary\Widgets\Base\FrontendMediaWidget;

class OneRandomImage extends FrontendMediaWidget
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
        /** @var MediaGroupMediaItem $randomConnectedItem */
        $randomConnectedItem = $this->mediaGroup->getConnectedItems()->get(
            array_rand($this->mediaGroup->getConnectedItems()->toArray())
        );

        if ($randomConnectedItem === null) {
            return;
        }

        // Add OpenGraph image
        $this->header->addOpenGraphImage($randomConnectedItem->getItem()->getAbsoluteWebPath());

        // Assign item (and their source and other custom resolutions)
        $this->template->assign('mediaItem', $randomConnectedItem->getItem());
    }
}
