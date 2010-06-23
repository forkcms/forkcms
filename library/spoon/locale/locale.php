<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	locale
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @author 		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Dave Lens <dave@spoon-library.com>
 * @since		1.1.0
 */


/**
 * This class is used to handle locale specific actions
 *
 * @package		spoon
 * @subpackage	locale
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
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
	 * Retrieve the list of countries.
	 *
	 * @return	array						An array with all known countries in the requested language.
	 * @param	string[optional] $language	The language to use (available languages can be found in SpoonLocale).
	 */
	public static function getCountries($language = 'en')
	{
		// init vars
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();

		// fetch file
		require 'data/'. $language .'.php';

		// fetch countries
		return $locale['countries'];
	}


	/**
	 * Retrieve the months of the year in a specified language.
	 *
	 * @return	array							An array with all the months in the requested language.
	 * @param	string[optional] $language		The language to use (available languages can be found in SpoonLocale).
	 * @param	bool[optional] $abbreviated		Should the months be abbreviated?
	 */
	public static function getMonths($language = 'en', $abbreviated = false)
	{
		// init vars
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();

		// fetch file
		require 'data/'. $language .'.php';

		// abbreviated?
		return ($abbreviated) ? $locale['date']['months']['abbreviated'] : $locale['date']['months']['full'];
	}


	/**
	 * Retrieve the days of the week in a specified language.
	 *
	 * @return	array							An array with all the days in the requested language.
	 * @param	string[optional] $language		The language to use (available languages can be found in SpoonLocale).
	 * @param	bool[optional] $abbreviated		Should the days be abbreviated?
	 * @param	string[optional] $firstDay		First day of the week (available options: monday, sunday).
	 */
	public static function getWeekDays($language = 'en', $abbreviated = false, $firstDay = 'monday')
	{
		// init vars
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$firstDay = SpoonFilter::getValue($firstDay, array('monday', 'sunday'), 'monday');
		$locale = array();

		// fetch file
		require 'data/'. $language .'.php';

		// data array
		$days = ($abbreviated) ? $locale['date']['days']['abbreviated'] : $locale['date']['days']['full'];

		// in some regions monday is not the first day of the week.
		if($firstDay == 'monday')
		{
			$sunday = $days['sun'];
			unset($days['sun']);
			$days['sun'] = $sunday;
		}

		return $days;
	}
}


/**
 * This exception is used to handle locale related exceptions.
 *
 * @package		spoon
 * @subpackage	locale
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.1.0
 */
class SpoonLocaleException extends SpoonException {}

?>