<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

interface StorageProviderInterface
{
    public function getAbsolutePath(MediaItem $mediaItem): string;

    public function getAbsoluteWebPath(MediaItem $mediaItem): string;

    public function getIncludeHTML(MediaItem $mediaItem): string;

    public function getLinkHTML(MediaItem $mediaItem): string;

    public function getWebPath(MediaItem $mediaItem): string;
}
