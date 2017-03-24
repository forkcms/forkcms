<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

class YoutubeStorageProvider implements StorageProviderInterface
{
    /** @var string */
    protected $linkURL;

    /** @var string */
    protected $includeURL;

    /**
     * YoutubeStorageProvider constructor.
     *
     * @param string $linkURL
     * @param string $includeURL
     */
    public function __construct(string $linkURL, string $includeURL)
    {
        $this->linkURL = $linkURL;
        $this->includeURL = $includeURL;
    }

    /**
     * @param MediaItem $mediaItem
     * @param string|null $subDirectory
     * @return string|null
     */
    public function getAbsolutePath(MediaItem $mediaItem, string $subDirectory = null): string
    {
        return $this->getAbsoluteWebPath($mediaItem, $subDirectory);
    }

    /**
     * @param MediaItem $mediaItem
     * @param string|null $subDirectory
     * @return string
     */
    public function getAbsoluteWebPath(MediaItem $mediaItem, string $subDirectory = null): string
    {
        return $this->getWebPath($mediaItem);
    }

    /**
     * @param MediaItem $mediaItem
     * @return string|null
     */
    public function getIncludeHTML(MediaItem $mediaItem, string $subDirectory = null): string
    {
        return '<iframe width="560" height="315" src="' . $this->includeURL . $mediaItem->getUrl() . '" frameborder="0" allowfullscreen></iframe>';
    }

    /**
     * @param MediaItem $mediaItem
     * @return string|null
     */
    public function getLinkHTML(MediaItem $mediaItem): string
    {
        return '<a href="' . $this->getAbsoluteWebPath($mediaItem) . '" title="' . $mediaItem->getTitle() . '" target="_blank">' . $mediaItem->getTitle() . '</a>';
    }

    /**
     * @param MediaItem $mediaItem
     * @param string|null $subDirectory
     * @return string|null
     */
    public function getWebPath(MediaItem $mediaItem, string $subDirectory = null): string
    {
        return $this->linkURL . $mediaItem->getUrl();
    }
}
