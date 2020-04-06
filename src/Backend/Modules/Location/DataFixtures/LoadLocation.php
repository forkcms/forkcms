<?php

namespace Backend\Modules\Location\DataFixtures;

use SpoonDatabase;

final class LoadLocation
{
    public const LOCATION_LOCATION_TITLE = 'Test location';
    public const LOCATION_LOCATION_DATA = [
        'locale' => 'en',
        'title' => self::LOCATION_LOCATION_TITLE,
        'street' => 'Afrikalaan',
        'number' => '289',
        'zip' => '9000',
        'city' => 'Ghent',
        'country' => 'BE',
        'latitude' => '51.0728',
        'longitude' => '3.73599',
        'showInOverview' => '1',
    ];

    public const LOCATION_LOCATION_MODULES_EXTRA_DATA = [
        'module' => 'Location',
        'type' => 'widget',
        'label' => 'Location',
        'action' => 'Location',
        'data' => 'a:4:{s:2:"id";i:1;s:11:"extra_label";s:23:"Location: '
                  . self::LOCATION_LOCATION_TITLE
                  . '";s:8:"language";s:2:"en";s:8:"edit_url";s:47:"/private/en/location/edit?token=2anvv4w8ry&id=1";}',
        'hidden' => 0,
        'sequence' => 5001,
    ];

    /** @var int|null */
    private static $locationId;

    /** @var int|null */
    private static $extraId;

    public function load(SpoonDatabase $database): void
    {
        self::$extraId = $database->insert('PagesModuleExtra', self::LOCATION_LOCATION_MODULES_EXTRA_DATA);
        self::$locationId = $database->insert(
            'location',
            [
                'extraId' => self::$extraId,
                'createdOn' => '2020-01-30 15:42:18',
                'editedOn' => '2020-01-30 15:42:18',
            ] + self::LOCATION_LOCATION_DATA
        );
    }

    public static function getLocationId(): ?int
    {
        return self::$locationId;
    }

    public static function getExtraId(): ?int
    {
        return self::$extraId;
    }
}
