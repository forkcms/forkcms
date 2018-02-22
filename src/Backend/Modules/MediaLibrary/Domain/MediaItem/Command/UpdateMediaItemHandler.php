<?php

namespace App\Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use App\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

final class UpdateMediaItemHandler
{
    public function handle(UpdateMediaItem $updateMediaItem): void
    {
        MediaItem::fromDataTransferObject($updateMediaItem);
    }
}
