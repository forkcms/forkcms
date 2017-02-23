<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery\Command;

use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryDataTransferObject;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type as MediaGroupType;

final class CreateMediaGallery extends MediaGalleryDataTransferObject
{
    /**
     * CreateMediaGallery constructor.
     *
     * @param integer $userId
     * @param MediaGroupType $mediaGroupType
     */
    public function __construct($userId, MediaGroupType $mediaGroupType)
    {
        parent::__construct();

        $this->userId = $userId;
        $this->mediaGroup = MediaGroup::create($mediaGroupType);
    }
}
