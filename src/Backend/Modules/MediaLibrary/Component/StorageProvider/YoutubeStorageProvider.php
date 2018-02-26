<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Component\StorageProvider;

use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

class YoutubeStorageProvider extends MovieStorageProvider
{
    public function getIncludeHTML(MediaItem $mediaItem): string
    {
        return '<iframe width="560" height="315" src="' . $this->includeUrl . $mediaItem->getUrl() . '" frameborder="0" allowfullscreen></iframe>';
    }
}
