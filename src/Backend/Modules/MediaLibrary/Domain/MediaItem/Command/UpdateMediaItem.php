<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemDataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateMediaItem extends MediaItemDataTransferObject
{
    /**
     * UpdateMediaItem constructor.
     *
     * @param MediaItem $mediaItem
     */
    public function __construct(MediaItem $mediaItem)
    {
        parent::__construct($mediaItem);
    }
}
