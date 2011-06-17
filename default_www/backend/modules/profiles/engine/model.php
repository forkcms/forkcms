<?php

/**
 * In this file we store all generic functions that we will be using in the profiles module.
 *
 * @package		backend
 * @subpackage	profiles
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendProfilesModel
{
	/**
	 * Browse groups for datagrid.
	 *
	 * @var	string
	 */
	const QRY_DATAGRID_BROWSE_PROFILE_GROUPS = 'SELECT gr.id, g.name AS group_name, UNIX_TIMESTAMP(gr.expires_on) AS expires_on
												FROM profiles_groups AS g
												INNER JOIN profiles_groups_rights AS gr ON gr.group_id = g.id AND (gr.expires_on IS NULL OR gr.expires_on > NOW())
												WHERE gr.profile_id = ?';


	/**
	 * Delete the given profiles.
	 *
	 * @return	void
	 * @param 	mixed $ids		One ID, or an array of IDs.
	 */
	public static function delete($ids)
	{
		// init db
		$db = BackendModel::getDB(true);

		// redefine
		$ids = (array) $ids;

		// delete profiles
		foreach($ids as $id)
		{
			// redefine
			$id = (int) $id;

			// delete sessions
			$db->delete('profiles_sessions', 'profile_id = ?', $id);

			// set profile status to deleted
			self::update($id, array('status' => 'deleted'));
		}
	}


	/**
	 * Delete a profile group.
	 *
	 * @return	void
	 * @param 	int $id			Id of the group.
	 */
	public static function deleteGroup($id)
	{
		// redefine
		$id = (int) $id;

		// delete rights
		BackendModel::getDB(true)->delete('profiles_groups_rights', 'group_id = ?', $id);

		// delete group
		BackendModel::getDB(true)->delete('profiles_groups', 'id = ?', $id);
	}


	/**
	 * Delete a membership of a profile in a group.
	 *
	 * @return	void
	 * @param 	int $id		Id of the membership.
	 */
	public static function deleteProfileGroup($id)
	{
		BackendModel::getDB(true)->delete('profiles_groups_rights', 'id = ?', (int) $id);
	}


	/**
	 * Delete a sessions of a profile.
	 *
	 * @return	void
	 * @param 	int $id		Profile id.
	 */
	public static function deleteSession($id)
	{
		BackendModel::getDB(true)->delete('profiles_sessions', 'profile_id = ?', (int) $id);
	}


	/**
	 * Check if a profile exists.
	 *
	 * @return	bool
	 * @param 	int $id		Profile id.
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(p.id)
														FROM profiles AS p
														WHERE p.id = ?',
														(int) $id);
	}


	/**
	 * Check if a profile exists by email address.
	 *
	 * @return	bool
	 * @param 	string $email			Email address to check for existence.
	 * @param	int[optional] $id		Profile id to ignore.
	 */
	public static function existsByEmail($email, $id = null)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(p.id)
														FROM profiles AS p
														WHERE p.email = ? AND p.id != ?',
														array((string) $email, (int) $id));
	}


	/**
	 * Check if a display name exists.
	 *
	 * @return	bool
	 * @param 	string $displayName		The display name to check.
	 * @param	int[optional] $id 		Profile id to ignore.
	 */
	public static function existsDisplayName($displayName, $id = null)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(p.id)
														FROM profiles AS p
														WHERE p.display_name = ? AND p.id != ?',
														array((string) $displayName, (int) $id));
	}


	/**
	 * Check if a group exists.
	 *
	 * @return	bool
	 * @param 	int $id		Group id.
	 */
	public static function existsGroup($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(pg.id)
														FROM profiles_groups AS pg
														WHERE pg.id = ?',
														(int) $id);
	}


	/**
	 * Check if a group name exists.
	 *
	 * @return	bool
	 * @param 	string $groupName	Group name.
	 * @param	int[optional] $id	Group id to ignore.
	 */
	public static function existsGroupName($groupName, $id = null)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(pg.id)
														FROM profiles_groups AS pg
														WHERE pg.name = ? AND pg.id != ?',
														array((string) $groupName, (int) $id));
	}


	/**
	 * Check if a profile is in a group.
	 *
	 * @return	bool
	 * @param 	int $id		Membership id.
	 */
	public static function existsProfileGroup($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(gr.id)
														FROM profiles_groups_rights AS gr
														WHERE gr.id = ?',
														(int) $id);
	}


	/**
	 * Get information about a profile.
	 *
	 * @return	array
	 * @param 	int $id		The profile id to get the information for.
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT p.id, p.email, p.status, p.display_name, p.url
															FROM profiles AS p
															WHERE p.id = ?',
															(int) $id);
	}


	/**
	 * Encrypt a string with a salt.
	 *
	 * @return	string
	 * @param	string $string		String to encrypt.
	 * @param	string $salt		Salt to saltivy the string with.
	 */
	public static function getEncryptedString($string, $salt)
	{
		return md5(sha1(md5((string) $string)) . sha1(md5((string) $salt)));
	}


	/**
	 * Get information about a profile group.
	 *
	 * @return	array
	 * @param 	int $id			Id of the group.
	 */
	public static function getGroup($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT pg.id, pg.name
															FROM profiles_groups AS pg
															WHERE pg.id = ?',
															(int) $id);
	}


	/**
	 * Get the list of all groups as array($groupId => $groupName).
	 *
	 * @return	array
	 */
	public static function getGroups()
	{
		return (array) BackendModel::getDB()->getPairs('SELECT id, name FROM profiles_groups ORDER BY name');
	}


	/**
	 * Get profile groups for dropdown not yet linked to a profile
	 *
	 * @return	array
	 * @param	int $profileId				Profile id.
	 * @param	int[optional] $includeId	Group id to always include.
	 */
	public static function getGroupsForDropDown($profileId, $includeId = null)
	{
		// init db
		$db = BackendModel::getDB();

		// get groups already linked but dont include the includeId
		if($includeId !== null) $groupIds = (array) $db->getColumn('SELECT group_id
																	FROM profiles_groups_rights
																	WHERE profile_id = ? AND id != ?',
																	array($profileId, $includeId));

		// get groups already linked
		else $groupIds = (array) $db->getColumn('SELECT group_id
													FROM profiles_groups_rights
													WHERE profile_id = ?',
													(int) $profileId);

		// get groups not yet linked
		return (array) $db->getPairs('SELECT id, name
										FROM profiles_groups
										WHERE id NOT IN(\'' . implode('\',\'', $groupIds) . '\')');
	}


	/**
	 * Get information about a profile group where a user is member of.
	 *
	 * @return	array
	 * @param	int $id		Membership id.
	 */
	public static function getProfileGroup($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT gr.id, gr.profile_id, g.id AS group_id, g.name, UNIX_TIMESTAMP(gr.expires_on) AS expires_on
														FROM profiles_groups_rights AS gr
														INNER JOIN profiles_groups AS g ON g.id = gr.group_id
														WHERE gr.id = ?',
														(int) $id);
	}


	/**
	 * Get the groups where a profile is member of.
	 *
	 * @return	array
	 * @param	int $id		The profile id to get the groups for.
	 */
	public static function getProfileGroups($id)
	{
		return (array) BackendModel::getDB()->getRecords('SELECT gr.id, gr.group_id, g.name AS group_name, gr.expires_on
															FROM profiles_groups AS g
															INNER JOIN profiles_groups_rights AS gr ON gr.group_id = g.id
															WHERE gr.profile_id = ?',
															(int) $id);
	}


	/**
	 * Generate a random string.
	 *
	 * @return	string
	 * @param	int[optional] $length			Length of random string.
	 * @param	bool[optional] $numeric			Use numeric characters.
	 * @param	bool[optional] $lowercase		Use alphanumeric lowercase characters.
	 * @param	bool[optional] $uppercase		Use alphanumeric uppercase characters.
	 * @param	bool[optional] $special			Use special characters.
	 */
	public static function getRandomString($length = 15, $numeric = true, $lowercase = true, $uppercase = true, $special = true)
	{
		// init
		$characters = '';
		$string = '';

		// possible characters
		if($numeric) $characters .= '1234567890';
		if($lowercase) $characters .= 'abcdefghijklmnopqrstuvwxyz';
		if($uppercase) $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		if($special) $characters .= '-_.:;,?!@#&=)([]{}*+%$';

		// get random characters
		for($i = 0; $i < $length; $i++)
		{
			// random index
			$index = mt_rand(0, strlen($characters));

			// add character to salt
			$string .= mb_substr($characters, $index, 1, SPOON_CHARSET);
		}

		// cough up
		return $string;
	}


	/**
	 * Get a setting for a profile.
	 *
	 * @return	string
	 * @param	int $id			Profile id.
	 * @param	string $name	Setting name.
	 */
	public static function getSetting($id, $name)
	{
		return unserialize((string) BackendModel::getDB()->getVar('SELECT ps.value
		                                                           FROM profiles_settings AS ps
		                                                           WHERE ps.profile_id = ? AND ps.name = ?',
		                                                          array((int) $id, (string) $name)));
	}


	/**
	 * Fetch the list of status, but for a dropdown.
	 *
	 * @return	array
	 */
	public static function getStatusForDropDown()
	{
		// fetch types
		$status = BackendModel::getDB()->getEnumValues('profiles', 'status');

		// init
		$labels = $status;

		// loop and build labels
		foreach($labels as &$row) $row = ucfirst(BackendLanguage::getLabel(ucfirst($row)));

		// build array
		return array_combine($status, $labels);
	}


	/**
	 * Retrieve a unique URL for a profile based on the display name.
	 *
	 * @return	string						The unique URL.
	 * @param	string $displayName			The display name to base on.
	 * @param	int[optional] $id			The id of the profile to ignore.
	 */
	public static function getUrl($displayName, $id = null)
	{
		// decode specialchars
		$displayName = SpoonFilter::htmlspecialcharsDecode((string) $displayName);

		// urlise
		$url = SpoonFilter::urlise((string) $displayName);

		// get db
		$db = BackendModel::getDB();

		// new item
		if($id === null)
		{
			// get number of profiles with this URL
			$number = (int) $db->getVar('SELECT COUNT(p.id)
											FROM profiles AS p
											WHERE p.url = ?',
											(string) $url);

			// already exists
			if($number != 0)
			{
				// add number
				$url = BackendModel::addNumber($url);

				// try again
				return self::getURL($url);
			}
		}

		// current profile should be excluded
		else
		{
			// get number of profiles with this URL
			$number = (int) $db->getVar('SELECT COUNT(p.id)
											FROM profiles AS p
											WHERE p.url = ? AND p.id != ?',
											array((string) $url, (int) $id));

			// already exists
			if($number != 0)
			{
				// add number
				$url = BackendModel::addNumber($url);

				// try again
				return self::getURL($url, $id);
			}
		}

		// cough up new url
		return $url;
	}


	/**
	 * Insert a new group.
	 *
	 * @return	int
	 * @param 	array $values	Group data.
	 */
	public static function insertGroup(array $values)
	{
		return (int) BackendModel::getDB(true)->insert('profiles_groups', $values);
	}


	/**
	 * Add a profile to a group.
	 *
	 * @return	int
	 * @param 	array $values	Membership data.
	 */
	public static function insertProfileGroup(array $values)
	{
		return (int) BackendModel::getDB(true)->insert('profiles_groups_rights', $values);
	}


	/**
	 * Insert or update a single profile setting.
	 *
	 * @return	void
	 * @param	int $id			Profile id.
	 * @param	string $name	Setting name.
	 * @param	mixed $value	Setting value.
	 */
	public static function setSetting($id, $name, $value)
	{
		// insert or update
		BackendModel::getDB(true)->execute('INSERT INTO profiles_settings(profile_id, name, value)
											VALUES(?, ?, ?)
											ON DUPLICATE KEY UPDATE value = ?',
											array((int) $id, $name, serialize($value), serialize($value)));
	}


	/**
	 * Update a profile.
	 *
	 * @return	int
	 * @param	int $id			The profile id.
	 * @param	array $values	The values to update.
	 */
	public static function update($id, array $values)
	{
		return (int) BackendModel::getDB(true)->update('profiles', $values, 'id = ?', (int) $id);
	}


	/**
	 * Update a profile group.
	 *
	 * @return	int
	 * @param 	int $id			Group id.
	 * @param 	array $values	Group data.
	 */
	public static function updateGroup($id, array $values)
	{
		return (int) BackendModel::getDB(true)->update('profiles_groups', $values, 'id = ?', (int) $id);
	}


	/**
	 * Update a membership of a profile in a group.
	 *
	 * @return	int
	 * @param 	int $id			Membership id.
	 * @param 	array $values	Membership data.
	 */
	public static function updateProfileGroup($id, array $values)
	{
		return (int) BackendModel::getDB(true)->update('profiles_groups_rights', $values, 'id = ?', (int) $id);
	}
}

?>