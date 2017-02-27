<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup\Command;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupDataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateMediaGroup extends MediaGroupDataTransferObject
{
    /**
     * UpdateMediaGroup constructor.
     *
     * @param MediaGroup $mediaGroup
     * @param array $mediaItemIdsToConnect
     */
    public function __construct(
        MediaGroup $mediaGroup,
        array $mediaItemIdsToConnect = array()
    ) {
        parent::__construct($mediaGroup);

        $this->mediaItemIdsToConnect = $mediaItemIdsToConnect;
    }
}
