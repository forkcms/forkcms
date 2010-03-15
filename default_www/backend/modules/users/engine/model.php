<?php

/**
 * BackendUserModel
 * In this file we store all generic functions that we will be using in the UsersModule
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
						WHERE u.deleted = ?;';


	/**
	 * Mark the user as deleted and reset the active-status
	 *
	 * @return	void
	 * @param	int $id		The userId to delete.
	 */
	public static function delete($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB(true);

		// update the user
		$db->update('users', array('active' => 'N', 'deleted' => 'Y'), 'id = ?', $id);
	}


	/**
	 * Deletes the reset_password_key and reset_password_timestamp for a given user ID
	 *
	 * @return	void
	 * @param	int $id		The userId wherfore the reset-stuff should be deleted.
	 */
	public static function deleteResetPasswordSettings($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB(true);

		// delete the settings
		$db->delete('users_settings', "(name = 'reset_password_key' OR name = 'reset_password_timestamp') AND user_id = ?", $id);
	}


	/**
	 * Does the user exist
	 *
	 * @return	bool
	 * @param	int $id						The userId to check for existance.
	 * @param	bool[optional] $active		Should the user be active also?
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
												WHERE u.id = ? AND u.deleted = ?;',
												array($id, 'N')) == 1);

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
	 * @param	string $email	The email to check for existance.
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
									INNER JOIN users_settings AS us ON u.id = us.user_id
									WHERE u.active = ? AND u.deleted = ? AND us.name = ? AND us.value = ?;',
									array('Y', 'N', 'email', serialize($email))) > 0);
	}


	/**
	 * Does a username already exist?
	 * If you specify a userId, the username with the given id will be ignored
	 *
	 * @return	bool
	 * @param	string $username		The username to check for existance.
	 * @param	int[optional] $id		The userId to be ignored.
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
	 * @param	int $id		The userId to get the data for.
	 */
	public static function get($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// get general user data
		$user = (array) $db->getRecord('SELECT u.id, u.username, u.active, u.group_id
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
	 * Fetch the list of date formats with examples of the formats.
	 *
	 * @return	array
	 */
	public static function getDateFormats()
	{
		// init var
		$possibleFormats = array();

		// loop available formats
		foreach(BackendModel::getSetting('users', 'date_formats') as $format)
		{
			$possibleFormats[$format] = SpoonDate::getDate($format, null, BackendAuthentication::getUser()->getSetting('interface_language'));
		}

		return $possibleFormats;
	}


	/**
	 * Get user groups
	 *
	 * @return	array
	 */
	public static function getGroups()
	{
		// get db
		$db = BackendModel::getDB();

		// return
		return (array) $db->getPairs('SELECT g.id, g.name
										FROM groups AS g');
	}


	/**
	 * Get the user ID linked to a given user e-mail address
	 *
	 * @return	int
	 * @param	string $email	The email for the user.
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
	 * @param	string $username	The username for the user.
	 */
	public static function getIdByUsername($username)
	{
		// get db
		$db = BackendModel::getDB();

		// get user-settings
		$userId = $db->getVar('SELECT u.id
								FROM users AS u
								WHERE u.username = ?;', (string) $username);

		// userId or false on error
		return ($userId == 0) ? false : (int) $userId;
	}


	/**
	 * Fetch the list of time formats with examples of the formats.
	 *
	 * @return	array
	 */
	public static function getTimeFormats()
	{
		// init var
		$possibleFormats = array();

		// loop available formats
		foreach(BackendModel::getSetting('users', 'time_formats') as $format)
		{
			$possibleFormats[$format] = SpoonDate::getDate($format, null, BackendAuthentication::getUser()->getSetting('interface_language'));
		}

		return $possibleFormats;
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
	 * @param	array $user			The userdata.
	 * @param	array $settings		The settings for the new user.
	 */
	public static function insert(array $user, array $settings)
	{
		// get db
		$db = BackendModel::getDB(true);

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
	 * @param	array $user			The userdata.
	 * @param	array $settings		The settings for the user.
	 */
	public static function update(array $user, array $settings)
	{
		// get db
		$db = BackendModel::getDB(true);

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
	 * @param BackendUser $user		An instance of BackendUser.
	 * @param string $password		The new password for the user.
	 */
	public static function updatePassword(BackendUser $user, $password)
	{
		// redefine
		$password = (string) $password;

		// fetch user info
		$userId = $user->getUserId();
		$key = $user->getSetting('password_key');

		// get db
		$db = BackendModel::getDB(true);

		// update user
		$db->update('users', array('password' => BackendAuthentication::getEncryptedString($password, $key)), 'id = ?', $userId);

		// remove the user settings linked to the resetting of passwords
		self::deleteResetPasswordSettings($userId);
	}
}

?>