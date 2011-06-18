<?php

/**
 * In this file we store all generic functions that we will be using in the groups module.
 *
 * @package		backend
 * @subpackage	groups
 *
 * @author		Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 * @since		2.0
 */
class BackendGroupsModel
{
	/**
	 * Overview of the active groups
	 *
	 * @var	string
	 */
	const QRY_BROWSE = 'SELECT g.id, g.name, COUNT(u.id) AS num_users
	                    FROM groups AS g
	                    LEFT OUTER JOIN users_groups AS ug ON g.id = ug.group_id
	                    LEFT OUTER JOIN users AS u ON u.id = ug.user_id
	                    GROUP BY g.id';


	/**
	 * Overview of the active users
	 *
	 * @var string
	 */
	const QRY_ACTIVE_USERS = 'SELECT u.id, u.email
	                          FROM users AS u
	                          INNER JOIN users_groups AS ug ON u.id = ug.user_id
	                          WHERE ug.group_id = ? AND u.deleted = ?';


	/**
	 * Add action permissions
	 *
	 * @return	void
	 * @param	array $actionPermissions	The action permissions to add.
	 */
	public static function addActionPermissions($actionPermissions)
	{
		// get the database
		$db = BackendModel::getDB(true);

		// loop through module permissions
		foreach((array) $actionPermissions as $permission)
		{
			// if permissions does not exist, add
			if(!self::existsActionPermission($permission)) $db->insert('groups_rights_actions', $permission);
		}
	}


	/**
	 * Add module permissions
	 *
	 * @return	void
	 * @param	array $modulePermissions	The module permissions to add.
	 */
	public static function addModulePermissions($modulePermissions)
	{
		// get the database
		$db = BackendModel::getDB(true);

		// loop through module permissions
		foreach((array) $modulePermissions as $permission)
		{
			// if permissions does not exist, add
			if(!self::existsModulePermission($permission)) $db->insert('groups_rights_modules', $permission);
		}
	}


	/**
	 * Check if a group already exists
	 *
	 * @return	bool
	 * @param	string $name	The name to check upon.
	 */
	public static function alreadyExists($name)
	{
		// redefine
		$name = (string) $name;

		// check if group exists
		return (bool) BackendModel::getDB()->getVar('SELECT i.*
		                                             FROM groups AS i
		                                             WHERE i.name = ?',
		                                             array($name));
	}


	/**
	 * Delete a group
	 *
	 * @return	void
	 * @param	int $id		The id of the group to delete.
	 */
	public static function delete($id)
	{
		// redefine
		$id = (int) $id;

		// delete group
		BackendModel::getDB(true)->delete('groups', 'id = ?', array($id));
	}


	/**
	 * Delete action permissions
	 *
	 * @return	void
	 * @param 	array $actionPermissions	The action permissions to delete.
	 */
	public static function deleteActionPermissions($actionPermissions)
	{
		// get the database
		$db = BackendModel::getDB(true);

		// loop through module permissions
		foreach((array) $actionPermissions as $permission)
		{
			// if permissions exists, delete
			if(self::existsActionPermission($permission)) $db->delete('groups_rights_actions', 'group_id = ? AND module = ? AND action = ?', array($permission['group_id'], $permission['module'], $permission['action']));
		}
	}


	/**
	 * Delete module permissions
	 *
	 * @return	void
	 * @param 	array $modulePermissions	The module permissions to delete.
	 */
	public static function deleteModulePermissions($modulePermissions)
	{
		// get the database
		$db = BackendModel::getDB(true);

		// loop through module permissions
		foreach((array) $modulePermissions as $permission)
		{
			// if permissions exists, delete
			if(self::existsModulePermission($permission)) $db->delete('groups_rights_modules', 'group_id = ? AND module = ?', array($permission['group_id'], $permission['module']));
		}
	}


	/**
	 * Delete a user's multiple groups
	 *
	 * @return	void
	 * @param 	int $userId		The id of the user.
	 */
	public static function deleteMultipleGroups($userId)
	{
		// delete multiple groups
		BackendModel::getDB(true)->delete('users_groups', 'user_id = ?', array($userId));
	}


	/**
	 * Check if a group already exists
	 *
	 * @return	bool
	 * @param	int $id		The id to check upon.
	 */
	public static function exists($id)
	{
		// redefine
		$id = (int) $id;

		// check if group exists
		return (bool) BackendModel::getDB()->getVar('SELECT i.*
		                                             FROM groups AS i
		                                             WHERE i.id = ?',
		                                             array($id));
	}


	/**
	 * Check if a action permission exists
	 *
	 * @return	bool
	 * @param 	array $permission	The permission to check upon.
	 */
	public static function existsActionPermission($permission)
	{
		// check if permission exists
		return (bool) BackendModel::getDB()->getVar('SELECT i.*
		                                             FROM groups_rights_actions AS i
		                                             WHERE i.module = ? AND i.group_id = ? AND i.action = ?',
		                                             array($permission['module'], $permission['group_id'], $permission['action']));
	}


	/**
	 * Check if a module permission exists
	 *
	 * @return	bool
	 * @param 	array $permission	The permission to check upon.
	 */
	public static function existsModulePermission($permission)
	{
		// check if permission exists
		return (bool) BackendModel::getDB()->getVar('SELECT i.*
		                                             FROM groups_rights_modules AS i
		                                             WHERE i.module = ? AND i.group_id = ?',
		                                             array($permission['module'], $permission['group_id']));
	}


	/**
	 * Get a group
	 *
	 * @return	array
	 * @param	int $id		The id of the group to fetch.
	 */
	public static function get($id)
	{
		// redefine
		$id = (int) $id;

		// get and return record
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
		                                                 FROM groups AS i
		                                                 WHERE i.id = ?',
		                                                 array($id));
	}


	/**
	 * Get group action permissions
	 *
	 * @return	array
	 * @param	int $id		The id of the group.
	 */
	public static function getActionPermissions($id)
	{
		// redefine
		$id = (int) $id;

		// get and return records
		return (array) BackendModel::getDB()->getRecords('SELECT i.module, i.action
		                                                  FROM groups_rights_actions AS i
		                                                  WHERE i.group_id = ?',
		                                                  array($id));
	}


	/**
	 * Get all groups
	 *
	 * @return	array
	 */
	public static function getAll()
	{
		// get and return all groups
		return (array) BackendModel::getDB()->getRecords('SELECT i.id AS value, i.name AS label
		                                                  FROM groups AS i');
	}


	/**
	 * Get all groups of one user
	 *
	 * @return	array
	 * @param 	int $id			The user id.
	 */
	public static function getGroupsByUser($id)
	{
		// get and return groups
		return (array) BackendModel::getDB()->getRecords('SELECT i.id, i.name
		                                                  FROM groups AS i
		                                                  INNER JOIN users_groups AS ug ON i.id = ug.group_id
		                                                  WHERE ug.user_id = ?',
		                                                  array($id));
	}


	/**
	 * Get group module permissions
	 *
	 * @return	array
	 * @param 	int $id		The id of the group.
	 */
	public static function getModulePermissions($id)
	{
		// redefine
		$id = (int) $id;

		// get and return records
		return (array) BackendModel::getDB()->getRecords('SELECT i.*
		                                                  FROM groups_rights_modules AS i
		                                                  WHERE i.group_id = ?',
		                                                  array($id));
	}


	/**
	 * Get a group setting
	 *
	 * @return	array
	 * @param	int $groupId		The id of the group of the setting.
	 * @param	string $name		The name of the setting to fetch.
	 */
	public static function getSetting($groupId, $name)
	{
		// redefine
		$groupId = (int) $groupId;
		$name = (string) $name;

		// get setting
		$setting = (array) BackendModel::getDB()->getRecord('SELECT i.*
		                                                     FROM groups_settings AS i
		                                                     WHERE i.group_id = ? AND i.name = ?',
		                                                     array($groupId, $name));

		// unserialize value and return
		if(isset($setting['value'])) return unserialize($setting['value']);
	}


	/**
	 * Get all users in a group
	 *
	 * @return	array
	 * @param 	int $groupId		The id of the group.
	 */
	public static function getUsers($groupId)
	{
		// redefine
		$groupId = (int) $groupId;

		// get and return all users
		return (array) BackendModel::getDB()->getRecords('SELECT i.*
		                                                  FROM users AS i
		                                                  INNER JOIN users_groups AS ug ON i.id = ug.user_id
		                                                  WHERE ug.group_id = ? AND i.deleted = ? AND i.active = ?',
		                                                  array($groupId, 'N', 'Y'));
	}


	/**
	 * Insert a group and a setting
	 *
	 * @return	void
	 * @param 	array $group		The group to insert.
	 * @param	array $setting		The setting to insert.
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
	 * @return	void
	 * @param 	int $userId			The id of the user.
	 * @param 	array $groups		The groups.
	 */
	public static function insertMultipleGroups($userId, $groups)
	{
		// redefine
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
	 * @return	int
	 * @param	array $setting	The setting to insert.
	 */
	public static function insertSetting($setting)
	{
		// insert setting and return id
		return BackendModel::getDB(true)->insert('groups_settings', $setting);
	}


	/**
	 * Update a group
	 *
	 * @return	void
	 * @param	array $group		The group to update.
	 * @param	array $setting		The setting to update.
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
	 * @return	void
	 * @param	array $setting	The setting to update.
	 */
	public static function updateSetting($setting)
	{
		// update setting
		BackendModel::getDB(true)->update('groups_settings', array('value' => $setting['value']), 'group_id = ? AND name = ?', array($setting['group_id'], $setting['name']));
	}
}

?>