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

    /**
     * CreateMediaItem constructor.
     *
     * @param MediaItem|null $mediaItem
     */
    public function __construct(MediaItem $mediaItem = null)
    {
        $this->mediaItemEntity = $mediaItem;

        if (!$this->hasExistingMediaItem()) {
            return;
        }

        $this->folder = $mediaItem->getFolder();
        $this->url = $mediaItem->getUrl();
        $this->title = $mediaItem->getTitle();
    }

    /**
     * @return MediaItem
     */
    public function getMediaItemEntity(): MediaItem
    {
        return $this->mediaItemEntity;
    }

    /**
     * @return bool
     */
    public function hasExistingMediaItem(): bool
    {
        return $this->mediaItemEntity instanceof MediaItem;
    }

    /**
     * @param MediaItem $mediaItemEntity
     */
    public function setMediaItemEntity(MediaItem $mediaItemEntity)
    {
        $this->mediaItemEntity = $mediaItemEntity;
    }
}
