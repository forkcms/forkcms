<?php

namespace App\Backend\Modules\MediaLibrary\Domain\MediaFolder\Command;

use App\Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use App\Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;

final class CreateMediaFolderHandler
{
    /** @var MediaFolderRepository */
    protected $mediaFolderRepository;

    public function __construct(MediaFolderRepository $mediaFolderRepository)
    {
        $this->mediaFolderRepository = $mediaFolderRepository;
    }

    public function handle(CreateMediaFolder $createMediaFolder): void
    {
        /** @var MediaFolder $mediaFolder */
        $mediaFolder = MediaFolder::fromDataTransferObject($createMediaFolder);
        $this->mediaFolderRepository->add($mediaFolder);

        // We redefine the MediaFolder, so we can use it in an action
        $createMediaFolder->setMediaFolderEntity($mediaFolder);
    }
}
