<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaFolder\Command;

use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderDataTransferObject;

final class UpdateMediaFolder extends MediaFolderDataTransferObject
{
    public function __construct(MediaFolder $mediaFolder)
    {
        parent::__construct($mediaFolder);
    }
}
