<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Symfony\Component\Validator\Constraints as Assert;

class MediaGalleryDataTransferObject
{
    /**
     * @var MediaGallery
     */
    private $mediaGalleryEntity;

    /**
     * @var Status
     */
    public $status;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $title;

    /**
     * @var string
     */
    public $text;

    /**
     * @var string
     */
    public $action;

    /**
     * @var MediaGroup
     */
    public $mediaGroup;

    public function __construct(MediaGallery $mediaGallery = null)
    {
        $this->mediaGalleryEntity = $mediaGallery;

        if (!$this->hasExistingMediaGallery()) {
            return;
        }

        $this->userId = $this->mediaGalleryEntity->getUserId();
        $this->status = $this->mediaGalleryEntity->getStatus();
        $this->title = $this->mediaGalleryEntity->getTitle();
        $this->text = $this->mediaGalleryEntity->getText();
        $this->action = $this->mediaGalleryEntity->getAction();
        $this->mediaGroup = $this->mediaGalleryEntity->getMediaGroup();
    }

    public function getMediaGalleryEntity(): MediaGallery
    {
        return $this->mediaGalleryEntity;
    }

    public function hasExistingMediaGallery(): bool
    {
        return $this->mediaGalleryEntity instanceof MediaGallery;
    }

    public function setMediaGalleryEntity(MediaGallery $mediaGalleryEntity): void
    {
        $this->mediaGalleryEntity = $mediaGalleryEntity;
    }
}
