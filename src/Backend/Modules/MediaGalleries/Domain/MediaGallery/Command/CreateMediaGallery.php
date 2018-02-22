<?php

namespace App\Backend\Modules\MediaGalleries\Domain\MediaGallery\Command;

use App\Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryDataTransferObject;
use App\Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use App\Backend\Modules\MediaLibrary\Domain\MediaGroup\Type as MediaGroupType;

final class CreateMediaGallery extends MediaGalleryDataTransferObject
{
    public function __construct(int $userId, MediaGroupType $mediaGroupType)
    {
        parent::__construct();

        $this->userId = $userId;
        $this->mediaGroup = MediaGroup::create($mediaGroupType);
    }
}
