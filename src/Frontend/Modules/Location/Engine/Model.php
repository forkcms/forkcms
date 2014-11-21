<?php

namespace Frontend\Modules\Location\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Frontend;
use Frontend\Core\Engine\Model as FrontendModel;

/**
 * In this file we store all generic functions that we will be using in the location module
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Mathias Dewelde <mathias@dewelde.be>
 */
class Model
{
    /**
     * This will build the url to google maps for a large map
     *
     * @param array $settings
     * @param array $markers
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
        switch (strtolower($settings['map_type'])) {
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
            $pointers[] = urlencode($marker->getTitle()) . '@' . $marker->getLat() . ',' . $marker->getLng();
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
     * @return array
     */
    public static function get($id)
    {
        $em = FrontendModel::get('doctrine.orm.entity_manager');
        return $em->getRepository('Backend\Modules\Location\Entity\Location')->findOneBy(
            array(
                'id' => $id,
                'language' => FRONTEND_LANGUAGE
            )
        );
    }

    /**
     * Get all items
     *
     * @return array
     */
    public static function getAll()
    {
        $em = FrontendModel::get('doctrine.orm.entity_manager');
        return $em->getRepository('Backend\Modules\Location\Entity\Location')->findBy(
            array(
                'language' => FRONTEND_LANGUAGE,
                'showOverview' => true
            )
        );
    }
}
