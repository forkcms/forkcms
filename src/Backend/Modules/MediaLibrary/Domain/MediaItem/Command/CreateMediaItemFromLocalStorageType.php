<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Command;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

final class CreateMediaItemFromLocalStorageType
{
    /** @var string */
    public $path;

    /** @var MediaFolder */
    public $mediaFolder;

    /** @var int */
    public $userId;

    /** @var MediaItem */
    private $mediaItem;

    /**
     * CreateMediaItemFromStorageType constructor.
     *
     * @param string $path
     * @param MediaFolder $mediaFolder
     * @param int $userId
     * @throws \Exception
     */
    public function __construct(
        string $path,
        MediaFolder $mediaFolder,
        int $userId = 0
    ) {
        $this->path = $path;
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
