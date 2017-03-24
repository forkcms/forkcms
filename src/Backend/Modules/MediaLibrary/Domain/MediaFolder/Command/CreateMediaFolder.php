<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder\Command;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderDataTransferObject;

final class CreateMediaFolder extends MediaFolderDataTransferObject
{
    /**
     * CreateMediaFolder constructor.
     *
     * @param string $name
     * @param MediaFolder|null $parent
     * @param int $userId
     */
    public function __construct(string $name, MediaFolder $parent = null, int $userId)
    {
        parent::__construct();

        $this->name = $name;
        $this->parent = $parent;
        $this->userId = $userId;
    }
}
