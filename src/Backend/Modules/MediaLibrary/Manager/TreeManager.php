<?php

namespace Backend\Modules\MediaLibrary\Manager;

use Backend\Modules\MediaLibrary\Builder\CacheBuilder;
use Backend\Core\Language\Language;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;

/**
 * In this file we store all generic functions that we will be using in the MediaLibrary module
 */
class TreeManager
{
    /** @var CacheBuilder */
    protected $cacheBuilder;

    /**
     * TreeManager constructor.
     *
     * @param CacheBuilder $cacheBuilder
     */
    public function __construct(CacheBuilder $cacheBuilder)
    {
        $this->cacheBuilder = $cacheBuilder;
    }

    /**
     * Get HTML from tree
     */
    public function getHTML()
    {
        // init html
        $html = '';

        // get tree
        $foldersTree = $this->cacheBuilder->getFoldersTree(null, null, 1);

        // has folders?
        if (count($foldersTree) > 0) {
            // init var
            $sequences = array();
            $keys = array();
            $return = array(0 => '');

            // loop folders tree
            foreach ($foldersTree as $folders) {
                // loop all items on this level
                /** @var MediaFolder $folder */
                foreach ($folders as $folder) {
                    // init var
                    $parentID = ($folder->hasParent()) ? $folder->getParent()->getId() : 0;
                    $folderID = $folder->getId();

                    // get URL for parent
                    $URL = (int) (isset($keys[$parentID])) ? $keys[$parentID] : 0;

                    // add it
                    $keys[$folderID] = $folderID;

                    // add to sequences
                    $sequences[$URL]['children'][] = $folderID;

                    // albums
                    $return[$folderID] = $folder->__toArray();
                }
            }

            // start
            $html .= '<h4>' . ucfirst(Language::lbl('Folders')) . '</h4>' . "\n";
            $html .= '<div class="clearfix">' . "\n";
            $html .= '  <ul>' . "\n";

            // add album subchildren
            $html .= $this->getSubtreeForFolder(0, $sequences, $return);

            // end
            $html .= '  </ul>' . "\n";
            $html .= '</div>' . "\n";
        }

        // return
        return $html;
    }

    /**
     * Get subtree for folder
     *
     * @param int $folderId
     * @param array $sequences
     * @param array $folders
     * @return string
     */
    private function getSubtreeForFolder(int $folderId, array $sequences, array $folders): string
    {
        // init html
        $html = '';

        // loop children
        foreach ($sequences[$folderId]['children'] as $folderId) {
            // set album
            $folder = $folders[$folderId];

            // start
            $html .= '<li id="folder-' . $folder['id'] . '" rel="folder">' . "\n";

            // insert link
            $html .= '  <a href="'
                . BackendModel::createURLForAction(
                    'MediaItemIndex',
                    null,
                    null,
                    array(
                        'folder' => $folder['id'],
                    )
                )
                . '"><ins>&#160;</ins>'
                . $folder['name']
                . ' (' . $folder['numberOfItems'] . ')</a>' . "\n";

            // find children albums or galleries
            if (isset($sequences[$folder['id']])) {
                // start
                $html .= '<ul>' . "\n";

                // add child albums
                $html .= $this->getSubtreeForFolder(
                    $folder['id'],
                    $sequences,
                    $folders
                );

                // end
                $html .= '</ul>' . "\n";
            }

            // end
            $html .= '</li>' . "\n";
        }

        return $html;
    }
}
