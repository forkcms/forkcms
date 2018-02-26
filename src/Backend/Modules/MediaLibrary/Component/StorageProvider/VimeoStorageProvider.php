<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Component\StorageProvider;

use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

class VimeoStorageProvider extends MovieStorageProvider
{
    public function getIncludeHTML(MediaItem $mediaItem): string
    {
        return '<iframe src="' . $this->includeUrl . $mediaItem->getUrl() . '?color=ffffff&title=0&byline=0&portrait=0&badge=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }
}
