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

    /**
     * LocalStorageProvider constructor.
     *
     * @param string $folderPath
     * @param string $baseUrl
     * @param string $basePath
     * @param CacheManager $cacheManager
     */
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

    /**
     * @param MediaItem $mediaItem
     * @return string|null
     */
    public function getAbsolutePath(MediaItem $mediaItem): string
    {
        return $mediaItem->getFullUrl() === null ? null : $this->getUploadRootDir() . '/' . $mediaItem->getFullUrl();
    }

    /**
     * @param MediaItem $mediaItem
     * @return string
     */
    public function getAbsoluteWebPath(MediaItem $mediaItem): string
    {
        return $this->baseUrl . $mediaItem->getWebPath();
    }

    /**
     * @param MediaItem $mediaItem
     * @return string|null
     */
    public function getIncludeHTML(MediaItem $mediaItem): string
    {
        if ($mediaItem->getType()->isImage()) {
            return '<img src="' . $mediaItem->getWebPath() . '" title="' . $mediaItem->getTitle() . '" />';
        }

        return '<a href="' . $mediaItem->getWebPath() . '" title="' . $mediaItem->getTitle() . '" target="_blank">' . $mediaItem->getTitle() . '</a>';
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
     * @return string
     */
    public function getUploadRootDir(): string
    {
        return $this->basePath . '/' . $this->folderPath;
    }

    /**
     * @return string
     */
    public function getWebDir(): string
    {
        return '/' . $this->folderPath;
    }

    /**
     * @param MediaItem $mediaItem
     * @return string
     */
    public function getWebPath(MediaItem $mediaItem): string
    {
        return $this->getWebDir() . '/' . $mediaItem->getFullUrl();
    }

    /**
     * @param MediaItem $mediaItem
     * @param string|null $filter The LiipImagineBundle filter name you want to use.
     * @return string
     */
    public function getWebPathWithFilter(MediaItem $mediaItem, string $filter): string
    {
        $webPath = $this->getWebPath($mediaItem);

        if (!$mediaItem->getType()->isImage()) {
            return $webPath;
        }

        if ($filter === 'backend') {
            $filter = 'media_library_backend_thumbnail';
        }

        return $this->cacheManager->getBrowserPath($webPath, $filter);
    }
}
