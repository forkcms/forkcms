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

    /** @var int */
    public $userId;

    /** @var MediaItem */
    private $mediaItem;

    /**
     * CreateMediaItemFromSource constructor.
     *
     * @param string $source
     * @param MediaFolder $mediaFolder
     * @param int $userId
     * @throws \Exception
     */
    public function __construct(
        string $source,
        MediaFolder $mediaFolder,
        int $userId = 0
    ) {
        $this->source = $source;
        $this->mediaFolder = $mediaFolder;
        $this->userId = $userId;
    }

    /**
     * @return MediaItem
     */
    public function getMediaItem(): MediaItem
    {
        return $this->mediaItem;
    }

    /**
     * @param MediaItem $mediaItem
     */
    public function setMediaItem(MediaItem $mediaItem)
    {
        $this->mediaItem = $mediaItem;
    }
}
