<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

final class DeleteMediaItem
{
    /**
     * @var MediaItem
     */
    public $mediaItem;

    public function __construct(MediaItem $mediaItem)
    {
        $this->mediaItem = $mediaItem;
    }
}
