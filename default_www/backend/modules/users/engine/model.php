<?php

/**
 * BackendUserModel
 *
 * In this file we store all generic functions that we will be using in the UsersModule
 *
 *
 * @package		backend
 * @subpackage	users
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
	 * Deletes the reset_password_key and reset_password_timestamp for a given user ID
	 *
	 * @return	void
	 * @param int $userId
	 */
	public static function deleteResetPasswordSettings($userId)
	{
		// redefine
		$userId = (int) $userId;

		// get db
		$db = BackendModel::getDB();

		// delete the settings
		$db->delete('users_settings', "(name = 'reset_password_key' OR name = 'reset_password_timestamp') AND user_id = ?", $userId);
	}


	/**
	 * Does the user exist
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
												array($id, 'Y', 'N')) == 1);

		// fallback, this doesn't hold the active nor deleted status in account
		return ($db->getNumRows('SELECT u.id
									FROM users AS u
									WHERE u.id = ?;',
									array($id)) >= 1);
	}


	/**
	 * Does the user with a given emailadress exist
	 *
	 * @return	bool
	 * @param	string $email
	 */
	public static function existsEmail($email)
	{
		// redefine
		$email = (string) $email;

		// get db
		$db = BackendModel::getDB();

		// check if the user is present in our database
		return ($db->getNumRows('SELECT u.id
									FROM users AS u
									INNER JOIN users_settings AS us
									WHERE us.name = ? AND us.value = ?;',
									array('email', serialize($email))) >= 1);
	}


	/**
	 * Does a username already exist?
	 * If you specify a userId, the username with the given id will be ignored
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
		$user = (array) $db->getRecord('SELECT u.id, u.username
										FROM users AS u
										WHERE u.id = ?;',
										array($id));

		// get user-settings
		$user['settings'] = (array) $db->getPairs('SELECT us.name, us.value
													FROM users_settings AS us
													WHERE us.user_id = ?;',
													array($id));

		// loop settings and unserialize them
		foreach($user['settings'] as $key => $value) $user['settings'][$key] = unserialize($value);

		// return
		return $user;
	}


	/**
	 * Get the user ID linked to a given user e-mail address
	 *
	 * @return	int
	 * @param	string $email
	 */
	public static function getIdByEmail($email)
	{
		// redefine
		$email = (string) $email;

		// get db
		$db = BackendModel::getDB();

		// get user-settings
		$userId = $db->getVar('SELECT user_id
													FROM users_settings AS us
													WHERE us.value = ?;',
													array(serialize($email)));

		// return
		return (int) $userId;
	}


	/**
	 * Get the user ID linked to a given username
	 *
	 * @return	int
	 * @param	string $username
	 */
	public static function getIdByUsername($username)
	{
		// redefine
		$username = (string) $username;

		// get db
		$db = BackendModel::getDB();

		// get user-settings
		$userId = $db->getVar('SELECT u.id
													FROM users AS u
													WHERE u.username = ?;',
													array($username));

		// return
		return (int) $userId;
	}


	/**
	 * Get all users
	 *
	 * @return	array
	 */
	public static function getUsers()
	{
		// get db
		$db = BackendModel::getDB();

		// get general user data and return
		return (array) $db->getPairs('SELECT u.id, u.username
										FROM users AS u;',
										null, 'id');
	}


	/**
	 * Add a new user
	 *
	 * @return	int
	 * @param	array $user
	 * @param	array $settings
	 */
	public static function insert(array $user, array $settings)
	{
		// get db
		$db = BackendModel::getDB();

		// update user
		$userId = (int) $db->insert('users', $user);

		// loop settings
		foreach($settings as $key => $value)
		{
			// insert or update
			$db->execute('INSERT INTO users_settings(user_id, name, value)
							VALUES(?, ?, ?)
							ON DUPLICATE KEY UPDATE value = ?;',
							array($userId, $key, serialize($value), serialize($value)));
		}

		// return the new users' id
		return $userId;
	}


	/**
	 * Save the changes for a given user
	 * Remark: $user['id'] should be available
	 *
	 * @return	void
	 * @param	array $user
	 * @param	array $settings
	 */
	public static function update(array $user, array $settings)
	{
		// get db
		$db = BackendModel::getDB();

		// update user
		$db->update('users', $user, 'id = ?', $user['id']);

		// loop settings
		foreach($settings as $key => $value)
		{
			// insert or update
			$db->execute('INSERT INTO users_settings(user_id, name, value)
							VALUES(?, ?, ?)
							ON DUPLICATE KEY UPDATE value = ?;',
							array($user['id'], $key, serialize($value), serialize($value)));
		}
	}


	/**
	 * Update the user password
	 *
	 * @return	void
	 * @param BackendUser $user
	 * @param string $password
	 */
	public static function updatePassword(BackendUser $user, $password)
	{
		// redefine
		$password = (string) $password;

		// fetch user info
		$userId = $user->getUserId();
		$username = $user->getUsername();
		$key = $user->getSetting('password_key');

		// get db
		$db = BackendModel::getDB();

		// update user
		$db->update('users', array('password' => BackendAuthentication::getEncryptedString($password, $key)), 'id = ?', $userId);

		// remove the user settings linked to the resetting of passwords
		self::deleteResetPasswordSettings($userId);
	}
}

?>