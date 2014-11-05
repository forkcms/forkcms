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
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jan Moesen <jan.moesen@netlash.com>
 */
class Model
{
    const MAX_DISPLAY_NAME_CHANGES = 2;

    /**
     * Avatars cache
     *
     * @var array
     */
    private static $avatars = array();

    /**
     * Delete a setting.
     *
     * @param  int    $id   Profile id.
     * @param  string $name Setting name.
     * @return int
     */
    public static function deleteSetting($id, $name)
    {
        return (int) FrontendModel::getContainer()->get('database')->delete(
            'profiles_settings',
            'profile_id = ? AND name = ?',
            array((int) $id, (string) $name)
        );
    }

    /**
     * Check if a profile exists by email address.
     *
     * @param  string $email    Email to check for existence.
     * @param  int    $ignoreId Profile id to ignore.
     * @return bool
     */
    public static function existsByEmail($email, $ignoreId = null)
    {
        return (bool) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles AS p
             WHERE p.email = ? AND p.id != ?
             LIMIT 1',
            array((string) $email, (int) $ignoreId)
        );
    }

    /**
     * Check if a display name exists.
     *
     * @param  string $displayName Display name to check for existence.
     * @param  int    $id          Profile id to ignore.
     * @return bool
     */
    public static function existsDisplayName($displayName, $id = null)
    {
        return (bool) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles AS p
             WHERE p.id != ? AND p.display_name = ?
             LIMIT 1',
            array((int) $id, (string) $displayName)
        );
    }

    /**
     * Get profile by its id.
     *
     * @param  int                     $profileId Id of the wanted profile.
     * @return FrontendProfilesProfile
     */
    public static function get($profileId)
    {
        return new FrontendProfilesProfile((int) $profileId);
    }

    /**
     * Get avatar
     *
     * @param  int    $id    The id for the profile we want to get the avatar from.
     * @param  string $email The email from the user we can use for gravatar.
     * @param  string $size  The resolution you want to use. Default: 240x240 pixels.
     * @return string $avatar            The absolute path to the avatar.
     */
    public static function getAvatar($id, $email = null, $size = '240x240')
    {
        // redefine id
        $id = (int) $id;

        // return avatar from cache
        if (isset(self::$avatars[$id])) {
            return self::$avatars[$id];
        }

        // define avatar path
        $avatarPath = FRONTEND_FILES_URL . '/Profiles/Avatars/' . $size . '/';

        // get user
        $user = self::get($id);

        // if no email is given
        if (!$email) {
            // redefine email
            $email = $user->getEmail();
        }

        // define avatar
        $avatar = $user->getSetting('avatar');

        // no custom avatar defined, get gravatar if allowed
        if (empty($avatar) && FrontendModel::getModuleSetting('Profiles', 'allow_gravatar', true)) {
            // define hash
            $hash = md5(strtolower(trim('d' . $email)));

            // define avatar url
            $avatar = 'http://www.gravatar.com/avatar/' . $hash;

            // when email not exists, it has to show our custom no-avatar image
            $avatar .= '?d=' . urlencode(SITE_URL . $avatarPath) . 'no-avatar.gif';
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
     * Get an encrypted string.
     *
     * @param  string $string String to encrypt.
     * @param  string $salt   Salt to add to the string.
     * @return string
     */
    public static function getEncryptedString($string, $salt)
    {
        return md5(sha1(md5((string) $string)) . sha1(md5((string) $salt)));
    }

    /**
     * Get profile id by email.
     *
     * @param  string $email Email address.
     * @return int
     */
    public static function getIdByEmail($email)
    {
        return (int) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT p.id FROM profiles AS p WHERE p.email = ?',
            (string) $email
        );
    }

    /**
     * Get profile id by setting.
     *
     * @param  string $name  Setting name.
     * @param  string $value Value of the setting.
     * @return int
     */
    public static function getIdBySetting($name, $value)
    {
        return (int) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT ps.profile_id
             FROM profiles_settings AS ps
             WHERE ps.name = ? AND ps.value = ?',
            array((string) $name, serialize((string) $value))
        );
    }

    /**
     * Generate a random string.
     *
     * @param  int    $length    Length of random string.
     * @param  bool   $numeric   Use numeric characters.
     * @param  bool   $lowercase Use alphanumeric lowercase characters.
     * @param  bool   $uppercase Use alphanumeric uppercase characters.
     * @param  bool   $special   Use special characters.
     * @return string
     */
    public static function getRandomString(
        $length = 15,
        $numeric = true,
        $lowercase = true,
        $uppercase = true,
        $special = true
    ) {
        // init
        $characters = '';
        $string = '';

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
        for ($i = 0; $i < $length; $i++) {
            // random index
            $index = mt_rand(0, strlen($characters));

            // add character to salt
            $string .= mb_substr($characters, $index, 1, SPOON_CHARSET);
        }

        return $string;
    }

    /**
     * Get a setting for a profile.
     *
     * @param  int    $id   Profile id.
     * @param  string $name Setting name.
     * @return string
     */
    public static function getSetting($id, $name)
    {
        return unserialize(
            (string) FrontendModel::getContainer()->get('database')->getVar(
                'SELECT ps.value
                 FROM profiles_settings AS ps
                 WHERE ps.profile_id = ? AND ps.name = ?',
                array((int) $id, (string) $name)
            )
        );
    }

    /**
     * Get all settings for a profile.
     *
     * @param  int   $id Profile id.
     * @return array
     */
    public static function getSettings($id)
    {
        // get settings
        $settings = (array) FrontendModel::getContainer()->get('database')->getPairs(
            'SELECT ps.name, ps.value
             FROM profiles_settings AS ps
             WHERE ps.profile_id = ?',
            (int) $id
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
     * @param  string $displayName The display name to base on.
     * @param  int    $id          The id of the profile to ignore.
     * @return string
     */
    public static function getUrl($displayName, $id = null)
    {
        // decode special chars
        $displayName = \SpoonFilter::htmlspecialcharsDecode((string) $displayName);

        // urlise
        $url = (string) CommonUri::getUrl($displayName);

        // get db
        $db = FrontendModel::getContainer()->get('database');

        // new item
        if ($id === null) {
            // get number of profiles with this URL
            $number = (int) $db->getVar(
                'SELECT 1
                 FROM profiles AS p
                 WHERE p.url = ?
                 LIMIT 1',
                (string) $url
            );

            // already exists
            if ($number != 0) {
                // add number
                $url = FrontendModel::addNumber($url);

                // try again
                return self::getURL($url);
            }
        } else {
            // current profile should be excluded
            // get number of profiles with this URL
            $number = (int) $db->getVar(
                'SELECT 1
                 FROM profiles AS p
                 WHERE p.url = ? AND p.id != ?
                 LIMIT 1',
                array((string) $url, (int) $id)
            );

            // already exists
            if ($number != 0) {
                // add number
                $url = FrontendModel::addNumber($url);

                // try again
                return self::getURL($url, $id);
            }
        }

        return $url;
    }

    /**
     * Insert a new profile.
     *
     * @param  array $values Profile data.
     * @return int
     */
    public static function insert(array $values)
    {
        return (int) FrontendModel::getContainer()->get('database')->insert('profiles', $values);
    }

    /**
     * Parse the general profiles info into the template.
     */
    public static function parse()
    {
        // get the template
        $tpl = FrontendModel::getContainer()->get('template');

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
        $ignoreUrls = array(
            FrontendNavigation::getURLForBlock('Profiles', 'Login'),
            FrontendNavigation::getURLForBlock('Profiles', 'Register'),
            FrontendNavigation::getURLForBlock('Profiles', 'ForgotPassword')
        );

        // query string
        $queryString = (isset($_GET['queryString'])) ? SITE_URL . '/' . urldecode($_GET['queryString']) : SELF;

        // check all ignore urls
        foreach ($ignoreUrls as $url) {
            // query string contains a boeboe url
            if (stripos($queryString, $url) !== false) {
                $queryString = '';
                break;
            }
        }

        // no need to add this if its empty
        $queryString = ($queryString != '') ? '?queryString=' . urlencode($queryString) : '';

        // useful urls
        $tpl->assign('loginUrl', FrontendNavigation::getURLForBlock('Profiles', 'Login') . $queryString);
        $tpl->assign('registerUrl', FrontendNavigation::getURLForBlock('Profiles', 'Register'));
        $tpl->assign('forgotPasswordUrl', FrontendNavigation::getURLForBlock('Profiles', 'ForgotPassword'));
    }

    /**
     * Insert or update a single profile setting.
     *
     * @param int    $id    Profile id.
     * @param string $name  Setting name.
     * @param mixed  $value New setting value.
     */
    public static function setSetting($id, $name, $value)
    {
        // insert or update
        FrontendModel::getContainer()->get('database')->execute(
            'INSERT INTO profiles_settings(profile_id, name, value)
             VALUES(?, ?, ?)
             ON DUPLICATE KEY UPDATE value = ?',
            array((int) $id, $name, serialize($value), serialize($value))
        );
    }

    /**
     * Insert or update multiple profile settings.
     *
     * @param int   $id     Profile id.
     * @param array $values Settings in key=>value form.
     */
    public static function setSettings($id, array $values)
    {
        // build parameters
        $parameters = array();
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
     * Update a profile.
     *
     * @param  int   $id     The profile id.
     * @param  array $values The values to update.
     * @return int
     */
    public static function update($id, array $values)
    {
        return (int) FrontendModel::getContainer()->get('database')->update('profiles', $values, 'id = ?', (int) $id);
    }
}
