<?php

namespace Backend\Modules\Location\Engine;

use Backend\Core\Language\Language;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Domain\Location\Location;
use Backend\Modules\Location\Domain\Location\LocationRepository;
use Backend\Modules\Location\Domain\LocationSetting\LocationSetting;
use Backend\Modules\Location\Domain\LocationSetting\LocationSettingRepository;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use ForkCMS\Utility\Geolocation;
use InvalidArgumentException;
use SpoonFilter;

class Model
{
    const QUERY_DATAGRID_BROWSE =
        'SELECT id, title, CONCAT(street, " ", number, ", ", zip, " ", city, ", ", country) AS address
         FROM location
         WHERE locale = ?';

    public static function delete(int $id): void
    {
        $locationRepository = self::getLocationRepository();
        $location = $locationRepository->find($id);

        if ($location instanceof Location) {
            BackendModel::deleteExtraById($location->getExtraId());

            $locationRepository->remove($location);
        }
    }

    public static function exists(int $id): bool
    {
        return self::getLocationRepository()->find($id) instanceof Location;
    }

    public static function get(int $id): array
    {
        $location = self::getLocationRepository()->find($id);

        if ($location instanceof Location) {
            return $location->toArray();
        }

        return [];
    }

    public static function getAllWithDefaultMapSettings(): array
    {
        $locations = self::getLocationRepository()->findBy([
            'overrideMapSettings' => false
        ]);

        return self::getLocationsAsArray($locations);
    }

    public static function getAll(): array
    {
        $locations = self::getLocationRepository()->findAll();

        return self::getLocationsAsArray($locations);
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

    public static function getMapSetting(int $locationId, string $name)
    {
        return self::getMapSettings($locationId)[$name] ?? false;
    }

    public static function getMapSettings(int $locationId): array
    {
        /** @var Location|null $location */
        $location = self::getLocationRepository()->find($locationId);

        if (!$location instanceof Location) {
            return [];
        }

        return array_reduce(
            $location->getSettings()->toArray(),
            function (array $carry, LocationSetting $setting) {
                $carry[$setting->getName()] = $setting->getValue();

                return $carry;
            },
            []
        );
    }

    public static function insert(array $item): int
    {
        $location = Location::fromArray($item);
        $locationRepository = self::getLocationRepository();

        // insert extra
        $extraId = BackendModel::insertExtra(
            ModuleExtraType::widget(),
            'Location',
            'Location'
        );

        $location->setExtraId($extraId);
        /**
         * @TODO: Make this obsolete by creating a ModuleExtra entity
         *
         * We use the entity manager to persist for now because we need it's ID to create an extra
         * We can't flush it yet because the entity is invalid without an extra ID
         */
        BackendModel::get('doctrine.orm.entity_manager')->persist($location);

        $locationRepository->save($location);

        // update extra (item id is now known)
        BackendModel::updateExtra(
            $extraId,
            'data',
            [
                'id' => $location->getId(),
                'extra_label' => SpoonFilter::ucfirst(Language::lbl('Location', 'Core'))
                    . ': '
                    . $location->getTitle(),
                'language' => $location->getLocale()->getLocale(),
                'edit_url' => BackendModel::createUrlForAction('Edit', 'Location')
                    . '&id='
                    . $location->getId(),
            ]
        );

        return $location->getId();
    }

    public static function setMapSetting(int $locationId, string $name, $value): void
    {
        $locationRepository = self::getLocationRepository();
        $locationSettingRepository = self::getLocationSettingRepository();

        /** @var Location|null $location */
        $location = $locationRepository->find($locationId);

        if (!$location instanceof Location) {
            throw new InvalidArgumentException('Location with id ' . $locationId . ' doesn\'t exist');
        }

        $setting = $locationSettingRepository->findOneBy(
            [
                'location' => $location,
                'name' => $name
            ]
        );

        if ($setting instanceof LocationSetting) {
            $setting->update($value);

            $locationSettingRepository->save($setting);

            return;
        }

        $location->addSetting(
            new LocationSetting(
                $location,
                $name,
                $value
            )
        );

        $locationRepository->save($location);
    }

    public static function update(array $item): int
    {
        $locationRepository = self::getLocationRepository();
        $currentLocation = $locationRepository->find($item['id']);

        if (!$currentLocation instanceof Location) {
            return 0;
        }

        $updatedLocation = Location::fromArray($item);

        $currentLocation->update(
            $updatedLocation->getTitle(),
            $updatedLocation->getStreet(),
            $updatedLocation->getNumber(),
            $updatedLocation->getZip(),
            $updatedLocation->getCity(),
            $updatedLocation->getCountry(),
            $updatedLocation->getLatitude(),
            $updatedLocation->getLongitude(),
            $updatedLocation->isShowInOverview(),
            $updatedLocation->isOverrideMapSettings()
        );

        $locationRepository->save($currentLocation);

        // update extra
        BackendModel::updateExtra(
            $currentLocation->getExtraId(),
            'data',
            [
                'id' => $currentLocation->getId(),
                'extra_label' => SpoonFilter::ucfirst(Language::lbl('Location', 'Core'))
                    . ': '
                    . $currentLocation->getTitle(),
                'language' => $currentLocation->getLocale()->getLocale(),
                'edit_url' => BackendModel::createUrlForAction('Edit', 'Location')
                    . '&id='
                    . $currentLocation->getId(),
            ]
        );

        return $currentLocation->getId();
    }

    private static function getLocationRepository(): LocationRepository
    {
        return BackendModel::get(LocationRepository::class);
    }

    private static function getLocationSettingRepository(): LocationSettingRepository
    {
        return BackendModel::get(LocationSettingRepository::class);
    }

    private static function getLocationsAsArray(array $locations): array
    {
        return array_map(
            function (Location $location) {
                return $location->toArray();
            },
            $locations
        );
    }
}
