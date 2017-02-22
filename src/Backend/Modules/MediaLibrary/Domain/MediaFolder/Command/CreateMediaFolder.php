<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder\Command;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;

final class CreateMediaFolder
{
    /** @var MediaFolder|null */
    public $parent;

    /** @var MediaFolder */
    public $mediaFolder;

    /** @var string */
    public $name;

    /** @var integer */
    public $userId;

    /**
     * CreateMediaFolder constructor.
     *
     * @param string $name
     * @param MediaFolder|null $parent
     * @param integer $userId
     */
    public function __construct(
        $name,
        MediaFolder $parent = null,
        $userId
    ) {
        $this->name = $name;
        $this->parent = $parent;
        $this->userId = $userId;
    }
}
