<?php

namespace App\Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use App\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use App\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemDataTransferObject;

final class UpdateMediaItem extends MediaItemDataTransferObject
{
    public function __construct(MediaItem $mediaItem)
    {
        parent::__construct($mediaItem);
    }
}
