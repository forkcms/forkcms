<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder\Command;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;

final class DeleteMediaFolderHandler
{
    /** @var MediaFolderRepository */
    protected $mediaFolderRepository;

    /**
     * DeleteMediaFolderHandler constructor.
     *
     * @param MediaFolderRepository $mediaFolderRepository
     */
    public function __construct(MediaFolderRepository $mediaFolderRepository)
    {
        $this->mediaFolderRepository = $mediaFolderRepository;
    }

    /**
     * @param DeleteMediaFolder $deleteMediaFolder
     */
    public function handle(DeleteMediaFolder $deleteMediaFolder)
    {
        /** @var MediaFolder $mediaFolder */
        $mediaFolder = $deleteMediaFolder->mediaFolder;

        $this->mediaFolderRepository->remove($mediaFolder);
    }
}
