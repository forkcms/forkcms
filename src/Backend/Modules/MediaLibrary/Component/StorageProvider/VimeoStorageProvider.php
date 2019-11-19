<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

class VimeoStorageProvider extends MovieStorageProvider
{
    public function getIncludeHTML(MediaItem $mediaItem): string
    {
        return '<iframe src="' . $this->includeUrl . $mediaItem->getUrl() . '?color=ffffff&title=0&byline=0&portrait=0&badge=0" width="100%" height="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }

    public function getThumbnail(MediaItem $mediaItem): string
    {
        $data = file_get_contents('https://vimeo.com/api/v2/video/' . $mediaItem->getUrl() . '.json');
        $data = json_decode($data, true);

        $thumbnailUrl = $data[0]['thumbnail_large'] ?? $data[0]['thumbnail_medium'] ?? $data[0]['thumbnail_small'];

        // fix not secure thumbnails
        return str_replace('http://', 'https://', $thumbnailUrl);
    }
}
