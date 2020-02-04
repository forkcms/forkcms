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

    public function load(SpoonDatabase $database): void
    {
        self::$startsOnTimestamp = time();
        self::$expiresOnTimestamp = self::$startsOnTimestamp + 60 * 60;

        $database->insert(
            'profiles_groups_rights',
            [
                'profile_id' => LoadProfilesProfile::getProfileId(),
                'group_id' => LoadProfilesGroup::getGroupId(),
                'starts_on' => date('Y-m-d H:i:s', self::$startsOnTimestamp),
                'expires_on' => date('Y-m-d H:i:s', self::$expiresOnTimestamp),
            ]
        );
    }

    public static function getExpiresOnTimestamp(): ?int
    {
        return self::$expiresOnTimestamp;
    }

    public static function getStartsOnTimestamp(): ?int
    {
        return self::$startsOnTimestamp;
    }
}
