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

    public function __construct(
        FileManager $fileManager,
        CacheManager $cacheManager,
        MessageBus $commandBus
    ) {
        $this->fileManager = $fileManager;
        $this->cacheManager = $cacheManager;
        $this->commandBus = $commandBus;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
        ];
    }

    private function deleteSource(MediaItem $mediaItem): void
    {
        $this->fileManager->deleteFile($mediaItem->getAbsolutePath());
    }

    private function deleteThumbnails(MediaItem $mediaItem): void
    {
        // We have an image, so we have thumbnails to delete
        if ($mediaItem->getType()->isImage()) {
            // Delete all thumbnails
            $this->cacheManager->remove($mediaItem->getWebPath());
        }
    }

    private function generateThumbnails(MediaItem $mediaItem): void
    {
        // We have an image, so we have a backend thumbnail to generate
        if ($mediaItem->getType()->isImage()) {
            // Generate Backend thumbnail
            $this->cacheManager->getBrowserPath($mediaItem->getWebPath(), 'media_library_backend_thumbnail');
        }
    }

    public function postPersist(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();
        if (!$entity instanceof MediaItem) {
            return;
        }

        $this->generateThumbnails($entity);
    }

    public function postRemove(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();
        if (!$entity instanceof MediaItem) {
            return;
        }

        $this->deleteSource($entity);
        $this->deleteThumbnails($entity);
    }
}
