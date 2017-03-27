<?php

namespace Backend\Modules\MediaLibrary\EventListener;

use Backend\Modules\MediaLibrary\Manager\FileManager;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemCreated;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemDeleted;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

/**
 * MediaItem Listener
 */
final class MediaItemListener
{
    /** @var CacheManager */
    protected $cacheManager;

    /** @var FileManager */
    protected $fileManager;

    /**
     * Construct
     *
     * @param FileManager $fileManager
     * @param CacheManager $cacheManager
     */
    public function __construct(
        FileManager $fileManager,
        CacheManager $cacheManager
    ) {
        $this->fileManager = $fileManager;
        $this->cacheManager = $cacheManager;
    }

    /**
     * On MediaItem added
     *
     * @param MediaItemCreated $event
     */
    public function onMediaItemCreated(MediaItemCreated $event)
    {
        /** @var MediaItem $mediaItem */
        $mediaItem = $event->getMediaItem();

        // We have an image, so we have a backend thumbnail to generate
        if ($mediaItem->getType()->isImage()) {
            // Generate Backend thumbnail
            $this->cacheManager->getBrowserPath($mediaItem->getWebPath(), 'media_library_backend_thumbnail');
        }
    }

    /**
     * On MediaItem deleted
     *
     * @param MediaItemDeleted $event
     */
    public function onMediaItemDeleted(MediaItemDeleted $event)
    {
        /** @var MediaItem $mediaItem */
        $mediaItem = $event->getMediaItem();

        // Delete Source file
        $this->fileManager->deleteFile($mediaItem->getAbsolutePath('source'));

        // We have an image, so we have thumbnails to delete
        if ($mediaItem->getType()->isImage()) {
            // Delete all thumbnails
            $this->cacheManager->remove($mediaItem->getWebPath());
        }
    }
}
