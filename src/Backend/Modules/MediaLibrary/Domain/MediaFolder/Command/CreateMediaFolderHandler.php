<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder\Command;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;

final class CreateMediaFolderHandler
{
    /** @var MediaFolderRepository */
    protected $mediaFolderRepository;

    /**
     * CreateMediaFolderHandler constructor.
     *
     * @param MediaFolderRepository $mediaFolderRepository
     */
    public function __construct(MediaFolderRepository $mediaFolderRepository)
    {
        $this->mediaFolderRepository = $mediaFolderRepository;
    }

    /**
     * @param CreateMediaFolder $createMediaFolder
     */
    public function handle(CreateMediaFolder $createMediaFolder)
    {
        /** @var MediaFolder $mediaFolder */
        $mediaFolder = MediaFolder::create(
            $createMediaFolder->name,
            $createMediaFolder->parent,
            $createMediaFolder->userId
        );

        $createMediaFolder->mediaFolder = $mediaFolder;

        $this->mediaFolderRepository->add($mediaFolder);
    }
}
