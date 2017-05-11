<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

interface LiipImagineBundleStorageProviderInterface
{
    public function getWebPathWithFilter(MediaItem $mediaItem, string $liipImagineBundleFilter = null): string;
}
