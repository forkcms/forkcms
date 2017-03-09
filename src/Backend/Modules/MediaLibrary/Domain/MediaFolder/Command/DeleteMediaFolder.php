<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder\Command;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;

final class DeleteMediaFolder
{
    /**
     * @var MediaFolder
     */
    public $mediaFolder;

    /**
     * MediaFolderDelete constructor.
     *
     * @param MediaFolder $mediaFolder
     */
    public function __construct(MediaFolder $mediaFolder)
    {
        $this->mediaFolder = $mediaFolder;
    }
}
