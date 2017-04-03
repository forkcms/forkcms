<?php

namespace Backend\Modules\MediaLibrary\Builder\MediaFolder;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;

class MediaFolderCacheItem
{
    public $id;
    public $name;
    public $slug;
    public $children = [];
    public $numberOfChildren = 0;
    public $numberOfMediaItems = 0;

    /**
     * @param MediaFolder $mediaFolder
     * @param string|null $parentSlug
     */
    public function __construct(MediaFolder $mediaFolder, string $parentSlug = null)
    {
        $this->id = $mediaFolder->getId();
        $this->name = $mediaFolder->getName();
        $this->numberOfMediaItems = $mediaFolder->getItems()->count();

        if ($parentSlug !== null) {
            $this->slug = $parentSlug . '/' . $this->name;
            return;
        }

        $this->slug = $this->name;
    }

    /**
     * @param array $children
     */
    public function setChildren(array $children)
    {
        $this->children = $children;
        $this->numberOfChildren = count($this->children);
    }
}
