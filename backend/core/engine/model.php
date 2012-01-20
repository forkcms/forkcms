<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the backend.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendModel
{
	/**
	 * The keys and structural data for pages
	 *
	 * @var	array
	 */
	private static $keys = array(), $navigation = array();

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
	 * @param string $string The string where the number will be appended to.
	 * @return string
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

		return $string;
	}

	/**
	 * Checks the settings and optionally returns an array with warnings
	 *
	 * @return array
	 */
	public static function checkSettings()
	{
		$warnings = array();

		// check if debug-mode is active
		if(SPOON_DEBUG) $warnings[] = array('message' => BL::err('DebugModeIsActive'));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('index', 'settings'))
		{
			// check if the fork API keys are available
			if(self::getModuleSetting('core', 'fork_api_private_key') == '' || self::getModuleSetting('core', 'fork_api_public_key') == '')
			{
				$warnings[] = array('message' => sprintf(BL::err('ForkAPIKeys'), BackendModel::createURLForAction('index', 'settings')));
			}
		}

		// check for extensions warnings
		$warnings = array_merge($warnings, BackendExtensionsModel::checkSettings());

		return $warnings;
	}

	/**
	 * Creates an URL for a given action and module
	 * If you don't specify an action the current action will be used.
	 * If you don't specify a module the current module will be used.
	 * If you don't specify a language the current language will be used.
	 *
	 * @param string[optional] $action The action to build the URL for.
	 * @param string[optional] $module The module to build the URL for.
	 * @param string[optional] $language The language to use, if not provided we will use the working language.
	 * @param array[optional] $parameters GET-parameters to use.
	 * @param bool[optional] $urlencode Should the parameters be urlencoded?
	 * @return string
	 */
	public static function createURLForAction($action = null, $module = null, $language = null, array $parameters = null, $urlencode = true)
	{
		// grab the URL from the reference
		$URL = Spoon::get('url');

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
	 * @param string[optional] $module The module wherefore the extra exists.
	 * @param string[optional] $type The type of extra, possible values are block, homepage, widget.
	 * @param array[optional] $data Extra data that exists.
	 */
	public static function deleteExtra($module = null, $type = null, array $data = null)
	{
		// init
		$query = 'SELECT i.id, i.data FROM modules_extras AS i WHERE 1';
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
	 * @param int $id The id of the extra to delete.
	 */
	public static function deleteExtraById($id)
	{
		$id = (int) $id;

		// unset blocks
		BackendModel::getDB(true)->update('pages_blocks', array('extra_id' => null), 'extra_id = ?', $id);

		// delete extra
		BackendModel::getDB(true)->delete('modules_extras', 'id = ?', $id);
	}

	/**
	 * Generate a totally random but readable/speakable password
	 *
	 * @param int[optional] $length The maximum length for the password to generate.
	 * @param bool[optional] $uppercaseAllowed Are uppercase letters allowed?
	 * @param bool[optional] $lowercaseAllowed Are lowercase letters allowed?
	 * @return string
	 */
	public static function generatePassword($length = 6, $uppercaseAllowed = true, $lowercaseAllowed = true)
	{
		// list of allowed vowels and vowelsounds
		$vowels = array('a', 'e', 'i', 'u', 'ae', 'ea');

		// list of allowed consonants and consonant sounds
		$consonants = array(
			'b', 'c', 'd', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w',
			'tr', 'cr', 'fr', 'dr', 'wr', 'pr', 'th', 'ch', 'ph', 'st'
		);

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

		return $pass;
	}

	/**
	 * Generate a random string
	 *
	 * @param int[optional] $length Length of random string.
	 * @param bool[optional] $numeric Use numeric characters.
	 * @param bool[optional] $lowercase Use alphanumeric lowercase characters.
	 * @param bool[optional] $uppercase Use alphanumeric uppercase characters.
	 * @param bool[optional] $special Use special characters.
	 * @return string
	 */
	public static function generateRandomString($length = 15, $numeric = true, $lowercase = true, $uppercase = true, $special = true)
	{
		$characters = '';
		$string = '';

		// possible characters
		if($numeric) $characters .= '1234567890';
		if($lowercase) $characters .= 'abcdefghijklmnopqrstuvwxyz';
		if($uppercase) $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		if($special) $characters .= '-_.:;,?!@#&=)([]{}*+%$';

		// get random characters
		for($i = 0; $i < $length; $i++)
		{
			// random index
			$index = mt_rand(0, strlen($characters));

			// add character to salt
			$string .= mb_substr($characters, $index, 1, SPOON_CHARSET);
		}

		return $string;
	}

	/**
	 * Fetch the list of long date formats including examples of these formats.
	 *
	 * @return array
	 */
	public static function getDateFormatsLong()
	{
		$possibleFormats = array();

		// loop available formats
		foreach((array) self::getModuleSetting('core', 'date_formats_long') as $format)
		{
			// get date based on given format
			$possibleFormats[$format] = SpoonDate::getDate($format, null, BackendAuthentication::getUser()->getSetting('interface_language'));
		}

		return $possibleFormats;
	}

	/**
	 * Fetch the list of short date formats including examples of these formats.
	 *
	 * @return array
	 */
	public static function getDateFormatsShort()
	{
		$possibleFormats = array();

		// loop available formats
		foreach((array) self::getModuleSetting('core', 'date_formats_short') as $format)
		{
			// get date based on given format
			$possibleFormats[$format] = SpoonDate::getDate($format, null, BackendAuthentication::getUser()->getSetting('interface_language'));
		}

		return $possibleFormats;
	}

	/**
	 * Get (or create and get) a database-connection
	 * If the database wasn't stored in the reference before we will create it and add it
	 *
	 * @param bool[optional] $write Do you want the write-connection or not?
	 * @return SpoonDatabase
	 */
	public static function getDB($write = false)
	{
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

		return Spoon::get('database');
	}

	/**
	 * Get the page-keys
	 *
	 * @param string[optional] $language The language to use, if not provided we will use the working language.
	 * @return array
	 */
	public static function getKeys($language = null)
	{
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

		return self::$keys[$language];
	}

	/**
	 * Get the modules
	 *
	 * @return array
	 */
	public static function getModules()
	{
		// validate cache
		if(empty(self::$modules))
		{
			// get all modules
			$modules = (array) self::getDB()->getColumn('SELECT m.name FROM modules AS m');

			// add modules to the cache
			foreach($modules as $module) self::$modules[] = $module;
		}

		return self::$modules;
	}

	/**
	 * Get a certain module-setting
	 *
	 * @param string $module The module in which the setting is stored.
	 * @param string $key The name of the setting.
	 * @param mixed[optional] $defaultValue The value to return if the setting isn't present.
	 * @return mixed
	 */
	public static function getModuleSetting($module, $key, $defaultValue = null)
	{
		$module = (string) $module;
		$key = (string) $key;

		// are the values available
		if(empty(self::$moduleSettings))
		{
			self::getModuleSettings();
		}

		// if the value isn't present we should set a defaultvalue
		if(!isset(self::$moduleSettings[$module][$key]))
		{
			return $defaultValue;
		}

		return self::$moduleSettings[$module][$key];
	}

	/**
	 * Get all module settings at once
	 *
	 * @return array
	 */
	public static function getModuleSettings()
	{
		// are the values available
		if(empty(self::$moduleSettings))
		{
			// get all settings
			$moduleSettings = (array) self::getDB()->getRecords(
				'SELECT ms.module, ms.name, ms.value
				 FROM modules_settings AS ms'
			);

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

		return self::$moduleSettings;
	}

	/**
	 * Fetch the list of modules, but for a dropdown.
	 *
	 * @return array
	 */
	public static function getModulesForDropDown()
	{
		$dropdown = array('core' => 'core');

		// fetch modules
		$modules = self::getModules();

		// loop and add into the return-array (with correct label)
		foreach($modules as $module)
		{
			$dropdown[$module] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($module)));
		}

		return $dropdown;
	}

	/**
	 * Get the navigation-items
	 *
	 * @param string[optional] $language The language to use, if not provided we will use the working language.
	 * @return array
	 */
	public static function getNavigation($language = null)
	{
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

		return self::$navigation[$language];
	}

	/**
	 * Fetch the list of number formats including examples of these formats.
	 *
	 * @return array
	 */
	public static function getNumberFormats()
	{
		$possibleFormats = array();

		// loop available formats
		foreach((array) self::getModuleSetting('core', 'number_formats') as $format => $example)
		{
			// reformat array
			$possibleFormats[$format] = $example;
		}

		return $possibleFormats;
	}

	/**
	 * Fetch the list of time formats including examples of these formats.
	 *
	 * @return array
	 */
	public static function getTimeFormats()
	{
		$possibleFormats = array();

		// loop available formats
		foreach(self::getModuleSetting('core', 'time_formats') as $format)
		{
			// get time based on given format
			$possibleFormats[$format] = SpoonDate::getDate($format, null, BackendAuthentication::getUser()->getSetting('interface_language'));
		}

		return $possibleFormats;
	}

	/**
	 * Get URL for a given pageId
	 *
	 * @param int $pageId The id of the page to get the URL for.
	 * @param string[optional] $language The language to use, if not provided we will use the working language.
	 * @return string
	 */
	public static function getURL($pageId, $language = null)
	{
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
		return urldecode($URL);
	}

	/**
	 * Get the URL for a give module & action combination
	 *
	 * @param string $module The module to get the URL for.
	 * @param string[optional] $action The action to get the URL for.
	 * @param string[optional] $language The language to use, if not provided we will use the working language.
	 * @return string
	 */
	public static function getURLForBlock($module, $action = null, $language = null)
	{
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
			foreach($level as $pages)
			{
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

		// still no page id?
		if($pageIdForURL === null) return self::getURL(404);

		// build URL
		$URL = self::getURL($pageIdForURL, $language);

		// set locale
		FrontendLanguage::setLocale($language);

		// append action
		$URL .= '/' . urldecode(FL::act(SpoonFilter::toCamelCase($action)));

		// return the unique URL!
		return $URL;
	}

	/**
	 * Get the UTC date in a specific format. Use this method when inserting dates in the database!
	 *
	 * @param string[optional] $format The format to return the timestamp in. Default is MySQL datetime format.
	 * @param int[optional] $timestamp The timestamp to use, if not provided the current time will be used.
	 * @return string
	 */
	public static function getUTCDate($format = null, $timestamp = null)
	{
		$format = ($format !== null) ? (string) $format : 'Y-m-d H:i:s';

		// no timestamp given
		if($timestamp === null) return gmdate($format);

		// timestamp given
		return gmdate($format, (int) $timestamp);
	}

	/**
	 * Get the UTC timestamp for a date/time object combination.
	 *
	 * @param SpoonFormDate $date An instance of SpoonFormDate.
	 * @param SpoonFormTime[optional] $time An instance of SpoonFormTime.
	 * @return int
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
	 * Image Delete
	 *
	 * @param string $module Module name.
	 * @param string $filename Filename.
	 * @param string[optional] $subDirectory Subdirectory.
	 * @param array[optional] $fileSizes Possible file sizes.
	 */
	public static function imageDelete($module, $filename, $subDirectory = '', $fileSizes = null)
	{
		// get fileSizes var from model
		if(empty($fileSizes))
		{
			$model = get_class_vars('Backend' . SpoonFilter::toCamelCase($module) . 'Model');
			$fileSizes = $model['fileSizes'];
		}

		// loop all directories
		foreach(array_keys($fileSizes) as $sizeDir) SpoonFile::delete(FRONTEND_FILES_PATH . '/' . $module . (empty($subDirectory) ? '/' : $subDirectory . '/') . $sizeDir . '/' . $filename);

		// delete original
		SpoonFile::delete(FRONTEND_FILES_PATH . '/' . $module . (empty($subDirectory) ? '/' : $subDirectory . '/') . 'source/' . $filename);
	}

	/**
	 * Image Save
	 *
	 * @param SpoonFormImage $imageFile ImageFile.
	 * @param string $module Module name.
	 * @param string $filename Filename.
	 * @param string[optional] $subDirectory Subdirectory.
	 * @param array[optional] $fileSizes Possible file sizes.
	 */
	public static function imageSave($imageFile, $module, $filename, $subDirectory = '', $fileSizes = null)
	{
		// get fileSizes var from model
		if(empty($fileSizes))
		{
			$model = get_class_vars('Backend' . SpoonFilter::toCamelCase($module) . 'Model');
			$fileSizes = $model['fileSizes'];
		}

		// loop all directories and create
		foreach($fileSizes as $sizeDir => $size)
		{
			// set parameters
			$filepath = FRONTEND_FILES_PATH . '/' . $module . (empty($subDirectory) ? '/' : $subDirectory . '/') . $sizeDir . '/' . $filename;
			$width = $size['width'];
			$height = $size['height'];
			$allowEnlargement = (empty($size['allowEnlargement']) ? null : $size['allowEnlargement']);
			$forceOriginalAspectRatio = (empty($size['forceOriginalAspectRatio']) ? null : $size['forceOriginalAspectRatio']);

			// create
			$imageFile->createThumbnail($filepath, $width, $height, $allowEnlargement, $forceOriginalAspectRatio);
		}

		// save original
		$imageFile->moveFile(FRONTEND_FILES_PATH . '/' . $module . (empty($subDirectory) ? '/' : $subDirectory . '/') . 'source/' . $filename);
	}

	/**
	 * Invalidate cache
	 *
	 * @param string[optional] $module A specific module to clear the cache for.
	 * @param string[optional] $language The language to use.
	 */
	public static function invalidateFrontendCache($module = null, $language = null)
	{
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
	 * @param string[optional] $pageOrFeedURL The page/feed that has changed.
	 * @param string[optional] $category An optional category for the site.
	 * @return bool If everything went fne true will, otherwise false.
	 */
	public static function ping($pageOrFeedURL = null, $category = null)
	{
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

		return true;
	}

	/**
	 * Saves a module-setting into the DB and the cached array
	 *
	 * @param string $module The module to set the setting for.
	 * @param string $key The name of the setting.
	 * @param string $value The value to store.
	 */
	public static function setModuleSetting($module, $key, $value)
	{
		$module = (string) $module;
		$key = (string) $key;
		$valueToStore = serialize($value);

		// store
		self::getDB(true)->execute(
			'INSERT INTO modules_settings(module, name, value)
			 VALUES(?, ?, ?)
			 ON DUPLICATE KEY UPDATE value = ?',
			array($module, $key, $valueToStore, $valueToStore)
		);

		// cache it
		self::$moduleSettings[$module][$key] = $value;
	}

	/**
	 * Start processing the hooks
	 */
	public static function startProcessingHooks()
	{
		// is the queue already running?
		if(SpoonFile::exists(BACKEND_CACHE_PATH . '/hooks/pid'))
		{
			// get the pid
			$pid = trim(SpoonFile::getContent(BACKEND_CACHE_PATH . '/hooks/pid'));

			// running on windows?
			if(strtolower(substr(php_uname('s'), 0, 3)) == 'win')
			{
				// get output
				$output = @shell_exec('tasklist.exe /FO LIST /FI "PID eq ' . $pid . '"');

				// validate output
				if($output == '' || $output === false)
				{
					// delete the pid file
					SpoonFile::delete(BACKEND_CACHE_PATH . '/hooks/pid');
				}

				// already running
				else return true;
			}

			// Mac
			elseif(strtolower(substr(php_uname('s'), 0, 6)) == 'darwin')
			{
				// get output
				$output = @posix_getsid($pid);

				// validate output
				if($output === false)
				{
					// delete the pid file
					SpoonFile::delete(BACKEND_CACHE_PATH . '/hooks/pid');
				}

				// already running
				else return true;
			}

			// UNIX
			else
			{
				// check if the process is still running, by checking the proc folder
				if(!SpoonFile::exists('/proc/' . $pid))
				{
					// delete the pid file
					SpoonFile::delete(BACKEND_CACHE_PATH . '/hooks/pid');
				}

				// already running
				else return true;
			}
		}

		// init var
		$parts = parse_url(SITE_URL);
		$errNo = '';
		$errStr = '';
		$defaultPort = 80;
		if($parts['scheme'] == 'https') $defaultPort = 433;

		// open the socket
		$socket = fsockopen($parts['host'], (isset($parts['port'])) ? $parts['port'] : $defaultPort, $errNo, $errStr, 1);

		// build the request
		$request = 'GET /backend/cronjob.php?module=core&action=process_queued_hooks HTTP/1.1' . "\r\n";
		$request .= 'Host: ' . $parts['host'] . "\r\n";
		$request .= 'Content-Length: 0' . "\r\n\r\n";
		$request .= 'Connection: Close' . "\r\n\r\n";

		// send the request
		fwrite($socket, $request);

		// close the socket
		fclose($socket);

		return true;
	}

	/**
	 * Submit ham, this call is intended for the marking of false positives, things that were incorrectly marked as spam.
	 *
	 * @param string $userIp IP address of the comment submitter.
	 * @param string $userAgent User agent information.
	 * @param string[optional] $content The content that was submitted.
	 * @param string[optional] $author Submitted name with the comment.
	 * @param string[optional] $email Submitted email address.
	 * @param string[optional] $url Commenter URL.
	 * @param string[optional] $permalink The permanent location of the entry the comment was submitted to.
	 * @param string[optional] $type May be blank, comment, trackback, pingback, or a made up value like "registration".
	 * @param string[optional] $referrer The content of the HTTP_REFERER header should be sent here.
	 * @param array[optional] $others Other data (the variables from $_SERVER).
	 * @return bool If everthing went fine, true will be returned, otherwise an exception will be triggered.
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
	 * @param string $userIp IP address of the comment submitter.
	 * @param string $userAgent User agent information.
	 * @param string[optional] $content The content that was submitted.
	 * @param string[optional] $author Submitted name with the comment.
	 * @param string[optional] $email Submitted email address.
	 * @param string[optional] $url Commenter URL.
	 * @param string[optional] $permalink The permanent location of the entry the comment was submitted to.
	 * @param string[optional] $type May be blank, comment, trackback, pingback, or a made up value like "registration".
	 * @param string[optional] $referrer The content of the HTTP_REFERER header should be sent here.
	 * @param array[optional] $others Other data (the variables from $_SERVER).
	 * @return bool If everything went fine true will be returned, otherwise an exception will be triggered.
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

	/**
	 * Subscribe to an event, when the subsription already exists, the callback will be updated.
	 *
	 * @param string $eventModule The module that triggers the event.
	 * @param string $eventName The name of the event.
	 * @param string $module The module that subsribes to the event.
	 * @param mixed $callback The callback that should be executed when the event is triggered.
	 */
	public static function subscribeToEvent($eventModule, $eventName, $module, $callback)
	{
		// validate
		if(!is_callable($callback)) throw new BackendException('Invalid callback!');

		// build record
		$item['event_module'] = (string) $eventModule;
		$item['event_name'] = (string) $eventName;
		$item['module'] = (string) $module;
		$item['callback'] = serialize($callback);
		$item['created_on'] = BackendModel::getUTCDate();

		// get db
		$db = self::getDB(true);

		// check if the subscription already exists
		$exists = (bool) $db->getVar(
			'SELECT COUNT(*)
			 FROM hooks_subscriptions AS i
			 WHERE i.event_module = ? AND i.event_name = ? AND i.module = ?',
			array($eventModule, $eventName, $module)
		);

		// update
		if($exists) $db->update('hooks_subscriptions', $item, 'event_module = ? AND event_name = ? AND module = ?', array($eventModule, $eventName, $module));

		// insert
		else $db->insert('hooks_subscriptions', $item);
	}

	/**
	 * Trigger an event
	 *
	 * @param string $module The module that triggers the event.
	 * @param string $eventName The name of the event.
	 * @param mixed[optional] $data The data that should be send to subscribers.
	 */
	public static function triggerEvent($module, $eventName, $data = null)
	{
		$module = (string) $module;
		$eventName = (string) $eventName;

		// create log instance
		$log = new SpoonLog('custom', PATH_WWW . '/backend/cache/logs/events');

		// logging when we are in debugmode
		if(SPOON_DEBUG) $log->write('Event (' . $module . '/' . $eventName . ') triggered.');

		// get all items that subscribe to this event
		$subscriptions = (array) self::getDB()->getRecords(
			'SELECT i.module, i.callback
			 FROM hooks_subscriptions AS i
			 WHERE i.event_module = ? AND i.event_name = ?',
			array($module, $eventName)
		);

		// any subscriptions?
		if(!empty($subscriptions))
		{
			// init var
			$queuedItems = array();

			// loop items
			foreach($subscriptions as $subscription)
			{
				// build record
				$item['module'] = $subscription['module'];
				$item['callback'] = $subscription['callback'];
				$item['data'] = serialize($data);
				$item['status'] = 'queued';
				$item['created_on'] = BackendModel::getUTCDate();

				// add
				$queuedItems[] = self::getDB(true)->insert('hooks_queue', $item);

				// logging when we are in debugmode
				if(SPOON_DEBUG) $log->write('Callback (' . $subscription['callback'] . ') is subcribed to event (' . $module . '/' . $eventName . ').');
			}

			// start processing
			self::startProcessingHooks();
		}
	}

	/**
	 * Unsubscribe from an event
	 *
	 * @param string $eventModule The module that triggers the event.
	 * @param string $eventName The name of the event.
	 * @param string $module The module that subsribes to the event.
	 */
	public static function unsubscribeFromEvent($eventModule, $eventName, $module)
	{
		$eventModule = (string) $eventModule;
		$eventName = (string) $eventName;
		$module = (string) $module;

		self::getDB(true)->delete(
			'hooks_subscriptions', 'event_module = ? AND event_name = ? AND module = ?',
			array($eventModule, $eventName, $module)
		);
	}
}
