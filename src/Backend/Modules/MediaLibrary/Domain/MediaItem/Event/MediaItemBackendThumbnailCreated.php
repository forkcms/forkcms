<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Event;

/**
 * MediaItem Backend thumbnail Created Event
 */
final class MediaItemBackendThumbnailCreated extends MediaItemEvent
{
    /**
     * @var string The name the listener needs to listen to to catch this event.
     */
    const EVENT_NAME = 'media_library.event.media_item_backend_thumbnail_created';
}
