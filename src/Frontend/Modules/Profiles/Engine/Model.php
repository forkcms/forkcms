<?php

namespace Frontend\Modules\Profiles\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Frontend\Modules\Profiles\Engine\Profile as FrontendProfilesProfile;

/**
 * In this file we store all generic functions that we will be using with profiles.
 */
class Model
{
    const MAX_DISPLAY_NAME_CHANGES = 2;

    /**
     * Avatars cache
     *
     * @var array
     */
    private static $avatars = [];

    public static function deleteSetting(int $profileId, string $name): int
    {
        return (int) FrontendModel::getContainer()->get('database')->delete(
            'profiles_settings',
            'profile_id = ? AND name = ?',
            [$profileId, $name]
        );
    }

    public static function existsByEmail(string $email, int $excludedId = null): bool
    {
        return (bool) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles AS p
             WHERE p.email = ? AND p.id != ?
             LIMIT 1',
            [$email, $excludedId]
        );
    }

    public static function existsDisplayName($displayName, int $excludedId = null): bool
    {
        return (bool) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles AS p
             WHERE p.id != ? AND p.display_name = ?
             LIMIT 1',
            [$excludedId, $displayName]
        );
    }

    public static function get(int $profileId): FrontendProfilesProfile
    {
        return new FrontendProfilesProfile($profileId);
    }

    /**
     * @param int $id The id for the profile we want to get the avatar from.
     * @param string $email The email from the user we can use for gravatar.
     * @param string $size The resolution you want to use. Default: 240x240 pixels.
     *
     * @return string $avatar The absolute path to the avatar.
     */
    public static function getAvatar(int $id, string $email = null, string $size = '240x240'): string
    {
        // return avatar from cache
        if (isset(self::$avatars[$id])) {
            return self::$avatars[$id];
        }

        // define avatar path
        $avatarPath = FRONTEND_FILES_URL . '/Profiles/Avatars/' . $size . '/';

        // get user
        $user = self::get($id);

        // if no email is given
        if (empty($email)) {
            // redefine email
            $email = $user->getEmail();
        }

        // define avatar
        $avatar = $user->getSetting('avatar');

        // no custom avatar defined, get gravatar if allowed
        if (empty($avatar) && FrontendModel::get('fork.settings')->get('Profiles', 'allow_gravatar', true)) {
            // define hash
            $hash = md5(mb_strtolower(trim('d' . $email)));

            // define avatar url
            $avatar = 'https://www.gravatar.com/avatar/' . $hash;

            // when email not exists, it has to show our custom no-avatar image
            $avatar .= '?d=' . rawurlencode(SITE_URL . $avatarPath) . 'no-avatar.gif';
        } elseif (empty($avatar)) {
            // define avatar as not found
            $avatar = SITE_URL . $avatarPath . 'no-avatar.gif';
        } else {
            // define custom avatar path
            $avatar = $avatarPath . $avatar;
        }

        // set avatar in cache
        self::$avatars[$id] = $avatar;

        // return avatar image path
        return $avatar;
    }

    /**
     * Encrypt the password with PHP password_hash function.
     *
     * @param string $password
     *
     * @return string
     */
    public static function encryptPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify the password with PHP password_verify function.
     *
     * @param string $email
     * @param string $password
     *
     * @return bool
     */
    public static function verifyPassword(string $email, string $password): bool
    {
        $encryptedPassword = self::getEncryptedPassword($email);

        return password_verify($password, $encryptedPassword);
    }

    /**
     * @param string $string
     * @param string $salt
     *
     * @return string
     */
    public static function getEncryptedString(string $string, string $salt): string
    {
        return md5(sha1(md5($string)) . sha1(md5($salt)));
    }

    public static function getIdByEmail(string $email): int
    {
        return (int) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT p.id FROM profiles AS p WHERE p.email = ?',
            $email
        );
    }

    /**
     * @param string $name Setting name.
     * @param mixed $value Value of the setting.
     *
     * @return int
     */
    public static function getIdBySetting(string $name, $value): int
    {
        return (int) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT ps.profile_id
             FROM profiles_settings AS ps
             WHERE ps.name = ? AND ps.value = ?',
            [(string) $name, serialize($value)]
        );
    }

    /**
     * @param int $length Length of random string.
     * @param bool $numeric Use numeric characters.
     * @param bool $lowercase Use alphanumeric lowercase characters.
     * @param bool $uppercase Use alphanumeric uppercase characters.
     * @param bool $special Use special characters.
     *
     * @return string
     */
    public static function getRandomString(
        int $length = 15,
        bool $numeric = true,
        bool $lowercase = true,
        bool $uppercase = true,
        bool $special = true
    ): string {
        // init
        $characters = '';
        $string = '';
        $charset = FrontendModel::getContainer()->getParameter('kernel.charset');

        // possible characters
        if ($numeric) {
            $characters .= '1234567890';
        }
        if ($lowercase) {
            $characters .= 'abcdefghijklmnopqrstuvwxyz';
        }
        if ($uppercase) {
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if ($special) {
            $characters .= '-_.:;,?!@#&=)([]{}*+%$';
        }

        // get random characters
        for ($i = 0; $i < $length; ++$i) {
            // random index
            $index = mt_rand(0, mb_strlen($characters));

            // add character to salt
            $string .= mb_substr($characters, $index, 1, $charset);
        }

        return $string;
    }

    /**
     * Get a setting for a profile.
     *
     * @param int $id Profile id.
     * @param string $name Setting name.
     *
     * @return mixed
     */
    public static function getSetting(int $id, string $name)
    {
        return unserialize(
            (string) FrontendModel::getContainer()->get('database')->getVar(
                'SELECT ps.value
                 FROM profiles_settings AS ps
                 WHERE ps.profile_id = ? AND ps.name = ?',
                [$id, $name]
            )
        );
    }

    public static function getSettings(int $profileId): array
    {
        // get settings
        $settings = (array) FrontendModel::getContainer()->get('database')->getPairs(
            'SELECT ps.name, ps.value
             FROM profiles_settings AS ps
             WHERE ps.profile_id = ?',
            $profileId
        );

        // unserialize values
        foreach ($settings as &$value) {
            $value = unserialize($value);
        }

        // return
        return $settings;
    }

    /**
     * Retrieve a unique URL for a profile based on the display name.
     *
     * @param string $displayName The display name to base on.
     * @param int $excludedId The id of the profile to ignore.
     *
     * @return string
     */
    public static function getUrl(string $displayName, int $excludedId = null): string
    {
        // decode special chars
        $displayName = \SpoonFilter::htmlspecialcharsDecode($displayName);

        // urlise
        $url = CommonUri::getUrl($displayName);

        // get database
        $database = FrontendModel::getContainer()->get('database');

        // new item
        if ($excludedId === null) {
            // get number of profiles with this URL
            $number = (int) $database->getVar(
                'SELECT 1
                 FROM profiles AS p
                 WHERE p.url = ?
                 LIMIT 1',
                (string) $url
            );

            // already exists
            if ($number !== 0) {
                // add number
                $url = FrontendModel::addNumber($url);

                // try again
                return self::getUrl($url);
            }

            return $url;
        }

        // current profile should be excluded
        // get number of profiles with this URL
        $number = (int) $database->getVar(
            'SELECT 1
             FROM profiles AS p
             WHERE p.url = ? AND p.id != ?
             LIMIT 1',
            [$url, $excludedId]
        );

        // already exists
        if ($number !== 0) {
            // add number
            $url = FrontendModel::addNumber($url);

            // try again
            return self::getUrl($url, $excludedId);
        }

        return $url;
    }

    public static function insert(array $profile): int
    {
        return (int) FrontendModel::getContainer()->get('database')->insert('profiles', $profile);
    }

    /**
     * Parse the general profiles info into the template.
     */
    public static function parse(): void
    {
        // get the template
        $tpl = FrontendModel::getContainer()->get('templating');

        // logged in
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            // get profile
            $profile = FrontendProfilesAuthentication::getProfile();

            // display name set?
            if ($profile->getDisplayName() != '') {
                $tpl->assign('profileDisplayName', $profile->getDisplayName());
            } else {
                // no display name -> use email
                $tpl->assign('profileDisplayName', $profile->getEmail());
            }

            // show logged in
            $tpl->assign('isLoggedIn', true);
        }

        // ignore these urls in the query string
        $ignoreUrls = [
            FrontendNavigation::getUrlForBlock('Profiles', 'Login'),
            FrontendNavigation::getUrlForBlock('Profiles', 'Register'),
            FrontendNavigation::getUrlForBlock('Profiles', 'ForgotPassword'),
        ];

        // query string
        $queryString = FrontendModel::getRequest()->query->has('queryString')
            ? SITE_URL . '/' . urldecode(FrontendModel::getRequest()->query->get('queryString'))
            : SITE_URL . FrontendModel::get('url')->getQueryString();

        // check all ignore urls
        foreach ($ignoreUrls as $url) {
            // query string contains a boeboe url
            if (mb_stripos($queryString, $url) !== false) {
                $queryString = '';
                break;
            }
        }

        // no need to add this if its empty
        $queryString = ($queryString !== '') ? '?queryString=' . rawurlencode($queryString) : '';

        // useful urls
        $tpl->assign('loginUrl', FrontendNavigation::getUrlForBlock('Profiles', 'Login') . $queryString);
        $tpl->assign('registerUrl', FrontendNavigation::getUrlForBlock('Profiles', 'Register'));
        $tpl->assign('forgotPasswordUrl', FrontendNavigation::getUrlForBlock('Profiles', 'ForgotPassword'));
    }

    /**
     * Insert or update a single profile setting.
     *
     * @param int $id Profile id.
     * @param string $name Setting name.
     * @param mixed $value New setting value.
     */
    public static function setSetting(int $id, string $name, $value): void
    {
        // insert or update
        FrontendModel::getContainer()->get('database')->execute(
            'INSERT INTO profiles_settings(profile_id, name, value)
             VALUES(?, ?, ?)
             ON DUPLICATE KEY UPDATE value = ?',
            [$id, $name, serialize($value), serialize($value)]
        );
    }

    /**
     * Insert or update multiple profile settings.
     *
     * @param int $id Profile id.
     * @param array $values Settings in key=>value form.
     */
    public static function setSettings(int $id, array $values): void
    {
        // build parameters
        $parameters = [];
        foreach ($values as $key => $value) {
            $parameters[] = $id;
            $parameters[] = $key;
            $parameters[] = serialize($value);
        }

        // build the query
        $query = 'INSERT INTO profiles_settings(profile_id, name, value)
                  VALUES';
        $query .= rtrim(str_repeat('(?, ?, ?), ', count($values)), ', ') . ' ';
        $query .= 'ON DUPLICATE KEY UPDATE value = VALUES(value)';

        FrontendModel::getContainer()->get('database')->execute($query, $parameters);
    }

    /**
     * @param int $id The profile id.
     * @param array $values The values to update.
     *
     * @return int
     */
    public static function update(int $id, array $values): int
    {
        return (int) FrontendModel::getContainer()->get('database')->update('profiles', $values, 'id = ?', $id);
    }

    /**
     * Get encrypted password for an email.
     *
     * @param string $email
     *
     * @return null|string
     */
    public static function getEncryptedPassword(string $email): ?string
    {
        return FrontendModel::get('database')->getVar(
            'SELECT password
             FROM profiles
             WHERE email = :email',
            ['email' => $email]
        );
    }
}
