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
	private static $moduleSettings;


	/**
	 * Checks the settings and optionally returns an array with warnings
	 *
	 * @return	array
	 */
	public static function checkSettings()
	{
		// init var
		$warnings = array();
		$akismetModules = BackendSettingsModel::getModulesThatRequireAkismet();
		$googleMapsModules = BackendSettingsModel::getModulesThatRequireGoogleMaps();

		// akismet key
		if(!empty($akismetModules) && BackendModel::getSetting('core', 'akismet_key', null) == '')
		{
			// add warning
			$warnings[] = array('message' => BL::getError('AkismetKey'));
		}

		// google maps key
		if(!empty($googleMapsModules) && BackendModel::getSetting('core', 'google_maps_key', null) == '')
		{
			// add warning
			$warnings[] = array('message' => BL::getError('GoogleMapsKey'));
		}

		return $warnings;
	}



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
	 * @param	array[optional] $parameters
	 * @param	bool[optional] $urlencode
	 */
	public static function createURLForAction($action = null, $module = null, $language = null, array $parameters = null, $urlencode = true)
	{
		// grab the url from the reference
		$url = Spoon::getObjectReference('url');

		// redefine parameters
		$action = ($action !== null) ? (string) $action : $url->getAction();
		$module = ($module !== null) ? (string) $module : $url->getModule();
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();
		$querystring = '';

		// add offset, order & sort (only if not yet manually added)
		if(isset($_GET['offset']) && !isset($parameters['offset'])) $parameters['offset'] = (int) $_GET['offset'];
		if(isset($_GET['order']) && !isset($parameters['order'])) $parameters['order'] = (string) $_GET['order'];
		if(isset($_GET['sort']) && !isset($parameters['sort'])) $parameters['sort'] = (string) $_GET['sort'];

		// add at least one parameter
		if(empty($parameters)) $parameters['token'] = 'true';

		// add parameters
		foreach($parameters as $key => $value) $querystring .= '&'. $key .'='. (($urlencode) ? urlencode($value) : $value);

		// add querystring
		$querystring = '?'. trim($querystring, '&');

		// build the url and return it
		return '/'. NAMED_APPLICATION .'/'. $language .'/'. $module .'/'. $action . $querystring;
	}


	/**
	 * Get the modules
	 *
	 * @todo deze lijst moet in lokale cache komen, aangezien die redelijk veel wordt opgevraagd.
	 *
	 * @return	array
	 * @param	bool[optional] $activeOnly
	 */
	public static function getModules($activeOnly = true)
	{
		// redefine
		$activeOnly = (bool) $activeOnly;

		// get db
		$db = self::getDB();

		// only return the active modules
		if($activeOnly) return $db->getColumn('SELECT name
												FROM modules
												WHERE active = ?;',
												array('Y'));

		// fallback
		return $db->getColumn('SELECT name
								FROM modules;');
	}


	/**
	 * Get (or create and get) a database-connection
	 * If the database wasn't stored in the reference before we will create it and add it
	 *
	 * @todo	extend SpoonDatabase with BackendDatabase
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
	public static function getSetting($module, $key, $defaultValue = null)
	{
		// are the values available
		if(empty(self::$moduleSettings)) self::getModuleSettings();

		// redefine
		$module = (string) $module;
		$key = (string) $key;

		// if the value isn't present we should set a defaultvalue
		if(!isset(self::$moduleSettings[$module][$key])) self::setSetting($module, $key, $defaultValue);

		// return
		return self::$moduleSettings[$module][$key];
	}


	/**
	 * Get all module settings at once
	 *
	 * @return	array
	 */
	public static function getModuleSettings()
	{
		// are the values available
		if(empty(self::$moduleSettings))
		{
			// get db
			$db = self::getDB();

			// get all settings
			$moduleSettings = (array) $db->retrieve('SELECT ms.module, ms.name, ms.value
														FROM modules_settings AS ms;');

			// loop and store settings in the cache
			foreach($moduleSettings as $setting) self::$moduleSettings[$setting['module']][$setting['name']] = unserialize($setting['value']);
		}

		// return
		return self::$moduleSettings;
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
		self::$moduleSettings[$module][$key] = $value;
	}
}

?>