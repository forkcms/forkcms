<?php

/**
 * This class will store the language-dependant content for the Backend, it will also store the current language for the user.
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendLanguage
{
	/**
	 * The labels
	 *
	 * @var	array
	 */
	protected static $err = array(),
					 $lbl = array(),
					 $msg = array();


	/**
	 * The active languages
	 *
	 * @var	array
	 */
	protected static $activeLanguages;


	/**
	 * The current interface-language
	 *
	 * @var	string
	 */
	protected static $currentInterfaceLanguage;


	/**
	 * The current language that the user is working with
	 *
	 * @var	string
	 */
	protected static $currentWorkingLanguage;


	/**
	 * Get the active languages
	 *
	 * @return	array
	 */
	public static function getActiveLanguages()
	{
		// validate the cache
		if(empty(self::$activeLanguages))
		{
			// grab from settings
			$activeLanguages = (array) BackendModel::getModuleSetting('core', 'active_languages');

			// store in cache
			self::$activeLanguages = $activeLanguages;
		}

		// return from cache
		return self::$activeLanguages;
	}


	/**
	 * Get an error from the language-file
	 *
	 * @return	string
	 * @param	string $key					The key to get.
	 * @param	string[optional] $module	The module wherin we should search.
	 */
	public static function getError($key, $module = null)
	{
		// do we know the module
		if($module === null)
		{
			if(Spoon::exists('url')) $module = Spoon::get('url')->getModule();
			elseif(isset($_GET['module']) && $_GET['module'] != '') $module = (string) $_GET['module'];
			else $module = 'core';
		}

		// redefine
		$key = (string) $key;
		$module = (string) $module;

		// if the error exists return it,
		if(isset(self::$err[$module][$key])) return self::$err[$module][$key];

		// if it exists in the core-errors
		if(isset(self::$err['core'][$key])) return self::$err['core'][$key];

		// otherwise return the key in label-format
		return '{$err' . SpoonFilter::toCamelCase($module) . $key . '}';
	}


	/**
	 * Get all the errors from the language-file
	 *
	 * @return	array
	 */
	public static function getErrors()
	{
		return (array) self::$err;
	}


	/**
	 * Get the current interface language
	 *
	 * @return	string
	 */
	public static function getInterfaceLanguage()
	{
		return self::$currentInterfaceLanguage;
	}


	/**
	 * Get all the possible interface languages
	 *
	 * @return	array
	 */
	public static function getInterfaceLanguages()
	{
		// init var
		$languages = array();

		// grab the languages from the settings & loop language to reset the label
		foreach((array) BackendModel::getModuleSetting('core', 'interface_languages', array('nl')) as $key)
		{
			// fetch language's translation
			$languages[$key] = self::getMessage(mb_strtoupper($key), 'core');
		}

		// sort alphabetically
		asort($languages);

		// return languages
		return $languages;
	}


	/**
	 * Get a label from the language-file
	 *
	 * @return	string
	 * @param	string $key					The key to get.
	 * @param	string[optional] $module	The module wherin we should search.
	 */
	public static function getLabel($key, $module = null)
	{
		// do we know the module
		if($module === null)
		{
			if(Spoon::exists('url')) $module = Spoon::get('url')->getModule();
			elseif(isset($_GET['module']) && $_GET['module'] != '') $module = (string) $_GET['module'];
			else $module = 'core';
		}

		// redefine
		$key = (string) $key;
		$module = (string) $module;

		// if the error exists return it,
		if(isset(self::$lbl[$module][$key])) return self::$lbl[$module][$key];

		// if it exists in the core-errors
		if(isset(self::$lbl['core'][$key])) return self::$lbl['core'][$key];

		// otherwise return the key in label-format
		return '{$lbl' . SpoonFilter::toCamelCase($module) . $key . '}';
	}


	/**
	 * Get all the labels from the language-file
	 *
	 * @return	array
	 */
	public static function getLabels()
	{
		return self::$lbl;
	}


	/**
	 * Get all the possible locale languages
	 *
	 * @return	array
	 */
	public static function getLocaleLanguages()
	{
		// grab from settings
		$languages = (array) BackendModel::getModuleSetting('locale', 'languages');

		// init var
		$return = array();

		// loop language to reset the label
		foreach($languages as $key) $return[$key] = self::getMessage(mb_strtoupper($key), 'core');

		// sort alphabetically
		asort($return);

		// return
		return $return;
	}


	/**
	 * Get a message from the language-file
	 *
	 * @return	string
	 * @param	string $key					The key to get.
	 * @param	string[optional] $module	The module wherin we should search.
	 */
	public static function getMessage($key, $module = null)
	{
		// do we know the module
		if($module === null)
		{
			if(Spoon::exists('url')) $module = Spoon::get('url')->getModule();
			elseif(isset($_GET['module']) && $_GET['module'] != '') $module = (string) $_GET['module'];
			else $module = 'core';
		}

		// redefine
		$key = (string) $key;
		$module = (string) $module;

		// if the error exists return it,
		if(isset(self::$msg[$module][$key])) return self::$msg[$module][$key];

		// if it exists in the core-errors
		if(isset(self::$msg['core'][$key])) return self::$msg['core'][$key];

		// otherwise return the key in label-format
		return '{$msg' . SpoonFilter::toCamelCase($module) . $key . '}';
	}


	/**
	 * Get the messages
	 *
	 * @return	array
	 */
	public static function getMessages()
	{
		return self::$msg;
	}


	/**
	 * Get the current working language
	 *
	 * @return	string
	 */
	public static function getWorkingLanguage()
	{
		return self::$currentWorkingLanguage;
	}


	/**
	 * Get all possible working languages
	 *
	 * @return	array
	 */
	public static function getWorkingLanguages()
	{
		// init var
		$languages = array();

		// grab the languages from the settings & loop language to reset the label
		foreach((array) BackendModel::getModuleSetting('core', 'languages', array('nl')) as $key)
		{
			// fetch the language's translation
			$languages[$key] = self::getMessage(mb_strtoupper($key), 'core');
		}

		// sort alphabetically
		asort($languages);

		// return languages
		return $languages;
	}


	/**
	 * Set locale
	 * It will require the correct file and init the needed vars
	 *
	 * @return	void
	 * @param	string $language		The language to load.
	 */
	public static function setLocale($language)
	{
		// redefine
		$language = (string) $language;

		// check if file exists
		if(!SpoonFile::exists(BACKEND_CACHE_PATH . '/locale/' . $language . '.php'))
		{
			// require the BackendLocaleModel
			require_once BACKEND_MODULES_PATH . '/locale/engine/model.php';

			// build locale file
			BackendLocaleModel::buildCache($language, APPLICATION);
		}

		// store
		self::$currentInterfaceLanguage = $language;

		// attempt to set a cookie
		try
		{
			// store in cookie
			SpoonCookie::set('interface_language', $language);
		}

		// catch exceptions
		catch(SpoonCookieException $e)
		{
			// settings cookies isn't allowed, because this isn't a real problem we ignore the exception
		}

		// store in session for TinyMCE
		SpoonSession::set('tiny_mce_language', $language);
		SpoonSession::set('interface_language', $language);

		// init vars
		$err = array();
		$lbl = array();
		$msg = array();

		// require file
		require BACKEND_CACHE_PATH . '/locale/' . $language . '.php';

		// set language specific labels
		self::$err = (array) $err;
		self::$lbl = (array) $lbl;
		self::$msg = (array) $msg;
	}


	/**
	 * Set the current working language
	 *
	 * @return	void
	 * @param	string $language		The language to use, if not provided we will use the working language.
	 */
	public static function setWorkingLanguage($language)
	{
		self::$currentWorkingLanguage = (string) $language;
	}
}


/**
 * An alias for BackendLanguage with some extras.
 *
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BL extends BackendLanguage
{
	/**
	 * Get an error from the language-file
	 *
	 * @return	string
	 * @param	string $key					The key to get.
	 * @param	string[optional] $module	The module wherein we should search.
	 */
	public static function err($key, $module = null)
	{
		return BackendLanguage::getError($key, $module);
	}


	/**
	 * Get a label from the language-file
	 *
	 * @return	string
	 * @param	string $key					The key to get.
	 * @param	string[optional] $module	The module wherein we should search.
	 */
	public static function lbl($key, $module = null)
	{
		return BackendLanguage::getLabel($key, $module);
	}


	/**
	 * Get a message from the language-file
	 *
	 * @return	string
	 * @param	string $key					The key to get.
	 * @param	string[optional] $module	The module wherein we should search.
	 */
	public static function msg($key, $module = null)
	{
		return BackendLanguage::getMessage($key, $module);
	}
}

?>