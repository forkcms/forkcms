<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup\Event;

use Symfony\Component\EventDispatcher\Event;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;

/**
 * MediaGroup Event
 */
class MediaGroupEvent extends Event
{
    /**
     * @var MediaGroup
     */
    protected $mediaGroup;

    /**
     * Construct
     *
     * @param MediaGroup $mediaGroup
     */
    public function __construct(MediaGroup $mediaGroup) {
        $this->mediaGroup = $mediaGroup;
    }

    /**
     * Get MediaGroup
     *
     * @return MediaGroup
     */
    public function getMediaGroup(): MediaGroup
    {
        return $this->mediaGroup;
    }
}
