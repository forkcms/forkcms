<?php


/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			date
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonDatabaseException class */
require_once 'spoon/date/exception.php';

/** SpoonFile class */
require_once 'spoon/filesystem/file.php';


/**
 * This class provides some extra functionality when working with dates & time
 *
 * @package			date
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonDate
{
	/**
	 * List of available languages
	 *
	 * @var	array
	 */
	private static $languages = array('de', 'en', 'es', 'fr', 'nl');


	/**
	 * An alias for php's date function that makes weekdays en months language dependant
	 *
	 * @return	string
	 * @param	string $format
	 * @param	int[optional] $timestamp
	 * @param	string[optional] $language
	 */
	public static function getDate($format, $timestamp = null, $language = 'en')
	{
		// redefine arguments
		$format = (string) $format;
		$timestamp = ($timestamp === null) ? time() : (int) $timestamp;
		$language = SpoonFilter::getValue($language, self::$languages, 'en');

		// create date
		$date = date($format, $timestamp);

		// only for non-english versions
		if($language != 'en')
		{
			// fetch language file
			require 'spoon/date/locale/'. $language .'.php';

			// weekdays (short & long)
			$date = str_replace(array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), $aDays, $date);
			$date = str_replace(array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'), $aDaysShort, $date);

			// months (short & long)
			$date = str_replace(array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'), $aMonths, $date);
			$date = str_replace(array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'), $aMonthsShort, $date);
		}

		return $date;
	}


	/**
	 * Retrieve an array with the names of the days of the week
	 *
	 * @return	array
	 * @param	string[optional] $firstDay
	 * @param	bool[optional] $abbreviation
	 * @param	string[optional] $language
	 */
	public static function getDaysOfTheWeek($firstDay = 'monday', $abbreviation = false, $language = 'en')
	{
		// redefine arguments
		$firstDay = SpoonFilter::getValue($firstDay, array('monday', 'sunday'), 'monday');
		$abbreviation = (bool) $abbreviation;
		$language = SpoonFilter::getValue($language, self::$languages, 'en');

		// fetch language file
		require 'spoon/date/locale/'. $language .'.php';

		// abbreviations
		$array = ($abbreviation) ? $aDaysShort : $aDays;

		// short days
		if($firstDay == 'sunday')
		{
			$array[-1] = $array[6];
			unset($array[6]);
			$array = SpoonFilter::arraySortKeys($array);
		}

		return $array;
	}


	/**
	 * Retrieves the list of supported languages
	 *
	 * @return	array
	 */
	public static function getLanguages()
	{
		return self::$languages;
	}


	/**
	 * Retrieve an array with the names of the months of the year
	 *
	 * @return	array
	 * @param	bool[optional] $abbreviation
	 * @param	string[optional] $language
	 */
	public static function getMonthsOfTheYear($abbreviation = false, $language = 'en')
	{
		// redefine arguments
		$abbreviation = (bool) $abbreviation;
		$language = SpoonFilter::getValue($language, self::$languages, 'en');

		// fetch language file
		require 'spoon/date/locale/'. $language .'.php';

		// abbreviations
		$array = ($abbreviation) ? $aMonthsShort : $aMonths;
		return $array;
	}
}

?>