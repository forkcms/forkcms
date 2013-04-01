<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * The class below will handle all stuff relates to users
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
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
	 * @param int[optional] $userId If you provide a userId, the object will be loaded with the data for this user.
	 */
	public function __construct($userId = null)
	{
		// if a userid is given we will load the user in this object
		if($userId !== null) $this->loadUser($userId);
	}

	/**
	 * Get a backend user
	 *
	 * @param int $userId The users id in the backend.
	 * @return FrontendUser
	 */
	public static function getBackendUser($userId)
	{
		// create new instance if necessary and cache it
		if(!isset(self::$cache[$userId])) self::$cache[$userId] = new FrontendUser($userId);

		return self::$cache[$userId];
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Get a setting
	 *
	 * @param string $key The name of the setting.
	 * @return mixed The stored value, if the setting wasn't found null will be returned
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
	 * @return array An key-value-array with all settings for this user.
	 */
	public function getSettings()
	{
		return (array) $this->settings;
	}

	/**
	 * Get userid
	 *
	 * @return int
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * Load the data for the given user
	 *
	 * @param int $userId The users id in the backend.
	 */
	public function loadUser($userId)
	{
		$userId = (int) $userId;

		// get database instance
		$db = FrontendModel::getContainer()->get('database');

		// get user-data
		$userData = (array) $db->getRecord(
			'SELECT u.id, u.email
			 FROM users AS u
			 WHERE u.id = ?
			 LIMIT 1',
			array($userId)
		);

		// if there is no data we have to destroy this object, I know this isn't a realistic situation
		if(empty($userData)) throw new FrontendException('The user (' . $userId . ') doesn\'t exist.');

		// set properties
		$this->setUserId($userData['id']);
		$this->setEmail($userData['email']);

		// get settings
		$settings = (array) $db->getPairs(
			'SELECT us.name, us.value
			 FROM users_settings AS us
			 WHERE us.user_id = ?',
			array($userId)
		);

		// loop settings and store them in the object
		foreach($settings as $key => $value) $this->settings[$key] = unserialize($value);
	}

	/**
	 * Set email
	 *
	 * @param string $value The email-address.
	 */
	private function setEmail($value)
	{
		$this->email = (string) $value;
	}

	/**
	 * Set userid
	 *
	 * @param int $value The user's id.
	 */
	private function setUserId($value)
	{
		$this->userId = (int) $value;
	}
}
