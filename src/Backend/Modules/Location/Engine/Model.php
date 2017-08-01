<?php

namespace Backend\Modules\Location\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Common\ModuleExtraType;
use Symfony\Component\Intl\Intl as Intl;

/**
 * In this file we store all generic functions that we will be using in the location module
 */
class Model
{
    const QUERY_DATAGRID_BROWSE =
        'SELECT id, title, CONCAT(street, " ", number, ", ", zip, " ", city, ", ", country) AS address
         FROM location
         WHERE language = ?';

    /**
     * Delete an item
     *
     * @param int $id The id of the record to delete.
     */
    public static function delete(int $id): void
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        // get item
        $item = self::get($id);

        BackendModel::deleteExtraById($item['extra_id']);

        // delete location and its settings
        $database->delete('location', 'id = ? AND language = ?', [$id, BL::getWorkingLanguage()]);
        $database->delete('location_settings', 'map_id = ?', [$id]);
    }

    /**
     * Check if an item exists
     *
     * @param int $id The id of the record to look for.
     *
     * @return bool
     */
    public static function exists(int $id): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM location AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            [$id, BL::getWorkingLanguage()]
        );
    }

    /**
     * Fetch a record from the database
     *
     * @param int $id The id of the record to fetch.
     *
     * @return array
     */
    public static function get(int $id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*
             FROM location AS i
             WHERE i.id = ? AND i.language = ?',
            [$id, BL::getWorkingLanguage()]
        );
    }

    /**
     * Fetch a record from the database
     *
     * @return array
     */
    public static function getAll(): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.*
             FROM location AS i
             WHERE i.language = ? AND i.show_overview = ?',
            [BL::getWorkingLanguage(), true]
        );
    }

    /**
     * Get coordinates latitude/longitude
     *
     * @param string $street
     * @param string $streetNumber
     * @param string $city
     * @param string $zip
     * @param string $country
     *
     * @return array  Contains 'latitude' and 'longitude' as variables
     */
    public static function getCoordinates(
        string $street = null,
        string $streetNumber = null,
        string $city = null,
        string $zip = null,
        string $country = null
    ): array {
        // init item
        $item = [];

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
            $item[] = Intl::getRegionBundle()->getCountryName($country, BL::getInterfaceLanguage());
        }

        // define address
        $address = implode(' ', $item);

        // fetch the geo coordinates
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . rawurlencode($address);
        $geocodes = json_decode(file_get_contents($url), true);

        // return coordinates latitude/longitude
        return [
            'latitude' => array_key_exists(
                0,
                $geocodes['results']
            ) ? $geocodes['results'][0]['geometry']['location']['lat'] : null,
            'longitude' => array_key_exists(
                0,
                $geocodes['results']
            ) ? $geocodes['results'][0]['geometry']['location']['lng'] : null,
        ];
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
        $serializedData = (string) BackendModel::getContainer()->get('database')->getVar(
            'SELECT s.value
             FROM location_settings AS s
             WHERE s.map_id = ? AND s.name = ?',
            [$mapId, $name]
        );

        if ($serializedData !== null) {
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
    public static function getMapSettings(int $mapId): array
    {
        $mapSettings = (array) BackendModel::getContainer()->get('database')->getPairs(
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

    /**
     * Insert an item
     *
     * @param array $item The data of the record to insert.
     *
     * @return int
     */
    public static function insert(array $item): int
    {
        $database = BackendModel::getContainer()->get('database');

        // insert extra
        $item['extra_id'] = BackendModel::insertExtra(
            ModuleExtraType::widget(),
            'Location'
        );

        // insert new location
        $item['created_on'] = $item['edited_on'] = BackendModel::getUTCDate();
        $item['id'] = $database->insert('location', $item);

        // update extra (item id is now known)
        BackendModel::updateExtra(
            $item['extra_id'],
            'data',
            [
                'id' => $item['id'],
                'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Location', 'Core')) . ': ' . $item['title'],
                'language' => $item['language'],
                'edit_url' => BackendModel::createUrlForAction('Edit') . '&id=' . $item['id'],
            ]
        );

        return $item['id'];
    }

    /**
     * Save the map settings
     *
     * @param int $mapId
     * @param string $key
     * @param mixed $value
     */
    public static function setMapSetting(int $mapId, string $key, $value): void
    {
        $value = serialize($value);

        BackendModel::getContainer()->get('database')->execute(
            'INSERT INTO location_settings(map_id, name, value)
             VALUES(?, ?, ?)
             ON DUPLICATE KEY UPDATE value = ?',
            [$mapId, $key, $value, $value]
        );
    }

    /**
     * Update an item
     *
     * @param array $item The data of the record to update.
     *
     * @return int
     */
    public static function update(array $item): int
    {
        // redefine edited on date
        $item['edited_on'] = BackendModel::getUTCDate();

        // we have an extra_id
        if (isset($item['extra_id'])) {
            // update extra
            BackendModel::updateExtra(
                $item['extra_id'],
                'data',
                [
                    'id' => $item['id'],
                    'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Location', 'core')) . ': ' . $item['title'],
                    'language' => $item['language'],
                    'edit_url' => BackendModel::createUrlForAction('Edit') . '&id=' . $item['id'],
                ]
            );
        }

        // update item
        return BackendModel::get('database')->update(
            'location',
            $item,
            'id = ? AND language = ?',
            [$item['id'], $item['language']]
        );
    }
}
