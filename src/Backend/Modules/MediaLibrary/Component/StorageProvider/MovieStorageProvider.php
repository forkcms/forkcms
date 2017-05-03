<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

class MovieStorageProvider implements StorageProviderInterface
{
    /** @var string */
    protected $linkURL;

    /** @var string */
    protected $includeURL;

    public function __construct(string $linkURL, string $includeURL)
    {
        $this->linkURL = $linkURL;
        $this->includeURL = $includeURL;
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
        return '<iframe src="' . $this->includeURL . $mediaItem->getUrl() . ' width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }

    public function getLinkHTML(MediaItem $mediaItem): string
    {
        return '<a href="' . $this->getAbsoluteWebPath($mediaItem) . '" title="' . $mediaItem->getTitle() . '" target="_blank">' . $mediaItem->getTitle() . '</a>';
    }

    public function getWebPath(MediaItem $mediaItem): string
    {
        return $this->linkURL . $mediaItem->getUrl();
    }
}
