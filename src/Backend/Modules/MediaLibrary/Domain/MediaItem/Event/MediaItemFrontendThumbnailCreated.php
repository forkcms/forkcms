<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Event;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Frontend\Modules\MediaLibrary\Component\FrontendResolution;
use Frontend\Modules\MediaLibrary\Event\FrontendMediaItemResolutionMissingEvent;

/**
 * MediaItem Frontend thumbnail Created Event
 */
final class MediaItemFrontendThumbnailCreated extends MediaItemEvent
{
    /**
     * @var string The name the listener needs to listen to to catch this event.
     */
    const EVENT_NAME = 'media_library.event.media_item_frontend_thumbnail_created';

    /** @var FrontendResolution */
    protected $frontendResolution;

    /**
     * MediaItemFrontendThumbnailCreated constructor.
     *
     * @param FrontendMediaItemResolutionMissingEvent $event
     */
    public function __construct(FrontendMediaItemResolutionMissingEvent $event)
    {
        parent::__construct($event->getMediaItem());
        $this->frontendResolution = $event->getResolution();
    }

    /**
     * @return FrontendResolution
     */
    public function getFrontendResolution(): FrontendResolution
    {
        return $this->frontendResolution;
    }
}
