<?php

namespace Backend\Modules\Profiles\DataFixtures;

use DateTime;
use DateTimeZone;
use SpoonDatabase;

final class LoadProfilesProfile
{
    public const PROFILES_OLD_SESSION_ID = 1234567890;
    public const PROFILES_PROFILE_PASSWORD = 'forkcms';
    public const PROFILES_ACTIVE_PROFILE_EMAIL = 'test-active@fork-cms.com';
    public const PROFILES_ACTIVE_PROFILE_DISPLAY_NAME = 'active Fork CMS profile';
    public const PROFILES_ACTIVE_PROFILE_URL = 'active-fork-cms-profile';
    public const PROFILES_ACTIVE_PROFILE_DATA = [
        'email' => self::PROFILES_ACTIVE_PROFILE_EMAIL,
        'status' => 'active',
        'displayName' => self::PROFILES_ACTIVE_PROFILE_DISPLAY_NAME,
        'url' => self::PROFILES_ACTIVE_PROFILE_URL,
    ];

    public const PROFILES_INACTIVE_PROFILE_EMAIL = 'test-inactive@fork-cms.com';
    public const PROFILES_INACTIVE_PROFILE_DISPLAY_NAME = 'inactive Fork CMS profile';
    public const PROFILES_INACTIVE_PROFILE_URL = 'inactive-fork-cms-profile';
    public const PROFILES_INACTIVE_PROFILE_DATA = [
        'email' => self::PROFILES_INACTIVE_PROFILE_EMAIL,
        'status' => 'inactive',
        'displayName' => self::PROFILES_INACTIVE_PROFILE_DISPLAY_NAME,
        'url' => self::PROFILES_INACTIVE_PROFILE_URL,
    ];
    public const PROFILES_DELETED_PROFILE_EMAIL = 'test-deleted@fork-cms.com';
    public const PROFILES_DELETED_PROFILE_DISPLAY_NAME = 'deleted Fork CMS profile';
    public const PROFILES_DELETED_PROFILE_URL = 'deleted-fork-cms-profile';
    public const PROFILES_DELETED_PROFILE_DATA = [
        'email' => self::PROFILES_DELETED_PROFILE_EMAIL,
        'status' => 'deleted',
        'displayName' => self::PROFILES_DELETED_PROFILE_DISPLAY_NAME,
        'url' => self::PROFILES_DELETED_PROFILE_URL,
    ];
    public const PROFILES_BLOCKED_PROFILE_EMAIL = 'test-blocked@fork-cms.com';
    public const PROFILES_BLOCKED_PROFILE_DISPLAY_NAME = 'blocked Fork CMS profile';
    public const PROFILES_BLOCKED_PROFILE_URL = 'blocked-fork-cms-profile';
    public const PROFILES_BLOCKED_PROFILE_DATA = [
        'email' => self::PROFILES_BLOCKED_PROFILE_EMAIL,
        'status' => 'blocked',
        'displayName' => self::PROFILES_BLOCKED_PROFILE_DISPLAY_NAME,
        'url' => self::PROFILES_BLOCKED_PROFILE_URL,
    ];

    /** @var DateTime */
    private static $dateWithinAMonthAgo;

    /** @var DateTime */
    private static $dateOverAMonthAgo;

    /** @var int */
    private static $profileActiveId;

    /** @var int */
    private static $profileInactiveId;

    /** @var int */
    private static $profileDeletedId;

    /** @var int */
    private static $profileBlockedId;

    public static function getEncryptedPassword(): string
    {
        return password_hash(self::PROFILES_PROFILE_PASSWORD, PASSWORD_DEFAULT);
    }

    public function load(SpoonDatabase $database): void
    {
        self::$profileActiveId = $database->insert(
            'ProfilesProfile',
            self::getProfileArray(self::PROFILES_ACTIVE_PROFILE_DATA)
        );
        $database->insert(
            'ProfilesSession',
            [
                [
                    'sessionId' => '0123456789',
                    'profile_id' => self::$profileActiveId,
                    'secretKey' => 'NotSoSecretNowIsIt',
                    'date' => self::getDateWithinAMonthAgo()->format('Y-m-d H:i:s'),
                ],
                [
                    'sessionId' => self::PROFILES_OLD_SESSION_ID,
                    'profile_id' => self::$profileActiveId,
                    'secretKey' => 'WeNeedToTalk',
                    'date' => self::getDateOverAMonthAgo()->format('Y-m-d H:i:s'),
                ],
            ]
        );
        self::$profileInactiveId = $database->insert(
            'ProfilesProfile',
            self::getProfileArray(self::PROFILES_INACTIVE_PROFILE_DATA)
        );
        self::$profileDeletedId = $database->insert(
            'ProfilesProfile',
            self::getProfileArray(self::PROFILES_DELETED_PROFILE_DATA)
        );
        self::$profileBlockedId = $database->insert(
            'ProfilesProfile',
            self::getProfileArray(self::PROFILES_BLOCKED_PROFILE_DATA)
        );
    }

    public static function getProfileArray(array $profileData): array
    {
        $utc = new DateTimeZone('UTC');

        return $profileData + [
            'password' => self::getEncryptedPassword(),
            'registeredOn' => self::getDateWithinAMonthAgo()->setTimezone($utc)->format('Y-m-d H:i:s'),
            'editedOn' => self::getDateWithinAMonthAgo()->setTimezone($utc)->format('Y-m-d H:i:s'),
            'lastLogin' => self::getDateOverAMonthAgo()->setTimezone($utc)->format('Y-m-d H:i:s'),
        ];
    }

    public static function getDateWithinAMonthAgo(): DateTime
    {
        if (self::$dateWithinAMonthAgo === null) {
            self::$dateWithinAMonthAgo = new DateTime('-1 week');
        }
        return self::$dateWithinAMonthAgo;
    }

    public static function getDateOverAMonthAgo(): DateTime
    {
        if (self::$dateOverAMonthAgo === null) {
            self::$dateOverAMonthAgo = new DateTime('-2 months');
        }

        return self::$dateOverAMonthAgo;
    }

    public static function getProfileActiveId(): int
    {
        return self::$profileActiveId;
    }

    public static function getProfileInactiveId(): int
    {
        return self::$profileInactiveId;
    }

    public static function getProfileDeletedId(): int
    {
        return self::$profileDeletedId;
    }

    public static function getProfileBlockedId(): int
    {
        return self::$profileBlockedId;
    }
}
