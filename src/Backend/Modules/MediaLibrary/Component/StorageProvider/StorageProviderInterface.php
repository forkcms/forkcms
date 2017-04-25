<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

interface StorageProviderInterface
{
    /**
     * @param MediaItem $mediaItem
     * @return string
     */
    public function getAbsolutePath(MediaItem $mediaItem): string;

    /**
     * @param MediaItem $mediaItem
     * @return string
     */
    public function getAbsoluteWebPath(MediaItem $mediaItem): string;

    /**
     * @param MediaItem $mediaItem
     * @return string
     */
    public function getIncludeHTML(MediaItem $mediaItem): string;

    /**
     * @param MediaItem $mediaItem
     * @return string
     */
    public function getLinkHTML(MediaItem $mediaItem): string;

    /**
     * @param MediaItem $mediaItem
     * @return string
     */
    public function getWebPath(MediaItem $mediaItem): string;
}
