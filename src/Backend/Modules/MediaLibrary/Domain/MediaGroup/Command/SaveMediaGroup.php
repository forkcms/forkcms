<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup\Command;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupDataTransferObject;

final class SaveMediaGroup extends MediaGroupDataTransferObject
{
    /** @var MediaGroup */
    protected $mediaGroup;

    public function __construct(
        MediaGroup $mediaGroup,
        array $mediaItemIdsToConnect = []
    ) {
        parent::__construct($mediaGroup);

        $this->mediaItemIdsToConnect = $mediaItemIdsToConnect;
        $this->mediaGroup = $mediaGroup;
    }

    public function getMediaGroup(): MediaGroup
    {
        return $this->mediaGroup;
    }

    public function setMediaGroup(MediaGroup $mediaGroup): void
    {
        $this->mediaGroup = $mediaGroup;
    }
}
