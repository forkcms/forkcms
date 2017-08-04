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

    public function __construct(
        string $path,
        MediaFolder $mediaFolder,
        int $userId = 0
    ) {
        $this->path = $path;
        $this->mediaFolder = $mediaFolder;
        $this->userId = $userId;
    }

    public function getMediaItem(): MediaItem
    {
        return $this->mediaItem;
    }

    public function setMediaItem(MediaItem $mediaItem): void
    {
        $this->mediaItem = $mediaItem;
    }
}
