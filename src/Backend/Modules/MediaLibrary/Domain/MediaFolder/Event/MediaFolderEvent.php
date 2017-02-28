<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder\Event;

use Symfony\Component\EventDispatcher\Event;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;

/**
 * MediaFolder Event
 */
class MediaFolderEvent extends Event
{
    /**
     * @var MediaFolder
     */
    protected $mediaFolder;

    /**
     * Construct
     *
     * @param MediaFolder $mediaFolder
     */
    public function __construct(MediaFolder $mediaFolder) {
        $this->mediaFolder = $mediaFolder;
    }

    /**
     * Get MediaFolder
     *
     * @return MediaFolder
     */
    public function getMediaFolder(): MediaFolder
    {
        return $this->mediaFolder;
    }
}
