<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

final class CreateMediaItemFromSource
{
    /** @var string */
    public $source;

    /** @var MediaFolder */
    public $mediaFolder;

    /** @var integer */
    public $userId;

    /** @var MediaItem */
    private $mediaItem;

    /**
     * CreateMediaItemFromSource constructor.
     *
     * @param $source
     * @param MediaFolder $mediaFolder
     * @param int $userId
     * @throws \Exception
     */
    public function __construct(
        $source,
        MediaFolder $mediaFolder,
        $userId = 0
    ) {
        $this->source = $source;
        $this->mediaFolder = $mediaFolder;
        $this->userId = $userId;
    }

    /**
     * @return MediaItem
     */
    public function getMediaItem()
    {
        return $this->mediaItem;
    }

    /**
     * @param MediaItem $mediaItem
     */
    public function setMediaItem($mediaItem)
    {
        $this->mediaItem = $mediaItem;
    }
}
