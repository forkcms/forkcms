<?php

namespace Backend\Modules\Location\Engine;

use Backend\Core\Language\Language;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Domain\Location;
use Backend\Modules\Location\Domain\LocationSetting;
use Common\ModuleExtraType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use SpoonFilter;
use Symfony\Component\Intl\Intl;

class Model
{
    const QUERY_DATAGRID_BROWSE =
        'SELECT id, title, CONCAT(street, " ", number, ", ", zip, " ", city, ", ", country) AS address
         FROM location
         WHERE locale = ?';

    public static function delete(int $id): void
    {
        $location = self::getLocationRepository()->find($id);

        if ($location instanceof Location) {
            BackendModel::deleteExtraById($location->getExtraId());

            $entityManager = self::getEntityManager();

            $entityManager->remove($location);
            $entityManager->flush();
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

    public static function getAll(): array
    {
        $locations = self::getLocationRepository()->findAll();

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
            $item[] = Intl::getRegionBundle()->getCountryName($country, Language::getInterfaceLanguage());
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

        $entityManager = self::getEntityManager();
        $entityManager->persist($location);

        // insert extra
        $extraId = BackendModel::insertExtra(
            ModuleExtraType::widget(),
            'Location',
            'Location'
        );

        $location->setExtraId($extraId);

        $entityManager->persist($location);
        $entityManager->flush();

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
                'edit_url' => BackendModel::createUrlForAction('Edit', 'Blog')
                    . '&id='
                    . $location->getId(),
            ]
        );

        return $location->getId();
    }

    public static function setMapSetting(int $locationId, string $name, $value): void
    {
        /** @var Location|null $location */
        $location = self::getLocationRepository()->find($locationId);

        if (!$location instanceof Location) {
            throw new InvalidArgumentException('Location with id ' . $locationId . ' doesn\'t exist');
        }

        $setting = self::getLocationSettingRepository()->findOneBy(
            [
                'location' => $location,
                'name' => $name
            ]
        );

        if ($setting instanceof LocationSetting) {
            $setting->update($value);

            self::getEntityManager()->flush($setting);

            return;
        }

        $location->addSetting(
            new LocationSetting(
                $location,
                $name,
                $value
            )
        );

        self::getEntityManager()->flush($location);
    }

    public static function update(array $item): int
    {
        $currentLocation = self::getLocationRepository()->find($item['id']);

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
            $updatedLocation->isShowInOverview()
        );

        self::getEntityManager()->flush($currentLocation);

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
                'edit_url' => BackendModel::createUrlForAction('Edit', 'Blog')
                    . '&id='
                    . $currentLocation->getId(),
            ]
        );

        return $currentLocation->getId();
    }

    private static function getEntityManager(): EntityManager
    {
        return BackendModel::get('doctrine.orm.default_entity_manager');
    }

    private static function getLocationRepository(): EntityRepository
    {
        return self::getEntityManager()->getRepository(Location::class);
    }

    private static function getLocationSettingRepository(): EntityRepository
    {
        return self::getEntityManager()->getRepository(LocationSetting::class);
    }
}
