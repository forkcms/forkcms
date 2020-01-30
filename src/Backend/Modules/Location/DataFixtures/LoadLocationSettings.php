<?php

namespace Backend\Modules\Blog\DataFixtures;

use SpoonDatabase;

final class LoadLocationSettings
{
    public const SETTING_NAME_1 = 'foo';
    public const SETTING_VALUE_1 = 'bar';
    public const SETTING_NAME_2 = 'ping';
    public const SETTING_VALUE_2 = 'pong';

    public function load(SpoonDatabase $database): void
    {
        $database->insert(
            'location_settings',
            [
                [
                    'map_id' => LoadLocation::getLocationId(),
                    'name' => self::SETTING_NAME_1,
                    serialize(self::SETTING_VALUE_1),
                ],
                [
                    'map_id' => LoadLocation::getLocationId(),
                    'name' => self::SETTING_NAME_2,
                    serialize(self::SETTING_VALUE_2),
                ],
            ]
        );
    }
}
