<?php

/**
 * BackendModel
 * In this file we store all generic functions that we will be using in the backend.
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
	 * The keys an structural data for pages
	 *
	 * @var	array
	 */
	private static	$keys = array(),
					$navigation = array();


	/**
	 * Cached modules
	 *
	 * @var	array
	 */
	private static $modules = array();


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
	 * @param	string $string	The string where the number will be appended to.
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

		// fork API keys
		if(BackendModel::getSetting('core', 'fork_api_private_key') == '' || BackendModel::getSetting('core', 'fork_api_public_key') == '')
		{
			$warnings[] = array('message' => BL::getError('ForkAPIKeys'));
		}

		// debug
		if(SPOON_DEBUG) $warnings[] = array('message' => BL::getError('DebugModeIsActive'));

		// robots.txt
		// @todo tijs - het is mij niet duidelijk vanaf wanneer robots.txt geldig is.
		if(SpoonFile::exists(PATH_WWW .'/robots.txt'))
		{
			// get content
			$content = SpoonFile::getContent(PATH_WWW .'/robots.txt');
			$isOK = true;

			// @todo davy - fix die lelijke code en zorg dat het werkt.
			if(str_replace(array("\n", ' ', "\r"), '', mb_strtolower($content)) == 'user-agent: *disallow: /') $isOK = false;
			// split into lines
			$lines = explode("\n", $content);

			// loop lines
			foreach($lines as $line)
			{
				// cleanup line
				$line = mb_strtolower(trim($line));

				// validate disallow
				if(substr($line, 0, 8) == 'disallow')
				{
					// split into chunks
					$chunks = explode(':', $line);

					// validate disallow
					if(isset($chunks[1]) && trim($chunks[1]) == '/') $isOK = false;
				}
			}

			// add warning
			if(!$isOK) $warnings[] = array('message' => BL::getError('RobotsFileIsNotOK'));
		}

		// return
		return $warnings;
	}


	/**
	 * Creates an URL for a given action and module
	 * If you don't specify an action the current action will be used.
	 * If you don't specify a module the current module will be used.
	 * If you don't specify a language the current language will be used.
	 *
	 * @return	string
	 * @param	string[optional] $action		The action to build the URL for.
	 * @param	string[optional] $module		The module to build the URL for.
	 * @param	string[optional] $language		The language to use, if not provided we will use the working language.
	 * @param	array[optional] $parameters		GET-parameters to use.
	 * @param	bool[optional] $urlencode		Should the parameters be urlencoded?
	 */
	public static function createURLForAction($action = null, $module = null, $language = null, array $parameters = null, $urlencode = true)
	{
		// grab the URL from the reference
		$URL = Spoon::getObjectReference('url');

		// redefine parameters
		$action = ($action !== null) ? (string) $action : $URL->getAction();
		$module = ($module !== null) ? (string) $module : $URL->getModule();
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

		// build the URL and return it
		return '/'. NAMED_APPLICATION .'/'. $language .'/'. $module .'/'. $action . $querystring;
	}


	/**
	 * Get (or create and get) a database-connection
	 * If the database wasn't stored in the reference before we will create it and add it
	 *
	 * @return	SpoonDatabase
	 * @param	bool[optional] $write	Do you want the write-connection or not?
	 */
	public static function getDB($write = false)
	{
		// redefine
		$write = (bool) $write;

		// do we have a db-object ready?
		if(!Spoon::isObjectReference('database'))
		{
			// create instance
			$db = new SpoonDatabase(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

			// utf8 compliance & MySQL-timezone
			$db->execute('SET CHARACTER SET utf8, NAMES utf8, time_zone = "+0:00";');

			// store
			Spoon::setObjectReference('database', $db);
		}

		// return it
		return Spoon::getObjectReference('database');
	}


	/**
	 * Get the page-keys
	 *
	 * @return	array
	 * @param	string[optional] $language		The language to use, if not provided we will use the working language.
	 */
	public static function getKeys($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// does the keys exists in the cache?
		if(!isset(self::$keys[$language]) || empty(self::$keys[$language]))
		{
			// validate file @later	the file should be regenerated
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/navigation/keys_'. $language .'.php'))
			{
				// require BackendPagesModel
				require_once BACKEND_MODULES_PATH .'/pages/engine/model.php';

				// regenerate cache
				BackendPagesModel::buildCache();
			}

			// init var
			$keys = array();

			// require file
			require FRONTEND_CACHE_PATH .'/navigation/keys_'. $language .'.php';

			// store
			self::$keys[$language] = $keys;
		}

		return self::$keys[$language];
	}


	/**
	 * Get the navigation-items
	 *
	 * @return	array
	 * @param	string[optional] $language		The language to use, if not provided we will use the working language.
	 */
	public static function getNavigation($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// does the keys exists in the cache?
		if(!isset(self::$navigation[$language]) || empty(self::$navigation[$language]))
		{
			// validate file @later: the file should be re-generated
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/navigation/navigation_'. $language .'.php'))
			{
				// require BackendPagesModel
				require_once BACKEND_MODULES_PATH .'/pages/engine/model.php';

				// regenerate cache
				BackendPagesModel::buildCache();
			}

			// init var
			$navigation = array();

			// require file
			require FRONTEND_CACHE_PATH .'/navigation/navigation_'. $language .'.php';

			// store
			self::$navigation[$language] = $navigation;
		}

		// return
		return self::$navigation[$language];
	}


	/**
	 * Get the modules
	 *
	 * @return	array
	 * @param	bool[optional] $activeOnly	Only return the active modules.
	 */
	public static function getModules($activeOnly = true)
	{
		// redefine
		$activeOnly = (bool) $activeOnly;

		// validate cache
		if(empty(self::$modules) || !isset(self::$modules['active']) || !isset(self::$modules['all']))
		{
			// get db
			$db = self::getDB();

			// get all modules
			$modules = (array) $db->getPairs('SELECT m.name, m.active
												FROM modules AS m');

			// loop
			foreach($modules as $module => $active)
			{
				// if the module is active
				if($active == 'Y') self::$modules['active'][] = $module;

				// add to all
				self::$modules['all'][] = $module;
			}
		}

		// only return the active modules
		if($activeOnly) return self::$modules['active'];

		// fallback
		return self::$modules['all'];
	}


	/**
	 * Fetch the list of modules, but for a dropdown
	 *
	 * @return	array
	 * @param	bool[optional] $activeOnly	Only return the active modules.
	 */
	public static function getModulesForDropDown($activeOnly = true)
	{
		// init var
		$dropdown = array('core' => 'core');

		// fetch modules
		$modules = self::getModules($activeOnly);

		// loop and add into the return-array (with correct label)
		foreach($modules as $module) $dropdown[$module] = ucfirst(BackendLanguage::getLabel(SpoonFilter::toCamelCase($module)));

		// return data
		return $dropdown;
	}


	/**
	 * Get a certain module-setting
	 *
	 * @return	mixed
	 * @param	string $module					The module wherin the setting is stored.
	 * @param	string $key						The name of the setting.
	 * @param	mixed[optional] $defaultValue	The value to store if the setting isn't present.
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
	 * Get URL for a given pageId
	 *
	 * @return	string
	 * @param	int $pageId						The id of the page to get the URL for.
	 * @param	string[optional] $language		The language to use, if not provided we will use the working language.
	 */
	public static function getURL($pageId, $language = null)
	{
		// redefine
		$pageId = (int) $pageId;
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// init URL
		$URL = (SITE_MULTILANGUAGE) ? '/'. $language .'/' : '/';

		// get the menuItems
		$keys = self::getKeys($language);

		// get the URL, if it doens't exist return 404
		if(!isset($keys[$pageId])) return self::getURL(404);

		// add URL
		else $URL .= $keys[$pageId];

		// return
		return $URL;
	}


	/**
	 * Get the URL for a give module & action combination
	 *
	 * @return	string
	 * @param	string $module					The module to get the URL for.
	 * @param	string[optional] $action		The action to get the URL for.
	 * @param	string[optional] $language		The language to use, if not provided we will use the working language.
	 */
	public static function getURLForBlock($module, $action = null, $language = null)
	{
		// redefine
		$module = (string) $module;
		$action = ($action !== null) ? (string) $action : null;
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// init var
		$pageIdForURL = null;

		// get the menuItems
		$navigation = self::getNavigation($language);

		// loop types
		foreach($navigation as $type => $level)
		{
			// loop level
			foreach($level as $parentId => $pages)
			{
				// loop pages
				foreach($pages as $pageId => $properties)
				{
					// only process pages with extra_blocks
					if(isset($properties['extra_blocks']))
					{
						// loop extras
						foreach($properties['extra_blocks'] as $extra)
						{
							// direct link?
							if($extra['module'] == $module && $extra['action'] == $action)
							{
								// exacte page was found, so return
								return self::getURL($properties['page_id']);
							}

							// correct module but no action
							elseif($extra['module'] == $module && $extra['action'] == null)
							{
								// store pageId
								$pageIdForURL = (int) $pageId;
							}
						}
					}
				}
			}
		}

		// pageId stored?
		if($pageIdForURL !== null)
		{
			// build URL
			$URL = self::getURL($pageIdForURL, $language);

			// set locale
			FrontendLanguage::setLocale($language);

			// append action
			$URL .= '/'. FrontendLanguage::getAction(SpoonFilter::toCamelCase($action));

			// return the URL
			return $URL;
		}

		// fallback
		return self::getURL(404);
	}


	/**
	 * Get the UTC date in a specific format. Use this method when inserting dates in the database!
	 *
	 * @return	string
	 * @param	string[optional] $format	The format to return the timestamp in. Default is MySQL datetime format.
	 * @param	int[optional] $timestamp	The timestamp to use, if not provided the current time will be used.
	 */
	public static function getUTCDate($format = null, $timestamp = null)
	{
		// init var
		$format = ($format !== null) ? (string) $format : 'Y-m-d H:i:s';

		// no timestamp given
		if($timestamp === null) return gmdate($format);

		// timestamp given
		return gmdate($format, (int) $timestamp);
	}


	/**
	 * Get the UTC timestamp for a date/time object combination.
	 *
	 * @return	int
	 * @param	SpoonformDate $date					An instance of SpoonFormDate.
	 * @param	SpoonFormTime[optional] $time		An instance of SpoonFormTime.
	 */
	public static function getUTCTimestamp(SpoonFormDate $date, SpoonFormTime $time = null)
	{
		// init vars
		$year = date('Y', $date->getTimestamp());
		$month = date('m', $date->getTimestamp());
		$day = date('j', $date->getTimestamp());

		// time object was given
		if($time !== null)
		{
			// define hour & minute
			list($hour, $minute) = explode(':', $time->getValue());
		}

		// user default time
		else
		{
			$hour = 12;
			$minute = 0;
		}

		return mktime($hour, $minute, 0, $month, $day, $year);
	}


	/**
	 * Ping the known webservices
	 *
	 * @return	bool								If everything went fine true will be returned, otherwise false
	 * @param	string[optional] $pageOrFeedURL		The page/feed that has changed
	 * @param	string[optional] $category			An optional category for the site
	 */
	public static function ping($pageOrFeedURL = null, $category = null)
	{
		// redefine
		$siteTitle = self::getSetting('core', 'site_title_'. BackendLanguage::getWorkingLanguage(), SITE_DEFAULT_TITLE);
		$siteURL = SITE_URL;
		$pageOrFeedURL = ($pageOrFeedURL !== null) ? (string) $pageOrFeedURL : null;
		$category = ($category !== null) ? (string) $category : null;

		// get ping services
		$pingServices = self::getSetting('core', 'ping_services', null);

		// no ping services available or older then one 30 days
		if($pingServices === null || $pingServices['date'] < (time() - (30 * 24 * 60 * 60)))
		{
			// get ForkAPI-keys
			$publicKey = self::getSetting('core', 'fork_api_public_key', '');
			$privateKey = self::getSetting('core', 'fork_api_private_key', '');

			// validate keys
			if($publicKey == '' || $privateKey == '') return false;

			// require the class
			require_once PATH_LIBRARY .'/external/fork_api.php';

			// create instance
			$forkAPI = new ForkAPI($publicKey, $privateKey);

			// try to get the services
			try
			{
				$pingServices['services'] = $forkAPI->pingGetServices();
				$pingServices['date'] = time();
			}

			// catch any exceptions
			catch (Exception $e)
			{
				// check if the error should be ignored
				if(substr_count($e->getMessage(), 'Operation timed out') > 0) continue;
				elseif(substr_count($e->getMessage(), 'Invalid headers') > 0) continue;

				// in debugmode we want to see the exceptions
				if(SPOON_DEBUG) throw $e;

				// stop
				else return false;
			}

			// store the services
			self::setSetting('core', 'ping_services', $pingServices);
		}

		// require SpoonXMLRPCClient
		require_once 'spoon/webservices/xmlrpc/client.php';

		// loop services
		foreach($pingServices['services'] as $service)
		{
			// create new client
			$client = new SpoonXMLRPCClient($service['url']);

			// set some properties
			$client->setUserAgent('Fork '. FORK_VERSION);
			$client->setTimeOut(10);

			// set port
			$client->setPort($service['port']);

			// try to ping
			try
			{
				// extended ping?
				if($service['type'] == 'extended')
				{
					// no page or feed URL present?
					if($pageOrFeedURL === null) continue;

					// build parameters
					$parameters[] = array('type' => 'string', 'value' => $siteTitle);
					$parameters[] = array('type' => 'string', 'value' => $siteURL);
					$parameters[] = array('type' => 'string', 'value' => $pageOrFeedURL);
					if($category !== null) $parameters[] = array('type' => 'string', 'value' => $category);

					// make the call
					$client->execute('weblogUpdates.extendedPing', $parameters);
				}

				// default ping
				else
				{
					// build parameters
					$parameters[] = array('type' => 'string', 'value' => $siteTitle);
					$parameters[] = array('type' => 'string', 'value' => $siteURL);

					// make the call
					$client->execute('weblogUpdates.ping', $parameters);
				}
			}

			// catch any exceptions
			catch (Exception $e)
			{
				// in debugmode we want to see the exceptions
				if(SPOON_DEBUG) throw $e;

				// next!
				else continue;
			}
		}

		// return
		return true;
	}


	/**
	 * Saves a module-setting into the DB and the cached array
	 *
	 * @return	void
	 * @param	string $module		The module to set the setting for.
	 * @param	string $key			The name of the setting.
	 * @param	string $value		The value to store.
	 */
	public static function setSetting($module, $key, $value)
	{
		// redefine
		$module = (string) $module;
		$key = (string) $key;
		$valueToStore = serialize($value);

		// get db
		$db = BackendModel::getDB(true);

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