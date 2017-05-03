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

        $this->userId = $mediaGallery->getUserId();
        $this->status = $mediaGallery->getStatus();
        $this->title = $mediaGallery->getTitle();
        $this->text = $mediaGallery->getText();
        $this->action = $mediaGallery->getAction();
        $this->mediaGroup = $mediaGallery->getMediaGroup();
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
