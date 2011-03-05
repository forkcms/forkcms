<?php

/**
 * The class below will handle all stuff relates to users
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
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
	 * The email
	 *
	 * @var	string
	 */
	private $email;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	int[optional] $userId	If you provide a userId, the object will be loaded with the data for this user.
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
	 * @param	int $userId		The users id in the backend.
	 */
	public static function getBackendUser($userId)
	{
		// create new instance if neccessary and cache it
		if(!isset(self::$cache[$userId])) self::$cache[$userId] = new FrontendUser($userId);

		return self::$cache[$userId];
	}


	/**
	 * Get email
	 *
	 * @return	string
	 */
	public function getEmail()
	{
		return $this->email;
	}


	/**
	 * Get a setting
	 *
	 * @return	mixed			The stored value, if the setting wasn't found null will be returned
	 * @param	string $key		The name of the setting.
	 */
	public function getSetting($key)
	{
		// redefine
		$key = (string) $key;

		// not set? return null
		if(!isset($this->settings[$key])) return null;

		// return
		return $this->settings[$key];
	}


	/**
	 * Get all settings at once
	 *
	 * @return	array	An key-value-array with all settings for this user.
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
	 * Load the data for the given user
	 *
	 * @return	void
	 * @param	int $userId		The users id in the backend.
	 */
	public function loadUser($userId)
	{
		// redefine
		$userId = (int) $userId;

		// get database instance
		$db = FrontendModel::getDB();

		// get user-data
		$userData = (array) $db->getRecord('SELECT u.id, u.email
											FROM users AS u
											WHERE u.id = ?
											LIMIT 1',
											array($userId));

		// if there is no data we have to destroy this object, I know this isn't a realistic situation
		if(empty($userData)) throw new FrontendException('The user (' . $userId . ') doesn\'t exist.');

		// set properties
		$this->setUserId($userData['id']);
		$this->setEmail($userData['email']);

		// get settings
		$settings = (array) $db->getPairs('SELECT us.name, us.value
											FROM users_settings AS us
											WHERE us.user_id = ?',
											array($userId));

		// loop settings and store them in the object
		foreach($settings as $key => $value) $this->settings[$key] = unserialize($value);
	}


	/**
	 * Set email
	 *
	 * @return	void
	 * @param	string $value	The email-address.
	 */
	private function setEmail($value)
	{
		$this->email = (string) $value;
	}


	/**
	 * Set userid
	 *
	 * @return	void
	 * @param	int $value	The user's id.
	 */
	private function setUserId($value)
	{
		$this->userId = (int) $value;
	}
}

?>