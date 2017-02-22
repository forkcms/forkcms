<?php

namespace Backend\Modules\MediaLibrary\EventListener;

use Backend\Modules\MediaLibrary\Manager\FileManager;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemDeleted;

/**
 * MediaItem Source Listener
 */
final class MediaItemSourceListener
{
    /**
     * @var FileManager
     */
    protected $fileManager;

    /**
     * Construct
     *
     * @param FileManager $fileManager
     */
    public function __construct(
        FileManager $fileManager
    ) {
        $this->fileManager = $fileManager;
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

        // Delete Source file if exists
        $this->fileManager->deleteFile($mediaItem->getAbsolutePath());
    }
}
