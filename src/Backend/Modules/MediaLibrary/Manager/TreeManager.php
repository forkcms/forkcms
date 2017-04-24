<?php

namespace Backend\Modules\MediaLibrary\Manager;

use Backend\Modules\MediaLibrary\Builder\MediaFolder\MediaFolderCache;
use Backend\Core\Language\Language;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\MediaLibrary\Builder\MediaFolder\MediaFolderCacheItem;

/**
 * In this file we store all generic functions that we will be using in the MediaLibrary module
 */
final class TreeManager
{
    /** @var MediaFolderCache */
    protected $mediaFolderCache;

    /**
     * TreeManager constructor.
     *
     * @param MediaFolderCache $mediaFolderCache
     */
    public function __construct(MediaFolderCache $mediaFolderCache)
    {
        $this->mediaFolderCache = $mediaFolderCache;
    }

    /**
     * @return string
     */
    public function getHTML(): string
    {
        $navigationItems = $this->mediaFolderCache->get();

        $html = '<h4>' . ucfirst(Language::lbl('Folders')) . '</h4>' . "\n";
        $html .= $this->buildNavigationTree($navigationItems);
        return $html;
    }

    /**
     * @param array $navigationItems
     * @return string
     */
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

    /**
     * @param MediaFolderCacheItem $cacheItem
     * @return string
     */
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

    /**
     * @param array $parameters
     * @return string
     */
    private function getLink($parameters = [])
    {
        return BackendModel::createURLForAction(
            'MediaItemIndex',
            null,
            null,
            $parameters
        );
    }
}
