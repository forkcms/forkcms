<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaFolder\Command;

use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderDataTransferObject;

final class CreateMediaFolder extends MediaFolderDataTransferObject
{
    public function __construct(string $name, int $userId, MediaFolder $parent = null)
    {
        parent::__construct();

        $this->name = $name;
        $this->parent = $parent;
        $this->userId = $userId;
    }
}
