<?php

namespace Backend\Modules\Location\Service;

use Backend\Modules\Location\Entity\Location;
use Backend\Core\Engine\Language;

/**
 * This service provides geocoding
 *
 * @author Mathias Dewelde <mathias@studiorauw.be>
 */
class Geocoder
{
    /**
     * @param  Location $location
     * @return array  Contains 'latitude' and 'longitude' as variables
     */
    public function getCoordinates(Location $location)
    {
        // init item
        $item = array(
            'street' => $location->getStreet(),
            'streetNumber' => $location->getNumber(),
            'city' => $location->getCity(),
            'zip' => $location->getZip(),
            'country' => ($location->getCountry() !== null) ? \SpoonLocale::getCountry($location->getCountry() , Language::getWorkingLanguage()) : null
        );

        // remove empty values
        foreach ($item as $key => $value) {
            if($value === null) unset($item[$key]);
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
}
