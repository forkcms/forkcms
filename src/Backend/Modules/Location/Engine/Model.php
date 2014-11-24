<?php

namespace Backend\Modules\Location\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Entity\Location;

/**
 * In this file we store all generic functions that we will be using in the location module
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 * @author Mathias Dewelde <mathias@studiorauw.be>
 */
class Model
{
    const QRY_DATAGRID_BROWSE =
        'SELECT id, title, CONCAT(street, " ", number, ", ", zip, " ", city, ", ", country) AS address
         FROM location
         WHERE language = ?';

    /**
     * Delete an item
     *
     * @param Location $location
     */
    public static function delete(Location $location)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->remove($location);
        $em->flush();
    }

    /**
     * Check if an item exists
     *
     * @param int $id The id of the record to look for.
     * @return bool
     */
    public static function exists($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM location AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            array((int) $id, BL::getWorkingLanguage())
        );
    }

    /**
     * Fetch a record from the database
     *
     * @param int $id The id of the record to fetch.
     * @return Location
     */
    public static function get($id)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        return $em->getRepository('Backend\Modules\Location\Entity\Location')->findOneBy(
            array(
                'id' => $id,
                'language' => BL::getWorkingLanguage()
            )
        );
    }

    /**
     * Fetch a record from the database
     *
     * @return array
     */
    public static function getAll()
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        return $em->getRepository('Backend\Modules\Location\Entity\Location')->findBy(
            array(
                'language' => BL::getWorkingLanguage(),
                'showOverview' => true
            )
        );
    }

    /**
     * Get coordinates latitude/longitude
     *
     * @param  string $street
     * @param  string $streetNumber
     * @param  string $city
     * @param  string $zip
     * @param  string $country
     * @return array  Contains 'latitude' and 'longitude' as variables
     */
    public static function getCoordinates(
        $street = null,
        $streetNumber = null,
        $city = null,
        $zip = null,
        $country = null
    ) {
        // init item
        $item = array();

        // building item
        if (!empty($street)) {
            $item[] = $street;
        }

        if (!empty($streetNumber)) {
            $item[] = $streetNumber;
        }

        if (!empty($city)) {
            $item[] = $city;
        }

        if (!empty($zip)) {
            $item[] = $zip;
        }

        if (!empty($country)) {
            $item[] = \SpoonLocale::getCountry($country, BL::getWorkingLanguage());
        }

        // define address
        $address = implode(' ', $item);

        // define url
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false';

        // define result
        $geocodes = json_decode(\SpoonHTTP::getContent($url), true);

        // return coordinates latitude/longitude
        return array(
            'latitude' => array_key_exists(0, $geocodes['results']) ? $geocodes['results'][0]['geometry']['location']['lat'] : null,
            'longitude' => array_key_exists(0, $geocodes['results']) ? $geocodes['results'][0]['geometry']['location']['lng'] : null
        );
    }

    /**
     * Retrieve a map setting
     *
     * @param int $mapId
     * @param string $name
     * @return mixed
     */
    public static function getMapSetting($mapId, $name)
    {
        $serializedData = (string) BackendModel::getContainer()->get('database')->getVar(
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
     * @return array
     */
    public static function getMapSettings($mapId)
    {
        $mapSettings = (array) BackendModel::getContainer()->get('database')->getPairs(
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
     * Insert an item
     *
     * @param Location $location
     * @return int
     */
    public static function insert(Location $location)
    {
        // insert extra
        $location->setExtraId(BackendModel::insertExtra(
            'widget',
            'Location'
        ));

        // insert new location
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->persist($location);
        $em->flush();

        // update extra (item id is now known)
        BackendModel::updateExtra(
            $location->getExtraId(),
            'data',
            array(
                'id' => $location->getId(),
                'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Location', 'Core')) . ': ' . $location->getTitle(),
                'language' => $location->getLanguage(),
                'edit_url' => BackendModel::createURLForAction('Edit') . '&id=' . $location->getId()
            )
        );

        return $location->getId();
    }

    /**
     * Save the map settings
     *
     * @param int $mapId
     * @param string $key
     * @param mixed $value
     */
    public static function setMapSetting($mapId, $key, $value)
    {
        $value = serialize($value);

        BackendModel::getContainer()->get('database')->execute(
            'INSERT INTO location_settings(map_id, name, value)
             VALUES(?, ?, ?)
             ON DUPLICATE KEY UPDATE value = ?',
            array((int) $mapId, $key, $value, $value)
        );
    }

    /**
     * Update an item
     *
     * @param Location $location
     * @return int
     */
    public static function update(Location $location)
    {
        // we have an extra_id
        $extraId = $location->getExtraId();
        if (isset($extraId)) {
            // update extra (item id is now known)
            BackendModel::updateExtra(
                $location->getExtraId(),
                'data',
                array(
                    'id' => $location->getId(),
                    'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Location', 'Core')) . ': ' . $location->getTitle(),
                    'language' => $location->getLanguage(),
                    'edit_url' => BackendModel::createURLForAction('Edit') . '&id=' . $location->getId()
                )
            );
        }

        // update location
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->persist($location);
        $em->flush();

        return $location->getId();
    }

	/**
	 * Persist an item
	 *
	 * @param Location $location
	 * @return int
	 */
	public static function persist(Location $location)
	{
		if ($location->getId() === null) {
			self::insert($location);
		} else {
			self::update($location);
		}
	}
}
