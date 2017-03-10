<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

final class UpdateMediaItemHandler
{
    /**
     * @param UpdateMediaItem $updateMediaItem
     */
    public function handle(UpdateMediaItem $updateMediaItem)
    {
        MediaItem::fromDataTransferObject($updateMediaItem);
    }
}
