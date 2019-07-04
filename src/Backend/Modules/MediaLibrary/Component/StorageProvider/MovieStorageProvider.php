<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

class MovieStorageProvider implements StorageProviderInterface
{
    /** @var string */
    protected $linkUrl;

    /** @var string */
    protected $includeUrl;

    public function __construct(string $linkUrl, string $includeUrl)
    {
        $this->linkUrl = $linkUrl;
        $this->includeUrl = $includeUrl;
    }

    public function getAbsolutePath(MediaItem $mediaItem): string
    {
        return $this->getAbsoluteWebPath($mediaItem);
    }

    public function getAbsoluteWebPath(MediaItem $mediaItem): string
    {
        return $this->getWebPath($mediaItem);
    }

    public function getIncludeHTML(MediaItem $mediaItem): string
    {
        return '<iframe src="' . $this->includeUrl . $mediaItem->getUrl() . ' width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }

    public function getLinkHTML(MediaItem $mediaItem): string
    {
        return '<a href="' . $this->getAbsoluteWebPath($mediaItem) . '" title="' . $mediaItem->getTitle() . '">' . $mediaItem->getTitle() . '</a>';
    }

    public function getWebPath(MediaItem $mediaItem): string
    {
        return $this->linkUrl . $mediaItem->getUrl();
    }

    public function getThumbnail(MediaItem $mediaItem): string
    {
        return $this->getWebPath($mediaItem);
    }
}
