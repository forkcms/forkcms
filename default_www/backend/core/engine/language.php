<?php

/**
 * BackendLanguage
 * This class will store the language-dependant content for the Backend, it will also store the current language for the user.
 *
 * @package		backend
 * @subpackage	language
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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
			if(Spoon::isObjectReference('url')) $module = Spoon::getObjectReference('url')->getModule();
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
		return '{$err'. SpoonFilter::toCamelCase($module) . $key .'}';
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
	 * @return string
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
		// grab from settings
		$languages = BackendModel::getSetting('core', 'interface_languages', array('nl'));

		// init var
		$return = array();

		// loop language to reset the label
		foreach($languages as $key) $return[$key] = BackendLanguage::getMessage(mb_strtoupper($key), 'core');

		// return
		return $return;
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
			if(Spoon::isObjectReference('url')) $module = Spoon::getObjectReference('url')->getModule();
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
		return '{$lbl'. SpoonFilter::toCamelCase($module) . $key .'}';
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
		$languages = BackendModel::getSetting('locale', 'languages');

		// init var
		$return = array();

		// loop language to reset the label
		foreach($languages as $key) $return[$key] = BackendLanguage::getMessage(mb_strtoupper($key), 'core');

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
			if(Spoon::isObjectReference('url')) $module = Spoon::getObjectReference('url')->getModule();
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
		return '{$msg'. SpoonFilter::toCamelCase($module) . $key .'}';
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
	 * @return string
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
		// get languages
		$languages = BackendModel::getSetting('core', 'languages', array('nl'));

		// init var
		$return = array();

		// loop language to reset the label
		foreach($languages as $key) $return[$key] = BackendLanguage::getMessage(mb_strtoupper($key), 'core');

		// return
		return $return;
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
		if(!SpoonFile::exists(BACKEND_CACHE_PATH .'/locale/'. $language .'.php'))
		{
			// require the BackendLocaleModel
			require_once BACKEND_MODULES_PATH .'/locale/engine/model.php';

			// build locale file
			BackendLocaleModel::buildCache($language, APPLICATION);
		}

		// store
		self::$currentInterfaceLanguage = $language;

		// store in cookie
		SpoonCookie::set('interface_language', $language);

		// store in session for TinyMCE
		SpoonSession::set('tiny_mce_language', $language);
		SpoonSession::set('interface_language', $language);

		// init vars
		$err = array();
		$lbl = array();
		$msg = array();

		// require file
		require BACKEND_CACHE_PATH .'/locale/'. $language .'.php';

		// set language specific labels
		self::$err = (array) $err;
		self::$lbl = (array) $lbl;
		self::$msg = (array) $msg;
	}
}


/**
 * BL (some kind of alias for BackendLanguage)
 *
 *
 * @package		backend
 * @subpackage	language
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BL extends BackendLanguage
{
	/**
	 * Get an error from the language-file
	 *
	 * @return	string
	 * @param	string $key					The key to get.
	 * @param	string[optional] $module	The module to look in.
	 */
	public static function err($key, $module = 'core')
	{
		return BackendLanguage::getError($key, $module);
	}


	/**
	 * Get a label from the language-file
	 *
	 * @return	string
	 * @param	string $key					The key to get.
	 * @param	string[optional] $module	The module to look in.
	 */
	public static function lbl($key, $module = 'core')
	{
		return BackendLanguage::getLabel($key, $module);
	}


	/**
	 * Get a message from the language-file
	 *
	 * @return	string
	 * @param	string $key					The key to get.
	 * @param	string[optional] $module	The module to look in.
	 */
	public static function msg($key, $module = 'core')
	{
		return BackendLanguage::getMessage($key, $module);
	}
}

?>