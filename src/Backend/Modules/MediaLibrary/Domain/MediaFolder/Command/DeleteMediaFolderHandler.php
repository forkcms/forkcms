<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder\Command;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;

final class MediaFolderDeleteHandler
{
    /** @var MediaFolderRepository */
    protected $mediaFolderRepository;

    /**
     * MediaFolderDeleteHandler constructor.
     *
     * @param MediaFolderRepository $mediaFolderRepository
     */
    public function __construct(MediaFolderRepository $mediaFolderRepository)
    {
        $this->mediaFolderRepository = $mediaFolderRepository;
    }

    /**
     * @param MediaFolderDelete $deleteMediaFolder
     */
    public function handle(MediaFolderDelete $deleteMediaFolder)
    {
        /** @var MediaFolder $mediaFolder */
        $mediaFolder = $deleteMediaFolder->mediaFolder;

        $this->mediaFolderRepository->remove($mediaFolder);
    }
}
