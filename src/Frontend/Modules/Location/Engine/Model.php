<?php

namespace Frontend\Modules\Location\Engine;

use Backend\Modules\Location\Engine\Model as BackendLocationModel;
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
        return BackendLocationModel::get($id);
    }

    public static function getAll(): array
    {
        return BackendLocationModel::getAll();
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
        return BackendLocationModel::getMapSetting($mapId, $name);
    }

    public static function getMapSettings(int $mapId): array
    {
        return BackendLocationModel::getMapSettings($mapId);
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
