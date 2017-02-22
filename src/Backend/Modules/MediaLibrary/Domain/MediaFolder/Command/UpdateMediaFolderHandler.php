<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder\Command;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;

final class UpdateMediaFolderHandler
{
    /**
     * @param UpdateMediaFolder $updateMediaFolder
     */
    public function handle(UpdateMediaFolder $updateMediaFolder)
    {
        /** @var MediaFolder $mediaFolder */
        $mediaFolder = $updateMediaFolder->mediaFolder;

        $mediaFolder->setName($updateMediaFolder->name);
    }
}
