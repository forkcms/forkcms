<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup\Command;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupDataTransferObject;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type;
use Ramsey\Uuid\Uuid;

final class CreateMediaGroup extends MediaGroupDataTransferObject
{
    /** @var MediaGroup|null */
    protected $mediaGroup;

    /**
     * CreateMediaGroup constructor.
     *
     * @param Type $type
     * @param Uuid|null $id
     */
    public function __construct(
        Type $type,
        Uuid $id = null
    ) {
        parent::__construct();

        $this->type = $type;
        $this->id = $id;
    }

    /**
     * @return MediaGroup|null
     */
    public function getMediaGroup()
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
