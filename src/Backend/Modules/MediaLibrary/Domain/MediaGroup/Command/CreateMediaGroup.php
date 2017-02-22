<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup\Command;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type;
use Ramsey\Uuid\Uuid;

final class CreateMediaGroup
{
    /**
     * You can give an id
     *
     * @var Uuid|null
     */
    public $id;

    /**
     * @var Type
     */
    public $type;

    /**
     * @var MediaGroup
     */
    private $mediaGroup;

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
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * @return MediaGroup
     */
    public function getMediaGroup()
    {
        return $this->mediaGroup;
    }

    /**
     * @param MediaGroup $mediaGroup
     */
    public function setMediaGroup($mediaGroup)
    {
        $this->mediaGroup = $mediaGroup;
    }
}
