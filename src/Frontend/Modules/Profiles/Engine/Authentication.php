<?php

namespace Frontend\Modules\Profiles\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;
use Frontend\Modules\Profiles\Engine\Profile as FrontendProfilesProfile;

/**
 * Profile authentication functions.
 */
class Authentication
{
    /**
     * The login credentials are correct and the profile is active.
     *
     * @var string
     */
    const LOGIN_ACTIVE = 'active';

    /**
     * The login credentials are correct, but the profile is inactive.
     *
     * @var string
     */
    const LOGIN_INACTIVE = 'inactive';

    /**
     * The login credentials are correct, but the profile has been deleted.
     *
     * @var string
     */
    const LOGIN_DELETED = 'deleted';

    /**
     * The login credentials are correct, but the profile has been blocked.
     *
     * @var string
     */
    const LOGIN_BLOCKED = 'blocked';

    /**
     * The login credentials are incorrect or the profile does not exist.
     *
     * @var string
     */
    const LOGIN_INVALID = 'invalid';

    /**
     * The current logged in profile.
     *
     * @var FrontendProfilesProfile
     */
    private static $profile;

    /**
     * Cleanup old session records in the database.
     */
    public static function cleanupOldSessions(): void
    {
        // remove all sessions with date older then 1 month
        FrontendModel::getContainer()->get('database')->delete(
            'profiles_sessions',
            'date <= DATE_SUB(NOW(), INTERVAL 1 MONTH)'
        );
    }

    /**
     * Get the login/profile status for the given e-mail and password.
     *
     * @param string $email Profile email address.
     * @param string $password Profile password.
     *
     * @return string One of the FrontendProfilesAuthentication::LOGIN_* constants.
     */
    public static function getLoginStatus(string $email, string $password): string
    {
        // check password
        if (!FrontendProfilesModel::verifyPassword($email, $password)) {
            return self::LOGIN_INVALID;
        }

        // get the status
        $loginStatus = FrontendModel::getContainer()->get('database')->getVar(
            'SELECT p.status
             FROM profiles AS p
             WHERE p.email = ?',
            [$email]
        );

        return empty($loginStatus) ? self::LOGIN_INVALID : $loginStatus;
    }

    public static function getProfile(): FrontendProfilesProfile
    {
        return self::$profile;
    }

    public static function isLoggedIn(): bool
    {
        // profile object exist? (this means the session/cookie checks have
        // already happened in the current request and we cached the profile)
        if (isset(self::$profile)) {
            return true;
        }

        if (FrontendModel::getSession()->get('frontend_profile_logged_in', false) === true) {
            // get session id
            $sessionId = FrontendModel::getSession()->getId();

            // get profile id
            $profileId = (int) FrontendModel::getContainer()->get('database')->getVar(
                'SELECT p.id
                 FROM profiles AS p
                 INNER JOIN profiles_sessions AS ps ON ps.profile_id = p.id
                 WHERE ps.session_id = ?',
                (string) $sessionId
            );

            // valid profile id
            if ($profileId !== 0) {
                // update session date
                FrontendModel::getContainer()->get('database')->update(
                    'profiles_sessions',
                    ['date' => FrontendModel::getUTCDate()],
                    'session_id = ?',
                    $sessionId
                );

                // new user object
                self::$profile = new FrontendProfilesProfile($profileId);

                // logged in
                return true;
            }

            // invalid session
            FrontendModel::getSession()->set('frontend_profile_logged_in', false);
        } elseif (FrontendModel::getContainer()->get('fork.cookie')->get('frontend_profile_secret_key', '') !== '') {
            // secret
            $secret = FrontendModel::getContainer()->get('fork.cookie')->get('frontend_profile_secret_key');

            // get profile id
            $profileId = (int) FrontendModel::getContainer()->get('database')->getVar(
                'SELECT p.id
                 FROM profiles AS p
                 INNER JOIN profiles_sessions AS ps ON ps.profile_id = p.id
                 WHERE ps.secret_key = ?',
                $secret
            );

            // valid profile id
            if ($profileId !== 0) {
                // get new secret key
                $profileSecret = FrontendProfilesModel::getEncryptedString(
                    FrontendModel::getSession()->getId(),
                    FrontendProfilesModel::getRandomString()
                );

                // update session record
                FrontendModel::getContainer()->get('database')->update(
                    'profiles_sessions',
                    [
                        'session_id' => FrontendModel::getSession()->getId(),
                        'secret_key' => $profileSecret,
                        'date' => FrontendModel::getUTCDate(),
                    ],
                    'secret_key = ?',
                    $secret
                );

                FrontendModel::getContainer()->get('fork.cookie')->set('frontend_profile_secret_key', $profileSecret);

                FrontendModel::getSession()->set('frontend_profile_logged_in', true);

                FrontendProfilesModel::update($profileId, ['last_login' => FrontendModel::getUTCDate()]);

                self::$profile = new FrontendProfilesProfile($profileId);

                return true;
            }

            // invalid cookie
            FrontendModel::getContainer()->get('fork.cookie')->delete('frontend_profile_secret_key');
        }

        // no one is logged in
        return false;
    }

    /**
     * @param int $profileId Login the profile with this id in.
     * @param bool $remember Should we set a cookie for later?
     */
    public static function login(int $profileId, bool $remember = false): void
    {
        $secretKey = null;

        // cleanup old sessions
        self::cleanupOldSessions();

        // set profile_logged_in to true
        FrontendModel::getSession()->set('frontend_profile_logged_in', true);

        // should we remember the user?
        if ($remember) {
            // generate secret key
            $secretKey = FrontendProfilesModel::getEncryptedString(
                FrontendModel::getSession()->getId(),
                FrontendProfilesModel::getRandomString()
            );

            // set cookie
            FrontendModel::getContainer()->get('fork.cookie')->set('frontend_profile_secret_key', $secretKey);
        }

        // delete all records for this session to prevent duplicate keys (this should never happen)
        FrontendModel::getContainer()->get('database')->delete(
            'profiles_sessions',
            'session_id = ?',
            FrontendModel::getSession()->getId()
        );

        // insert new session record
        FrontendModel::getContainer()->get('database')->insert(
            'profiles_sessions',
            [
                'profile_id' => $profileId,
                'session_id' => FrontendModel::getSession()->getId(),
                'secret_key' => $secretKey,
                'date' => FrontendModel::getUTCDate(),
            ]
        );

        // update last login
        FrontendProfilesModel::update($profileId, ['last_login' => FrontendModel::getUTCDate()]);

        // load the profile object
        self::$profile = new FrontendProfilesProfile($profileId);
    }

    public static function logout(): void
    {
        // delete session records
        FrontendModel::getContainer()->get('database')->delete(
            'profiles_sessions',
            'session_id = ?',
            [FrontendModel::getSession()->getId()]
        );

        // set is_logged_in to false
        FrontendModel::getSession()->set('frontend_profile_logged_in', false);

        FrontendModel::getContainer()->get('fork.cookie')->delete('frontend_profile_secret_key');
    }

    /**
     * Update profile password and salt.
     *
     * @param int $profileId Profile id for which we are changing the password.
     * @param string $password New password.
     */
    public static function updatePassword(int $profileId, string $password): void
    {
        // encrypt password
        $encryptedPassword = FrontendProfilesModel::encryptPassword($password);

        // update password
        FrontendProfilesModel::update($profileId, ['password' => $encryptedPassword]);
    }
}
