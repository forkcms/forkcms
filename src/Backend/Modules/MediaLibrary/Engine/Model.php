<?php

namespace Backend\Modules\MediaLibrary\Engine;

use Symfony\Component\Finder\Finder;
use Backend\Core\Language\Language;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

/**
 * In this file we store all generic functions that we will be using in the MediaLibrary module
 */
class Model
{
    // Backend thumbnail settings
    const BACKEND_THUMBNAIL_HEIGHT = 90;
    const BACKEND_THUMBNAIL_WIDTH = 140;
    const BACKEND_THUMBNAIL_QUALITY = 95;

    /**
     * Get movie mimes for dropdown
     *
     * @return array
     */
    public static function getMovieMimesForDropdown(): array
    {
        // Init
        $ddmAllowedMovieSourcesValues = array();

        // Define
        $allowedMovieSourcesValues = MediaItem::getMimesForMovie();

        // Loop all allowed movie sources
        foreach ($allowedMovieSourcesValues as $value) {
            // add value
            $ddmAllowedMovieSourcesValues[] = array(
                'key' => $value,
                'label' => Language::lbl(ucfirst($value))
            );
        }

        return $ddmAllowedMovieSourcesValues;
    }

    /**
     * Get possible widget actions
     *
     * @return array
     */
    public static function getPossibleWidgetActions(): array
    {
        // Define actions
        $actions = array();

        $finder = new Finder();
        $finder->files()->in(
            FRONTEND_MODULES_PATH . '/MediaLibrary/Widgets'
        )->exclude('Base');

        foreach ($finder as $file) {
            $actions[] = $file->getBasename('.' . $file->getExtension());
        }

        return $actions;
    }

    /**
     * Get tree html
     */
    public static function getTreeHTML()
    {
        // init html
        $html = '';

        // get tree
        $foldersTree = BackendModel::get('media_library.cache_builder')->getFoldersTree(null, null, 1);

        // has folders?
        if (count($foldersTree) > 0) {
            // init var
            $sequences = array();
            $keys = array();
            $return = array(0 => '');

            // loop folders tree
            foreach ($foldersTree as $folders) {
                // loop all items on this level
                foreach ($folders as $folderID => $folder) {
                    // init var
                    $parentID = (int) $folder['parentMediaFolderId'];

                    // get URL for parent
                    $URL = (int) (isset($keys[$parentID])) ? $keys[$parentID] : 0;

                    // add it
                    $keys[$folderID] = $folderID;

                    // add to sequences
                    $sequences[$URL]['children'][] = $folderID;

                    // albums
                    $return[$folderID] = $folder;
                }
            }

            // start
            $html .= '<h4>' . ucfirst(Language::lbl('Folders')) . '</h4>' . "\n";
            $html .= '<div class="clearfix">' . "\n";
            $html .= '  <ul>' . "\n";

            // add album subchildren
            $html .= self::getSubtreeForFolder(0, $sequences, $return);

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
    public static function getSubtreeForFolder(int $folderId, array $sequences, array $folders): string
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
                    'Index',
                    null,
                    null,
                    array(
                        'folder' => $folder['id']
                    )
                )
                . '"><ins>&#160;</ins>'
                . $folder['name']
                . ' (' . $folder['count'] . ')</a>' . "\n";

            // find children albums or galleries
            if (isset($sequences[$folder['id']])) {
                // start
                $html .= '<ul>' . "\n";

                // add child albums
                $html .= self::getSubtreeForFolder(
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
