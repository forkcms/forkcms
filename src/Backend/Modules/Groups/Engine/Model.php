<?php

namespace Backend\Modules\Groups\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;

/**
 * In this file we store all generic functions that we will be using in the groups module.
 */
class Model
{
    const QUERY_BROWSE =
        'SELECT g.id, g.name, COUNT(u.id) AS num_users
         FROM groups AS g
         LEFT OUTER JOIN users_groups AS ug ON g.id = ug.group_id
         LEFT OUTER JOIN users AS u ON u.id = ug.user_id
         GROUP BY g.id';

    const QUERY_ACTIVE_USERS =
        'SELECT u.id, u.email
         FROM users AS u
         INNER JOIN users_groups AS ug ON u.id = ug.user_id
         WHERE ug.group_id = ? AND u.deleted = ?';

    public static function addActionPermissions(array $actionPermissions): void
    {
        foreach ((array) $actionPermissions as $permission) {
            if (!self::existsActionPermission($permission)) {
                BackendModel::getContainer()->get('database')->insert('groups_rights_actions', $permission);
            }
        }
    }

    public static function addModulePermissions(array $modulePermissions): void
    {
        foreach ((array) $modulePermissions as $permission) {
            if (!self::existsModulePermission($permission)) {
                BackendModel::getContainer()->get('database')->insert('groups_rights_modules', $permission);
            }
        }
    }

    public static function alreadyExists(string $groupName): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.*
             FROM groups AS i
             WHERE i.name = ?',
            [$groupName]
        );
    }

    public static function delete(int $groupId): void
    {
        BackendModel::getContainer()->get('database')->delete('groups', 'id = ?', [$groupId]);
    }

    public static function deleteActionPermissions(array $actionPermissions): void
    {
        foreach ((array) $actionPermissions as $permission) {
            if (self::existsActionPermission($permission)) {
                BackendModel::getContainer()->get('database')->delete(
                    'groups_rights_actions',
                    'group_id = ? AND module = ? AND action = ?',
                    [$permission['group_id'], $permission['module'], $permission['action']]
                );
            }
        }
    }

    public static function deleteModulePermissions(array $modulePermissions): void
    {
        foreach ((array) $modulePermissions as $permission) {
            if (self::existsModulePermission($permission)) {
                BackendModel::getContainer()->get('database')->delete(
                    'groups_rights_modules',
                    'group_id = ? AND module = ?',
                    [$permission['group_id'], $permission['module']]
                );
            }
        }
    }

    public static function deleteMultipleGroups(int $userId): void
    {
        BackendModel::getContainer()->get('database')->delete('users_groups', 'user_id = ?', [$userId]);
    }

    /**
     * Check if a group already exists
     *
     * @param int $id The id to check upon.
     *
     * @return bool
     */
    public static function exists(int $id): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.*
             FROM groups AS i
             WHERE i.id = ?',
            [$id]
        );
    }

    public static function existsActionPermission(array $permission): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.*
             FROM groups_rights_actions AS i
             WHERE i.module = ? AND i.group_id = ? AND i.action = ?',
            [$permission['module'], $permission['group_id'], $permission['action']]
        );
    }

    public static function existsModulePermission(array $permission): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.*
             FROM groups_rights_modules AS i
             WHERE i.module = ? AND i.group_id = ?',
            [$permission['module'], $permission['group_id']]
        );
    }

    public static function get(int $groupId): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*
             FROM groups AS i
             WHERE i.id = ?',
            [$groupId]
        );
    }

    public static function getActionPermissions(int $groupId): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.module, i.action
             FROM groups_rights_actions AS i
             WHERE i.group_id = ?',
            [$groupId]
        );
    }

    public static function getAll(): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id AS value, i.name AS label FROM groups AS i'
        );
    }

    public static function getGroupsByUser(int $userId): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.name
             FROM groups AS i
             INNER JOIN users_groups AS ug ON i.id = ug.group_id
             WHERE ug.user_id = ?',
            [$userId]
        );
    }

    public static function isUserInGroup(int $userId, int $groupId): bool
    {
        $groupsByUser = static::getGroupsByUser($userId);

        foreach ($groupsByUser as $group) {
            if ($group['id'] === $groupId) {
                return true;
            }
        }

        return false;
    }

    public static function getModulePermissions(int $groupId): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.*
             FROM groups_rights_modules AS i
             WHERE i.group_id = ?',
            [$groupId]
        );
    }

    public static function getSetting(int $groupId, string $settingName): array
    {
        $setting = (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.value
             FROM groups_settings AS i
             WHERE i.group_id = ? AND i.name = ?',
            [$groupId, $settingName]
        );

        if (empty($setting)) {
            return [];
        }

        if (isset($setting['value'])) {
            return unserialize($setting['value']);
        }

        return [];
    }

    public static function getUsers(int $groupId): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.*
             FROM users AS i
             INNER JOIN users_groups AS ug ON i.id = ug.user_id
             WHERE ug.group_id = ? AND i.deleted = ? AND i.active = ?',
            [$groupId, false, true]
        );
    }

    /**
     * Insert a group and a setting
     *
     * @param array $group The group to insert.
     * @param array $setting The setting to insert.
     *
     * @return int
     */
    public static function insert(array $group, array $setting): int
    {
        // insert group
        $groupId = BackendModel::getContainer()->get('database')->insert('groups', $group);

        // build setting
        $setting['group_id'] = $groupId;

        // insert setting
        self::insertSetting($setting);

        // return the id
        return $groupId;
    }

    public static function insertMultipleGroups(int $userId, array $groups): void
    {
        // delete all previous user groups
        self::deleteMultipleGroups($userId);

        // loop through groups

        foreach ($groups as $group) {
            // insert item
            BackendModel::getContainer()->get('database')->insert(
                'users_groups',
                ['user_id' => $userId, 'group_id' => $group]
            );
        }
    }

    public static function insertSetting(array $setting): int
    {
        return BackendModel::getContainer()->get('database')->insert('groups_settings', $setting);
    }

    /**
     * Update a group
     *
     * @param array $group The group to update.
     * @param array $setting The setting to update.
     */
    public static function update(array $group, array $setting): void
    {
        // update group
        BackendModel::getContainer()->get('database')->update('groups', ['name' => $group['name']], 'id = ?', [$group['id']]);

        // update setting
        self::updateSetting($setting);
    }

    public static function updateSetting(array $setting): void
    {
        BackendModel::getContainer()->get('database')->update('groups_settings', ['value' => $setting['value']], 'group_id = ? AND name = ?', [$setting['group_id'], $setting['name']]);
    }
}
