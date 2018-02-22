<?php

namespace App\Backend\Modules\MediaLibrary\Domain\MediaFolder\Command;

use App\Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use App\Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderDataTransferObject;

final class UpdateMediaFolder extends MediaFolderDataTransferObject
{
    public function __construct(MediaFolder $mediaFolder)
    {
        parent::__construct($mediaFolder);
    }
}
