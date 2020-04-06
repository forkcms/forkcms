<?php

namespace Backend\Modules\Profiles\DataFixtures;

use SpoonDatabase;

class LoadProfilesGroup
{
    public const PROFILES_GROUP_NAME = 'My Fork CMS group';
    public const PROFILES_GROUP_DATA = [
        'name' => self::PROFILES_GROUP_NAME,
        'createdOn' => '1991-03-24 03:16:00',
        'editedOn' => '2020-12-20 14:00:00',
    ];

    /** @var int|null */
    private static $groupId;

    public function load(SpoonDatabase $database): void
    {
        self::$groupId = $database->insert(
            'ProfilesGroup',
            self::PROFILES_GROUP_DATA
        );
    }

    public static function getGroupId(): ?int
    {
        return self::$groupId;
    }
}
