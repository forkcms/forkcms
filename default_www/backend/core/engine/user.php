<?php

/**
 * BackendUser
 *
 * The class below will handle all stuff relates to the current authenticated user
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendUser
{
	/**
	 * All settings
	 *
	 * @var	array
	 */
	private $aSettings = array();


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
	 * Last timestamp the user logged in
	 *
	 * @var	int
	 */
	private $lastLoggedInDate;


	/**
	 * The users password
	 *
	 * @var	string
	 */
	private $passwordRaw;


	/**
	 * The session id for the user
	 *
	 * @var	string
	 */
	private $sessionId;


	/**
	 * The secret key for the user
	 *
	 * @var	string
	 */
	private $secretKey;


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
	 * Default constructor
	 *
	 * @return	void
	 * @param	int[optional] $userId
	 */
	public function __construct($userId = null)
	{
		// if a userid is given we will load the user in this object
		if($userId !== null) $this->loadUser($userId);
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
	 * Get password
	 *
	 * @return	string
	 */
	public function getPassword()
	{
		return $this->passwordRaw;
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
	 * @param	string $key
	 * @param	mixed[optional] $defaultValue
	 */
	public function getSetting($key, $defaultValue = null)
	{
		// redefine
		$key = (string) $key;

		// if the value isn't present we should set a defaultvalue
		if(!isset($this->aSettings[$key])) $this->setSetting($key, $defaultValue);

		// return
		return $this->aSettings[$key];
	}


	/**
	 * Get all settings at once
	 *
	 * @return	array
	 */
	public function getSettings()
	{
		return (array) $this->aSettings;
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
	 * Is the current userobject a authenticated user?
	 *
	 * @return	bool
	 */
	public function isAuthenticated()
	{
		return $this->isAuthenticated;
	}


	/**
	 * Load a user
	 *
	 * @return	void
	 * @param	int $userId
	 */
	public function loadUser($userId)
	{
		// redefine
		$userId = (int) $userId;

		// get database instance
		$db = BackendModel::getDB();

		// get user-data
		$aUserData = (array) $db->getRecord('SELECT u.id, u.group_id, u.username, u.password_raw,
											us.session_id, us.secret_key, UNIX_TIMESTAMP(us.date) AS date
											FROM users AS u
											INNER JOIN users_sessions AS us On u.id = us.user_id
											WHERE u.id = ? AND u.active = ? AND u.deleted = ?
											LIMIT 1;',
											array($userId, 'Y', 'N'));

		// if there is no data we have to destroy this object, I know this isn't a realistic situation
		if(empty($aUserData)) unset($this);

		// set properties
		$this->setUserId($aUserData['id']);
		$this->setGroupId($aUserData['group_id']);
		$this->setUsername($aUserData['username']);
		$this->setPassword($aUserData['password_raw']);
		$this->setSessionId($aUserData['session_id']);
		$this->setSecretKey($aUserData['secret_key']);
		$this->setLastloggedInDate($aUserData['date']);
		$this->isAuthenticated = true;


		// get settings
		$aSettings = (array) $db->getPairs('SELECT us.name, us.value
											FROM users_settings AS us
											WHERE us.user_id = ?;',
											array($userId));

		// loop settings and store them in the object
		foreach($aSettings as $key => $value) $this->aSettings[$key] = unserialize($value);
	}


	/**
	 * Set groupid
	 *
	 * @return	void
	 * @param	int $value
	 */
	private function setGroupId($value)
	{
		$this->groupId = (int) $value;
	}


	/**
	 * Set last logged in date
	 *
	 * @return	void
	 * @param	int $value
	 */
	private function setLastloggedInDate($value)
	{
		$this->lastLoggedInDate = (int) $value;
	}


	/**
	 * Set password
	 *
	 * @return	void
	 * @param	string $value
	 */
	private function setPassword($value)
	{
		$this->passwordRaw = (string) $value;
	}


	/**
	 * Set secretkey
	 *
	 * @return	void
	 * @param	string $value
	 */
	private function setSecretKey($value)
	{
		$this->secretKey = (string) $value;
	}


	/**
	 * Set sessionid
	 *
	 * @return	void
	 * @param	string $value
	 */
	private function setSessionId($value)
	{
		$this->sessionId = (string) $value;
	}


	/**
	 * Set a setting
	 *
	 * @return	void
	 * @param	string $key
	 * @param	mixed $value
	 */
	public function setSetting($key, $value)
	{
		// redefine
		$key = (string) $key;
		$valueToStore = serialize($value);

		// get db
		$db = BackendModel::getDB();

		// store
		$db->execute('INSERT INTO users_settings(user_id, name, value)
						VALUES(?, ?, ?)
						ON DUPLICATE KEY UPDATE value = ?;',
						array($this->getUserId(), $key, $valueToStore, $valueToStore));

		// cache it
		$this->aSettings[(string) $key] = $value;
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