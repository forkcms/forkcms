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

    /** @var integer */
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
     * @return \Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder
     */
    public function getMediaFolderEntity()
    {
        return $this->mediaFolderEntity;
    }

    /**
     * @return bool
     */
    public function hasExistingMediaFolder()
    {
        return $this->mediaFolderEntity instanceof MediaFolder;
    }

    /**
     * @param \Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder $mediaFolderEntity
     */
    public function setMediaFolderEntity($mediaFolderEntity)
    {
        $this->mediaFolderEntity = $mediaFolderEntity;
    }
}
