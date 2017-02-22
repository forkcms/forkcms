<?php

namespace Frontend\Modules\MediaLibrary\Event;

use Symfony\Component\EventDispatcher\Event;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Frontend\Modules\MediaLibrary\Component\FrontendResolution;

/**
 * Frontend MediaItem Resolution Missing Event
 */
final class FrontendMediaItemResolutionMissingEvent extends Event
{
    /**
     * @var string The name the listener needs to listen to to catch this event.
     */
    const EVENT_NAME = 'media_item.frontend_resolution_missing';

    /**
     * @var MediaItem
     */
    protected $mediaItem;

    /**
     * @var FrontendResolution
     */
    protected $resolution;

    /**
     * Construct
     *
     * @param MediaItem $mediaItem
     * @param FrontendResolution $resolution
     */
    public function __construct(
        MediaItem $mediaItem,
        FrontendResolution $resolution
    ) {
        $this->mediaItem = $mediaItem;
        $this->resolution = $resolution;
    }

    /**
     * Get MediaItem
     *
     * @return MediaItem
     */
    public function getMediaItem()
    {
        return $this->mediaItem;
    }

    /**
     * Get resolution
     *
     * @return FrontendResolution
     */
    public function getResolution()
    {
        return $this->resolution;
    }
}
