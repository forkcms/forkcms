<?php

namespace Frontend\Modules\Location\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Theme as FrontendTheme;

/**
 * In this file we store all generic functions that we will be using in the location module
 */
class Model
{
    /**
     * This will build the url to google maps for a large map
     *
     * @param array $settings
     * @param array $markers
     *
     * @return string
     */
    public static function buildUrl(array $settings, array $markers = array())
    {
        $url = 'http://maps.google.be/?';

        // add the center point
        $url .= 'll=' . $settings['center']['lat'] . ',' . $settings['center']['lng'];

        // add the zoom level
        $url .= '&z=' . $settings['zoom_level'];

        // set the map type
        switch (mb_strtolower($settings['map_type'])) {
            case 'roadmap':
                $url .= '&t=m';
                break;
            case 'hybrid':
                $url .= '&t=h';
                break;
            case 'terrain':
                $url .= '&t=p';
                break;
            default:
                $url .= '&t=k';
                break;
        }

        $pointers = array();
        // add the markers to the url
        foreach ($markers as $marker) {
            $pointers[] = rawurlencode($marker['title']) . '@' . $marker['lat'] . ',' . $marker['lng'];
        }

        if (!empty($pointers)) {
            $url .= '&q=' . implode('|', $pointers);
        }

        return $url;
    }

    /**
     * Get an item
     *
     * @param int $id The id of the item to fetch.
     *
     * @return array
     */
    public static function get($id)
    {
        return (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT *
             FROM location
             WHERE id = ? AND language = ?',
            array((int) $id, LANGUAGE)
        );
    }

    /**
     * Get all items
     *
     * @return array
     */
    public static function getAll()
    {
        return (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM location WHERE language = ? AND show_overview = ?',
            array(LANGUAGE, 'Y')
        );
    }

    /**
     * Retrieve a map setting
     *
     * @param int    $mapId
     * @param string $name
     *
     * @return mixed
     */
    public static function getMapSetting($mapId, $name)
    {
        $serializedData = (string) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT s.value
             FROM location_settings AS s
             WHERE s.map_id = ? AND s.name = ?',
            array((int) $mapId, (string) $name)
        );

        if ($serializedData != null) {
            return unserialize($serializedData);
        }

        return false;
    }

    /**
     * Fetch all the settings for a specific map
     *
     * @param int $mapId
     *
     * @return array
     */
    public static function getMapSettings($mapId)
    {
        $mapSettings = (array) FrontendModel::getContainer()->get('database')->getPairs(
            'SELECT s.name, s.value
             FROM location_settings AS s
             WHERE s.map_id = ?',
            array((int) $mapId)
        );

        foreach ($mapSettings as $key => $value) {
            $mapSettings[$key] = unserialize($value);
        }

        return $mapSettings;
    }

    /**
     * Get path to map styles
     *
     * @param boolean $backend
     * @return string
     */
    public static function getPathToMapStyles($backend = true)
    {
        $path = 'src/Frontend';
        $jsFile = 'Location/Js/LocationMapStyles.js';
        $moveToPath = '../../../../..';

        // User can override the map styles in the frontend
        if (file_exists($path . '/Themes/' . FrontendTheme::getTheme() . '/Modules/' . $jsFile)) {
            if ($backend) {
                return $moveToPath . '/' . $path . '/Themes/' . FrontendTheme::getTheme() . '/Modules/' . $jsFile;
            } else {
                return '/' . $path . '/Themes/' . FrontendTheme::getTheme() . '/Modules/' . $jsFile;
            }
        // Otherwise use default
        } else {
            if ($backend) {
                return $moveToPath . '/' . $path . '/Modules/' . $jsFile;
            } else {
                return '/' . $path . '/Modules/' . $jsFile;
            }
        }
    }
}
