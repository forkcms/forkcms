<?php

namespace Frontend\Modules\MediaLibrary\EventListener;

use Backend\Modules\MediaLibrary\Manager\FileManager;
use Frontend\Modules\MediaLibrary\Event\FrontendMediaItemResolutionMissingEvent;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Frontend\Modules\MediaLibrary\Component\FrontendResolution;

/**
 * Frontend MediaItem Resolution missing Listener
 * This will automatically create a new thumbnail for the frontend.
 */
final class FrontendMediaItemResolutionMissingListener
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
     * On MediaItem requested missing frontend resolution
     *
     * @param FrontendMediaItemResolutionMissingEvent $event
     */
    public function onMediaItemRequestedMissingFrontendResolution(
        FrontendMediaItemResolutionMissingEvent $event
    ) {
        /** @var MediaItem $mediaItem */
        $mediaItem = $event->getMediaItem();

        // Generate Frontend thumbnail
        $this->generateFrontendThumbnail(
            $mediaItem,
            $event->getResolution()
        );
    }

    /**
     * Generate thumbnail
     *
     * @param MediaItem $mediaItem
     * @param FrontendResolution $resolution
     */
    protected function generateFrontendThumbnail(
        MediaItem $mediaItem,
        FrontendResolution $resolution
    ) {
        $sourcePath = MediaItem::getUploadRootDir() . '/' . $mediaItem->getScardingFolderName();
        $destinationPath = MediaItem::getUploadRootDir('frontend')
            . '/' . $resolution->getImageSettings()->toString()
            . '/' . $mediaItem->getScardingFolderName();

        // Generate thumbnail
        $this->fileManager->generateThumbnail(
            $mediaItem->getUrl(),
            $sourcePath,
            $destinationPath,
            $resolution->getImageSettings()
        );
    }
}
