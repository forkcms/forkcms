<?php

namespace Backend\Modules\Users\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\User as BackendUser;

/**
 * In this file we store all generic functions that we will be using in the users module.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class Model
{
    const QRY_BROWSE =
        'SELECT i.id
         FROM users AS i
         WHERE i.deleted = ?';

    /**
     * Mark the user as deleted and deactivate his account.
     *
     * @param int $id The userId to delete.
     */
    public static function delete($id)
    {
        BackendModel::getContainer()->get('database')->update(
            'users',
            array('active' => 'N', 'deleted' => 'Y'),
            'id = ?',
            array((int) $id)
        );
    }

    /**
     * Deletes the reset_password_key and reset_password_timestamp for a given user ID
     *
     * @param int $id The userId wherefore the reset-stuff should be deleted.
     */
    public static function deleteResetPasswordSettings($id)
    {
        BackendModel::getContainer()->get('database')->delete(
            'users_settings',
            '(name = \'reset_password_key\' OR name = \'reset_password_timestamp\') AND user_id = ?',
            array((int) $id)
        );
    }

    /**
     * Was a user deleted before?
     *
     * @param string $email The e-mail address to check.
     * @return bool
     */
    public static function emailDeletedBefore($email)
    {
        // no user to ignore
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM users AS i
             WHERE i.email = ? AND i.deleted = ?
             LIMIT 1',
            array((string) $email, 'Y')
        );
    }

    /**
     * Does the user exist.
     *
     * @param int  $id     The userId to check for existence.
     * @param bool $active Should the user be active also?
     * @return bool
     */
    public static function exists($id, $active = true)
    {
        $id = (int) $id;
        $active = (bool) $active;

        // get db
        $db = BackendModel::getContainer()->get('database');

        // if the user should also be active, there should be at least one row to return true
        if ($active) {
            return (bool) $db->getVar(
                'SELECT 1
                 FROM users AS i
                 WHERE i.id = ? AND i.deleted = ?
                 LIMIT 1',
                array($id, 'N')
            );
        }

        // fallback, this doesn't take the active nor deleted status in account
        return (bool) $db->getVar(
            'SELECT 1
             FROM users AS i
             WHERE i.id = ?
             LIMIT 1',
            array($id)
        );
    }

    /**
     * Does a email already exist?
     * If you specify a userId, the email with the given id will be ignored.
     *
     * @param string $email The email to check for.
     * @param int    $id    The userId to be ignored.
     * @return bool
     */
    public static function existsEmail($email, $id = null)
    {
        $email = (string) $email;
        $id = ($id !== null) ? (int) $id : null;

        // get db
        $db = BackendModel::getContainer()->get('database');

        // userid specified?
        if ($id !== null) {
            return (bool) $db->getVar(
                'SELECT 1
                 FROM users AS i
                 WHERE i.id != ? AND i.email = ?
                 LIMIT 1',
                array($id, $email)
            );
        }

        // no user to ignore
        return (bool) $db->getVar(
            'SELECT 1
             FROM users AS i
             WHERE i.email = ?
             LIMIT 1',
            array($email)
        );
    }

    /**
     * Get all data for a given user
     *
     * @param int $id The userId to get the data for.
     * @return array
     */
    public static function get($id)
    {
        // redefine
        $id = (int) $id;

        // get db
        $db = BackendModel::getContainer()->get('database');

        // get general user data
        $user = (array) $db->getRecord(
            'SELECT i.id, i.email, i.password, i.active
             FROM users AS i
             WHERE i.id = ?',
            array($id)
        );

        // Don't add a settings element, just return an empty array here if no user is found.
        if (empty($user)) {
            return array();
        }

        // get user-settings
        $user['settings'] = (array) $db->getPairs(
            'SELECT s.name, s.value
             FROM users_settings AS s
             WHERE s.user_id = ?',
            array($id)
        );

        // loop settings and unserialize them
        foreach ($user['settings'] as &$value) {
            $value = unserialize($value);
        }

        // return
        return $user;
    }

    /**
     * Get the possible line endings for a CSV-file
     *
     * @return array
     */
    public static function getCSVLineEndings()
    {
        return array(
            '\n' => '\n',
            '\r\n' => '\r\n'
        );
    }

    /**
     * Get the possible CSV split characters
     *
     * @return array
     */
    public static function getCSVSplitCharacters()
    {
        return array(
            ';' => ';',
            ',' => ','
        );
    }

    /**
     * Fetch the list of date formats including examples of these formats.
     *
     * @return array
     */
    public static function getDateFormats()
    {
        // init var
        $possibleFormats = array();

        // loop available formats
        foreach ((array) BackendModel::getModuleSetting('Users', 'date_formats') as $format) {
            $possibleFormats[$format] = \SpoonDate::getDate(
                $format,
                null,
                BackendAuthentication::getUser()->getSetting('interface_language')
            );
        }

        // return
        return $possibleFormats;
    }

    /**
     * Get user groups
     *
     * @return array
     */
    public static function getGroups()
    {
        return (array) BackendModel::getContainer()->get('database')->getPairs(
            'SELECT i.id, i.name
             FROM groups AS i'
        );
    }

    /**
     * Get the user ID linked to a given email
     *
     * @param string $email The email for the user.
     * @return int
     */
    public static function getIdByEmail($email)
    {
        // get user-settings
        $userId = BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.id
             FROM users AS i
             WHERE i.email = ?',
            array((string) $email)
        );

        // userId or false on error
        return ($userId == 0) ? false : (int) $userId;
    }

    /**
     * Fetch the list of number formats including examples of these formats.
     *
     * @return array
     */
    public static function getNumberFormats()
    {
        // init var
        $possibleFormats = array();

        // loop available formats
        foreach ((array) BackendModel::getModuleSetting('Core', 'number_formats') as $format => $example) {
            $possibleFormats[$format] = $example;
        }

        // return
        return $possibleFormats;
    }

    /**
     * Fetch a user setting for a specific user
     *
     * @param int    $userId  The id of the user.
     * @param string $setting The name of the setting to get.
     * @return mixed
     */
    public static function getSetting($userId, $setting)
    {
        return @unserialize(
            BackendModel::getContainer()->get('database')->getVar(
                'SELECT value
                 FROM users_settings
                 WHERE user_id = ? AND name = ?',
                array((int) $userId, (string) $setting)
            )
        );
    }

    /**
     * Fetch the list of time formats including examples of these formats.
     *
     * @return array
     */
    public static function getTimeFormats()
    {
        // init var
        $possibleFormats = array();

        // loop available formats
        foreach (BackendModel::getModuleSetting('Users', 'time_formats') as $format) {
            $possibleFormats[$format] = \SpoonDate::getDate(
                $format,
                null,
                BackendAuthentication::getUser()->getSetting('interface_language')
            );
        }

        // return
        return $possibleFormats;
    }

    /**
     * Get all users
     *
     * @return array
     */
    public static function getUsers()
    {
        // fetch users
        $users = (array) BackendModel::getContainer()->get('database')->getPairs(
            'SELECT i.id, s.value
             FROM users AS i
             INNER JOIN users_settings AS s ON i.id = s.user_id AND s.name = ?
             WHERE i.active = ? AND i.deleted = ?',
            array('nickname', 'Y', 'N')
        );

        // loop users & unserialize
        foreach ($users as &$value) {
            $value = unserialize($value);
        }

        // return
        return $users;
    }

    /**
     * Add a new user.
     *
     * @param array $user     The userdata.
     * @param array $settings The settings for the new user.
     * @return int
     */
    public static function insert(array $user, array $settings)
    {
        // get db
        $db = BackendModel::getContainer()->get('database');

        // update user
        $userId = (int) $db->insert('users', $user);
        $userSettings = array();

        // loop settings
        foreach ($settings as $key => $value) {
            $userSettings[] = array(
                'user_id' => $userId,
                'name' => $key,
                'value' => serialize($value)
            );
        }

        // insert all settings at once
        $db->insert('users_settings', $userSettings);

        // return the new users' id
        return $userId;
    }

    /**
     * Set a user setting for a specific user
     *
     * @param int    $userId  The id of the user.
     * @param string $setting The name of the setting to set.
     * @param string $value   The value of the setting to set.
     * @return mixed
     */
    public static function setSetting($userId, $setting, $value)
    {
        // insert or update
        BackendModel::getContainer()->get('database')->execute(
            'INSERT INTO users_settings(user_id, name, value)
             VALUES(?, ?, ?)
             ON DUPLICATE KEY UPDATE value = ?',
            array($userId, $setting, serialize($value), serialize($value))
        );
    }

    /**
     * Restores a user
     * @later    this method should check if all needed data is present
     *
     * @param string $email The e-mail address of the user to restore.
     * @return bool
     */
    public static function undoDelete($email)
    {
        // redefine
        $email = (string) $email;

        // get db
        $db = BackendModel::getContainer()->get('database');

        // get id
        $id = $db->getVar(
            'SELECT id
             FROM users AS i
             INNER JOIN users_settings AS s ON i.id = s.user_id
             WHERE i.email = ? AND i.deleted = ?',
            array($email, 'Y')
        );

        // no valid users
        if ($id === null) {
            return false;
        } else {
            // restore
            $db->update('users', array('active' => 'Y', 'deleted' => 'N'), 'id = ?', (int) $id);

            // return
            return true;
        }
    }

    /**
     * Save the changes for a given user
     * Remark: $user['id'] should be available
     *
     * @param array $user     The userdata.
     * @param array $settings The settings for the user.
     */
    public static function update(array $user, array $settings)
    {
        // get db
        $db = BackendModel::getContainer()->get('database');

        // update user
        $updated = $db->update('users', $user, 'id = ?', array($user['id']));

        // loop settings
        foreach ($settings as $key => $value) {
            // insert or update
            $db->execute(
                'INSERT INTO users_settings(user_id, name, value)
                 VALUES(?, ?, ?)
                 ON DUPLICATE KEY UPDATE value = ?',
                array($user['id'], $key, serialize($value), serialize($value))
            );
        }

        return $updated;
    }

    /**
     * Update the user password
     *
     * @param BackendUser $user     An instance of BackendUser.
     * @param string      $password The new password for the user.
     */
    public static function updatePassword(BackendUser $user, $password)
    {
        // fetch user info
        $userId = $user->getUserId();
        $key = $user->getSetting('password_key');

        // update user
        BackendModel::getContainer()->get('database')->update(
            'users',
            array('password' => BackendAuthentication::getEncryptedString((string) $password, $key)),
            'id = ?',
            $userId
        );

        // remove the user settings linked to the resetting of passwords
        static::deleteResetPasswordSettings($userId);
    }
}
