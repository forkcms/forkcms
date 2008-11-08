<?php

/** Require Model */
require_once FRONTEND_CORE_PATH .'/engine/model.php';

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	core
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
	private static $act = array(), $err = array(), $lbl = array(), $msg = array();


	/**
	 * The possible languages
	 *
	 * @var	array
	 */
	private static $aLanguages = array('active' => array('nl', 'fr', 'en'),
										'possible_redirect' => array('nl', 'fr', 'en'));


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
	 * @param	bool[optional]	$forRedirect
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
					if(in_array($language, $aRedirectLanguages)) return $language;
				}
				else
				{
					if(in_array($language, $aPossibleLanguages)) return $language;
				}
			}
		}

		// fallback
		return self::DEFAULT_LANGUAGE;
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

		// require file
		require FRONTEND_CACHE_PATH .'/locale/'. $language .'.php';

		// set language specific labels
		self::$act = $act;
		self::$err = $err;
		self::$lbl = $lbl;
		self::$msg = $msg;
	}
}
?>