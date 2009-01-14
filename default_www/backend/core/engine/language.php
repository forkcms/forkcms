<?php

/**
 * BackendLanguage
 *
 * This class will store the language-dependant content for the CMS, it will also store the current language for the user.
 *
 * @package		backend
 * @subpackage	language
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendLanguage
{
	// Default language for the CMS-user-interface
	const DEFAULT_LANGUAGE = 'nl';


	/**
	 * The labels
	 *
	 * @var	array
	 */
	protected static $err = array(),
					 $lbl = array(),
					 $msg = array();


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
	 * @param	string $key
	 * @param	string[optional] $module
	 */
	public static function getError($key, $module = 'core')
	{
		// redefine
		$key = (string) $key;
		$module = (string) $module;

		// if the error exists return it, otherwise return the key in label-format
		return (isset(self::$err[$module][$key])) ? self::$err[$module][$key] : '{$err'. SpoonFilter::toCamelCase($module .'_'. $key)  .'}';
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
	 * Get a label from the language-file
	 *
	 * @return	string
	 * @param	string $key
	 * @param	string[optional] $module
	 */
	public static function getLabel($key, $module = 'core')
	{
		// redefine
		$key = (string) $key;
		$module = (string) $module;

		// if the error exists return it, otherwise return the key in label-format
		return (isset(self::$lbl[$module][$key])) ? self::$lbl[$module][$key] : '{$lbl'. SpoonFilter::toCamelCase($module .'_'. $key) .'}';
	}


	/**
	 * Get all the labels from the language-file
	 *
	 * @return	array
	 */
	public static function getLabels()
	{
		return (array) self::$lbl;
	}


	/**
	 * Get a message from the language-file
	 *
	 * @return	string
	 * @param	string $key
	 * @param	string[optional] $module
	 */
	public static function getMessage($key, $module = 'core')
	{
		// redefine
		$key = (string) $key;
		$module = (string) $module;

		// if the message exists return it, otherwise return the key in label-format
		return (isset(self::$msg[$module][$key])) ? self::$msg[$module][$key] : '{$msg'. SpoonFilter::toCamelCase($module .'_'. $key)  .'}';
	}


	/**
	 * Get the messages
	 *
	 * @return	array
	 */
	public static function getMessages()
	{
		return (array) self::$msg;
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
	 * Set the current working language
	 *
	 * @return	void
	 * @param	string $language
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
	 * @param	string $language
	 */
	public static function setLocale($language)
	{
		// redefine
		$language = (string) $language;

		// check if file exists
		if(!SpoonFile::exists(BACKEND_CACHE_PATH .'/locale/'. $language .'.php')) throw new BackendException('Languagefile ('. $language .') can\'t be found.');

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
	 * @param	string $key
	 * @param	string[optional] $module
	 */
	public static function err($key, $module = 'core')
	{
		return BackendLanguage::getError($key, $module);
	}
}

?>