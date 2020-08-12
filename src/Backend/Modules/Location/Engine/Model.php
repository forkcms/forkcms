<?php

namespace Backend\Modules\Location\Engine;

use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Common\ModuleExtraType;
use ForkCMS\Utility\Geolocation;

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
     * @deprecated
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
        return BackendModel::get(Geolocation::class)->getCoordinates(
            $street,
            $streetNumber,
            $city,
            $zip,
            $country
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
        $serializedData = (string) BackendModel::getContainer()->get('database')->getVar(
            'SELECT s.value
             FROM location_settings AS s
             WHERE s.map_id = ? AND s.name = ?',
            [$mapId, $name]
        );

        if (!empty($serializedData)) {
            return unserialize($serializedData, ['allowed_classes' => false]);
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
            $mapSettings[$key] = unserialize($value, ['allowed_classes' => false]);
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
            'Location',
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
                'edit_url' => BackendModel::createUrlForAction('Edit', 'Location') . '&id=' . $item['id'],
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
                    'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Location', 'Core')) . ': ' . $item['title'],
                    'language' => $item['language'],
                    'edit_url' => BackendModel::createUrlForAction('Edit', 'Location') . '&id=' . $item['id'],
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
