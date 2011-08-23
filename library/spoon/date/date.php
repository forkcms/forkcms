<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	date
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */


/**
 * This class provides some extra functionality when working with dates & time
 *
 * @package		spoon
 * @subpackage	date
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
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
	 * @param	bool[optional] $GMT			Should we consider this timestamp to be GMT/UTC?
	 */
	public static function getDate($format, $timestamp = null, $language = 'en', $GMT = false)
	{
		// redefine arguments
		$format = (string) $format;
		$timestamp = ($timestamp === null) ? time() : (int) $timestamp;
		$language = SpoonFilter::getValue($language, SpoonLocale::getAvailableLanguages(), 'en');

		// create date
		$date = (!$GMT) ? date($format, $timestamp) : gmdate($format, $timestamp);

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
	 * @param	string[optional] $format		The format to return if the time passed is greather then a week.
	 */
	public static function getTimeAgo($timestamp, $language = 'en', $format = null)
	{
		// init vars
		$timestamp = (int) $timestamp;
		$language = SpoonFilter::getValue($language, SpoonLocale::getAvailableLanguages(), 'en', 'string');
		$locale = array();

		// fetch language
		require 'spoon/locale/data/' . $language . '.php';

		// get seconds between given timestamp and current timestamp
		$secondsBetween = time() - $timestamp;

		// calculate years ago
		$yearsAgo = floor($secondsBetween / (365.242199 * 24 * 60 * 60));
		if($yearsAgo > 1 && $format === null) return sprintf($locale['time']['YearsAgo'], $yearsAgo);
		if($yearsAgo == 1 && $format === null) return $locale['time']['YearAgo'];
		if($yearsAgo >= 1 && $format !== null) return self::getDate($format, $timestamp, $language);

		// calculate months ago
		$monthsAgo = floor($secondsBetween / ((365.242199/12) * 24 * 60 * 60));
		if($monthsAgo > 1 && $format === null) return sprintf($locale['time']['MonthsAgo'], $monthsAgo);
		if($monthsAgo == 1 && $format === null) return $locale['time']['MonthAgo'];
		if($monthsAgo >= 1 && $format !== null) return self::getDate($format, $timestamp, $language);

		// calculate weeks ago
		$weeksAgo = floor($secondsBetween / (7 * 24 * 60 * 60));
		if($weeksAgo > 1 && $format === null) return sprintf($locale['time']['WeeksAgo'], $weeksAgo);
		if($weeksAgo == 1 && $format === null) return $locale['time']['WeekAgo'];
		if($weeksAgo >= 1 && $format !== null) return self::getDate($format, $timestamp, $language);

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


/**
 * This exception is used to handle date related exceptions.
 *
 * @package		spoon
 * @subpackage	date
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonDateException extends SpoonException {}
