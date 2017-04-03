<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

class YoutubeStorageProvider extends MovieStorageProvider
{
    /**
     * @param MediaItem $mediaItem
     * @return string
     */
    public function getIncludeHTML(MediaItem $mediaItem): string
    {
        return '<iframe width="560" height="315" src="' . $this->includeURL . $mediaItem->getUrl() . '" frameborder="0" allowfullscreen></iframe>';
    }
}
