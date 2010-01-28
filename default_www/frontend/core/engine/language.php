<?php

/**
 * FrontendLanguage
 *
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
	// default language
	const DEFAULT_LANGUAGE = 'nl';


	/**
	 * The labels
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
	private static $languages = array('active' => array(),
										'possible_redirect' => array());


	/**
	 * Get an action from the language-file
	 *
	 * @return	string
	 * @param	string $key
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
		return (array) self::$act;
	}


	/**
	 * Get the active languages
	 *
	 * @return	array
	 */
	public static function getActiveLanguages()
	{
		// validate array
		if(empty(self::$languages['active']))
		{
			// grab from settings
			$activeLanguages = (array) FrontendModel::getModuleSetting('core', 'languages');

			// store
			self::$languages['active'] = $activeLanguages;
		}

		// return
		return self::$languages['active'];
	}


	/**
	 * Get the prefered language by using the browser-language
	 *
	 * @return	string
	 * @param	bool[optional] $forRedirect
	 */
	public static function getBrowserLanguage($forRedirect = true)
	{
		// browser language set
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strlen($_SERVER['HTTP_ACCEPT_LANGUAGE']) >= 2)
		{
			// get languages
			$aPossibleLanguages = self::getActiveLanguages();
			$aRedirectLanguages = self::getRedirectLanguages();

			// prefered languages
			$aBrowserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

			// loop until result
			foreach($aBrowserLanguages as $language)
			{
				// redefine language
				$language = substr($language, 0, 2); // first two characters

				// find possible language
				if($forRedirect)
				{
					// check in the redirect-languages
					if(in_array($language, $aRedirectLanguages)) return $language;
				}
				else
				{
					// check in the active-languages
					if(in_array($language, $aPossibleLanguages)) return $language;
				}
			}
		}

		// fallback
		return self::DEFAULT_LANGUAGE;
	}


	/**
	 * Get an error from the language-file
	 *
	 * @return	string
	 * @param	string $key
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
		return (array) self::$err;
	}


	/**
	 * Get a label from the language-file
	 *
	 * @return	string
	 * @param	string $key
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
		return (array) self::$lbl;
	}


	/**
	 * Get a message from the language-file
	 *
	 * @return	string
	 * @param	string $key
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
		return (array) self::$msg;
	}


	/**
	 * Get the redirect languages
	 *
	 * @return	array
	 */
	public static function getRedirectLanguages()
	{
		// validate array
		if(empty(self::$languages['possible_redirect']))
		{
			// grab from settings
			$redirectLanguages = (array) FrontendModel::getModuleSetting('core', 'active_languages');

			// store
			self::$languages['possible_redirect'] = $redirectLanguages;
		}

		// return
		return self::$languages['possible_redirect'];
	}


	/**
	 * Set locale
	 *
	 * @return	void
	 * @param	string[optional] $language
	 */
	public static function setLocale($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// validate language
		if(!in_array($language, self::getActiveLanguages())) throw new FrontendException('Invalid language ('. $language .').');

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
	 * @param	string $key
	 */
	public static function act($key)
	{
		return FrontendLanguage::getAction($key);
	}


	/**
	 * Get an error from the language-file
	 *
	 * @return	string
	 * @param	string $key
	 */
	public static function err($key)
	{
		return FrontendLanguage::getError($key);
	}


	/**
	 * Get a label from the language-file
	 *
	 * @return	string
	 * @param	string $key
	 */
	public static function lbl($key)
	{
		return FrontendLanguage::getLabel($key);
	}


	/**
	 * Get a message from the language-file
	 *
	 * @return	string
	 * @param	string $key
	 * @param	string[optional] $module
	 */
	public static function msg($key)
	{
		return FrontendLanguage::getMessage($key);
	}
}

?>