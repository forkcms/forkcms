<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Event;

/**
 * MediaItem Created Event
 */
final class MediaItemCreated extends MediaItemEvent
{
    /**
     * @var string The name the listener needs to listen to to catch this event.
     */
    const EVENT_NAME = 'media_library.event.media_item_created';
}
