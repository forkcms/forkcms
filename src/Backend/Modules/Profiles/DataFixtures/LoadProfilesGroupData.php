<?php

namespace Backend\Modules\Profiles\DataFixtures;

use SpoonDatabase;

class LoadProfilesGroupData
{
    public const PROFILES_GROUP_NAME = 'My Fork CMS group';
    public const PROFILES_PROFILE_DATA = [
        'name' => self::PROFILES_GROUP_NAME,
    ];

    /** @var int|null */
    private static $expiresOnTimestamp;

    /** @var int|null */
    private static $startsOnTimestamp;

    /** @var int|null */
    private static $id;

    public function load(SpoonDatabase $database): void
    {
        self::$id = $database->insert(
            'profiles_groups_rights',
            [
                'profile_id' => LoadProfilesProfile::getProfileActiveId(),
                'group_id' => LoadProfilesGroup::getGroupId(),
                'starts_on' => date('Y-m-d H:i:s', self::getStartsOnTimestamp()),
                'expires_on' => date('Y-m-d H:i:s', self::getExpiresOnTimestamp()),
            ]
        );
    }

    public static function getExpiresOnTimestamp(): int
    {
        if (self::$expiresOnTimestamp === null) {
            self::$expiresOnTimestamp = self::getStartsOnTimestamp() + 60 * 60;
        }

        return self::$expiresOnTimestamp;
    }

    public static function getStartsOnTimestamp(): int
    {
        if (self::$startsOnTimestamp === null) {
            self::$startsOnTimestamp = time();
        }

        return self::$startsOnTimestamp;
    }

    public static function getId(): ?int
    {
        return self::$id;
    }
}
