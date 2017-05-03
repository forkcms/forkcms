<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder;

class MediaFolderDataTransferObject
{
    /** @var MediaFolder */
    protected $mediaFolderEntity;

    /** @var MediaFolder|null */
    public $parent;

    /** @var string */
    public $name;

    /** @var int */
    public $userId;

    public function __construct(MediaFolder $mediaFolder = null)
    {
        $this->mediaFolderEntity = $mediaFolder;

        if (!$this->hasExistingMediaFolder()) {
            return;
        }

        $this->name = $mediaFolder->getName();
        $this->parent = $mediaFolder->getParent();
        $this->userId = $mediaFolder->getUserId();
    }

    public function getMediaFolderEntity(): MediaFolder
    {
        return $this->mediaFolderEntity;
    }

    public function hasExistingMediaFolder(): bool
    {
        return $this->mediaFolderEntity instanceof MediaFolder;
    }

    public function setMediaFolderEntity(MediaFolder $mediaFolderEntity): void
    {
        $this->mediaFolderEntity = $mediaFolderEntity;
    }
}
