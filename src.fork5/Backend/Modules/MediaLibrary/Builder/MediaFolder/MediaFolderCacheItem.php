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

    public function __construct(MediaFolder $mediaFolder, string $parentSlug = null)
    {
        $this->id = $mediaFolder->getId();
        $this->name = $mediaFolder->getName();
        $this->slug = ($parentSlug !== null) ? $parentSlug . '/' . $this->name : $this->name;
        $this->numberOfMediaItems = $mediaFolder->getItems()->count();
    }

    public function setChildren(array $children)
    {
        $this->children = $children;
        $this->numberOfChildren = count($this->children);
    }
}
