<?php

/**
 * The class below will handle all stuff relates to the current authenticated user
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendUser
{
	/**
	 * The group id
	 *
	 * @var	int
	 */
	private $groupId;


	/**
	 * Is the user-object a valid one? As in: is the user authenticated
	 *
	 * @var	bool
	 */
	private $isAuthenticated = false;


	/**
	 * Is the authenticated user a god?
	 *
	 * @var	bool
	 */
	private $isGod = false;


	/**
	 * Last timestamp the user logged in
	 *
	 * @var	int
	 */
	private $lastLoggedInDate;


	/**
	 * The secret key for the user
	 *
	 * @var	string
	 */
	private $secretKey;


	/**
	 * The session id for the user
	 *
	 * @var	string
	 */
	private $sessionId;


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
	 * Default constructor
	 *
	 * @return	void
	 * @param	int[optional] $userId		The id of the user.
	 * @param	string[optional] $email		The e-mail address of the user.
	 */
	public function __construct($userId = null, $email = null)
	{
		if($userId !== null) $this->loadUser((int) $userId);
		if($email !== null) $this->loadUserByEmail($email);
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
	 * Get groupid
	 *
	 * @return	int
	 */
	public function getGroupId()
	{
		return $this->groupId;
	}


	/**
	 * Get last logged in date
	 *
	 * @return	int
	 */
	public function getLastloggedInDate()
	{
		return $this->lastLoggedInDate;
	}


	/**
	 * Get secretkey
	 *
	 * @return	string
	 */
	public function getSecretKey()
	{
		return $this->secretKey;
	}


	/**
	 * Get sessionId
	 *
	 * @return	string
	 */
	public function getSessionId()
	{
		return $this->sessionId;
	}


	/**
	 * Get a setting
	 *
	 * @return	mixed
	 * @param	string $key						The key for the setting to get.
	 * @param	mixed[optional] $defaultValue	Default value, will be stored if the setting isn't set.
	 */
	public function getSetting($key, $defaultValue = null)
	{
		// redefine
		$key = (string) $key;

		// if the value isn't present we should set a defaultvalue
		if(!isset($this->settings[$key])) $this->setSetting($key, $defaultValue);

		// return
		return $this->settings[$key];
	}


	/**
	 * Fetch a user setting for a specific user
	 *
	 * @return	mixed
	 * @param	int $userId			The id of the user.
	 * @param	string $setting		The name of the setting to get.
	 */
	public static function getSettingByUserId($userId, $setting)
	{
		return @unserialize(BackendModel::getDB()->getVar('SELECT value
															FROM users_settings
															WHERE user_id = ? AND name = ?',
															array((int) $userId, (string) $setting)));
	}


	/**
	 * Get all settings at once
	 *
	 * @return	array
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
	 * Is the current userobject a authenticated user?
	 *
	 * @return	bool
	 */
	public function isAuthenticated()
	{
		return $this->isAuthenticated;
	}


	/**
	 * Is the current user a God?
	 *
	 * @return	bool
	 */
	public function isGod()
	{
		return $this->isGod;
	}


	/**
	 * Load a user
	 *
	 * @return	void
	 * @param	int $userId		The id of the user to load.
	 */
	public function loadUser($userId)
	{
		// redefine
		$userId = (int) $userId;

		// get database instance
		$db = BackendModel::getDB();

		// get user-data
		$userData = (array) $db->getRecord('SELECT u.id, u.email, u.is_god,
											us.session_id, us.secret_key, UNIX_TIMESTAMP(us.date) AS date
											FROM users AS u
											LEFT OUTER JOIN users_sessions AS us ON u.id = us.user_id AND us.session_id = ?
											WHERE u.id = ?
											LIMIT 1',
											array(SpoonSession::getSessionId(), $userId));

		// if there is no data we have to destroy this object, I know this isn't a realistic situation
		if(empty($userData)) throw new BackendException('user (' . $userId . ') can\'t be loaded.');

		// set properties
		$this->setUserId($userData['id']);
		$this->setEmail($userData['email']);
		$this->setSessionId($userData['session_id']);
		$this->setSecretKey($userData['secret_key']);
		$this->setLastloggedInDate($userData['date']);
		$this->isAuthenticated = true;
		$this->isGod = ($userData['is_god'] == 'Y');

		// get settings
		$settings = (array) $db->getPairs('SELECT us.name, us.value
											FROM users_settings AS us
											WHERE us.user_id = ?',
											array($userId));

		// loop settings and store them in the object
		foreach($settings as $key => $value) $this->settings[$key] = unserialize($value);

		// nickname available?
		if(!isset($this->settings['nickname']) || $this->settings['nickname'] == '') $this->setSetting('nickname', $this->settings['name'] . ' ' . $this->settings['surname']);
	}


	/**
	 * Load a user by his e-mail adress
	 *
	 * @return	void
	 * @param	string $email		The email of the user to load.
	 */
	public function loadUserByEmail($email)
	{
		// redefine
		$email = (string) $email;

		// get database instance
		$db = BackendModel::getDB();

		// get user-data
		$userData = (array) $db->getRecord('SELECT u.id, u.email, u.is_god,
											us.session_id, us.secret_key, UNIX_TIMESTAMP(us.date) AS date
											FROM users AS u
											LEFT OUTER JOIN users_sessions AS us ON u.id = us.user_id AND us.session_id = ?
											WHERE u.email = ?
											LIMIT 1',
											array(SpoonSession::getSessionId(), $email));

		// if there is no data we have to destroy this object, I know this isn't a realistic situation
		if(empty($userData)) throw new BackendException('user (' . $email . ') can\'t be loaded.');

		// set properties
		$this->setUserId($userData['id']);
		$this->setEmail($userData['email']);
		$this->setSessionId($userData['session_id']);
		$this->setSecretKey($userData['secret_key']);
		$this->setLastloggedInDate($userData['date']);
		$this->isAuthenticated = true;
		$this->isGod = ($userData['is_god'] == 'Y');

		// get settings
		$settings = (array) $db->getPairs('SELECT us.name, us.value
											FROM users_settings AS us
											INNER JOIN users AS u ON us.user_id = u.id
											WHERE u.email = ?',
											array($email));

		// loop settings and store them in the object
		foreach($settings as $key => $value) $this->settings[$key] = unserialize($value);

		// nickname available?
		if(!isset($this->settings['nickname']) || $this->settings['nickname'] == '') $this->setSetting('nickname', $this->settings['name'] . ' ' . $this->settings['surname']);
	}


	/**
	 * Set email
	 *
	 * @return	void
	 * @param	string $value	The email to set.
	 */
	private function setEmail($value)
	{
		$this->email = (string) $value;
	}


	/**
	 * Set groupid
	 *
	 * @return	void
	 * @param	int $value	The id of the group.
	 */
	private function setGroupId($value)
	{
		$this->groupId = (int) $value;
	}


	/**
	 * Set last logged in date
	 *
	 * @return	void
	 * @param	int $value	The date (UNIX-timestamp) to set.
	 */
	private function setLastloggedInDate($value)
	{
		$this->lastLoggedInDate = (int) $value;
	}


	/**
	 * Set secretkey
	 *
	 * @return	void
	 * @param	string $value	The secret key.
	 */
	private function setSecretKey($value)
	{
		$this->secretKey = (string) $value;
	}


	/**
	 * Set sessionid
	 *
	 * @return	void
	 * @param	string $value	The sessionID.
	 */
	private function setSessionId($value)
	{
		$this->sessionId = (string) $value;
	}


	/**
	 * Set a setting
	 *
	 * @return	void
	 * @param	string $key		The key of the setting.
	 * @param	mixed $value	The value to store.
	 */
	public function setSetting($key, $value)
	{
		// redefine
		$key = (string) $key;
		$valueToStore = serialize($value);

		// get db
		$db = BackendModel::getDB(true);

		// store
		$db->execute('INSERT INTO users_settings(user_id, name, value)
						VALUES(?, ?, ?)
						ON DUPLICATE KEY UPDATE value = ?',
						array($this->getUserId(), $key, $valueToStore, $valueToStore));

		// cache it
		$this->settings[(string) $key] = $value;
	}


	/**
	 * Set userid
	 *
	 * @return	void
	 * @param	int $value	The Id of the user.
	 */
	private function setUserId($value)
	{
		$this->userId = (int) $value;
	}
}

?>