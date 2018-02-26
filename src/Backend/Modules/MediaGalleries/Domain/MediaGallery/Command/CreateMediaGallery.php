<?php

namespace ForkCMS\Backend\Modules\MediaGalleries\Domain\MediaGallery\Command;

use ForkCMS\Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryDataTransferObject;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaGroup\Type as MediaGroupType;

final class CreateMediaGallery extends MediaGalleryDataTransferObject
{
    public function __construct(int $userId, MediaGroupType $mediaGroupType)
    {
        parent::__construct();

        $this->userId = $userId;
        $this->mediaGroup = MediaGroup::create($mediaGroupType);
    }
}
