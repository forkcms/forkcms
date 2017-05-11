<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;

class MediaItemDataTransferObject
{
    /** @var MediaItem */
    protected $mediaItemEntity;

    /** @var MediaFolder|null */
    public $folder;

    /** @var string */
    public $title;

    /** @var string */
    public $url;

    /** @var int */
    public $userId;

    public function __construct(MediaItem $mediaItem = null)
    {
        $this->mediaItemEntity = $mediaItem;

        if (!$this->hasExistingMediaItem()) {
            return;
        }

        $this->folder = $mediaItem->getFolder();
        $this->url = $mediaItem->getUrl();
        $this->title = $mediaItem->getTitle();
        $this->userId = $mediaItem->getUserId();
    }

    public function getMediaItemEntity(): MediaItem
    {
        return $this->mediaItemEntity;
    }

    public function hasExistingMediaItem(): bool
    {
        return $this->mediaItemEntity instanceof MediaItem;
    }

    public function setMediaItemEntity(MediaItem $mediaItemEntity): void
    {
        $this->mediaItemEntity = $mediaItemEntity;
    }
}
