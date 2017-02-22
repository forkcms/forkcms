<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder\Command;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;

final class UpdateMediaFolder
{
    /** @var MediaFolder */
    public $mediaFolder;

    /** @var string */
    public $name;

    /**
     * CreateMediaFolder constructor.
     *
     * @param MediaFolder $mediaFolder
     */
    public function __construct(
        MediaFolder $mediaFolder
    ) {
        $this->mediaFolder = $mediaFolder;
        $this->name = $mediaFolder->getName();
    }
}
