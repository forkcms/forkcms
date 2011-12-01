<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the groups module.
 *
 * @author Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 */
class BackendGroupsModel
{
	const QRY_BROWSE =
		'SELECT g.id, g.name, COUNT(u.id) AS num_users
		 FROM groups AS g
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
	 * @param array $actionPermissions
	 */
	public static function addActionPermissions($actionPermissions)
	{
		foreach((array) $actionPermissions as $permission)
		{
			if(!self::existsActionPermission($permission))
			{
				BackendModel::getDB(true)->insert('groups_rights_actions', $permission);
			}
		}
	}

	/**
	 * Add module permissions
	 *
	 * @param array $modulePermissions
	 */
	public static function addModulePermissions($modulePermissions)
	{
		foreach((array) $modulePermissions as $permission)
		{
			if(!self::existsModulePermission($permission))
			{
				BackendModel::getDB(true)->insert('groups_rights_modules', $permission);
			}
		}
	}

	/**
	 * Check if a group already exists
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function alreadyExists($name)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT i.*
			 FROM groups AS i
			 WHERE i.name = ?',
			array((string) $name)
			);
	}

	/**
	 * Delete a group
	 *
	 * @param int $id The id of the group to delete.
	 */
	public static function delete($id)
	{
		BackendModel::getDB(true)->delete('groups', 'id = ?', array((int) $id));
	}

	/**
	 * Delete action permissions
	 *
	 * @param  array $actionPermissions The action permissions to delete.
	 */
	public static function deleteActionPermissions($actionPermissions)
	{
		foreach((array) $actionPermissions as $permission)
		{
			if(self::existsActionPermission($permission))
			{
				BackendModel::getDB(true)->delete(
					'groups_rights_actions',
					'group_id = ? AND module = ? AND action = ?',
					array($permission['group_id'], $permission['module'], $permission['action'])
				);
			}
		}
	}

	/**
	 * Delete module permissions
	 *
	 * @param  array $modulePermissions The module permissions to delete.
	 */
	public static function deleteModulePermissions($modulePermissions)
	{
		foreach((array) $modulePermissions as $permission)
		{
			if(self::existsModulePermission($permission))
			{
				BackendModel::getDB(true)->delete(
					'groups_rights_modules',
					'group_id = ? AND module = ?',
					array($permission['group_id'], $permission['module'])
				);
			}
		}
	}

	/**
	 * Delete a user's multiple groups
	 *
	 * @param  int $userId The id of the user.
	 */
	public static function deleteMultipleGroups($userId)
	{
		BackendModel::getDB(true)->delete('users_groups', 'user_id = ?', array($userId));
	}

	/**
	 * Check if a group already exists
	 *
	 * @param int $id The id to check upon.
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT i.*
			 FROM groups AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Check if a action permission exists
	 *
	 * @param  array $permission The permission to check upon.
	 * @return bool
	 */
	public static function existsActionPermission($permission)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT i.*
			 FROM groups_rights_actions AS i
			 WHERE i.module = ? AND i.group_id = ? AND i.action = ?',
			array($permission['module'], $permission['group_id'], $permission['action'])
		);
	}

	/**
	 * Check if a module permission exists
	 *
	 * @param  array $permission The permission to check upon.
	 * @return bool
	 */
	public static function existsModulePermission($permission)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT i.*
			 FROM groups_rights_modules AS i
			 WHERE i.module = ? AND i.group_id = ?',
			array($permission['module'], $permission['group_id'])
		);
	}

	/**
	 * Get a group
	 *
	 * @param int $id The id of the group to fetch.
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT i.*
			 FROM groups AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get group action permissions
	 *
	 * @param int $id The id of the group.
	 * @return array
	 */
	public static function getActionPermissions($id)
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT i.module, i.action
			 FROM groups_rights_actions AS i
			 WHERE i.group_id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get all groups
	 *
	 * @return array
	 */
	public static function getAll()
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT i.id AS value, i.name AS label FROM groups AS i'
		);
	}

	/**
	 * Get all groups of one user
	 *
	 * @param  int $id
	 * @return array
	 */
	public static function getGroupsByUser($id)
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT i.id, i.name
			 FROM groups AS i
			 INNER JOIN users_groups AS ug ON i.id = ug.group_id
			 WHERE ug.user_id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get group module permissions
	 *
	 * @param  int $id The id of the group.
	 * @return array
	 */
	public static function getModulePermissions($id)
	{
		return (array) BackendModel::getDB()->getRecords(
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
		$setting = (array) BackendModel::getDB()->getRecord(
			'SELECT i.*
			 FROM groups_settings AS i
			 WHERE i.group_id = ? AND i.name = ?',
			array((int) $groupId, (string) $name)
		);

		if(isset($setting['value']))
		{
			return unserialize($setting['value']);
		}
	}

	/**
	 * Get all users in a group
	 *
	 * @param  int $groupId The id of the group.
	 * @return array
	 */
	public static function getUsers($groupId)
	{
		return (array) BackendModel::getDB()->getRecords(
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
	 * @param  array $group The group to insert.
	 * @param array $setting The setting to insert.
	 */
	public static function insert($group, $setting)
	{
		// insert group
		$id = BackendModel::getDB(true)->insert('groups', $group);

		// build setting
		$setting['group_id'] = $id;

		// insert setting
		self::insertSetting($setting);

		// return the id
		return $id;
	}

	/**
	 * Insert a user's multiple groups
	 *
	 * @param  int $userId The id of the user.
	 * @param  array $groups The groups.
	 */
	public static function insertMultipleGroups($userId, array $groups)
	{
		$userId = (int) $userId;
		$groups = (array) $groups;

		// delete all previous user groups
		self::deleteMultipleGroups($userId);

		// loop through groups
		foreach($groups as $group)
		{
			// add user id
			$item['user_id'] = $userId;
			$item['group_id'] = $group;

			// insert item
			BackendModel::getDB(true)->insert('users_groups', $item);
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
		return BackendModel::getDB(true)->insert('groups_settings', $setting);
	}

	/**
	 * Update a group
	 *
	 * @param array $group The group to update.
	 * @param array $setting The setting to update.
	 */
	public static function update($group, $setting)
	{
		// update group
		BackendModel::getDB(true)->update('groups', array('name' => $group['name']), 'id = ?', array($group['id']));

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
		BackendModel::getDB(true)->update('groups_settings', array('value' => $setting['value']), 'group_id = ? AND name = ?', array($setting['group_id'], $setting['name']));
	}
}
