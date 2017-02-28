<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Event;

use Symfony\Component\EventDispatcher\Event;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

/**
 * MediaItem Event
 */
class MediaItemEvent extends Event
{
    /**
     * @var MediaItem
     */
    protected $mediaItem;

    /**
     * Construct
     *
     * @param MediaItem $mediaItem
     */
    public function __construct(
        MediaItem $mediaItem
    ) {
        $this->mediaItem = $mediaItem;
    }

    /**
     * Get MediaItem
     *
     * @return MediaItem
     */
    public function getMediaItem(): MediaItem
    {
        return $this->mediaItem;
    }
}
