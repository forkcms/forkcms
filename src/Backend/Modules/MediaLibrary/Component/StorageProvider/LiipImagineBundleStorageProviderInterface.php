<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Component\StorageProvider;

use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

interface LiipImagineBundleStorageProviderInterface
{
    public function getWebPathWithFilter(MediaItem $mediaItem, string $liipImagineBundleFilter = null): string;
}
