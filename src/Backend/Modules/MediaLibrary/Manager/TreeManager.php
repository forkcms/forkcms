<?php

namespace Backend\Modules\MediaLibrary\Manager;

use Backend\Modules\MediaLibrary\Builder\MediaFolder\MediaFolderCache;
use Backend\Core\Language\Language;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\MediaLibrary\Builder\MediaFolder\MediaFolderCacheItem;
use SpoonFilter;

/**
 * In this file we store all generic functions that we will be using in the MediaLibrary module
 */
final class TreeManager
{
    /** @var MediaFolderCache */
    protected $mediaFolderCache;

    /** @var string */
    private $itemAction;

    public function __construct(MediaFolderCache $mediaFolderCache, string $itemAction)
    {
        $this->mediaFolderCache = $mediaFolderCache;
        $this->itemAction = $itemAction;
    }

    public function getHTML(): string
    {
        $navigationItems = $this->mediaFolderCache->get();

        $html = '<h4>' . SpoonFilter::ucfirst(Language::lbl('Folders')) . '</h4>' . "\n";
        $html .= $this->buildNavigationTree($navigationItems);

        return $html;
    }

    private function buildNavigationTree(array $navigationItems): string
    {
        // start
        $html = '<div class="clearfix">' . "\n";
        $html .= '  <ul>' . "\n";

        /** @var MediaFolderCacheItem $cacheItem */
        foreach ($navigationItems as $cacheItem) {
            $html .= $this->buildNavigationItem($cacheItem);
        }

        // end
        $html .= '  </ul>' . "\n";
        $html .= '</div>' . "\n";

        return $html;
    }

    private function buildNavigationItem(MediaFolderCacheItem $cacheItem): string
    {
        // define url
        $url = $this->getLink(['folder' => $cacheItem->id]);

        // start
        $html = '<li id="folder-' . $cacheItem->id . '" rel="folder">' . "\n";

        // insert link
        $html .= '<a href="' . $url . '"><ins>&#160;</ins>' . $cacheItem->name . ' (' . $cacheItem->numberOfMediaItems . ')</a>' . "\n";

        if ($cacheItem->numberOfChildren > 0) {
            $html .= $this->buildNavigationTree($cacheItem->children);
        }

        return $html . '</li>' . "\n";
    }

    private function getLink($parameters = [])
    {
        return BackendModel::createUrlForAction(
            $this->itemAction,
            null,
            null,
            $parameters
        );
    }
}
