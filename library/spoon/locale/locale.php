<?php

// @todo fix the languages. They dont need to be alphabetically and the list needs to be revised.

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		locale
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		1.1.0
 */


/** SpoonFilter class */
require_once 'spoon/filter/filter.php';


/**
 * This exception is used to handle locale related exceptions.
 *
 * @package		locale
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		1.1.0
 */
class SpoonLocaleException extends SpoonException {}


/**
 * This class is used to handle locale specific actions
 *
 * @package		locale
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		1.1.0
 */
class SpoonLocale
{
	/**
	 * Languages supported by spoon
	 *
	 * @var	array
	 */
	private static $languages = array('de', 'en', 'es', 'fr', 'nl');


	/**
	 * Retrieve a list of the available languages
	 *
	 * @return	array
	 */
	public static function getAvailableLanguages()
	{
		return self::$languages;
	}


	/**
	 * Retrieve the list of countries
	 *
	 * @return	array						An array with all known countries in the requested language.
	 * @param	string[optional] $language	The language to use (available languages can be found in SpoonLocale).
	 */
	public static function getCountries($language = 'en')
	{
		// language
		$language = SpoonFilter::getValue($language, self::$languages, 'en');

		// fetch file
		require 'data/'. $language .'.php';

		// fetch countries
		return $aLocale['countries'];
	}


	/**
	 * Retrieve the list of languages
	 *
	 * @return	array						An array containing all known languages in the requested language.
	 * @param	string[optional] $language	The language to use (available languages can be found in SpoonLocale).
	 */
	public static function getLanguages($language = 'en')
	{
		// language
		$language = SpoonFilter::getValue($language, self::$languages, 'en');

		// fetch file
		require 'data/'. $language .'.php';

		// fetch languages
		return $aLocale['languages'];
	}


	/**
	 * Retrieve the months of the year
	 *
	 * @return	array							An array with all the months in the requested language.
	 * @param	string[optional] $language		The language to use (available languages can be found in SpoonLocale).
	 * @param	bool[optional] $abbreviated		Should the months be abbreviated?
	 */
	public static function getMonths($language = 'en', $abbreviated = false)
	{
		// language
		$language = SpoonFilter::getValue($language, self::$languages, 'en');

		// fetch file
		require 'data/'. $language .'.php';

		// abbreviated?
		return ($abbreviated) ? $aLocale['date']['months']['abbreviated'] : $aLocale['date']['months']['full'];
	}


	/**
	 * Retrieve the days of the week
	 *
	 * @return	array							An array with all the days in the requested language.
	 * @param	string[optional] $language		The language to use (available languages can be found in SpoonLocale).
	 * @param	bool[optional] $abbreviated		Should the days be abbreviated?
	 * @param	string[optional] $firstDay		First day of the week (available options: monday, sunday).
	 */
	public static function getWeekDays($language = 'en', $abbreviated = false, $firstDay = 'monday')
	{
		// redefine arguments
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$firstDay = SpoonFilter::getValue($firstDay, array('monday', 'sunday'), 'monday');

		// fetch file
		require 'data/'. $language .'.php';

		// data array
		$aDays = ($abbreviated) ? $aLocale['date']['days']['abbreviated'] : $aLocale['date']['days']['full'];

		// in some regions monday is not the first day of the week.
		if($firstDay == 'monday')
		{
			$sunday = $aDays['sun'];
			unset($aDays['sun']);
			$aDays['sun'] = $sunday;
		}

		return $aDays;
	}
}

?>