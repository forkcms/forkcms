<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class LocalStorageProvider implements LocalStorageProviderInterface
{
    /** @var string */
    protected $basePath;

    /** @var string */
    protected $baseUrl;

    /** @var CacheManager */
    protected $cacheManager;

    /** @var string */
    protected $folderPath;

    public function __construct(
        string $folderPath,
        string $baseUrl,
        string $basePath,
        CacheManager $cacheManager
    ) {
        $this->folderPath = trim($folderPath, '/\\');
        $this->baseUrl = $baseUrl;
        $this->basePath = $basePath;
        $this->cacheManager = $cacheManager;
    }

    public function getAbsolutePath(MediaItem $mediaItem): string
    {
        return $this->getUploadRootDir() . '/' . $mediaItem->getFullUrl();
    }

    public function getAbsoluteWebPath(MediaItem $mediaItem): string
    {
        return $this->baseUrl . $mediaItem->getWebPath();
    }

    public function getIncludeHTML(MediaItem $mediaItem): string
    {
        if ($mediaItem->getType()->isImage()) {
            return '<img src="' . $mediaItem->getWebPath() . '" title="' . $mediaItem->getTitle() . '" />';
        }

        return '<a href="' . $mediaItem->getWebPath() . '" title="' . $mediaItem->getTitle() . '">' . $mediaItem->getTitle() . '</a>';
    }

    public function getLinkHTML(MediaItem $mediaItem): string
    {
        return '<a href="' . $this->getAbsoluteWebPath($mediaItem) . '" title="' . $mediaItem->getTitle() . '">' . $mediaItem->getTitle() . '</a>';
    }

    public function getUploadRootDir(): string
    {
        return $this->basePath . '/' . $this->folderPath;
    }

    public function getWebDir(): string
    {
        return '/' . $this->folderPath;
    }

    public function getWebPath(MediaItem $mediaItem): string
    {
        return $this->getWebDir() . '/' . $mediaItem->getFullUrl();
    }

    public function getThumbnail(MediaItem $mediaItem): string
    {
        return $this->getWebPath($mediaItem);
    }

    public function getWebPathWithFilter(MediaItem $mediaItem, string $liipImagineBundleFilter = null): string
    {
        $webPath = $this->getWebPath($mediaItem);

        if ($liipImagineBundleFilter === null || !$mediaItem->getType()->isImage()) {
            return $webPath;
        }

        if ($liipImagineBundleFilter === 'backend') {
            $liipImagineBundleFilter = 'media_library_backend_thumbnail';
        }

        return $this->cacheManager->getBrowserPath($webPath, $liipImagineBundleFilter);
    }
}
