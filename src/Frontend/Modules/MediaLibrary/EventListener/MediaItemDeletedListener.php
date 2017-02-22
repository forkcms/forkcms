<?php

namespace Frontend\Modules\MediaLibrary\EventListener;

use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;
use Backend\Modules\MediaLibrary\Manager\FileManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemDeleted;

/**
 * MediaItem Deleted Listener
 * This will automatically delete the generated images from the frontend.
 */
final class MediaItemDeletedListener
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
     * @return mixed
     */
    public function onMediaItemDeleted(MediaItemDeleted $event)
    {
        /** @var MediaItem $mediaItem */
        $mediaItem = $event->getMediaItem();

        // We have an image
        if ($mediaItem->getType() == Type::IMAGE) {
            // Init finder
            $finder = new Finder();
            $fs = new Filesystem();

            $frontendPath = MediaItem::getUploadRootDir('frontend');

            // Folder not exists (this can happen in the beginning), stop here
            if (!$fs->exists($frontendPath)) {
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
    }
}
