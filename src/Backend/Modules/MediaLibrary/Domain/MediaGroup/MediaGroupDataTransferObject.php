<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type;
use Ramsey\Uuid\Uuid;

class MediaGroupDataTransferObject
{
    /**
     * @var MediaGroup
     */
    private $mediaGroupEntity;

    /**
     * You can give an id
     *
     * @var Uuid|null
     */
    public $id;

    /**
     * @var Type
     */
    public $type;

    /**
     * @var array
     */
    public $mediaItemIdsToConnect;

    /**
     * @var bool
     */
    public $removeAllPreviousConnectedMediaItems = true;

    /**
     * CreateMediaGroup constructor.
     *
     * @param Type $type
     * @param Uuid|null $id
     */
    public function __construct(
        MediaGroup $mediaGroup = null
    ) {
        $this->mediaGroupEntity = $mediaGroup;

        if ($this->hasExistingMediaGroup()) {
            $this->id = $mediaGroup->getId();
            $this->type = $mediaGroup->getType();
        }
    }

    /**
     * @return MediaGroup
     */
    public function getMediaGroupEntity(): MediaGroup
    {
        return $this->mediaGroupEntity;
    }

    /**
     * @return boolean
     */
    public function hasExistingMediaGroup(): bool
    {
        return $this->mediaGroupEntity instanceof MediaGroup;
    }

    /**
     * @param MediaGroup $mediaGroup
     */
    public function setMediaGroupEntity(MediaGroup $mediaGroup)
    {
        $this->mediaGroupEntity = $mediaGroup;
    }
}
