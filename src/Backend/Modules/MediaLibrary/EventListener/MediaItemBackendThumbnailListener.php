<?php

namespace Backend\Modules\MediaLibrary\EventListener;

use Backend\Modules\MediaLibrary\Component\ImageSettings;
use Backend\Modules\MediaLibrary\Component\ImageTransformationMethod;
use Backend\Modules\MediaLibrary\Manager\FileManager;
use Backend\Modules\MediaLibrary\Engine\Model as BackendMediaLibraryModel;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemCreated;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemDeleted;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemUpdated;

/**
 * MediaItem Backend Thumbnail Listener
 */
final class MediaItemBackendThumbnailListener
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
     * On MediaItem added
     *
     * @param MediaItemCreated $event
     */
    public function onMediaItemCreated(MediaItemCreated $event)
    {
        // Generate Backend thumbnail
        $this->generateBackendThumbnail(
            $event->getMediaItem()
        );
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

        // We have an image
        if ($mediaItem->getType()->isImage()) {
            // Delete Backend image
            $this->fileManager->deleteFile($mediaItem->getWebPath('backend'));
        }
    }

    /**
     * On MediaItem updated
     *
     * @param MediaItemUpdated $event
     */
    public function onMediaItemUpdated(MediaItemUpdated $event)
    {
        // Generate Backend thumbnail
        $this->generateBackendThumbnail(
            $event->getMediaItem()
        );
    }

    /**
     * @param MediaItem $mediaItem
     * @return bool|void
     */
    protected function generateBackendThumbnail(
        MediaItem $mediaItem
    ) {
        // Stop if we don't have an image
        if (!$mediaItem->getType()->isImage()) {
            return false;
        }

        $sourcePath = MediaItem::getUploadRootDir() . '/' . $mediaItem->getScardingFolderName();
        $destinationPath = MediaItem::getUploadRootDir('backend') . '/' . $mediaItem->getScardingFolderName();

        /** @var ImageSettings $imageSettings */
        $imageSettings = ImageSettings::create(
            ImageTransformationMethod::crop(),
            BackendMediaLibraryModel::BACKEND_THUMBNAIL_WIDTH,
            BackendMediaLibraryModel::BACKEND_THUMBNAIL_HEIGHT,
            BackendMediaLibraryModel::BACKEND_THUMBNAIL_QUALITY
        );

        // Generate thumbnail
        $this->fileManager->generateThumbnail(
            $mediaItem->getUrl(),
            $sourcePath,
            $destinationPath,
            $imageSettings
        );
    }
}
