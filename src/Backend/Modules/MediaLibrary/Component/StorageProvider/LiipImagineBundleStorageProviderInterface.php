<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

interface LiipImagineBundleStorageProviderInterface
{
    /**
     * @param MediaItem $mediaItem
     * @param string $filter The LiipImagineBundle filter name you want to use.
     *
     * @return string
     */
    public function getWebPathWithFilter(MediaItem $mediaItem, string $filter): string;
}
