<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Event;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

/**
 * MediaItem Frontend thumbnail Created Event
 */
final class MediaItemFrontendThumbnailCreated extends MediaItemEvent
{
    /**
     * @var string The name the listener needs to listen to to catch this event.
     */
    const EVENT_NAME = 'media_library.event.media_item_frontend_thumbnail_created';

    /** @var string */
    protected $path;

    /**
     * MediaItemFrontendThumbnailCreated constructor.
     *
     * @param MediaItem $mediaItem
     * @param string $path
     */
    public function __construct(MediaItem $mediaItem, string $path)
    {
        parent::__construct($mediaItem);
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
