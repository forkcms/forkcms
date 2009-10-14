<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		date
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		0.1.1
 */


/** SpoonLocale class */
require_once 'spoon/locale/locale.php';


/**
 * This exception is used to handle date related exceptions.
 *
 * @package		date
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonDateException extends SpoonException {}


/**
 * This class provides some extra functionality when working with dates & time
 *
 * @package		date
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonDate
{
	/**
	 * An alias for php's date function that makes weekdays and months language dependant
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
		$language = SpoonFilter::getValue($language, SpoonLocale::getAvailableLanguages(), 'en');

		// create date
		$date = date($format, $timestamp);

		// only for non-english versions
		if($language != 'en')
		{
			// weekdays (short & long)
			$date = str_replace(array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), SpoonLocale::getWeekDays($language), $date);
			$date = str_replace(array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'), SpoonLocale::getWeekDays($language, true), $date);

			// months (short & long)
			$date = str_replace(array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'), SpoonLocale::getMonths($language), $date);
			$date = str_replace(array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'), SpoonLocale::getMonths($language, true), $date);
		}

		return $date;
	}
}

?>