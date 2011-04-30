<?php

/**
 * In this file we store all generic functions that we will be using in the backend.
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendModel
{
	/**
	 * The keys and structural data for pages
	 *
	 * @var	array
	 */
	private static $keys = array(),
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

			// join together, and increment the last one
			$string = implode('-', $chunks ) . '-' . ((int) $last + 1);
		}

		// not numeric, so add -2
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

		// check if the akismet key is available if there are modules that require it
		if(!empty($akismetModules) && self::getModuleSetting('core', 'akismet_key', null) == '')
		{
			// add warning
			$warnings[] = array('message' => BL::err('AkismetKey'));
		}

		// check if the google maps key is available if there are modules that require it
		if(!empty($googleMapsModules) && self::getModuleSetting('core', 'google_maps_key', null) == '')
		{
			// add warning
			$warnings[] = array('message' => BL::err('GoogleMapsKey'));
		}

		// check if the fork API keys are available
		if(self::getModuleSetting('core', 'fork_api_private_key') == '' || self::getModuleSetting('core', 'fork_api_public_key') == '')
		{
			$warnings[] = array('message' => BL::err('ForkAPIKeys'));
		}

		// check if debug-mode is active
		if(SPOON_DEBUG) $warnings[] = array('message' => BL::err('DebugModeIsActive'));
/*
		// @note: robots.txt are removed
		// 	indexability is now based on meta noindex (SPOON_DEBUG true = not noindex)

		// try to validate robots.txt
		if(SpoonFile::exists(PATH_WWW . '/robots.txt'))
		{
			// get content
			$content = SpoonFile::getContent(PATH_WWW . '/robots.txt');
			$isOK = true;

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
			if(!$isOK) $warnings[] = array('message' => BL::err('RobotsFileIsNotOK'));
		}
*/
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
		$URL = Spoon::get('url');

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

		// init counter
		$i = 1;

		// add parameters
		foreach($parameters as $key => $value)
		{
			// first element
			if($i == 1) $querystring .= '?' . $key . '=' . (($urlencode) ? urlencode($value) : $value);

			// other elements
			else $querystring .= '&amp;' . $key . '=' . (($urlencode) ? urlencode($value) : $value);

			// update counter
			$i++;
		}

		// build the URL and return it
		return '/' . NAMED_APPLICATION . '/' . $language . '/' . $module . '/' . $action . $querystring;
	}


	/**
	 * Delete a page extra by module, type or data.
	 *
	 * Data is a key/value array. Example: array(id => 23, language => nl);
	 *
	 * @return	void
	 * @param	string[optional] $module	The module wherefore the extra exists.
	 * @param	string[optional] $type		The type of extra, possible values are block, homepage, widget.
	 * @param	array[optional] $data		Extra data that exists.
	 */
	public static function deleteExtra($module = null, $type = null, array $data = null)
	{
		// init
		$query = 'SELECT i.id, i.data FROM pages_extras AS i WHERE 1';
		$parameters = array();

		// module
		if($module !== null)
		{
			$query .= ' AND i.module = ?';
			$parameters[] = (string) $module;
		}

		// type
		if($type !== null)
		{
			$query .= ' AND i.type = ?';
			$parameters[] = (string) $type;
		}

		// get extras
		$extras = (array) BackendModel::getDB(true)->getRecords($query, $parameters);

		// loop found extras
		foreach($extras as $extra)
		{
			// match by parameters
			if($data !== null && $extra['data'] !== null)
			{
				// unserialize
				$extraData = (array) unserialize($extra['data']);

				// skip extra if parameters do not match
				if(count(array_intersect($data, $extraData)) !== count($data)) continue;
			}

			// delete extra
			self::deleteExtraById($extra['id']);
		}
	}


	/**
	 * Delete a page extra by its id
	 *
	 * @return	void
	 * @param	int $id		The id of the extra to delete.
	 */
	public static function deleteExtraById($id)
	{
		// redefine
		$id = (int) $id;

		// unset blocks
		BackendModel::getDB(true)->update('pages_blocks', array('extra_id' => null), 'extra_id = ?', $id);

		// delete extra
		BackendModel::getDB(true)->delete('pages_extras', 'id = ?', $id);
	}


	/**
	 * Generate a totally random but readable/speakable password
	 *
	 * @return	string
	 * @param	int[optional] $length				The maximum length for the password to generate.
	 * @param	bool[optional] $uppercaseAllowed	Are uppercase letters allowed?
	 * @param	bool[optional] $lowercaseAllowed	Are lowercase letters allowed?
	 */
	public static function generatePassword($length = 6, $uppercaseAllowed = true, $lowercaseAllowed = true)
	{
		// list of allowed vowels and vowelsounds
		$vowels = array('a', 'e', 'i', 'u', 'ae', 'ea');

		// list of allowed consonants and consonant sounds
		$consonants = array('b', 'c', 'd', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'tr', 'cr', 'fr', 'dr', 'wr', 'pr', 'th', 'ch', 'ph', 'st');

		// init vars
		$consonantsCount = count($consonants);
		$vowelsCount = count($vowels);
		$pass = '';
		$tmp = '';

		// create temporary pass
		for($i = 0; $i < $length; $i++) $tmp .= ($consonants[rand(0, $consonantsCount - 1)] . $vowels[rand(0, $vowelsCount - 1)]);

		// reformat the pass
		for($i = 0; $i < $length; $i++)
		{
			if(rand(0, 1) == 1) $pass .= strtoupper(substr($tmp, $i, 1));
			else $pass .= substr($tmp, $i, 1);
		}

		// reformat it again, if uppercase isn't allowed
		if(!$uppercaseAllowed) $pass = strtolower($pass);

		// reformat it again, if uppercase isn't allowed
		if(!$lowercaseAllowed) $pass = strtoupper($pass);

		// return pass
		return $pass;
	}


	/**
	 * Fetch the list of long date formats including examples of these formats.
	 *
	 * @return	array
	 */
	public static function getDateFormatsLong()
	{
		// init var
		$possibleFormats = array();

		// loop available formats
		foreach((array) self::getModuleSetting('core', 'date_formats_long') as $format)
		{
			// get date based on given format
			$possibleFormats[$format] = SpoonDate::getDate($format, null, BackendAuthentication::getUser()->getSetting('interface_language'));
		}

		// return
		return $possibleFormats;
	}


	/**
	 * Fetch the list of short date formats including examples of these formats.
	 *
	 * @return	array
	 */
	public static function getDateFormatsShort()
	{
		// init var
		$possibleFormats = array();

		// loop available formats
		foreach((array) self::getModuleSetting('core', 'date_formats_short') as $format)
		{
			// get date based on given format
			$possibleFormats[$format] = SpoonDate::getDate($format, null, BackendAuthentication::getUser()->getSetting('interface_language'));
		}

		// return
		return $possibleFormats;
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
		if(!Spoon::exists('database'))
		{
			// create instance
			$db = new SpoonDatabase(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

			// utf8 compliance & MySQL-timezone
			$db->execute('SET CHARACTER SET utf8, NAMES utf8, time_zone = "+0:00"');

			// store
			Spoon::set('database', $db);
		}

		// return
		return Spoon::get('database');
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
			// validate file
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH . '/navigation/keys_' . $language . '.php'))
			{
				// regenerate cache
				BackendPagesModel::buildCache($language);
			}

			// init var
			$keys = array();

			// require file
			require FRONTEND_CACHE_PATH . '/navigation/keys_' . $language . '.php';

			// store
			self::$keys[$language] = $keys;
		}

		// return
		return self::$keys[$language];
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
			// get all modules
			$modules = (array) self::getDB()->getPairs('SELECT m.name, m.active
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
	 * Get a certain module-setting
	 *
	 * @return	mixed
	 * @param	string $module					The module in which the setting is stored.
	 * @param	string $key						The name of the setting.
	 * @param	mixed[optional] $defaultValue	The value to return if the setting isn't present.
	 */
	public static function getModuleSetting($module, $key, $defaultValue = null)
	{
		// are the values available
		if(empty(self::$moduleSettings)) self::getModuleSettings();

		// redefine
		$module = (string) $module;
		$key = (string) $key;

		// if the value isn't present we should set a defaultvalue
		if(!isset(self::$moduleSettings[$module][$key])) return $defaultValue;

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
			// get all settings
			$moduleSettings = (array) self::getDB()->getRecords('SELECT ms.module, ms.name, ms.value
																	FROM modules_settings AS ms');

			// loop and store settings in the cache
			foreach($moduleSettings as $setting)
			{
				// unserialize value
				$value = @unserialize($setting['value']);

				// validate
				if($value === false && serialize(false) != $setting['value']) throw new BackendException('The modulesetting (' . $setting['module'] . ': ' . $setting['name'] . ') wasn\'t saved properly.');

				// cache the setting
				self::$moduleSettings[$setting['module']][$setting['name']] = $value;
			}
		}

		// return
		return self::$moduleSettings;
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
		foreach($modules as $module) $dropdown[$module] = ucfirst(BL::lbl(SpoonFilter::toCamelCase($module)));

		// return data
		return $dropdown;
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
			// validate file
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH . '/navigation/navigation_' . $language . '.php'))
			{
				// regenerate cache
				BackendPagesModel::buildCache($language);
			}

			// init var
			$navigation = array();

			// require file
			require FRONTEND_CACHE_PATH . '/navigation/navigation_' . $language . '.php';

			// store
			self::$navigation[$language] = $navigation;
		}

		// return
		return self::$navigation[$language];
	}


	/**
	 * Fetch the list of number formats including examples of these formats.
	 *
	 * @return	array
	 */
	public static function getNumberFormats()
	{
		// init var
		$possibleFormats = array();

		// loop available formats
		foreach((array) self::getModuleSetting('core', 'number_formats') as $format => $example)
		{
			// reformat array
			$possibleFormats[$format] = $example;
		}

		// return
		return $possibleFormats;
	}


	/**
	 * Fetch the list of available themes
	 *
	 * @return	array
	 */
	public static function getThemes()
	{
		// fetch themes
		$themes = (array) SpoonDirectory::getList(FRONTEND_PATH . '/themes/', false, array('.svn'));

		// create array
		$themes = array_combine($themes, $themes);

		// add core templates
		$themes = array_merge(array('core' => BL::lbl('NoTheme')), $themes);

		return $themes;
	}


	/**
	 * Fetch the list of time formats including examples of these formats.
	 *
	 * @return	array
	 */
	public static function getTimeFormats()
	{
		// init var
		$possibleFormats = array();

		// loop available formats
		foreach(self::getModuleSetting('core', 'time_formats') as $format)
		{
			// get time based on given format
			$possibleFormats[$format] = SpoonDate::getDate($format, null, BackendAuthentication::getUser()->getSetting('interface_language'));
		}

		// return
		return $possibleFormats;
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
		$URL = (SITE_MULTILANGUAGE) ? '/' . $language . '/' : '/';

		// get the menuItems
		$keys = self::getKeys($language);

		// get the URL, if it doens't exist return 404
		if(!isset($keys[$pageId])) return self::getURL(404);

		// add URL
		else $URL .= $keys[$pageId];

		// return the unique URL!
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
		foreach($navigation as $level)
		{
			// loop level
			foreach($level as $pages)
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
								return self::getURL($properties['page_id'], $language);
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
			$URL .= '/' . FL::act(SpoonFilter::toCamelCase($action));

			// return the unique URL!
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
	 * @param	SpoonFormDate $date					An instance of SpoonFormDate.
	 * @param	SpoonFormTime[optional] $time		An instance of SpoonFormTime.
	 */
	public static function getUTCTimestamp(SpoonFormDate $date, SpoonFormTime $time = null)
	{
		// validate date/time object
		if(!$date->isValid() || ($time !== null && !$time->isValid())) throw new BackendException('You need to provide two objects that actaully contain valid data.');

		// init vars
		$year = gmdate('Y', $date->getTimestamp());
		$month = gmdate('m', $date->getTimestamp());
		$day = gmdate('j', $date->getTimestamp());

		// time object was given
		if($time !== null)
		{
			// define hour & minute
			list($hour, $minute) = explode(':', $time->getValue());
		}

		// user default time
		else
		{
			$hour = 0;
			$minute = 0;
		}

		// make and return timestamp
		return mktime($hour, $minute, 0, $month, $day, $year);
	}


	/**
	 * Invalidate cache
	 *
	 * @return	void
	 * @param	string[optional] $module	A specific module to clear the cache for.
	 * @param	string[optional] $language	The language to use.
	 */
	public static function invalidateFrontendCache($module = null, $language = null)
	{
		// redefine
		$module = ($module !== null) ? (string) $module : null;
		$language = ($language !== null) ? (string) $language : null;

		// get cache path
		$path = FRONTEND_CACHE_PATH . '/cached_templates';

		// build regular expresion
		if($module !== null)
		{
			if($language !== null) $regexp = '/' . '(.*)' . $module . '(.*)_cache\.tpl/i';
			else $regexp = '/' . $language . '_' . $module . '(.*)_cache\.tpl/i';
		}
		else
		{
			if($language !== null) $regexp = '/(.*)_cache\.tpl/i';
			else $regexp = '/' . $language . '_(.*)_cache\.tpl/i';
		}

		// get files to delete
		$files = SpoonFile::getList($path, $regexp);

		// delete files
		foreach($files as $file) SpoonFile::delete($path . '/' . $file);
	}


	/**
	 * Ping the known webservices
	 *
	 * @return	bool								If everything went fine true will be returned, otherwise false.
	 * @param	string[optional] $pageOrFeedURL		The page/feed that has changed.
	 * @param	string[optional] $category			An optional category for the site.
	 */
	public static function ping($pageOrFeedURL = null, $category = null)
	{
		// redefine
		$siteTitle = self::getModuleSetting('core', 'site_title_' . BackendLanguage::getWorkingLanguage(), SITE_DEFAULT_TITLE);
		$siteURL = SITE_URL;
		$pageOrFeedURL = ($pageOrFeedURL !== null) ? (string) $pageOrFeedURL : null;
		$category = ($category !== null) ? (string) $category : null;

		// get ping services
		$pingServices = self::getModuleSetting('core', 'ping_services', null);

		// no ping services available or older than one month ago
		if($pingServices === null || $pingServices['date'] < strtotime('-1 month'))
		{
			// get ForkAPI-keys
			$publicKey = self::getModuleSetting('core', 'fork_api_public_key', '');
			$privateKey = self::getModuleSetting('core', 'fork_api_private_key', '');

			// validate keys
			if($publicKey == '' || $privateKey == '') return false;

			// require the class
			require_once PATH_LIBRARY . '/external/fork_api.php';

			// create instance
			$forkAPI = new ForkAPI($publicKey, $privateKey);

			// try to get the services
			try
			{
				$pingServices['services'] = $forkAPI->pingGetServices();
				$pingServices['date'] = time();
			}

			// catch any exceptions
			catch(Exception $e)
			{
				// check if the error should not be ignored
				if(strpos($e->getMessage(), 'Operation timed out') === false && strpos($e->getMessage(), 'Invalid headers') === false)
				{
					// in debugmode we want to see the exceptions
					if(SPOON_DEBUG) throw $e;

					// stop
					else return false;
				}
			}

			// store the services
			self::setModuleSetting('core', 'ping_services', $pingServices);
		}

		// make sure services array will not trigger an error (even if we couldn't load any)
		if(!isset($pingServices['services']) || !$pingServices['services']) $pingServices['services'] = array();

		// loop services
		foreach($pingServices['services'] as $service)
		{
			// create new client
			$client = new SpoonXMLRPCClient($service['url']);

			// set some properties
			$client->setUserAgent('Fork ' . FORK_VERSION);
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
			catch(Exception $e)
			{
				// check if the error should not be ignored
				if(strpos($e->getMessage(), 'Operation timed out') === false && strpos($e->getMessage(), 'Invalid headers') === false)
				{
					// in debugmode we want to see the exceptions
					if(SPOON_DEBUG) throw $e;
				}

				// next!
				continue;
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
	public static function setModuleSetting($module, $key, $value)
	{
		// redefine
		$module = (string) $module;
		$key = (string) $key;
		$valueToStore = serialize($value);

		// store
		self::getDB(true)->execute('INSERT INTO modules_settings(module, name, value)
											VALUES(?, ?, ?)
											ON DUPLICATE KEY UPDATE value = ?',
											array($module, $key, $valueToStore, $valueToStore));

		// cache it
		self::$moduleSettings[$module][$key] = $value;
	}


	/**
	 * Submit ham, this call is intended for the marking of false positives, things that were incorrectly marked as spam.
	 *
	 * @return	bool						If everything went fine true will be returned, otherwise an exception will be triggered.
	 * @param	string $userIp				IP address of the comment submitter.
	 * @param	string $userAgent			User agent information.
	 * @param	string[optional] $content	The content that was submitted.
	 * @param	string[optional] $author	Submitted name with the comment.
	 * @param	string[optional] $email		Submitted email address.
	 * @param	string[optional] $url		Commenter URL.
	 * @param	string[optional] $permalink	The permanent location of the entry the comment was submitted to.
	 * @param	string[optional] $type		May be blank, comment, trackback, pingback, or a made up value like "registration".
	 * @param	string[optional] $referrer	The content of the HTTP_REFERER header should be sent here.
	 * @param	array[optional] $others		Other data (the variables from $_SERVER).
	 */
	public static function submitHam($userIp, $userAgent, $content, $author = null, $email = null, $url = null, $permalink = null, $type = null, $referrer = null, $others = null)
	{
		// get some settings
		$akismetKey = self::getModuleSetting('core', 'akismet_key');

		// invalid key, so we can't detect spam
		if($akismetKey === '') return false;

		// require the class
		require_once PATH_LIBRARY . '/external/akismet.php';

		// create new instance
		$akismet = new Akismet($akismetKey, SITE_URL);

		// set properties
		$akismet->setTimeOut(10);
		$akismet->setUserAgent('Fork CMS/2.1');

		// try it to decide it the item is spam
		try
		{
			// check with Akismet if the item is spam
			return $akismet->submitHam($userIp, $userAgent, $content, $author = null, $email = null, $url = null, $permalink = null, $type = null, $referrer = null, $others = null);
		}

		// catch exceptions
		catch(Exception $e)
		{
			// in debug mode we want to see exceptions, otherwise the fallback will be triggered
			if(SPOON_DEBUG) throw $e;
		}

		// when everything fails
		return false;
	}


	/**
	 * Submit spam, his call is for submitting comments that weren't marked as spam but should have been.
	 *
	 * @return	bool						If everything went fine true will be returned, otherwise an exception will be triggered.
	 * @param	string $userIp				IP address of the comment submitter.
	 * @param	string $userAgent			User agent information.
	 * @param	string[optional] $content	The content that was submitted.
	 * @param	string[optional] $author	Submitted name with the comment.
	 * @param	string[optional] $email		Submitted email address.
	 * @param	string[optional] $url		Commenter URL.
	 * @param	string[optional] $permalink	The permanent location of the entry the comment was submitted to.
	 * @param	string[optional] $type		May be blank, comment, trackback, pingback, or a made up value like "registration".
	 * @param	string[optional] $referrer	The content of the HTTP_REFERER header should be sent here.
	 * @param	array[optional] $others		Other data (the variables from $_SERVER).
	 */
	public static function submitSpam($userIp, $userAgent, $content, $author = null, $email = null, $url = null, $permalink = null, $type = null, $referrer = null, $others = null)
	{
		// get some settings
		$akismetKey = self::getModuleSetting('core', 'akismet_key');

		// invalid key, so we can't detect spam
		if($akismetKey === '') return false;

		// require the class
		require_once PATH_LIBRARY . '/external/akismet.php';

		// create new instance
		$akismet = new Akismet($akismetKey, SITE_URL);

		// set properties
		$akismet->setTimeOut(10);
		$akismet->setUserAgent('Fork CMS/2.1');

		// try it to decide it the item is spam
		try
		{
			// check with Akismet if the item is spam
			return $akismet->submitSpam($userIp, $userAgent, $content, $author = null, $email = null, $url = null, $permalink = null, $type = null, $referrer = null, $others = null);
		}

		// catch exceptions
		catch(Exception $e)
		{
			// in debug mode we want to see exceptions, otherwise the fallback will be triggered
			if(SPOON_DEBUG) throw $e;
		}

		// when everything fails
		return false;
	}
}

?>