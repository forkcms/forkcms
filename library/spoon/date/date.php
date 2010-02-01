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
	 * An alias for php's date function that makes weekdays and months language dependant.
	 *
	 * @return	string						A formatted date.
	 * @param	string $format				The wanted format.
	 * @param	int[optional] $timestamp	A UNIX-timestamp representing the date that should be formatted.
	 * @param	string[optional] $language	The language to use (available languages can be found in SpoonLocale).
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


	/**
	 * Fetch the time ago as a language dependant sentence.
	 *
	 * @return	string							String containing a sentence like 'x minutes ago'
	 * @param	int $timestamp					Timestamp you want to make a sentence of.
	 * @param	string[optional] $language		Language to use, check SpoonLocale::getAvailableLanguages().
	 */
	public static function getTimeAgo($timestamp, $language = 'en')
	{
		// init vars
		$timestamp = (int) $timestamp;
		$language = SpoonFilter::getValue($language, SpoonLocale::getAvailableLanguages(), 'en', 'string');
		$locale = array();

		// fetch language
		require 'spoon/locale/data/'. $language .'.php';

		// get seconds between given timestamp and current timestamp
		$secondsBetween = time() - $timestamp;

		// calculate years ago
		$yearsAgo = floor($secondsBetween / (365.242199 * 24 * 60 * 60));
		if($yearsAgo > 1) return sprintf($locale['time']['YearsAgo'], $yearsAgo);
		if($yearsAgo == 1) return $locale['time']['YearAgo'];

		// calculate months ago
		$monthsAgo = floor($secondsBetween / ((365.242199/12) * 24 * 60 * 60));
		if($monthsAgo > 1) return sprintf($locale['time']['MonthsAgo'], $monthsAgo);
		if($monthsAgo == 1) return $locale['time']['MonthAgo'];

		// calculate weeks ago
		$weeksAgo = floor($secondsBetween / (7 * 24 * 60 * 60));
		if($weeksAgo > 1) return sprintf($locale['time']['WeeksAgo'], $weeksAgo);
		if($weeksAgo == 1) return $locale['time']['WeekAgo'];

		// calculate days ago
		$daysAgo = floor($secondsBetween / (24 * 60 * 60));
		if($daysAgo > 1) return sprintf($locale['time']['DaysAgo'], $daysAgo);
		if($daysAgo == 1) return $locale['time']['DayAgo'];

		// calculate hours ago
		$hoursAgo = floor($secondsBetween / (60 * 60));
		if($hoursAgo > 1) return sprintf($locale['time']['HoursAgo'], $hoursAgo);
		if($hoursAgo == 1) return $locale['time']['HourAgo'];

		// calculate minutes ago
		$minutesAgo = floor($secondsBetween / 60);
		if($minutesAgo > 1) return sprintf($locale['time']['MinutesAgo'], $minutesAgo);
		if($minutesAgo == 1) return $locale['time']['MinuteAgo'];

		// calculate seconds ago
		$secondsAgo = floor($secondsBetween);
		if($secondsAgo > 1) return sprintf($locale['time']['SecondsAgo'], $secondsAgo);
		if($secondsAgo <= 1) return $locale['time']['SecondAgo'];
	}
}

?>