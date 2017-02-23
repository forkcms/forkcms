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
     * @param integer $userId
     */
    public function __construct(
        $name,
        MediaFolder $parent = null,
        $userId
    ) {
        parent::__construct(null);

        $this->name = $name;
        $this->parent = $parent;
        $this->userId = $userId;
    }
}
