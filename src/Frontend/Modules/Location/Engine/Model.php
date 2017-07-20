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
    public static function buildUrl(array $settings, array $markers = []): string
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

        $pointers = [];
        // add the markers to the url
        foreach ($markers as $marker) {
            $pointers[] = rawurlencode($marker['title']) . '@' . $marker['lat'] . ',' . $marker['lng'];
        }

        if (!empty($pointers)) {
            $url .= '&q=' . implode('|', $pointers);
        }

        return $url;
    }

    public static function get(int $id): array
    {
        return (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT *
             FROM location
             WHERE id = ? AND language = ?',
            [$id, LANGUAGE]
        );
    }

    public static function getAll(): array
    {
        return (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM location WHERE language = ? AND show_overview = ?',
            [LANGUAGE, true]
        );
    }

    /**
     * Retrieve a map setting
     *
     * @param int $mapId
     * @param string $name
     *
     * @return mixed
     */
    public static function getMapSetting(int $mapId, string $name)
    {
        $serializedData = (string) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT s.value
             FROM location_settings AS s
             WHERE s.map_id = ? AND s.name = ?',
            [$mapId, $name]
        );

        if ($serializedData != null) {
            return unserialize($serializedData);
        }

        return false;
    }

    public static function getMapSettings(int $mapId): array
    {
        $mapSettings = (array) FrontendModel::getContainer()->get('database')->getPairs(
            'SELECT s.name, s.value
             FROM location_settings AS s
             WHERE s.map_id = ?',
            [$mapId]
        );

        foreach ($mapSettings as $key => $value) {
            $mapSettings[$key] = unserialize($value);
        }

        return $mapSettings;
    }

    public static function getPathToMapStyles(bool $backend = true): string
    {
        $path = 'src/Frontend';
        $jsFile = 'Location/Js/LocationMapStyles.js';
        $moveToPath = '../../../../..';

        // User can override the map styles in the frontend
        if (file_exists($path . '/Themes/' . FrontendTheme::getTheme() . '/Modules/' . $jsFile)) {
            if ($backend) {
                return $moveToPath . '/' . $path . '/Themes/' . FrontendTheme::getTheme() . '/Modules/' . $jsFile;
            }

            return '/' . $path . '/Themes/' . FrontendTheme::getTheme() . '/Modules/' . $jsFile;
        }

        if ($backend) {
            return $moveToPath . '/' . $path . '/Modules/' . $jsFile;
        }

        return '/' . $path . '/Modules/' . $jsFile;
    }
}
