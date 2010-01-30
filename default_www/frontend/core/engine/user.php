<?php

/**
 * FrontendUser
 *
 * The class below will handle all stuff relates to the current authenticated user
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendUser
{
	/**
	 * An array that will store all userobjects
	 *
	 * @var	array
	 */
	private static $cache = array();


	/**
	 * All settings
	 *
	 * @var	array
	 */
	private $settings = array();


	/**
	 * The users id
	 *
	 * @var	int
	 */
	private $userId;


	/**
	 * The username
	 *
	 * @var	string
	 */
	private $userName;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	int[optional] $userId	If you provide a userId, the object will be loaded with the data for this user
	 */
	public function __construct($userId = null)
	{
		// if a userid is given we will load the user in this object
		if($userId !== null) $this->loadUser($userId);
	}


	/**
	 * Get a backend user
	 *
	 * @return	FrontendUser
	 * @param	int[optional] $userId	The users id in the backend
	 */
	public static function getBackendUser($userId)
	{
		if(!isset(self::$cache[$userId])) self::$cache[$userId] = new FrontendUser($userId);

		// create new instance and return it
		return self::$cache[$userId];
	}


	/**
	 * Get a setting
	 *
	 * @return	mixed			The stored value, if the setting wasn't found null will be returned
	 * @param	string $key		The name of the setting
	 */
	public function getSetting($key)
	{
		// redefine
		$key = (string) $key;

		if(!isset($this->settings[$key])) return null;

		// return
		return $this->settings[$key];
	}


	/**
	 * Get all settings at once
	 *
	 * @return	array	An key-value-array with all settings for this user
	 */
	public function getSettings()
	{
		return (array) $this->settings;
	}


	/**
	 * Get userid
	 *
	 * @return	int
	 */
	public function getUserId()
	{
		return $this->userId;
	}


	/**
	 * Get username
	 *
	 * @return	string
	 */
	public function getUsername()
	{
		return $this->userName;
	}


	/**
	 * Load the data for the given user
	 *
	 * @return	void
	 * @param	int $userId		The users id in the backend
	 */
	public function loadUser($userId)
	{
		// redefine
		$userId = (int) $userId;

		// get database instance
		$db = FrontendModel::getDB();

		// get user-data
		$userData = (array) $db->getRecord('SELECT u.id, u.username
											FROM users AS u
											WHERE u.id = ? AND u.active = ? AND u.deleted = ?
											LIMIT 1;',
											array($userId, 'Y', 'N'));

		// if there is no data we have to destroy this object, I know this isn't a realistic situation
		if(empty($userData)) throw new FrontendException('The user ('. $userId .') doesn\'t exist.');

		// set properties
		$this->setUserId($userData['id']);
		$this->setUsername($userData['username']);

		// get settings
		$settings = (array) $db->getPairs('SELECT us.name, us.value
											FROM users_settings AS us
											WHERE us.user_id = ?;',
											array($userId));

		// loop settings and store them in the object
		foreach($settings as $key => $value) $this->settings[$key] = unserialize($value);
	}


	/**
	 * Set userid
	 *
	 * @return	void
	 * @param	int $value
	 */
	private function setUserId($value)
	{
		$this->userId = (int) $value;
	}


	/**
	 * Set username
	 *
	 * @return	void
	 * @param	string $value
	 */
	private function setUsername($value)
	{
		$this->userName = (string) $value;
	}
}

?>