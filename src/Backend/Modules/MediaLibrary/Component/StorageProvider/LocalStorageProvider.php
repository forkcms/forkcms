<?php

namespace Backend\Modules\MediaLibrary\Component\StorageProvider;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

class LocalStorageProvider implements StorageProviderInterface
{
    /** @var string */
    protected $basePath;

    /** @var string */
    protected $baseUrl;

    /** @var string */
    protected $folderPath;

    /**
     * LocalStorageProvider constructor.
     *
     * @param string $folderPath
     * @param string $baseUrl
     * @param string $basePath
     */
    public function __construct(
        string $folderPath,
        string $baseUrl,
        string $basePath
    ) {
        $this->folderPath = trim($folderPath, '/\\');
        $this->baseUrl = $baseUrl;
        $this->basePath = $basePath;
    }

    /**
     * @param MediaItem $mediaItem
     * @param string|null $subDirectory
     * @return string|null
     */
    public function getAbsolutePath(MediaItem $mediaItem, string $subDirectory = null): string
    {
        return $mediaItem->getFullUrl() === null ? null : $this->getUploadRootDir($subDirectory) . '/' . $mediaItem->getFullUrl();
    }

    /**
     * @param MediaItem $mediaItem
     * @param string|null $subDirectory
     * @return string
     */
    public function getAbsoluteWebPath(MediaItem $mediaItem, string $subDirectory = null): string
    {
        return $this->baseUrl . $mediaItem->getWebPath($subDirectory);
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
     * @param string|null $subDirectory
     * @return string
     */
    protected static function getSubdirectory(string $subDirectory = null): string
    {
        if ($subDirectory === null || $subDirectory === 'source') {
            return 'Source';
        } elseif (strtolower($subDirectory) === 'backend') {
            return 'Backend';
        } elseif (strtolower($subDirectory) === 'frontend') {
            return 'Frontend';
        } else {
            return 'Frontend/' . $subDirectory;
        }
    }

    /**
     * @param string|null $subDirectory
     * @return string
     */
    public function getUploadRootDir(string $subDirectory = null): string
    {
        $subDirectory = $this->getSubdirectory($subDirectory);
        $parentUploadRootDir = $this->basePath . '/' . $this->folderPath;

        // the absolute directory path where uploaded
        // documents should be saved
        if ($subDirectory !== null) {
            return $parentUploadRootDir . '/' . $subDirectory;
        }

        return $parentUploadRootDir;
    }

    /**
     * @param string|null $subDirectory
     * @return string
     */
    public function getWebDir(string $subDirectory = null): string
    {
        $subDirectory = $this->getSubdirectory($subDirectory);

        $webPath = $this->folderPath . '/';
        if ($subDirectory !== null) {
            $webPath .= $subDirectory . '/';
        }

        return $webPath;
    }

    /**
     * @param MediaItem $mediaItem
     * @param string|null $subDirectory
     * @return string|null
     */
    public function getWebPath(MediaItem $mediaItem, string $subDirectory = null)
    {
        return '/' . $this->getWebDir($subDirectory) . $mediaItem->getFullUrl();
    }
}
