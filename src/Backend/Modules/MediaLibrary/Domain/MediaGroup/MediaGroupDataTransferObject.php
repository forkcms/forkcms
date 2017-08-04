<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Ramsey\Uuid\Uuid;

class MediaGroupDataTransferObject
{
    /** @var MediaGroup */
    private $mediaGroupEntity;

    /**
     * You can give an id
     *
     * @var Uuid|null
     */
    public $id;

    /** @var Type */
    public $type;

    /** @var array */
    public $mediaItemIdsToConnect;

    /** @var bool */
    public $removeAllPreviousConnectedMediaItems = true;

    public function __construct(MediaGroup $mediaGroup = null)
    {
        $this->mediaGroupEntity = $mediaGroup;

        if ($this->hasExistingMediaGroup()) {
            $this->id = $this->mediaGroupEntity->getId();
            $this->type = $this->mediaGroupEntity->getType();
        }
    }

    public function getMediaGroupEntity(): MediaGroup
    {
        return $this->mediaGroupEntity;
    }

    public function hasExistingMediaGroup(): bool
    {
        return $this->mediaGroupEntity instanceof MediaGroup;
    }
}
