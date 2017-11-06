<?php

namespace Backend\Modules\Location\Engine;

use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Domain\Location;
use Common\ModuleExtraType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
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
        /** @var EntityRepository $repository */
        $repository = BackendModel::get('doctrine.orm.default_entity_manager')->getRepository(Location::class);

        $location = $repository->find($id);

        if ($location instanceof Location) {
            BackendModel::deleteExtraById($location->getExtraId());
            BackendModel::getContainer()->get('database')->delete('location_settings', 'map_id = ?', [$location->getId()]);

            /** @var EntityManager $entityManager */
            $entityManager = BackendModel::get('doctrine.orm.default_entity_manager');

            $entityManager->remove($location);
            $entityManager->flush();
        }
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
        /** @var EntityRepository $repository */
        $repository = BackendModel::get('doctrine.orm.default_entity_manager')->getRepository(Location::class);

        return $repository->find($id) instanceof Location;
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
        /** @var EntityRepository $repository */
        $repository = BackendModel::get('doctrine.orm.default_entity_manager')->getRepository(Location::class);

        $location = $repository->find($id);

        if ($location instanceof Location) {
            return $location->toArray();
        }

        return [];
    }

    /**
     * Fetch a record from the database
     *
     * @return array
     */
    public static function getAll(): array
    {
        /** @var EntityRepository $repository */
        $repository = BackendModel::get('doctrine.orm.default_entity_manager')->getRepository(Location::class);

        $locations = $repository->findAll();

        return array_map(
            function (Location $location) {
                return $location->toArray();
            },
            $locations
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

    public static function insert(array $item): int
    {
        $location = Location::fromArray($item);

        /** @var EntityManager $entityManager */
        $entityManager = BackendModel::get('doctrine.orm.default_entity_manager');

        $entityManager->persist($location);

        // insert extra
        $extraId = BackendModel::insertExtra(
            ModuleExtraType::widget(),
            'Location',
            'Location'
        );

        $location->update(
            $extraId,
            $location->getTitle(),
            $location->getStreet(),
            $location->getNumber(),
            $location->getZip(),
            $location->getCity(),
            $location->getCountry(),
            $location->getLatitude(),
            $location->getLongitude()
        );

        $entityManager->persist($location);
        $entityManager->flush();

        // update extra (item id is now known)
        BackendModel::updateExtra(
            $extraId,
            'data',
            [
                'id' => $location->getId(),
                'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Location', 'Core')) . ': ' . $location->getTitle(),
                'language' => $location->getLocale()->getLocale(),
                'edit_url' => BackendModel::createUrlForAction('Edit', 'Blog') . '&id=' . $location->getId(),
            ]
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
        /** @var EntityRepository $repository */
        $repository = BackendModel::get('doctrine.orm.default_entity_manager')->getRepository(Location::class);

        $currentLocation = $repository->find($item['id']);

        if (!$currentLocation instanceof Location) {
            return 0;
        }

        $updatedLocation = Location::fromArray($item);

        $currentLocation->update(
            $updatedLocation->getExtraId(),
            $updatedLocation->getTitle(),
            $updatedLocation->getStreet(),
            $updatedLocation->getNumber(),
            $updatedLocation->getZip(),
            $updatedLocation->getCity(),
            $updatedLocation->getCountry(),
            $updatedLocation->getLatitude(),
            $updatedLocation->getLongitude(),
            $updatedLocation->isShowInOverview()
        );

        /** @var EntityManager $entityManager */
        $entityManager = BackendModel::get('doctrine.orm.default_entity_manager');

        $entityManager->flush($currentLocation);

        // update extra
        BackendModel::updateExtra(
            $currentLocation->getExtraId(),
            'data',
            [
                'id' => $currentLocation->getId(),
                'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Location', 'Core')) . ': ' . $currentLocation->getTitle(),
                'language' => $currentLocation->getLocale()->getLocale(),
                'edit_url' => BackendModel::createUrlForAction('Edit', 'Blog') . '&id=' . $currentLocation->getId(),
            ]
        );

        return $currentLocation->getId();
    }
}
