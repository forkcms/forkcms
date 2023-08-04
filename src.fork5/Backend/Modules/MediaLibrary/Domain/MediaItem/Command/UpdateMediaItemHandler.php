<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

final class UpdateMediaItemHandler
{
    public function handle(UpdateMediaItem $updateMediaItem): void
    {
        MediaItem::fromDataTransferObject($updateMediaItem);
    }
}
