<?php

namespace Backend\Modules\MediaLibrary\EventListener;

use Backend\Modules\MediaLibrary\Manager\FileManager;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use SimpleBus\Message\Bus\MessageBus;

/**
 * MediaItem Subscriber
 */
final class MediaItemSubscriber implements EventSubscriber
{
    /** @var CacheManager */
    protected $cacheManager;

    /** @var MessageBus */
    protected $commandBus;

    /** @var FileManager */
    protected $fileManager;

    /**
     * Construct
     *
     * @param FileManager $fileManager
     * @param CacheManager $cacheManager
     * @param MessageBus $commandBus
     */
    public function __construct(
        FileManager $fileManager,
        CacheManager $cacheManager,
        MessageBus $commandBus
    ) {
        $this->fileManager = $fileManager;
        $this->cacheManager = $cacheManager;
        $this->commandBus = $commandBus;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return array(
            Events::postPersist,
            Events::postRemove,
        );
    }

    /**
     * @param MediaItem $mediaItem
     */
    private function deleteSource(MediaItem $mediaItem)
    {
        $this->fileManager->deleteFile($mediaItem->getAbsolutePath());
    }

    /**
     * @param MediaItem $mediaItem
     */
    private function deleteThumbnails(MediaItem $mediaItem)
    {
        // We have an image, so we have thumbnails to delete
        if ($mediaItem->getType()->isImage()) {
            // Delete all thumbnails
            $this->cacheManager->remove($mediaItem->getWebPath());
        }
    }

    /**
     * @param MediaItem $mediaItem
     */
    private function generateThumbnails(MediaItem $mediaItem)
    {
        // We have an image, so we have a backend thumbnail to generate
        if ($mediaItem->getType()->isImage()) {
            // Generate Backend thumbnail
            $this->cacheManager->getBrowserPath($mediaItem->getWebPath(), 'media_library_backend_thumbnail');
        }
    }

    /**
     * On MediaItem added
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();
        if (!$entity instanceof MediaItem) {
            return;
        }

        $this->generateThumbnails($entity);
    }

    /**
     * On MediaItem deleted
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postRemove(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();
        if (!$entity instanceof MediaItem) {
            return;
        }

        $this->deleteSource($entity);
        $this->deleteThumbnails($entity);
    }
}
