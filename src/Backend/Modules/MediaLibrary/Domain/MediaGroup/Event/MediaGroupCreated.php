<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup\Event;

/**
 * MediaGroup Created Event
 */
final class MediaGroupCreated extends MediaGroupEvent
{
    /**
     * @var string The name the listener needs to listen to to catch this event.
     */
    const EVENT_NAME = 'media_library.event.media_group_created';
}
