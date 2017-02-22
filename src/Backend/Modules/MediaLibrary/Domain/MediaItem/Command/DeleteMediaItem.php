<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

final class DeleteMediaItem
{
    /**
     * @var MediaItem
     */
    public $mediaItem;

    /**
     * DeleteMediaItem constructor.
     *
     * @param $mediaItem
     */
    public function __construct(MediaItem $mediaItem)
    {
        $this->mediaItem = $mediaItem;
    }
}
