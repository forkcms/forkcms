<?php

namespace Backend\Modules\Profiles\DataFixtures;

use SpoonDatabase;

final class LoadProfilesProfile
{
    public const PROFILES_PROFILE_EMAIL = 'test@fork-cms.com';
    public const PROFILES_PROFILE_PASSWORD = 'forkcms';
    public const PROFILES_PROFILE_DISPLAY_NAME = 'Fork CMS';
    public const PROFILES_PROFILE_URL = 'fork-cms';
    public const PROFILES_PROFILE_DATA = [
        'email' => self::PROFILES_PROFILE_EMAIL,
        'status' => 'active',
        'display_name' => self::PROFILES_PROFILE_DISPLAY_NAME,
        'url' => self::PROFILES_PROFILE_URL,
        'registered_on' => '2018-03-05 09:45:12',
        'last_login' => '1970-01-01 00:00:00',
    ];

    /** @var int|null */
    private static $profileId;

    public static function getPassword(): string
    {
        return password_hash(self::PROFILES_PROFILE_PASSWORD, PASSWORD_DEFAULT);
    }

    public function load(SpoonDatabase $database): void
    {
        self::$profileId = $database->insert(
            'profiles',
            ['password' => self::getPassword()] + self::PROFILES_PROFILE_DATA
        );
    }

    public static function getProfileId(): ?int
    {
        return self::$profileId;
    }
}
