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
        $mediaFolder = MediaFolder::fromDataTransferObject($updateMediaFolder);

        // We redefine the MediaFolder, so we can use it in an action
        $updateMediaFolder->setMediaFolderEntity($mediaFolder);
    }
}
