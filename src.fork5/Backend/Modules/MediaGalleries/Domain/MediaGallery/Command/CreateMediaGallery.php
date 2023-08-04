<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery\Command;

use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryDataTransferObject;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\Status;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type as MediaGroupType;

final class CreateMediaGallery extends MediaGalleryDataTransferObject
{
    public function __construct(int $userId, MediaGroupType $mediaGroupType)
    {
        parent::__construct();

        $this->userId = $userId;
        $this->status = Status::active();
        $this->mediaGroup = MediaGroup::create($mediaGroupType);
    }
}
