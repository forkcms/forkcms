<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type as MediaGroupType;
use Symfony\Component\Validator\Constraints as Assert;

class MediaGalleryDataTransferObject
{
    /**
     * @var MediaGallery
     */
    private $mediaGalleryEntity;

    /**
     * @var string
     */
    public $status;

    /**
     * @var integer
     */
    private $userId;

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
     * @var \DateTime
     */
    public $publishOn;

    /**
     * @var MediaGroup
     */
    public $mediaGroup;

    /**
     * MediaGalleryDataTransferObject constructor.
     *
     * @param MediaGallery|null $mediaGallery
     * @param integer|null $userId
     * @param MediaGroupType $mediaGroupType
     */
    public function __construct(
        MediaGallery $mediaGallery = null,
        $userId = null,
        MediaGroupType $mediaGroupType = null
    ) {
        $this->mediaGalleryEntity = $mediaGallery;

        if (!$this->hasExistingMediaGallery()) {
            $this->userId = $userId;
            $this->publishOn = new \DateTime();
            $this->mediaGroup = MediaGroup::create($mediaGroupType);

            return;
        }

        $this->status = (string) $mediaGallery->getStatus();
        $this->title = $mediaGallery->getTitle();
        $this->text = $mediaGallery->getText();
        $this->action = $mediaGallery->getAction();
        $this->publishOn = $mediaGallery->getPublishOn();
        $this->mediaGroup = $mediaGallery->getGroup();
    }

    /**
     * @return \Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGallery
     */
    public function getMediaGalleryEntity()
    {
        return $this->mediaGalleryEntity;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return bool
     */
    public function hasExistingMediaGallery()
    {
        return $this->mediaGalleryEntity instanceof MediaGallery;
    }

    /**
     * @param MediaGallery $mediaGalleryEntity
     */
    public function setMediaGalleryEntity($mediaGalleryEntity)
    {
        $this->mediaGalleryEntity = $mediaGalleryEntity;
    }
}
