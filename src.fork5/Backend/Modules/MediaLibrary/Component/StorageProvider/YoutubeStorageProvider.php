<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

class YoutubeStorageProvider extends MovieStorageProvider
{
    public function getIncludeHTML(MediaItem $mediaItem): string
    {
        return '<iframe width="100%" height="100%" src="' . $this->includeUrl . $mediaItem->getUrl() . '" frameborder="0" allowfullscreen></iframe>';
    }

    public function getThumbnail(MediaItem $mediaItem): string
    {
        return 'https://img.youtube.com/vi/' . $mediaItem->getUrl() . '/maxresdefault.jpg';
    }
}
