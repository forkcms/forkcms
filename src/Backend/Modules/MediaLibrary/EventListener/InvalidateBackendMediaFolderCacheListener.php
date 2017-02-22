<?php

namespace Backend\Modules\MediaLibrary\EventListener;

use Backend\Modules\MediaLibrary\Engine\CacheBuilder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Event\MediaFolderEvent;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemEvent;

/**
 * Invalidate Backend MediaFolder Cache Listener
 */
final class InvalidateBackendMediaFolderCacheListener
{
    /**
     * @var CacheBuilder
     */
    protected $cacheBuilder;

    /**
     * @param CacheBuilder $cacheBuilder
     */
    public function __construct(
        CacheBuilder $cacheBuilder
    ) {
        $this->cacheBuilder = $cacheBuilder;
    }

    /**
     * On MediaFolder Event
     *
     * @param MediaFolderEvent $event
     */
    public function onMediaFolderEvent(MediaFolderEvent $event)
    {
        $this->invalidateBackendMediaFolderCache();
    }

    /**
     * On MediaItem Event
     *
     * @param MediaItemEvent $event
     */
    public function onMediaItemEvent(MediaItemEvent $event)
    {
        $this->invalidateBackendMediaFolderCache();
    }

    /**
     * Invalidate Backend Media Folder Cache
     */
    protected function invalidateBackendMediaFolderCache()
    {
        // Delete old cache
        $this->cacheBuilder->deleteCache();

        // Create new cache
        $this->cacheBuilder->createCache();
    }
}
