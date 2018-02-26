<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemDataTransferObject;

final class UpdateMediaItem extends MediaItemDataTransferObject
{
    public function __construct(MediaItem $mediaItem)
    {
        parent::__construct($mediaItem);
    }
}
