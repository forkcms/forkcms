<?php

/**
 * BackendUserModel
 *
 * In this file we store all generic functions that we will be using in the UserModule
 *
 *
 * @package		backend
 * @subpackage	user
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendUsersModel
{
	// overview of the active users
	const QRY_BROWSE = 'SELECT u.id, u.username
						FROM users AS u
						WHERE u.active = ? AND u.deleted = ?;';


	/**
	 * Mark the user as deleted and reset the active-status
	 *
	 * @return	void
	 * @param	int $id
	 */
	public static function delete($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// update the user
		$db->update('users', array('active' => 'N', 'deleted' => 'Y'), 'id = ?', $id);
	}


	/**
	 * Does the user exists
	 *
	 * @return	bool
	 * @param	int $id
	 * @param	bool[optional] $active
	 */
	public static function exists($id, $active = true)
	{
		// redefine
		$id = (int) $id;
		$active = (bool) $active;

		// get db
		$db = BackendModel::getDB();

		// if the user should also be active, there should be at least one row to return true
		if($active) return ($db->getNumRows('SELECT u.id
												FROM users AS u
												WHERE u.id = ? AND u.active = ? AND u.deleted = ?;',
												array($id, 'Y', 'N')) >= 1);

		// fallback, this doesn't hold the active nor deleted status in account
		return ($db->getNumRows('SELECT u.id
									FROM users AS u
									WHERE u.id = ?;',
									array($id)) >= 1);
	}


	/**
	 * Does a username already exists?
	 * If you specify an userId, the username with the given id will be ignored
	 *
	 * @return	bool
	 * @param	string $username
	 * @param	int[optional] $id
	 */
	public static function existsUsername($username, $id = null)
	{
		// redefine
		$username = (string) $username;
		$id = ($id !== null) ? (int) $id : null;

		// get db
		$db = BackendModel::getDB();

		// userid specified?
		if($id !== null) return (bool) ($db->getNumRows('SELECT u.id
															FROM users AS u
															WHERE u.id != ? AND u.username = ?;',
															array($id, $username)) >= 1);

		// no user to ignore
		return (bool) ($db->getNumRows('SELECT u.id
										FROM users AS u
										WHERE u.username = ?;',
										array($username)) >= 1);
	}


	/**
	 * Get all data for a given user
	 *
	 * @return	array
	 * @param	int $id
	 */
	public static function get($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// get general user data
		$aUser = (array) $db->getRecord('SELECT u.id, u.username, u.password_raw
										FROM users AS u
										WHERE u.id = ?
										LIMIT 1;',
										array($id));

		// get user-settings
		$aUser['settings'] = (array) $db->getPairs('SELECT us.name, us.value
													FROM users_settings AS us
													WHERE us.user_id = ?;',
													array($id));

		// loop settings and unserialize them
		foreach($aUser['settings'] as $key => $value) $aUser['settings'][$key] = unserialize($value);

		// return
		return $aUser;
	}


	/**
	 * Add a new user
	 *
	 * @return	int
	 * @param	array $aUser
	 * @param	array $aSettings
	 */
	public static function insert(array $aUser, array $aSettings)
	{
		// redefine
		$aUser = (array) $aUser;
		$aSettings = (array) $aSettings;

		// get db
		$db = BackendModel::getDB();

		// update user
		$userId = (int) $db->insert('users', $aUser);

		// loop settings
		foreach($aSettings as $key => $value)
		{
			// insert or update
			$db->execute('INSERT INTO users_settings(user_id, name, value)
							VALUES(?, ?, ?)
							ON DUPLICATE KEY UPDATE value = ?;',
							array($userId, $key, serialize($value), serialize($value)));
		}

		// return the new users id
		return $userId;
	}


	/**
	 * Save the changes for a given user
	 * Remark: $aUser['id'] should be available
	 *
	 * @return	void
	 * @param	array $aUser
	 * @param	array $aSettings
	 */
	public static function update(array $aUser, array $aSettings)
	{
		// redefine
		$aUser = (array) $aUser;
		$aSettings = (array) $aSettings;

		// get db
		$db = BackendModel::getDB();

		// update user
		$db->update('users', $aUser, 'id = ?', $aUser['id']);

		// loop settings
		foreach($aSettings as $key => $value)
		{
			// insert or update
			$db->execute('INSERT INTO users_settings(user_id, name, value)
							VALUES(?, ?, ?)
							ON DUPLICATE KEY UPDATE value = ?;',
							array($aUser['id'], $key, serialize($value), serialize($value)));
		}
	}

}

?>