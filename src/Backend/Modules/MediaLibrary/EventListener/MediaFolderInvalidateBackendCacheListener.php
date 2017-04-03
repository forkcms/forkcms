<?php

namespace Backend\Modules\MediaLibrary\EventListener;

use Backend\Modules\MediaLibrary\Builder\MediaFolder\MediaFolderCache;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Event\MediaFolderEvent;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemEvent;

/**
 * Invalidate MediaFolder Backend Cache Listener
 */
final class MediaFolderInvalidateBackendCacheListener
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
        $this->mediaFolderCache->delete();
    }
}
