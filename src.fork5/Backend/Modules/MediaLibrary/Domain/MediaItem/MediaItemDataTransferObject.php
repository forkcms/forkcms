<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Symfony\Component\Validator\Constraints as Assert;

class MediaItemDataTransferObject
{
    /** @var MediaItem */
    protected $mediaItemEntity;

    /** @var MediaFolder|null */
    public $folder;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
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

        $this->folder = $this->mediaItemEntity->getFolder();
        $this->url = $this->mediaItemEntity->getUrl();
        $this->title = $this->mediaItemEntity->getTitle();
        $this->userId = $this->mediaItemEntity->getUserId();
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
