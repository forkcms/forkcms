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

    /**
     * @param MediaFolderCache $mediaFolderCache
     */
    public function __construct(MediaFolderCache $mediaFolderCache)
    {
        $this->mediaFolderCache = $mediaFolderCache;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return array(
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        );
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    private function invalidateBackendCacheForMediaFolders(LifecycleEventArgs $eventArgs)
    {
        if ($eventArgs->getObject() instanceof MediaFolder || $eventArgs->getObject() instanceof MediaItem) {
            $this->mediaFolderCache->delete();
        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $this->invalidateBackendCacheForMediaFolders($eventArgs);
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->invalidateBackendCacheForMediaFolders($eventArgs);
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postRemove(LifecycleEventArgs $eventArgs)
    {
        $this->invalidateBackendCacheForMediaFolders($eventArgs);
    }
}
