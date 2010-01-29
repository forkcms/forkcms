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
	 * Add a number to the string
	 *
	 * @return	string
	 * @param	string $string
	 */
	public static function addNumber($string)
	{
		// split
		$chunks = explode('-', $string);

		// count the chunks
		$count = count($chunks);

		// get last chunk
		$last = $chunks[$count - 1];

		// is nummeric
		if(SpoonFilter::isNumeric($last))
		{
			// remove last chunk
			array_pop($chunks);

			// join together
			$string = implode('-', $chunks ) .'-'. ((int) $last + 1);
		}

		// not numeric
		else $string .= '-2';

		// return
		return $string;
	}


	/**
	 * Calculate the time ago from a given timestamp and returns a decent sentence.
	 *
	 * @return	string
	 * @param	string $timestamp
	 */
	public static function calculateTimeAgo($timestamp)
	{
		// redefine
		$timestamp = (int) $timestamp;

		// get seconds between given timestamp and current timestamp
		$secondsBetween = time() - $timestamp;

		// calculate years ago
		$yearsAgo = floor($secondsBetween / (365.242199 * 24 * 60 * 60));
		if($yearsAgo > 1) return sprintf(BL::getMessage('TimeYearsAgo'), $yearsAgo);
		if($yearsAgo == 1) return BL::getMessage('TimeOneYearAgo');

		// calculate months ago
		$monthsAgo = floor($secondsBetween / ((365.242199/12) * 24 * 60 * 60));
		if($monthsAgo > 1) return sprintf(BL::getMessage('TimeMonthsAgo'), $monthsAgo);
		if($monthsAgo == 1) return BL::getMessage('TimeOneMonthAgo');

		// calculate weeks ago
		$weeksAgo = floor($secondsBetween / (7 * 24 * 60 * 60));
		if($weeksAgo > 1) return sprintf(BL::getMessage('TimeWeeksAgo'), $weeksAgo);
		if($weeksAgo == 1) return BL::getMessage('TimeOneWeekAgo');

		// calculate days ago
		$daysAgo = floor($secondsBetween / (24 * 60 * 60));
		if($daysAgo > 1) return sprintf(BL::getMessage('TimeDaysAgo'), $daysAgo);
		if($daysAgo == 1) return BL::getMessage('TimeOneDayAgo');

		// calculate hours ago
		$hoursAgo = floor($secondsBetween / (60 * 60));
		if($hoursAgo > 1) return sprintf(BL::getMessage('TimeHoursAgo'), $hoursAgo);
		if($hoursAgo == 1) return BL::getMessage('TimeOneHourAgo');

		// calculate minutes ago
		$minutesAgo = floor($secondsBetween / 60);
		if($minutesAgo > 1) return sprintf(BL::getMessage('TimeMinutesAgo'), $minutesAgo);
		if($minutesAgo == 1) return BL::getMessage('TimeOneMinuteAgo');

		// calculate seconds ago
		$secondsAgo = floor($secondsBetween);
		if($secondsAgo > 1) return sprintf(BL::getMessage('TimeSecondsAgo'), $secondsAgo);
		if($secondsAgo <= 1) return BL::getMessage('TimeOneSecondAgo');
	}


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
	 * Fetch the list of modules, but for a dropdown
	 *
	 * @return	array
	 * @param	bool[optional] $activeOnly
	 */
	public static function getModulesForDropDown($activeOnly = true)
	{
		// init var
		$dropdown = array('core' => 'core');

		// fetch modules
		$modules = self::getModules($activeOnly);

		// @todo davy - later moeten de modules als language labels geparsed worden
		foreach($modules as $module) $dropdown[$module] = $module;

		// return data
		return $dropdown;
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

			// utf8 compliance
			$db->execute('SET CHARACTER SET utf8;');
			$db->execute('SET NAMES utf8;');

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
			foreach($moduleSettings as $setting)
			{
				// unserialize value
				$value = @unserialize($setting['value']);

				// validate
				if($value === false && serialize(false) != $setting['value']) throw new BackendException('The modulesetting ('. $setting['module'] .': '. $setting['name'] .') wasn\'t saved properly.');

				// cache the setting
				self::$moduleSettings[$setting['module']][$setting['name']] = $value;
			}
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