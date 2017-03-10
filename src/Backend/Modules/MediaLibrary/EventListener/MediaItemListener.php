<?php

namespace Backend\Modules\MediaLibrary\EventListener;

use Backend\Modules\MediaLibrary\Component\ImageSettings;
use Backend\Modules\MediaLibrary\Component\ImageTransformationMethod;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemBackendThumbnailCreated;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemFrontendThumbnailCreated;
use Backend\Modules\MediaLibrary\Manager\FileManager;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemCreated;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemDeleted;
use Common\ModulesSettings;
use Frontend\Modules\MediaLibrary\Event\FrontendMediaItemResolutionMissingEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

/**
 * MediaItem Listener
 */
final class MediaItemListener
{
    /** @var FileManager */
    protected $fileManager;

    /** @var ModulesSettings */
    protected $settings;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * Construct
     *
     * @param FileManager $fileManager
     * @param ModulesSettings $settings
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        FileManager $fileManager,
        ModulesSettings $settings,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->fileManager = $fileManager;
        $this->settings = $settings;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * On MediaItem added
     *
     * @param MediaItemCreated $event
     */
    public function onMediaItemCreated(MediaItemCreated $event)
    {
        // Generate Backend thumbnail
        $this->generateThumbnail(
            $event->getMediaItem(),
            ImageSettings::create(
                ImageTransformationMethod::crop(),
                $this->settings->get('MediaLibrary', 'backend_thumbnail_width'),
                $this->settings->get('MediaLibrary', 'backend_thumbnail_height'),
                $this->settings->get('MediaLibrary', 'backend_thumbnail_quality')
            ),
            MediaItem::getUploadRootDir('backend') . '/' . $event->getMediaItem()->getScardingFolderName()
        );

        // We dispatch an event, so other modules can hook into this
        $this->eventDispatcher->dispatch(
            MediaItemBackendThumbnailCreated::EVENT_NAME,
            new MediaItemBackendThumbnailCreated($event->getMediaItem())
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

        // Delete Source file
        $this->fileManager->deleteFile($mediaItem->getAbsolutePath('source'));

        // We have an image, so we have thumbnails to delete
        if ($mediaItem->getType()->isImage()) {
            // Delete Backend thumbnail
            $this->fileManager->deleteFile($mediaItem->getAbsolutePath('backend'));

            // Delete Frontend thumbnails
            $this->deleteFrontendThumbnails($mediaItem);
        }
    }

    /**
     * On MediaItem requested missing frontend resolution
     *
     * @param FrontendMediaItemResolutionMissingEvent $event
     */
    public function onMediaItemRequestedMissingFrontendResolution(FrontendMediaItemResolutionMissingEvent $event)
    {
        // Define frontend thumbnail path
        $path = MediaItem::getUploadRootDir('frontend')
            . '/' . $event->getResolution()->getImageSettings()->toString()
            . '/' . $event->getMediaItem()->getScardingFolderName();

        // Generate Frontend thumbnail
        $this->generateThumbnail(
            $event->getMediaItem(),
            $event->getResolution()->getImageSettings(),
            $path
        );

        // We dispatch an event, so other modules can hook into this
        $this->eventDispatcher->dispatch(
            MediaItemFrontendThumbnailCreated::EVENT_NAME,
            new MediaItemFrontendThumbnailCreated(
                $event->getMediaItem(),
                $path
            )
        );
    }

    /**
     * Delete Frontend thumbnails
     *
     * @param MediaItem $mediaItem
     * @return bool|void
     */
    private function deleteFrontendThumbnails(MediaItem $mediaItem)
    {
        // Init finder
        $finder = new Finder();
        $frontendPath = MediaItem::getUploadRootDir('frontend');

        // Folder not exists (this can happen in the beginning), stop here
        if (!$this->fileManager->exists($frontendPath)) {
            return false;
        }

        // Loop all folders
        foreach ($finder->directories()->in($frontendPath) as $folder) {
            // Define fileName
            $fileName = $folder . '/' . $mediaItem->getFullUrl();

            // Delete Frontend generated image if exists
            $this->fileManager->deleteFile(
                $fileName
            );
        }
    }

    /**
     * Generate thumbnail
     *
     * @param MediaItem $mediaItem
     * @param ImageSettings $imageSettings
     * @param string $destinationPath
     * @return bool|void
     */
    private function generateThumbnail(
        MediaItem $mediaItem,
        ImageSettings $imageSettings,
        string $destinationPath
    ) {
        // Stop if we don't have an image
        if (!$mediaItem->getType()->isImage()) {
            return false;
        }

        // Generate thumbnail
        $this->fileManager->generateThumbnail(
            $mediaItem->getUrl(),
            MediaItem::getUploadRootDir() . '/' . $mediaItem->getScardingFolderName(),
            $destinationPath,
            $imageSettings
        );
    }
}
