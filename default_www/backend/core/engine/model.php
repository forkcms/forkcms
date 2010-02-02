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
	 * The keys an structural data for pages
	 *
	 * @var	array
	 */
	private static	$keys = array(),
					$navigation = array();


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

		// build the url and return it
		return '/'. NAMED_APPLICATION .'/'. $language .'/'. $module .'/'. $action . $querystring;
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
	 * Get the page-keys
	 *
	 * @return	array
	 */
	public static function getKeys($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// does the keys exists in the cache?
		if(!isset(self::$keys[$language]) || empty(self::$keys[$language]))
		{
			// validate file @later	the file should be regenerated
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/navigation/keys_'. $language .'.php')) throw new BackendException('No key-file (keys_'. $language .'.php) found.');

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
	 * @param	string[optional] $language
	 */
	public static function getNavigation($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// does the keys exists in the cache?
		if(!isset(self::$navigation[$language]) || empty(self::$navigation[$language]))
		{
			// validate file @later: the file should be regenerated
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/navigation/navigation_'. $language .'.php')) throw new BackendException('No navigation-file (navigation_'. $language .'.php) found.');

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
	 * Get URL for a given pageId
	 *
	 * @return	string
	 * @param	int $pageId
	 * @param	string[optional] $language
	 */
	public static function getURL($pageId, $language = null)
	{
		// redefine
		$pageId = (int) $pageId;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// init url
		$URL = (SITE_MULTILANGUAGE) ? '/'. $language .'/' : '/';

		// get the menuItems
		$keys = self::getKeys($language);

		// get the url, if it doens't exist return 404
		if(!isset($keys[$pageId])) return self::getURL(404);

		// add url
		else $URL .= $keys[$pageId];

		// return
		return $URL;
	}

	/**
	 * Get the URL for a give module & action combination
	 *
	 * @return	string
	 * @param	string $module
	 * @param	string[optional] $action
	 * @param	string[optional] $language
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
			// build url
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