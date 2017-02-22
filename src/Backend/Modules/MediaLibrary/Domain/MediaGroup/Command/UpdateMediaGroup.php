<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup\Command;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateMediaGroup
{
    /**
     * @var MediaGroup
     */
    public $mediaGroup;

    /**
     * @var array
     */
    public $mediaItemIdsToConnect;

    /**
     * @var bool
     */
    public $removeAllPreviousConnectedMediaItems = true;

    /**
     * UpdateMediaGroup constructor.
     *
     * @param MediaGroup $mediaGroup
     * @param array $mediaItemIdsToConnect
     */
    public function __construct(
        MediaGroup $mediaGroup,
        $mediaItemIdsToConnect = array()
    ) {
        $this->mediaGroup = $mediaGroup;
        $this->mediaItemIdsToConnect = $mediaItemIdsToConnect;
    }
}
