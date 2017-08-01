<?php

namespace Backend\Modules\MediaLibrary\EventListener;

use Backend\Modules\MediaLibrary\Builder\MediaFolder\MediaFolderCache;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

/**
 * Invalidate MediaFolder Backend Cache Subscriber
 */
final class MediaFolderInvalidateBackendCacheSubscriber implements EventSubscriber
{
    /**
     * @var MediaFolderCache
     */
    protected $mediaFolderCache;

    public function __construct(MediaFolderCache $mediaFolderCache)
    {
        $this->mediaFolderCache = $mediaFolderCache;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    private function invalidateBackendCacheForMediaFolders(LifecycleEventArgs $eventArgs): void
    {
        if ($eventArgs->getObject() instanceof MediaFolder || $eventArgs->getObject() instanceof MediaItem) {
            $this->mediaFolderCache->delete();
        }
    }

    public function postPersist(LifecycleEventArgs $eventArgs): void
    {
        $this->invalidateBackendCacheForMediaFolders($eventArgs);
    }

    public function postUpdate(LifecycleEventArgs $eventArgs): void
    {
        $this->invalidateBackendCacheForMediaFolders($eventArgs);
    }

    public function postRemove(LifecycleEventArgs $eventArgs): void
    {
        $this->invalidateBackendCacheForMediaFolders($eventArgs);
    }
}
