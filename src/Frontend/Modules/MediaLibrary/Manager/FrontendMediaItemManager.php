<?php

namespace Frontend\Modules\MediaLibrary\Manager;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Frontend\Modules\MediaLibrary\Event\FrontendMediaItemResolutionMissingEvent;
use Frontend\Modules\MediaLibrary\Component\FrontendResolution;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

/**
 * FrontendMediaItem Manager
 */
class FrontendMediaItemManager
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var array
     */
    protected static $cachedFolderNames;

    /**
     * Construct
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Generate thumbnail if not exists
     *
     * @param MediaItem $mediaItem
     * @param FrontendResolution $frontendMediaItemResolution
     */
    public function generateThumbnailIfNotExists(
        MediaItem $mediaItem,
        FrontendResolution $frontendMediaItemResolution
    ) {
        // We check in the $generatedThumbnails array if it exists, if not create it
        $generate = true;

        // Define folder name
        $folderName = $frontendMediaItemResolution->getImageSettings()->toString();

        // Media id is in cache
        if (isset(self::$cachedFolderNames[(string) $mediaItem->getId()])
            && in_array(
                $folderName,
                self::$cachedFolderNames[(string) $mediaItem->getId()]
        )) {
            // Redefine generate
            $generate = false;
        // We check on the server for existence
        } else {
            $fs = new Filesystem();
            if ($fs->exists($mediaItem->getWebPath())) {
                // Don't generate
                $generate = false;

                // Add to cache
                self::$cachedFolderNames[(string) $mediaItem->getId()][] = $folderName;
            }
        }

        // We must generate thumbnail
        if ($generate) {
            // Dispatch event
            $this->eventDispatcher->dispatch(
                FrontendMediaItemResolutionMissingEvent::EVENT_NAME,
                new FrontendMediaItemResolutionMissingEvent(
                    $mediaItem,
                    $frontendMediaItemResolution
                )
            );
        }
    }
}
