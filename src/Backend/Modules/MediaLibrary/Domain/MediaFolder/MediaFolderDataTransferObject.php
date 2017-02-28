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

    /**
     * CreateMediaFolder constructor.
     *
     * @param MediaFolder|null $mediaFolder
     */
    public function __construct(MediaFolder $mediaFolder = null)
    {
        $this->mediaFolderEntity = $mediaFolder;

        if ($this->hasExistingMediaFolder()) {
            $this->name = $mediaFolder->getName();
            $this->parent = $mediaFolder->getParent();
            $this->userId = $mediaFolder->getUserId();
        }
    }

    /**
     * @return MediaFolder
     */
    public function getMediaFolderEntity(): MediaFolder
    {
        return $this->mediaFolderEntity;
    }

    /**
     * @return bool
     */
    public function hasExistingMediaFolder(): bool
    {
        return $this->mediaFolderEntity instanceof MediaFolder;
    }

    /**
     * @param MediaFolder $mediaFolderEntity
     */
    public function setMediaFolderEntity(MediaFolder $mediaFolderEntity)
    {
        $this->mediaFolderEntity = $mediaFolderEntity;
    }
}
