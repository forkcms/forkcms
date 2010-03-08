<?php

/**
 * FrontendLanguage
 * This class will store the language-dependant content for the frontend.
 *
 * @package		frontend
 * @subpackage	language
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendLanguage
{
	/**
	 * Locale arrays
	 *
	 * @var	array
	 */
	private static	$act = array(),
					$err = array(),
					$lbl = array(),
					$msg = array();


	/**
	 * The possible languages
	 *
	 * @var	array
	 */
	private static $languages = array('active' => array(), 'possible_redirect' => array());


	/**
	 * Get an action from the language-file
	 *
	 * @return	string
	 * @param	string $key		The key to get.
	 */
	public static function getAction($key)
	{
		// redefine
		$key = (string) $key;

		// if the action exists return it,
		if(isset(self::$act[$key])) return self::$act[$key];

		// otherwise return the key in label-format
		return '{$act'. SpoonFilter::toCamelCase($key)  .'}';
	}


	/**
	 * Get all the actions
	 *
	 * @return	array
	 */
	public static function getActions()
	{
		return self::$act;
	}


	/**
	 * Get the active languages
	 *
	 * @return	array
	 */
	public static function getActiveLanguages()
	{
		// validate the cache
		if(empty(self::$languages['active']))
		{
			// grab from settings
			$activeLanguages = (array) FrontendModel::getModuleSetting('core', 'active_languages');

			// store in cache
			self::$languages['active'] = $activeLanguages;
		}

		// return from cache
		return self::$languages['active'];
	}


	/**
	 * Get the prefered language by using the browser-language
	 *
	 * @return	string
	 * @param	bool[optional] $forRedirect		Only look in the languages to redirect?
	 */
	public static function getBrowserLanguage($forRedirect = true)
	{
		// browser language set
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strlen($_SERVER['HTTP_ACCEPT_LANGUAGE']) >= 2)
		{
			// get languages
			$redirectLanguages = self::getRedirectLanguages();

			// prefered languages
			$browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

			// loop until result
			foreach($browserLanguages as $language)
			{
				// redefine language
				$language = substr($language, 0, 2); // first two characters

				// find possible language
				if($forRedirect)
				{
					// check in the redirect-languages
					if(in_array($language, $redirectLanguages)) return $language;
				}
			}
		}

		// fallback
		return SITE_DEFAULT_LANGUAGE;
	}


	/**
	 * Get an error from the language-file
	 *
	 * @return	string
	 * @param	string $key		The key to get.
	 */
	public static function getError($key)
	{
		// redefine
		$key = (string) $key;

		// if the error exists return it,
		if(isset(self::$err[$key])) return self::$err[$key];

		// otherwise return the key in label-format
		return '{$err'. SpoonFilter::toCamelCase($key)  .'}';
	}


	/**
	 * Get all the errors
	 *
	 * @return	array
	 */
	public static function getErrors()
	{
		return self::$err;
	}


	/**
	 * Get a label from the language-file
	 *
	 * @return	string
	 * @param	string $key		The key to get.
	 */
	public static function getLabel($key)
	{
		// redefine
		$key = (string) $key;

		// if the error exists return it,
		if(isset(self::$lbl[$key])) return self::$lbl[$key];

		// otherwise return the key in label-format
		return '{$lbl'. SpoonFilter::toCamelCase($key)  .'}';
	}


	/**
	 * Get all the labels
	 *
	 * @return	array
	 */
	public static function getLabels()
	{
		return self::$lbl;
	}


	/**
	 * Get a message from the language-file
	 *
	 * @return	string
	 * @param	string $key		The key to get.
	 */
	public static function getMessage($key)
	{
		// redefine
		$key = (string) $key;

		// if the error exists return it,
		if(isset(self::$msg[$key])) return self::$msg[$key];

		// otherwise return the key in label-format
		return '{$msg'. SpoonFilter::toCamelCase($key)  .'}';
	}


	/**
	 * Get all the messages
	 *
	 * @return	array
	 */
	public static function getMessages()
	{
		return self::$msg;
	}


	/**
	 * Get the redirect languages
	 *
	 * @return	array
	 */
	public static function getRedirectLanguages()
	{
		// validate the cache
		if(empty(self::$languages['possible_redirect']))
		{
			// grab from settings
			$redirectLanguages = (array) FrontendModel::getModuleSetting('core', 'active_languages');

			// store in cache
			self::$languages['possible_redirect'] = $redirectLanguages;
		}

		// return
		return self::$languages['possible_redirect'];
	}


	/**
	 * Set locale
	 *
	 * @return	void
	 * @param	string[optional] $language	The language to load, if not provided we will load the language based on the URL.
	 */
	public static function setLocale($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// validate language
		if(!in_array($language, self::getActiveLanguages())) throw new FrontendException('Invalid language ('. $language .').');

		// validate file
		if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/locale/'. $language .'.php')) throw new FrontendException('No locale-file ('. $language .'.php) found.');

		// init vars
		$act = array();
		$err = array();
		$lbl = array();
		$msg = array();

		// require file
		require FRONTEND_CACHE_PATH .'/locale/'. $language .'.php';

		// set language specific labels
		self::$act = (array) $act;
		self::$err = (array) $err;
		self::$lbl = (array) $lbl;
		self::$msg = (array) $msg;
	}
}


/**
 * FL (some kind of alias for FrontendLanguage)
 *
 *
 * @package		frontend
 * @subpackage	language
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FL extends FrontendLanguage
{
	/**
	 * Get an action from the language-file
	 *
	 * @return	string
	 * @param	string $key		The key to get.
	 */
	public static function act($key)
	{
		return FrontendLanguage::getAction($key);
	}


	/**
	 * Get an error from the language-file
	 *
	 * @return	string
	 * @param	string $key		The key to get.
	 */
	public static function err($key)
	{
		return FrontendLanguage::getError($key);
	}


	/**
	 * Get a label from the language-file
	 *
	 * @return	string
	 * @param	string $key		The key to get.
	 */
	public static function lbl($key)
	{
		return FrontendLanguage::getLabel($key);
	}


	/**
	 * Get a message from the language-file
	 *
	 * @return	string
	 * @param	string $key		The key to get.
	 */
	public static function msg($key)
	{
		return FrontendLanguage::getMessage($key);
	}
}

?>