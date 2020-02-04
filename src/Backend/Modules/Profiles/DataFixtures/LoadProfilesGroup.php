<?php

namespace Backend\Modules\Profiles\DataFixtures;

use SpoonDatabase;

class LoadProfilesGroup
{
    public const PROFILES_GROUP_NAME = 'My Fork CMS group';
    public const PROFILES_PROFILE_DATA = [
        'name' => self::PROFILES_GROUP_NAME,
    ];

    /** @var int|null */
    private static $groupId;

    public function load(SpoonDatabase $database): void
    {
        self::$groupId = $database->insert(
            'profiles_group',
            self::PROFILES_PROFILE_DATA
        );
    }

    public static function getGroupId(): ?int
    {
        return self::$groupId;
    }
}
