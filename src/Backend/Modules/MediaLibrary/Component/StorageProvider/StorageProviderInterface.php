<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

interface StorageProviderInterface
{
    /**
     * @param MediaItem $mediaItem
     * @param string|null $subDirectory
     * @return string|null
     */
    public function getAbsolutePath(MediaItem $mediaItem, string $subDirectory = null): string;

    /**
     * @param MediaItem $mediaItem
     * @param string|null $subDirectory
     * @return string
     */
    public function getAbsoluteWebPath(MediaItem $mediaItem, string $subDirectory = null): string;

    /**
     * @param MediaItem $mediaItem
     * @return string|null
     */
    public function getIncludeHTML(MediaItem $mediaItem): string;

    /**
     * @param MediaItem $mediaItem
     * @return string|null
     */
    public function getLinkHTML(MediaItem $mediaItem): string;

    /**
     * @param MediaItem $mediaItem
     * @param string|null $subDirectory
     * @return string|null
     */
    public function getWebPath(MediaItem $mediaItem, string $subDirectory = null);
}
