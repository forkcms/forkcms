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
 */
class Model
{
    const QUERY_BROWSE =
        'SELECT i.id
         FROM users AS i
         WHERE i.deleted = ?';

    /**
     * Mark the user as deleted and deactivate his account.
     *
     * @param int $id The userId to delete.
     */
    public static function delete(int $id): void
    {
        BackendModel::getContainer()->get('database')->update(
            'users',
            ['active' => false, 'deleted' => true],
            'id = ?',
            [$id]
        );
    }

    /**
     * Deletes the reset_password_key and reset_password_timestamp for a given user ID
     *
     * @param int $id The userId wherefore the reset-stuff should be deleted.
     */
    public static function deleteResetPasswordSettings(int $id): void
    {
        BackendModel::getContainer()->get('database')->delete(
            'users_settings',
            '(name = \'reset_password_key\' OR name = \'reset_password_timestamp\') AND user_id = ?',
            [$id]
        );
    }

    /**
     * Was a user deleted before?
     *
     * @param string $email The e-mail address to check.
     *
     * @return bool
     */
    public static function emailDeletedBefore(string $email): bool
    {
        // no user to ignore
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM users AS i
             WHERE i.email = ? AND i.deleted = ?
             LIMIT 1',
            [$email, true]
        );
    }

    /**
     * Does the user exist.
     *
     * @param int  $id     The userId to check for existence.
     * @param bool $active Should the user be active also?
     *
     * @return bool
     */
    public static function exists(int $id, bool $active = true): bool
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        // if the user should also be active, there should be at least one row to return true
        if ($active) {
            return (bool) $database->getVar(
                'SELECT 1
                 FROM users AS i
                 WHERE i.id = ? AND i.deleted = ?
                 LIMIT 1',
                [$id, false]
            );
        }

        // fallback, this doesn't take the active nor deleted status in account
        return (bool) $database->getVar(
            'SELECT 1
             FROM users AS i
             WHERE i.id = ?
             LIMIT 1',
            [$id]
        );
    }

    /**
     * Does a email already exist?
     * If you specify a userId, the email with the given id will be ignored.
     *
     * @param string $email The email to check for.
     * @param int    $id    The userId to be ignored.
     *
     * @return bool
     */
    public static function existsEmail(string $email, int $id = null): bool
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        // userid specified?
        if ($id !== null) {
            return (bool) $database->getVar(
                'SELECT 1
                 FROM users AS i
                 WHERE i.id != ? AND i.email = ?
                 LIMIT 1',
                [$id, $email]
            );
        }

        // no user to ignore
        return (bool) $database->getVar(
            'SELECT 1
             FROM users AS i
             WHERE i.email = ?
             LIMIT 1',
            [$email]
        );
    }

    public static function get(int $id): array
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        // get general user data
        $user = (array) $database->getRecord(
            'SELECT i.id, i.email, i.password, i.active
             FROM users AS i
             WHERE i.id = ?',
            [$id]
        );

        // Don't add a settings element, just return an empty array here if no user is found.
        if (empty($user)) {
            return [];
        }

        // get user-settings
        $user['settings'] = (array) $database->getPairs(
            'SELECT s.name, s.value
             FROM users_settings AS s
             WHERE s.user_id = ?',
            [$id]
        );

        // loop settings and unserialize them
        foreach ($user['settings'] as &$value) {
            $value = unserialize($value);
        }

        // return
        return $user;
    }

    public static function getCSVLineEndings(): array
    {
        return [
            '\n' => '\n',
            '\r\n' => '\r\n',
        ];
    }

    public static function getCSVSplitCharacters(): array
    {
        return [
            ';' => ';',
            ',' => ',',
        ];
    }

    /**
     * Fetch the list of date formats including examples of these formats.
     *
     * @return array
     */
    public static function getDateFormats(): array
    {
        // init var
        $possibleFormats = [];

        // loop available formats
        foreach ((array) BackendModel::get('fork.settings')->get('Users', 'date_formats') as $format) {
            $possibleFormats[$format] = \SpoonDate::getDate(
                $format,
                null,
                BackendAuthentication::getUser()->getSetting('interface_language')
            );
        }

        // return
        return $possibleFormats;
    }

    public static function getGroups(): array
    {
        return (array) BackendModel::getContainer()->get('database')->getPairs(
            'SELECT i.id, i.name
             FROM groups AS i'
        );
    }

    /**
     * Get all module action combinations a user has access to
     *
     * @param string $module
     *
     * @return array
     */
    public static function getModuleGroupsRightsActions(string $module): array
    {
        return (array) BackendModel::get('database')->getRecords(
            'SELECT a.module, a.action
            FROM groups AS g
                INNER JOIN users_groups AS u ON u.group_id = g.id
                INNER JOIN groups_rights_modules AS m ON m.group_id = g.id
                INNER JOIN groups_rights_actions AS a ON a.group_id = g.id
                    AND m.module = a.module
            WHERE m.module = ?
            GROUP BY a.module, a.action',
            $module
        );
    }

    /**
     * Get the user ID linked to a given email
     *
     * @param string $email The email for the user.
     *
     * @return int|false
     */
    public static function getIdByEmail(string $email)
    {
        // get user-settings
        $userId = (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.id
             FROM users AS i
             WHERE i.email = ?',
            [$email]
        );

        if ($userId === 0) {
            return false;
        }

        return $userId;
    }

    /**
     * Fetch the list of number formats including examples of these formats.
     *
     * @return array
     */
    public static function getNumberFormats(): array
    {
        // init var
        $possibleFormats = [];

        // loop available formats
        foreach ((array) BackendModel::get('fork.settings')->get('Core', 'number_formats') as $format => $example) {
            $possibleFormats[$format] = $example;
        }

        // return
        return $possibleFormats;
    }

    public static function getSetting(int $userId, string $setting)
    {
        return @unserialize(
            BackendModel::getContainer()->get('database')->getVar(
                'SELECT value
                 FROM users_settings
                 WHERE user_id = ? AND name = ?',
                [$userId, $setting]
            )
        );
    }

    /**
     * Fetch the list of time formats including examples of these formats.
     *
     * @return array
     */
    public static function getTimeFormats(): array
    {
        // init var
        $possibleFormats = [];

        // loop available formats
        foreach (BackendModel::get('fork.settings')->get('Users', 'time_formats') as $format) {
            $possibleFormats[$format] = \SpoonDate::getDate(
                $format,
                null,
                BackendAuthentication::getUser()->getSetting('interface_language')
            );
        }

        // return
        return $possibleFormats;
    }

    public static function getUsers(): array
    {
        // fetch users
        $users = (array) BackendModel::getContainer()->get('database')->getPairs(
            'SELECT i.id, s.value
             FROM users AS i
             INNER JOIN users_settings AS s ON i.id = s.user_id AND s.name = ?
             WHERE i.active = ? AND i.deleted = ?',
            ['nickname', true, false]
        );

        // loop users & unserialize
        foreach ($users as &$value) {
            $value = unserialize($value);
        }

        // return
        return $users;
    }

    public static function insert(array $user, array $settings): int
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        // update user
        $userId = (int) $database->insert('users', $user);
        $userSettings = [];

        // loop settings
        foreach ($settings as $key => $value) {
            $userSettings[] = [
                'user_id' => $userId,
                'name' => $key,
                'value' => serialize($value),
            ];
        }

        // insert all settings at once
        $database->insert('users_settings', $userSettings);

        // return the new users' id
        return $userId;
    }

    public static function setSetting(int $userId, string $setting, string $value): void
    {
        // insert or update
        BackendModel::getContainer()->get('database')->execute(
            'INSERT INTO users_settings(user_id, name, value)
             VALUES(?, ?, ?)
             ON DUPLICATE KEY UPDATE value = ?',
            [$userId, $setting, serialize($value), serialize($value)]
        );
    }

    /**
     * Restores a user
     *
     * @later this method should check if all needed data is present
     *
     * @param string $email The e-mail address of the user to restore.
     *
     * @return bool
     */
    public static function undoDelete(string $email): bool
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        // get id
        $id = $database->getVar(
            'SELECT id
             FROM users AS i
             INNER JOIN users_settings AS s ON i.id = s.user_id
             WHERE i.email = ? AND i.deleted = ?',
            [$email, true]
        );

        // no valid users
        if ($id === null) {
            return false;
        }

        // restore
        $database->update('users', ['active' => true, 'deleted' => false], 'id = ?', (int) $id);

        // return
        return true;
    }

    /**
     * Save the changes for a given user
     * Remark: $user['id'] should be available
     *
     * @param array $user The userdata.
     * @param array $settings The settings for the user.
     */
    public static function update(array $user, array $settings): int
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        // update user
        $updated = $database->update('users', $user, 'id = ?', [$user['id']]);

        // loop settings
        foreach ($settings as $key => $value) {
            // insert or update
            $database->execute(
                'INSERT INTO users_settings(user_id, name, value)
                 VALUES(?, ?, ?)
                 ON DUPLICATE KEY UPDATE value = ?',
                [$user['id'], $key, serialize($value), serialize($value)]
            );
        }

        return $updated;
    }

    /**
     * Update the user password
     *
     * @param BackendUser $user An instance of BackendUser.
     * @param string $password The new password for the user.
     */
    public static function updatePassword(BackendUser $user, string $password): void
    {
        // fetch user info
        $userId = $user->getUserId();

        // update user
        BackendModel::getContainer()->get('database')->update(
            'users',
            ['password' => BackendAuthentication::encryptPassword($password)],
            'id = ?',
            $userId
        );

        // remove the user settings linked to the resetting of passwords
        self::deleteResetPasswordSettings($userId);
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
        return BackendModel::get('database')->getVar(
            'SELECT password
             FROM users
             WHERE email = :email',
            ['email' => $email]
        );
    }
}
