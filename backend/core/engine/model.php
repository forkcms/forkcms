<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use \TijsVerkoyen\Akismet\Akismet;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;

require_once __DIR__ . '/../../../app/BaseModel.php';

/**
 * In this file we store all generic functions that we will be using in the backend.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class BackendModel extends BaseModel
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

		// is numeric
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
		$parameters['token'] = self::getToken();

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

		// some applications aren't real separate applications, they are virtual applications inside the backend.
		$namedApplication = NAMED_APPLICATION;
		if(in_array($namedApplication, array('backend_direct', 'backend_ajax', 'backend_js', 'backend_cronjob'))) $namedApplication = 'backend';

		// build the URL and return it
		return '/' . $namedApplication . '/' . $language . '/' . $module . '/' . $action . $querystring;
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
		$extras = (array) BackendModel::getContainer()->get('database')->getRecords($query, $parameters);

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
	 * @param bool $deleteBlock Should the block be deleted? Default is false.
	 */
	public static function deleteExtraById($id, $deleteBlock = false)
	{
		$id = (int) $id;
		$deleteBlock = (bool) $deleteBlock;

		// delete the blocks
		if($deleteBlock)
		{
			BackendModel::getContainer()->get('database')->delete('pages_blocks', 'extra_id = ?', $id);
		}

		// unset blocks
		else
		{
			BackendModel::getContainer()->get('database')->update('pages_blocks', array('extra_id' => null), 'extra_id = ?', $id);
		}

		// delete extra
		BackendModel::getContainer()->get('database')->delete('modules_extras', 'id = ?', $id);
	}

	/**
	 * Delete all extras for a certain value in the data array of that module_extra.
	 *
	 * @param string $module 			The module for the extra.
	 * @param string $field 			The field of the data you want to check the value for.
	 * @param string $value 			The value to check the field for.
	 * @param string[optional] $action 	In case you want to search for a certain action.
	 */
	public static function deleteExtrasForData($module, $field, $value, $action = null)
	{
		// get ids
		$ids = self::getExtrasForData((string) $module, (string) $field, (string) $value, $action);

		// we have extras
		if(!empty($ids))
		{
			// delete extras
			BackendModel::getContainer()->get('database')->delete('modules_extras', 'id IN (' . implode(',', $ids) . ')');

			// invalidate the cache for the module
			BackendModel::invalidateFrontendCache((string) $module, BL::getWorkingLanguage());
		}
	}

	/**
	 * Delete thumbnails based on the folders in the path
	 *
	 * @param string $path The path wherein the thumbnail-folders exist.
	 * @param string $thumbnail The filename to be deleted.
	 */
	public static function deleteThumbnails($path, $thumbnail)
	{
		// if there is no image provided we can't do anything
		if($thumbnail == '') return;

		$finder = new Finder();
		$fs = new Filesystem();
		foreach($finder->directories()->in($path) as $directory)
		{
			$fileName = $directory->getRealPath() . '/' . $thumbnail;
			if(is_file($fileName))
			{
				$fs->remove($fileName);
			}
		}
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
	 * Generate thumbnails based on the folders in the path
	 * Use
	 *  - 128x128 as foldername to generate an image where the width will be 128px and the height will be 128px
	 *  - 128x as foldername to generate an image where the width will be 128px, the height will be calculated based on the aspect ratio.
	 *  - x128 as foldername to generate an image where the height will be 128px, the width will be calculated based on the aspect ratio.
	 *
	 * @param string $path The path wherein the thumbnail-folders will be stored.
	 * @param string $sourceFile The location of the source file.
	 */
	public static function generateThumbnails($path, $sourceFile)
	{
		// get folder listing
		$folders = self::getThumbnailFolders($path);
		$filename = basename($sourceFile);

		// loop folders
		foreach($folders as $folder)
		{
			// generate the thumbnail
			$thumbnail = new SpoonThumbnail($sourceFile, $folder['width'], $folder['height']);
			$thumbnail->setAllowEnlargement(true);

			// if the width & height are specified we should ignore the aspect ratio
			if($folder['width'] !== null && $folder['height'] !== null) $thumbnail->setForceOriginalAspectRatio(false);
			$thumbnail->parseToFile($folder['path'] . '/' . $filename);
		}
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
	 * Get extras
	 *
	 * @param array $ids 	The ids of the modules_extras to get.
	 * @return array
	 */
	public static function getExtras($ids)
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$extraIdPlaceHolders = array_fill(0, count($ids), '?');

		// get extras
		return (array) $db->getRecords(
			'SELECT i.*
			 FROM modules_extras AS i
			 WHERE i.id IN (' . implode(', ', $extraIdPlaceHolders) . ')',
			$ids
		);
	}

	/**
	 * Get extras for data
	 *
	 * @param string $module 			The module for the extra.
	 * @param string $key 				The key of the data you want to check the value for.
	 * @param string $value 			The value to check the key for.
	 * @param string[optional] $action 	In case you want to search for a certain action.
	 * @return array					The ids for the extras.
	 */
	public static function getExtrasForData($module, $key, $value, $action = null)
	{
		// init variables
		$module = (string) $module;
		$key = (string) $key;
		$value = (string) $value;
		$result = array();

		// init query
		$query =
			'SELECT i.id, i.data
			 FROM modules_extras AS i
			 WHERE i.module = ? AND i.data != ?';

		// init parameters
		$parameters = array($module, 'NULL');

		// we have an action
		if($action)
		{
			// redefine query
			$query .= ' AND i.action = ?';

			// add action to parameters
			$parameters[] = (string) $action;
		}

		// get items
		$items = (array) BackendModel::getContainer()->get('database')->getPairs($query, $parameters);

		// stop here when no items
		if(empty($items)) return $result;

		// loop items
		foreach($items as $id => $data)
		{
			// unserialize data
			$data = unserialize($data);

			// check if the field is present in the data and add it to result
			if(isset($data[$key]) && $data[$key] == $value)
			{
				// add id to result
				$result[] = $id;
			}
		}

		return $result;
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
			if(!is_file(FRONTEND_CACHE_PATH . '/navigation/keys_' . $language . '.php'))
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
			$modules = (array) self::getContainer()->get('database')->getColumn('SELECT m.name FROM modules AS m');

			// add modules to the cache
			foreach($modules as $module) self::$modules[] = $module;
		}

		return self::$modules;
	}

	/**
	 * Get the modules that are available on the filesystem
	 *
	 * @param bool[optional] $includeCore   Should core be included as a module?
	 * @return array
	 */
	public static function getModulesOnFilesystem($includeCore = true)
	{
		if($includeCore) $return = array('core');
		else $return = array();
		$finder = new Finder();
		foreach($finder->directories()->in(PATH_WWW . '/backend/modules')->depth('==0') as $folder)
		{
			$return[] = $folder->getBasename();
		}

		return $return;
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
		// redefine
		$module = (string) $module;
		$key = (string) $key;

		// define settings
		$settings = self::getModuleSettings($module);

		// return if exists, otherwise return default value
		return (isset($settings[$key])) ? $settings[$key] : $defaultValue;
	}

	/**
	 * Get all module settings at once
	 *
	 * @param string[optional] $module You can get all settings for a module.
	 * @return array
	 */
	public static function getModuleSettings($module = null)
	{
		// redefine
		$module = ((bool) $module) ? (string) $module : false;

		// are the values available
		if(empty(self::$moduleSettings))
		{
			// get all settings
			$moduleSettings = (array) self::getContainer()->get('database')->getRecords(
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

		// you want module settings
		if($module)
		{
			// return module settings if there are some, if not return empty array
			return (isset(self::$moduleSettings[$module])) ? self::$moduleSettings[$module] : array();
		}

		// else return all settings
		else return self::$moduleSettings;
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
			if(!is_file(FRONTEND_CACHE_PATH . '/navigation/navigation_' . $language . '.php'))
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
	 * Get the thumbnail folders
	 *
	 * @param string $path The path
	 * @param bool[optional] $includeSource Should the source-folder be included in the return-array.
	 * @return array
	 */
	public static function getThumbnailFolders($path, $includeSource = false)
	{
		$return = array();
		$finder = new Finder();
		$finder->name('/^([0-9]*)x([0-9]*)$/');
		if($includeSource) $finder->name('source');

		foreach($finder->directories()->in($path) as $directory) {
			$chunks = explode('x', $directory->getBasename(), 2);
			if(count($chunks) != 2 && !$includeSource) continue;

			$item = array();
			$item['dirname'] = $directory->getBasename();
			$item['path'] = $directory->getRealPath();
			if(substr($path, 0, strlen(PATH_WWW)) == PATH_WWW) $item['url'] = substr($path, strlen(PATH_WWW));

			if($item['dirname'] == 'source') {
				$item['width'] = null;
				$item['height'] = null;
			} else {
				$item['width'] = ($chunks[0] != '') ? (int) $chunks[0] : null;
				$item['height'] = ($chunks[1] != '') ? (int) $chunks[1] : null;
			}

			$return[] = $item;
		}

		return $return;
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
	 * Get the token which will protect us
	 *
	 * @return string
	 */
	public static function getToken()
	{
		if(SpoonSession::exists('csrf_token') && SpoonSession::get('csrf_token') != '')
		{
			$token = SpoonSession::get('csrf_token');
		}
		else
		{
			$token = self::generateRandomString(10, true, true, false, false);
			SpoonSession::set('csrf_token', $token);
		}

		return $token;
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

		// get the URL, if it doesn't exist return 404
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
					if(!isset($properties['extra_blocks'])) continue;

					// loop extras
					foreach($properties['extra_blocks'] as $extra)
					{
						// direct link?
						if($extra['module'] == $module && $extra['action'] == $action)
						{
							// exact page was found, so return
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

		// still no page id?
		if($pageIdForURL === null) return self::getURL(404);

		// build URL
		$URL = self::getURL($pageIdForURL, $language);

		// set locale with force
		FrontendLanguage::setLocale($language, true);

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
		if(!$date->isValid() || ($time !== null && !$time->isValid())) throw new BackendException('You need to provide two objects that actually contain valid data.');

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
		if(empty($fileSizes))
		{
			$model = get_class_vars('Backend' . SpoonFilter::toCamelCase($module) . 'Model');
			$fileSizes = $model['fileSizes'];
		}

		$fs = new Filesystem();
		foreach(array_keys($fileSizes) as $sizeDir) {
			$fullPath = FRONTEND_FILES_PATH . '/' . $module . (empty($subDirectory) ? '/' : $subDirectory . '/') . $sizeDir . '/' . $filename;
			if(is_file($fullPath))
			{
				$fs->remove($fullPath);
			}
		}
		$fullPath = FRONTEND_FILES_PATH . '/' . $module . (empty($subDirectory) ? '/' : $subDirectory . '/') . 'source/' . $filename;
		if(is_file($fullPath))
		{
			$fs->remove($fullPath);
		}
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

		if(is_dir($path))
		{
			// build regular expression
			if($module !== null)
			{
				if($language === null) $regexp = '/' . '(.*)' . $module . '(.*)_cache\.tpl/i';
				else $regexp = '/' . $language . '_' . $module . '(.*)_cache\.tpl/i';
			}
			else
			{
				if($language === null) $regexp = '/(.*)_cache\.tpl/i';
				else $regexp = '/' . $language . '_(.*)_cache\.tpl/i';
			}

			$finder = new Finder();
			$fs = new Filesystem();
			foreach($finder->files()->name($regexp)->in($path) as $file)
			{
				$fs->remove($file->getRealPath());
			}
		}
	}

	/**
	 * Is module installed?
	 *
	 * @param string $module
	 * @return bool
	 */
	public static function isModuleInstalled($module)
	{
		// get installed modules
		$modules = self::getModules();

		// return if module is installed or not
		return (in_array((string) $module, $modules));
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
					// in debug mode we want to see the exceptions
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
		self::getContainer()->get('database')->execute(
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
		$fs = new Filesystem();

		// is the queue already running?
		if($fs->exists(BACKEND_CACHE_PATH . '/hooks/pid'))
		{
			// get the pid
			$pid = trim(file_get_contents(BACKEND_CACHE_PATH . '/hooks/pid'));

			// running on windows?
			if(strtolower(substr(php_uname('s'), 0, 3)) == 'win')
			{
				// get output
				$output = @shell_exec('tasklist.exe /FO LIST /FI "PID eq ' . $pid . '"');

				// validate output
				if($output == '' || $output === false)
				{
					// delete the pid file
					$fs->remove(BACKEND_CACHE_PATH . '/hooks/pid');
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
					$fs->remove(BACKEND_CACHE_PATH . '/hooks/pid');
				}

				// already running
				else return true;
			}

			// UNIX
			else
			{
				// check if the process is still running, by checking the proc folder
				if(!$fs->exists('/proc/' . $pid))
				{
					// delete the pid file
					$fs->remove(BACKEND_CACHE_PATH . '/hooks/pid');
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
	 * @return bool If everything went fine, true will be returned, otherwise an exception will be triggered.
	 */
	public static function submitHam($userIp, $userAgent, $content, $author = null, $email = null, $url = null, $permalink = null, $type = null, $referrer = null, $others = null)
	{
		// get some settings
		$akismetKey = self::getModuleSetting('core', 'akismet_key');

		// invalid key, so we can't detect spam
		if($akismetKey === '') return false;

		// create new instance
		$akismet = new Akismet($akismetKey, SITE_URL);

		// set properties
		$akismet->setTimeOut(10);
		$akismet->setUserAgent('Fork CMS/2.1');

		// try it to decide it the item is spam
		try
		{
			// check with Akismet if the item is spam
			return $akismet->submitHam($userIp, $userAgent, $content, $author, $email, $url, $permalink, $type, $referrer, $others);
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

		// create new instance
		$akismet = new Akismet($akismetKey, SITE_URL);

		// set properties
		$akismet->setTimeOut(10);
		$akismet->setUserAgent('Fork CMS/2.1');

		// try it to decide it the item is spam
		try
		{
			// check with Akismet if the item is spam
			return $akismet->submitSpam($userIp, $userAgent, $content, $author, $email, $url, $permalink, $type, $referrer, $others);
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
	 * Subscribe to an event, when the subscription already exists, the callback will be updated.
	 *
	 * @param string $eventModule The module that triggers the event.
	 * @param string $eventName The name of the event.
	 * @param string $module The module that subscribes to the event.
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
		$db = self::getContainer()->get('database');

		// check if the subscription already exists
		$exists = (bool) $db->getVar(
			'SELECT 1
			 FROM hooks_subscriptions AS i
			 WHERE i.event_module = ? AND i.event_name = ? AND i.module = ?
			 LIMIT 1',
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
		$log = self::getContainer()->get('logger');
		$log->info('Event (' . $module . '/' . $eventName . ') triggered.');

		// get all items that subscribe to this event
		$subscriptions = (array) self::getContainer()->get('database')->getRecords(
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
				$queuedItems[] = self::getContainer()->get('database')->insert('hooks_queue', $item);

				$log->info('Callback (' . $subscription['callback'] . ') is subscribed to event (' . $module . '/' . $eventName . ').');
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
	 * @param string $module The module that subscribes to the event.
	 */
	public static function unsubscribeFromEvent($eventModule, $eventName, $module)
	{
		$eventModule = (string) $eventModule;
		$eventName = (string) $eventName;
		$module = (string) $module;

		self::getContainer()->get('database')->delete(
			'hooks_subscriptions', 'event_module = ? AND event_name = ? AND module = ?',
			array($eventModule, $eventName, $module)
		);
	}

	/**
	 * Update extra
	 *
	 * @param int $id			The id for the extra.
	 * @param string $key		The key you want to update.
	 * @param string $value 	The new value.
	 */
	public static function updateExtra($id, $key, $value)
	{
		// error checking the key
		if(!in_array((string) $key, array('label', 'action', 'data', 'hidden', 'sequence')))
		{
			throw new BackendException('The key ' . $key . ' can\'t be updated.');
		}

		// init item
		$item = array();

		// build item
		$item[(string) $key] = (string) $value;

		// update the extra
		BackendModel::getContainer()->get('database')->update('modules_extras', $item, 'id = ?', array((int) $id));
	}

	/**
	 * Update extra data
	 *
	 * @param int $id			The id for the extra.
	 * @param string $key		The key in the data you want to update.
	 * @param string $value		The new value.
	 */
	public static function updateExtraData($id, $key, $value)
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		// get data
		$data = (string) $db->getVar(
			'SELECT i.data
			 FROM modules_extras AS i
			 WHERE i.id = ?',
			 array((int) $id)
		);

		// unserialize data
		$data = unserialize($data);

		// built item
		$data[(string) $key] = (string) $value;

		// update value
		$db->update('modules_extras', array('data' => serialize($data)), 'id = ?', array((int) $id));
	}
}
