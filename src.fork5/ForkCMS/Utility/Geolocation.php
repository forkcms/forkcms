<?php

namespace ForkCMS\Utility;

use Backend\Core\Language\Language;
use Common\ModulesSettings;
use JeroenDesloovere\Geolocation\Geolocation as API;
use JeroenDesloovere\Geolocation\Result\Coordinates;
use JeroenDesloovere\Geolocation\Exception;
use Symfony\Component\Intl\Intl;

class Geolocation
{
    /** @var API */
    private $api;

    public function __construct(ModulesSettings $settings)
    {
        $this->api = new API($settings->get('Core', 'google_maps_key'));
    }

    /**
     * @param string|null $street
     * @param string|null $streetNumber
     * @param string|null $city
     * @param string|null $zip
     * @param string|null $country
     * @return array - Example: ['latitude' => 50.8864, 'longitude' => 3.42928]
     */
    public function getCoordinates(
        string $street = null,
        string $streetNumber = null,
        string $city = null,
        string $zip = null,
        string $country = null
    ): array {
        if (!empty($country)) {
            $country = Intl::getRegionBundle()->getCountryName($country, Language::getInterfaceLanguage());
        }

        try {
            /** @var Coordinates $coordinates */
            $coordinates = $this->api->getCoordinates(
                $street,
                $streetNumber,
                $city,
                $zip,
                $country
            );
        } catch (Exception $e) {
            $coordinates = null;
        }

        return [
            'latitude' => ($coordinates instanceof Coordinates) ? $coordinates->getLatitude() : null,
            'longitude' => ($coordinates instanceof Coordinates) ? $coordinates->getLongitude() : null,
        ];
    }
}
