<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using to get and set profile information.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jan Moesen <jan.moesen@netlash.com>
 */
class FrontendProfilesProfile
{
	/**
	 * The display name.
	 *
	 * @var	string
	 */
	private $displayName;

	/**
	 * The profile email.
	 *
	 * @var	string
	 */
	private $email;

	/**
	 * The groups this profile belongs to, if any. The keys are the group IDs, the values the HTML-escaped group names.
	 *
	 * @var	array
	 */
	protected $groups;

	/**
	 * The profile id.
	 *
	 * @var	int
	 */
	private $id;

	/**
	 * The profile register date (unix timestamp).
	 *
	 * @var	int
	 */
	private $registeredOn;

	/**
	 * The profile settings.
	 *
	 * @var	array
	 */
	private $settings = array();

	/**
	 * The profile status.
	 *
	 * @var	string
	 */
	private $status;

	/**
	 * The profile url.
	 *
	 * @var	string
	 */
	private $url;

	/**
	 * Constructor.
	 *
	 * @param int[optional] $profileId The profile id to load data from.
	 */
	public function __construct($profileId = null)
	{
		if($profileId !== null) $this->loadProfile((int) $profileId);
	}

	/**
	 * Get display name.
	 *
	 * @return string
	 */
	public function getDisplayName()
	{
		return $this->displayName;
	}

	/**
	 * Get email.
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Get profile id.
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get registered on date.
	 *
	 * @return int
	 */
	public function getRegisteredOn()
	{
		return $this->registeredOn;
	}

	/**
	 * Get a profile setting by name.
	 *
	 * @param string $name Setting name.
	 * @param string[optional] $defaultValue Default value is used when the setting does not exist.
	 * @return mixed
	 */
	public function getSetting($name, $defaultValue = null)
	{
		// if settings array does not exists then get it first
		if(empty($this->settings)) $this->settings = $this->getSettings();

		// when setting exists return it
		if(array_key_exists($name, $this->settings)) return $this->settings[$name];

		// if not return default value
		else return $defaultValue;
	}

	/**
	 * Get all settings.
	 *
	 * @return array
	 */
	public function getSettings()
	{
		// if settings array does not exist then get it first
		if(empty($this->settings)) $this->settings = FrontendProfilesModel::getSettings($this->getId());

		// return settings
		return $this->settings;
	}

	/**
	 * Get status.
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Get profile url.
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Does this user belong to the group with the given ID?
	 *
	 * @param int $groupId Group id.
	 * @return int
	 */
	public function isInGroup($groupId)
	{
		return isset($this->groups[$groupId]);
	}

	/**
	 * Load a user profile by id.
	 *
	 * @param int $id Profile id to load.
	 */
	private function loadProfile($id)
	{
		// get profile data
		$profileData = (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT p.id, p.email, p.status, p.display_name, p.url, UNIX_TIMESTAMP(p.registered_on) AS registered_on
			 FROM profiles AS p
			 WHERE p.id = ?',
			(int) $id
		);

		// set properties
		$this->setId($profileData['id']);
		$this->setUrl($profileData['url']);
		$this->setEmail($profileData['email']);
		$this->setStatus($profileData['status']);
		$this->setDisplayName($profileData['display_name']);
		$this->setRegisteredOn($profileData['registered_on']);

		// get the groups (only the ones we still have access to)
		$this->groups = (array) FrontendModel::getContainer()->get('database')->getPairs(
			'SELECT pg.id, pg.name
			 FROM profiles_groups AS pg
			 INNER JOIN profiles_groups_rights AS pgr ON pg.id = pgr.group_id
			 WHERE pgr.profile_id = :id AND (pgr.expires_on IS NULL OR pgr.expires_on >= NOW())',
			array(':id' => (int) $id)
		);
	}

	/**
	 * Load a profile by URL
	 *
	 * @param string $url
	 */
	public function loadProfileByUrl($url)
	{
		// get profile data
		$profileData = (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT p.id, p.email, p.status, p.display_name, UNIX_TIMESTAMP(p.registered_on) AS registered_on
			 FROM profiles AS p
			 WHERE p.url = ?',
			(string) $url
		);

		// set properties
		$this->setId($profileData['id']);
		$this->setEmail($profileData['email']);
		$this->setStatus($profileData['status']);
		$this->setDisplayName($profileData['display_name']);
		$this->setRegisteredOn($profileData['registered_on']);

		// get the groups (only the ones we still have access to)
		$this->groups = (array) FrontendModel::getContainer()->get('database')->getPairs(
			'SELECT pg.id, pg.name
			 FROM profiles_groups AS pg
			 INNER JOIN profiles_groups_rights AS pgr ON pg.id = pgr.group_id
			 WHERE pgr.profile_id = :id AND (pgr.expires_on IS NULL OR pgr.expires_on >= NOW())',
			array(':id' => (int) $this->getId())
		);

		$this->settings = (array) FrontendModel::getContainer()->get('database')->getPairs(
			'SELECT i.name, i.value
			 FROM profiles_settings AS i
			 WHERE i.profile_id = ?',
			 $this->getId()
		);

		foreach($this->settings as &$value) $value = unserialize($value);
	}

	/**
	 * Set a display name.
	 *
	 * @param string $value Display name value.
	 */
	public function setDisplayName($value)
	{
		$this->displayName = (string) $value;
	}

	/**
	 * Set a profile email.
	 *
	 * @param string $value Email address.
	 */
	public function setEmail($value)
	{
		$this->email = (string) $value;
	}

	/**
	 * Set a profile id.
	 *
	 * @param int $value Id of the profile.
	 */
	public function setId($value)
	{
		$this->id = (int) $value;
	}

	/**
	 * Set a register date.
	 *
	 * @param int $value Register date timestamp.
	 */
	public function setRegisteredOn($value)
	{
		$this->registeredOn = (int) $value;
	}

	/**
	 * Set a profile setting.
	 *
	 * @param string $name Setting name.
	 * @param string $value New setting value.
	 */
	public function setSetting($name, $value)
	{
		// set setting
		FrontendProfilesModel::setSetting($this->getId(), (string) $name, $value);

		// add setting to cache
		$this->settings[$name] = $value;
	}

	/**
	 * Insert or update multiple profile settings.
	 *
	 * @param array $values Settings in key=>value form.
	 */
	public function setSettings(array $values)
	{
		// set settings
		FrontendProfilesModel::setSettings($this->getId(), $values);

		// add settings to cache
		foreach($values as $key => $value)
		{
			$this->settings[$key] = $value;
		}
	}

	/**
	 * Set a profile status.
	 *
	 * @param string $value Status.
	 */
	public function setStatus($value)
	{
		$this->status = (string) $value;
	}

	/**
	 * Set a profile url.
	 *
	 * @param string $value Url.
	 */
	public function setUrl($value)
	{
		$this->url = (string) $value;
	}

	/**
	 * Convert the object into an array for usage in the template
	 *
	 * @return array
	 */
	public function toArray()
	{
		// basis info
		$return['display_name'] = $this->getDisplayName();
		$return['registered_on'] = $this->getRegisteredOn();

		// add settings
		foreach($this->settings as $key => $value) $return['settings'][$key] = $value;

		// urls
		$return['url']['dashboard'] = FrontendNavigation::getURLForBlock('profiles');
		$return['url']['settings'] = FrontendNavigation::getURLForBlock('profiles', 'settings');
		$return['url']['url'] = $this->getUrl();

		return $return;
	}
}
