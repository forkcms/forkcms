<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder\Event;

/**
 * MediaFolder Deleted Event
 */
final class MediaFolderDeleted extends MediaFolderEvent
{
    /**
     * @var string The name the listener needs to listen to to catch this event.
     */
    const EVENT_NAME = 'media_library.event.media_folder_deleted';
}
