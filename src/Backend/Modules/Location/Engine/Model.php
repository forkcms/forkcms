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

/**
 * In this file we store all generic functions that we will be using in the location module
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 */
class Model
{
    const QRY_DATAGRID_BROWSE =
        'SELECT id, title, CONCAT(street, " ", number, ", ", zip, " ", city, ", ", country) AS address
         FROM location
         WHERE language = ? AND site_id = ?';

    /**
     * Delete an item
     *
     * @param int $id The id of the record to delete.
     */
    public static function delete($id)
    {
        // get db
        $db = BackendModel::getContainer()->get('database');

        // get item
        $item = self::get($id);

        // build extra
        $extra = array(
            'id' => $item['extra_id'],
            'module' => 'Location',
            'type' => 'widget',
            'action' => 'Location'
        );

        $db->delete('modules_extras', 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));
        $db->delete(
            'location',
            'id = ? AND language = ? AND site_id = ?',
            array(
                (int) $id,
                BL::getWorkingLanguage(),
                BackendModel::get('current_site')->getId(),
            )
        );
        $db->delete('location_settings', 'map_id = ?', array((int) $id));
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
             WHERE i.id = ? AND i.language = ? AND i.site_id = ?
             LIMIT 1',
            array(
                (int) $id,
                BL::getWorkingLanguage(),
                BackendModel::get('current_site')->getId(),
            )
        );
    }

    /**
     * Fetch a record from the database
     *
     * @param int $id The id of the record to fetch.
     * @return array
     */
    public static function get($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*
             FROM location AS i
             WHERE i.id = ? AND i.language = ? AND i.site_id = ?',
            array(
                (int) $id,
                BL::getWorkingLanguage(),
                BackendModel::get('current_site')->getId(),
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
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.*
             FROM location AS i
             WHERE i.language = ? AND i.site_id = ? AND i.show_overview = ?',
            array(
                BL::getWorkingLanguage(),
                BackendModel::get('current_site')->getId(),
                'Y',
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

        if ($serializedData != null) return unserialize($serializedData);
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

        foreach ($mapSettings as $key => $value) $mapSettings[$key] = unserialize($value);

        return $mapSettings;
    }

    /**
     * Insert an item
     *
     * @param array $item The data of the record to insert.
     * @return int
     */
    public static function insert($item)
    {
        $db = BackendModel::getContainer()->get('database');
        $item['created_on'] = BackendModel::getUTCDate();
        $item['edited_on'] = BackendModel::getUTCDate();

        // build extra
        $extra = array(
            'module' => 'Location',
            'type' => 'widget',
            'label' => 'Location',
            'action' => 'Location',
            'data' => null,
            'hidden' => 'N',
            'sequence' => $db->getVar(
                'SELECT MAX(i.sequence) + 1
                 FROM modules_extras AS i
                 WHERE i.module = ?', array('Location')
                )
            );
        if (is_null($extra['sequence'])) {
            $extra['sequence'] = $db->getVar(
                'SELECT CEILING(MAX(i.sequence) / 1000) * 1000 FROM modules_extras AS i'
            );
        }

        // insert extra
        $item['extra_id'] = $db->insert('modules_extras', $extra);
        $extra['id'] = $item['extra_id'];

        // insert and return the new id
        $item['id'] = $db->insert('location', $item);

        // update extra (item id is now known)
        $extra['data'] = serialize(array(
            'id' => $item['id'],
            'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Location', 'core')) . ': ' . $item['title'],
            'language' => $item['language'],
            'site_id' => $item['site_id'],
            'edit_url' => BackendModel::createURLForAction('Edit') . '&id=' . $item['id'])
        );
        $db->update(
            'modules_extras',
            $extra,
            'id = ? AND module = ? AND type = ? AND action = ?',
            array(
                $extra['id'],
                $extra['module'],
                $extra['type'],
                $extra['action'],
            )
        );

        // return the new id
        return $item['id'];
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
     * @param array $item The data of the record to update.
     * @return int
     */
    public static function update($item)
    {
        $db = BackendModel::getContainer()->get('database');
        $item['edited_on'] = BackendModel::getUTCDate();

        if (isset($item['extra_id'])) {
            // build extra
            $extra = array(
                'id' => $item['extra_id'],
                'module' => 'Location',
                'type' => 'widget',
                'label' => 'Location',
                'action' => 'Location',
                'data' => serialize(array(
                    'id' => $item['id'],
                    'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Location', 'core')) . ': ' . $item['title'],
                    'language' => $item['language'],
                    'site_id' => $item['site_id'],
                    'edit_url' => BackendModel::createURLForAction('Edit') . '&id=' . $item['id'])
                ),
                'hidden' => 'N'
            );

            // update extra
            $db->update(
                'modules_extras',
                $extra,
                'id = ? AND module = ? AND type = ? AND action = ?',
                array(
                    $extra['id'],
                    $extra['module'],
                    $extra['type'],
                    $extra['action'],
                )
            );
        }

        // update item
        return $db->update(
            'location',
            $item,
            'id = ? AND language = ? AND site_id = ?',
            array(
                $item['id'],
                $item['language'],
                $item['site_id'],
            )
        );
    }
}
