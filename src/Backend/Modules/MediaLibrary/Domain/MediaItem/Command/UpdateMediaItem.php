<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemDataTransferObject;

final class UpdateMediaItem extends MediaItemDataTransferObject
{
    public function __construct(MediaItem $mediaItem)
    {
        parent::__construct($mediaItem);
    }
}
