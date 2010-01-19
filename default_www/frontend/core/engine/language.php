<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	language
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendLanguage
{
	// Default language
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
	private static $aLanguages = array('active' => array('nl', 'fr', 'en'),
										'possible_redirect' => array('nl', 'fr', 'en'));


	/**
	 * Get the actions
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
		return self::$aLanguages['active'];
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
	 * Get the errors
	 *
	 * @return	array
	 */
	public static function getErrors()
	{
		return (array) self::$err;
	}


	/**
	 * Get the labels
	 *
	 * @return	array
	 */
	public static function getLabels()
	{
		return (array) self::$lbl;
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
	 * Get the redirect languages
	 *
	 * @return	array
	 */
	public static function getRedirectLanguages()
	{
		return self::$aLanguages['possible_redirect'];
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
		if(!in_array($language, self::$aLanguages['active'])) throw new FrontendException('Invalid language ('. $language .').');

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
?>