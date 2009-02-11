<?php

/**
 * BackendModel
 *
 * In this file we store all generic functions that we will be using in the backend.
 *
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendModel
{
	/**
	 * Cached module settings
	 *
	 * @var	array
	 */
	private static $aModuleSettings;


	/**
	 * Creates an url for a given action and module
	 * If you don't specify an action the current action will be used
	 * If you don't specify a module the current module will be used
	 * If you don't specify a language the current language will be used
	 *
	 * @return	string
	 * @param	string[optional] $action
	 * @param	string[optiona] $module
	 * @param	string[optiona] $language
	 */
	public static function createURLForAction($action = null, $module = null, $language = null)
	{
		// grab the url from the reference
		$url = Spoon::getObjectReference('url');

		// redefine parameters
		$action = ($action !== null) ? (string) $action : $url->getAction();
		$module = ($module !== null) ? (string) $module : $url->getModule();
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// build the url and return it
		return '/'. NAMED_APPLICATION .'/'. $language .'/'. $module .'/'. $action;
	}


	/**
	 * Get (or create and get) a database-connection
	 * If the database wasn't stored in teh reference before we will create it and add it
	 *
	 * @return	SpoonDatabase
	 */
	public static function getDB()
	{
		// do we have a db-object ready?
		if(!Spoon::isObjectReference('database'))
		{
			// create instance
			$db = new SpoonDatabase(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

			// store
			Spoon::setObjectReference('database', $db);
		}

		// return it
		return Spoon::getObjectReference('database');
	}


	/**
	 * Get a certain module-setting
	 *
	 * @return	mixed
	 * @param	string $module
	 * @param	string $key
	 * @param	mixed $defaultValue
	 */
	public static function getModuleSetting($module, $key, $defaultValue = null)
	{
		// are the values available
		if(empty(self::$aModuleSettings)) self::getModuleSettings();

		// redefine
		$module = (string) $module;
		$key = (string) $key;

		// if the value isn't present we should set a defaultvalue
		if(!isset(self::$aModuleSettings[$module][$key])) self::setSetting($module, $key, $defaultValue);

		// return
		return self::$aModuleSettings[$module][$key];
	}


	/**
	 * Get all module settings at once
	 *
	 * @return	array
	 */
	public static function getModuleSettings()
	{
		// are the values available
		if(empty(self::$aModuleSettings))
		{
			// get db
			$db = self::getDB();

			// get all settings
			$aModuleSettings = (array) $db->retrieve('SELECT ms.module, ms.name, ms.value
														FROM modules_settings AS ms;');

			// loop and store settings in the cache
			foreach($aModuleSettings as $setting) self::$aModuleSettings[$setting['module']][$setting['name']] = unserialize($setting['value']);
		}

		// return
		return self::$aModuleSettings;
	}


	/**
	 * Saves a module-setting into the DB and the cached array
	 *
	 * @return	void
	 * @param	string $module
	 * @param	string $key
	 * @param	string $value
	 */
	public static function setSetting($module, $key, $value)
	{
		// redefine
		$module = (string) $module;
		$key = (string) $key;
		$valueToStore = serialize($value);

		// get db
		$db = BackendModel::getDB();

		// store
		$db->execute('INSERT INTO modules_settings(module, name, value)
						VALUES(?, ?, ?)
						ON DUPLICATE KEY UPDATE value = ?;',
						array($module, $key, $valueToStore, $valueToStore));

		// cache it
		self::$aModuleSettings[$module][$key] = $value;
	}
}

?>