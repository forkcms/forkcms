<?php

namespace Backend\Modules\Groups\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Groups\Entity\Group;
use Backend\Modules\Groups\Entity\GroupActionRight;
use Backend\Modules\Groups\Entity\GroupModuleRight;

/**
 * In this file we store all generic functions that we will be using in the groups module.
 *
 * @author Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 * @author Mathias Dewelde <mathias@dewelde.be>
 */
class Model
{
    const GROUP_ENTITY_CLASS = 'Backend\Modules\Groups\Entity\Group';
    const ACTION_RIGHT_ENTITY_CLASS = 'Backend\Modules\Groups\Entity\GroupActionRight';
    const MODULE_RIGHT_ENTITY_CLASS = 'Backend\Modules\Groups\Entity\GroupModuleRight';

    // @todo integrate with doctrine
    const QRY_BROWSE =
        'SELECT g.id, g.name, COUNT(u.id) AS num_users
         FROM Groups AS g
         LEFT OUTER JOIN users_groups AS ug ON g.id = ug.group_id
         LEFT OUTER JOIN users AS u ON u.id = ug.user_id
         GROUP BY g.id';

    const QRY_ACTIVE_USERS =
        'SELECT u.id, u.email
         FROM users AS u
         INNER JOIN users_groups AS ug ON u.id = ug.user_id
         WHERE ug.group_id = ? AND u.deleted = ?';


    /**
     * Add action permissions
     *
     * @param Group $group
     * @param array $actionPermissions
     */
    public static function addActionPermissions(Group $group, $actionPermissions)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');

        foreach ((array) $actionPermissions as $permission) {
            if (!self::existsActionPermission($group, $permission)) {
                $actionRight = new GroupActionRight();
                $actionRight->setAction($permission['action']);
                $actionRight->setModule($permission['module']);
                $actionRight->setGroup($group);

                $em->persist($actionRight);
            }
        }

        $em->flush();
    }

    /**
     * Add module permissions
     *
     * @param Group $group
     * @param array $modulePermissions
     */
    public static function addModulePermissions(Group $group, $modulePermissions)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');

        foreach ((array) $modulePermissions as $permission) {
            if (!self::existsModulePermission($group, $permission)) {
                $moduleRight = new GroupModuleRight();
                $moduleRight->setModule($permission['module']);
                $moduleRight->setGroup($group);

                $em->persist($moduleRight);
            }
        }

        $em->flush();
    }

    /**
     * Get a group by name
     *
     * @param string $name
     * @return Group
     */
    public static function getByName($name)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');

        return $em->getRepository(self::GROUP_ENTITY_CLASS)->findOneBy(array('name' => $name));
    }

    /**
     * Delete a group
     *
     * @param Group $group
     */
    public static function delete(Group $group)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');

        $em->remove($group);
        $em->flush();
    }

    /**
     * Delete action permissions
     *
     * @param Group $group
     * @param array $actionPermissions The action permissions to delete.
     */
    public static function deleteActionPermissions(Group $group, $actionPermissions)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');

        foreach ((array) $actionPermissions as $permission) {
            foreach ($group->getAllowedActions() as $allowedAction) {
                if ($permission['module'] == $allowedAction->getModule() && $permission['action'] == $allowedAction->getAction()) {
                    $em->remove($allowedAction);
                }
            }
        }

        $em->flush();
    }

    /**
     * Delete module permissions
     *
     * @param Group $group
     * @param array $modulePermissions The module permissions to delete.
     */
    public static function deleteModulePermissions(Group $group, $modulePermissions)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');

        foreach ((array) $modulePermissions as $permission) {
            foreach ($group->getAllowedModules() as $allowedModule) {
                if ($permission['module'] == $allowedModule->getModule()) {
                    $em->remove($allowedModule);
                }
            }
        }

        $em->flush();
    }

    /**
     * Delete a user's multiple groups
     *
     * @param int $userId The id of the user.
     */
    public static function deleteMultipleGroups($userId)
    {
        // @todo Integrate doctrine
        BackendModel::getContainer()->get('database')->delete('users_groups', 'user_id = ?', array($userId));
    }

    /**
     * Check if a group already exists
     *
     * @param int $id The id to check upon.
     * @return bool
     * @deprecated
     * // @todo Remove this function
     */
    public static function exists($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.*
             FROM groups AS i
             WHERE i.id = ?',
            array((int) $id)
        );
    }

    /**
     * Check if a action permission exists
     *
     * @param Group $group
     * @param array $permission The permission to check upon.
     * @return bool
     */
    public static function existsActionPermission(Group $group, $permission)
    {
        foreach ($group->getAllowedActions() as $allowedAction) {
            if ($permission['module'] == $allowedAction->getModule()
                && $permission['action'] == $allowedAction->getAction()
            ) return true;
        }

        return false;
    }

    /**
     * Check if a module permission exists
     *
     * @param Group $group
     * @param array $permission The permission to check upon.
     * @return bool
     */
    public static function existsModulePermission(Group $group, $permission)
    {
        foreach ($group->getAllowedModules() as $allowedModule) {
            if ($permission['module'] == $allowedModule->getModule()) return true;
        }

        return false;
    }

    /**
     * Get a group
     *
     * @param int $id The id of the group to fetch.
     * @return array
     */
    public static function get($id)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');

        return $em->getRepository(self::GROUP_ENTITY_CLASS)->find($id);
    }

    /**
     * Get group action permissions
     *
     * @param int $id The id of the group.
     * @return array
     * @deprecated
     * // @todo Remove this function
     */
    public static function getActionPermissions($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.module, i.action
             FROM groups_rights_actions AS i
             WHERE i.group_id = ?',
            array((int) $id)
        );
    }

    /**
     * Get all groups to populate a dropdown
     *
     * // @todo Rename this function
     * @return array
     */
    public static function getAll()
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');

        $groups = $em->getRepository(self::GROUP_ENTITY_CLASS)->findAll();

        foreach ($groups as &$group) {
            $group = array(
                'label' => $group->getName(),
                'value' => $group->getId()
            );
        }

        return $groups;
    }

    /**
     * Get all groups of one user
     *
     * @param int $id
     * @return array
     */
    public static function getGroupsByUser($id)
    {
        // @todo Integrate doctrine
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.name
             FROM ForkGroup AS i
             INNER JOIN users_groups AS ug ON i.id = ug.group_id
             WHERE ug.user_id = ?',
            array((int) $id)
        );
    }

    /**
     * Check if a certain user is in a certain group
     *
     * @param int $userId  The user id
     * @param int $groupId The group id
     *
     * @return bool
     */
    public static function isUserInGroup($userId, $groupId)
    {
        $groupsByUser = static::getGroupsByUser($userId);

        $userInGroup = false;

        foreach ($groupsByUser as $group) {
            if ((int) $group['id'] === (int) $groupId) {
                $userInGroup = true;
                break;
            }
        }

        return $userInGroup;
    }

    /**
     * Get group module permissions
     *
     * @param int $id The id of the group.
     * @return array
     * @deprecated
     */
    public static function getModulePermissions($id)
    {
        // @todo Integrate doctrine
        $em = BackendModel::get('doctrine.orm.entity_manager');
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.*
             FROM groups_rights_modules AS i
             WHERE i.group_id = ?',
            array((int) $id)
        );
    }

    /**
     * Get a group setting
     *
     * @param int $groupId The id of the group of the setting.
     * @param string $name The name of the setting to fetch.
     * @return array
     */
    public static function getSetting($groupId, $name)
    {
        // @todo Integrate doctrine
        $setting = (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*
             FROM groups_settings AS i
             WHERE i.group_id = ? AND i.name = ?',
            array((int) $groupId, (string) $name)
        );

        if (isset($setting['value'])) {
            return unserialize($setting['value']);
        }
    }

    /**
     * Get all users in a group
     *
     * @param int $groupId The id of the group.
     * @return array
     */
    public static function getUsers($groupId)
    {
        // @todo use doctrine when the user module supports doctrine too.
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.*
             FROM users AS i
             INNER JOIN users_groups AS ug ON i.id = ug.user_id
             WHERE ug.group_id = ? AND i.deleted = ? AND i.active = ?',
            array((int) $groupId, 'N', 'Y')
        );
    }

    /**
     * Insert a group and a setting
     *
     * @param Group $group The group to insert.
     * @param array $setting The setting to insert.
     */
    public static function insert($group, $setting)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');

        $em->persist($group);
        $em->flush();

        // build setting
        $setting['group_id'] = $group->getId();

        // insert setting
        self::insertSetting($setting);

        // return the id
        return $group;
    }

    /**
     * Insert a user's multiple groups
     *
     * @param int $userId The id of the user.
     * @param array $groups The groups.
     */
    public static function insertMultipleGroups($userId, array $groups)
    {
        // @todo Integrate doctrine
        $userId = (int) $userId;
        $groups = (array) $groups;

        // delete all previous user groups
        self::deleteMultipleGroups($userId);

        // loop through groups
        foreach ($groups as $group) {
            // add user id
            $item['user_id'] = $userId;
            $item['group_id'] = $group;

            // insert item
            BackendModel::getContainer()->get('database')->insert('users_groups', $item);
        }
    }

    /**
     * Insert a group setting
     *
     * @param array $setting
     * @return int
     */
    public static function insertSetting($setting)
    {
        // @todo Integrate doctrine
        return BackendModel::getContainer()->get('database')->insert('groups_settings', $setting);
    }

    /**
     * Update a group
     *
     * @param array $group The group to update.
     * @param array $setting The setting to update.
     */
    public static function update($group, $setting)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');

        // update group
        $em->persist($group);

        // update setting
        self::updateSetting($setting);
    }

    /**
     * Update a group setting
     *
     * @param array $setting The setting to update.
     */
    public static function updateSetting($setting)
    {
        // @todo Integrate doctrine
        BackendModel::getContainer()->get('database')->update('groups_settings', array('value' => $setting['value']), 'group_id = ? AND name = ?', array($setting['group_id'], $setting['name']));
    }
}
