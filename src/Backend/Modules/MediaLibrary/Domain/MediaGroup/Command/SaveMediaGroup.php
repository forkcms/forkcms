<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup\Command;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupDataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;

final class SaveMediaGroup extends MediaGroupDataTransferObject
{
    /** @var MediaGroup */
    protected $mediaGroup;

    /**
     * SaveMediaGroup constructor.
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
        $this->mediaGroup = $mediaGroup;
    }

    /**
     * @return MediaGroup
     */
    public function getMediaGroup(): MediaGroup
    {
        return $this->mediaGroup;
    }

    /**
     * @param MediaGroup $mediaGroup
     */
    public function setMediaGroup(MediaGroup $mediaGroup)
    {
        $this->mediaGroup = $mediaGroup;
    }
}
