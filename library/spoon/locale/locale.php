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
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
 * @author		Siesqo <info@siesqo.be>
 * @since		1.1.0
 */
class SpoonLocale
{
	/**
	 * Languages supported by spoon
	 *
	 * @var	array
	 */
	private static $languages = array('de', 'en', 'es', 'fr', 'nl', 'it');


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
	 * Fetch the name of a conjunction based on the code.
	 *
	 * @return	string
	 * @param	string $name					The name of the conjunction to fetch.
	 * @param	string[optional] $language		The language to use, possible values can be found by calling SpoonLocale::getAvailableLanguages().
	 */
	public static function getConjunction($name, $language = 'en')
	{
		// init vars
		$name = (string) $name;
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();

		// fetch file
		require 'data/' . $language . '.php';

		// doesn't exist
		if(!isset($locale['conjunctions'][$name])) throw new SpoonLocaleException('There is no conjunction named: ' . $name);

		// all seems fine
		return $locale['conjunctions'][$name];
	}


	/**
	 * Retrieve a list of conjunctions.
	 *
	 * @return	array						An array with all known conjunctions in the requested language.
	 * @param	string[optional] $language	The language to use (available languages can be found in SpoonLocale).
	 */
	public static function getConjunctions($language = 'en')
	{
		// init vars
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();

		// fetch file
		require 'data/' . $language . '.php';

		// fetch countries
		return $locale['conjunctions'];
	}


	/**
	 * Fetch the name of a continent based on the code.
	 *
	 * @return	array						An array with all known continents in the requested language.
	 * @param	string $code				The official continents-code.
	 * @param	string[optional] $language	The language to use (available languages can be found in SpoonLocale).
	 */
	public static function getContinent($code, $language = 'en')
	{
		// init vars
		$code = (string) $code;
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();

		// fetch file
		require 'data/' . $language . '.php';

		// doesn't exist
		if(!isset($locale['continents'][$code])) throw new SpoonLocaleException('There is no continent with the code: ' . $code);

		// fetch countries
		return $locale['continents'][$code];
	}


	/**
	 * Retrieve the list of continents.
	 *
	 * @return	array						An array with all known continents in the requested language.
	 * @param	string[optional] $language	The language to use (available languages can be found in SpoonLocale).
	 */
	public static function getContinents($language = 'en')
	{
		// init vars
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();

		// fetch file
		require 'data/' . $language . '.php';

		// fetch countries
		return $locale['continents'];
	}


	/**
	 * Retrieve continent for the country.
	 *
	 * @return	array							The continent-code.
	 * @param	string $code					The official country-code.
	 * @param	string[optional] $language		The language to use (available languages can be found in SpoonLocale).
	 * @param	bool[optional] $returnLocale	Should we return the locale for continent? Default = true
	 */
	public static function getContinentForCountry($code, $language = 'en', $returnLocale = true)
	{
		// init vars
		$continentCountries = array();
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();
		$returnLocale = (bool) $returnLocale;

		// fetch file
		require 'data/' . $language . '.php';
		require 'data/continent_countries.php';

		// define contintents
		$continents = $locale['continents'];

		// loop all continents
		foreach($continents as $key => $label)
		{
			// the country is in this continent
			if(in_array($code, $continentCountries[$key]))
			{
				// return label, or key
				return ($returnLocale) ? $label : $key;

				// stop here
				break;
			}
		}

		// no continent for this country found
		return false;
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
		require 'data/' . $language . '.php';

		// fetch countries
		return $locale['countries'];
	}


	/**
	 * Retrieve the list of countries for a continent.
	 *
	 * @return	array						An array with all known continents in the requested language.
	 * @param	string $continent			The continent you want all countries from.
	 * @param	string[optional] $language	The language to use (available languages can be found in SpoonLocale).
	 */
	public static function getCountriesForContinent($continent, $language = 'en')
	{
		// init vars
		$continentCountries = array();
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();
		$results = array();

		// fetch file
		require 'data/' . $language . '.php';
		require 'data/continent_countries.php';

		// doesn't exist
		if(!isset($continentCountries[$continent])) throw new SpoonLocaleException('There is no continent with the name: ' . $continent);

		// fetch countries
		$countries = $continentCountries[$continent];

		// loop all countries for the continent
		foreach($countries as $code)
		{
			// add country to results
			$results[$code] = $locale['countries'][$code];
		}

		// sort the results alphabetical
		asort($results);

		// return re
		return $results;
	}


	/**
	 * Fetch the name of a country based on the code.
	 *
	 * @return	string
	 * @param	string $code					The official country-code.
	 * @param	string[optional] $language		The language to use, possible values can be found by calling SpoonLocale::getAvailableLanguages().
	 */
	public static function getCountry($code, $language = 'en')
	{
		// init vars
		$code = (string) $code;
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();

		// fetch file
		require 'data/' . $language . '.php';

		// doesn't exist
		if(!isset($locale['countries'][$code])) throw new SpoonLocaleException('There is no country with the code: ' . $code);

		// all seems fine
		return $locale['countries'][$code];
	}


	/**
	 * Fetch the language name in the requested language.
	 *
	 * @return	string
	 * @param	string $code					Language code of which we want the name.
	 * @param	string[optional] $language		Requested language.
	 */
	public static function getLanguage($code, $language = 'en')
	{
		// init vars
		$code = (string) $code;
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();

		// fetch file
		require 'data/' . $language . '.php';

		// doesn't exist
		if(!isset($locale['languages'][$code])) throw new SpoonLocaleException('There is no language with the code: ' . $code);

		// all seems fine
		return $locale['languages'][$code];
	}


	/**
	 * Retrieve the list of languages.
	 *
	 * @return	array
	 * @param	string[optional] $language		Requested language.
	 */
	public static function getLanguages($language = 'en')
	{
		// init vars
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();

		// fetch file
		require 'data/' . $language . '.php';

		// fetch languages
		return $locale['languages'];
	}


	/**
	 * Fetch the language specific month.
	 *
	 * @return	string
	 * @param	string $month					The name/number of the month to retrieve.
	 * @param	string[optional] $language		The language to use, possible values can be found by calling SpoonLocale::getAvailableLanguages().
	 * @param	bool[optional] $abbreviated		Should the abbreviated value be used?
	 */
	public static function getMonth($month, $language = 'en', $abbreviated = false)
	{
		// init vars
		$months = array(1 => 'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();

		// which month?
		if(SpoonFilter::isInteger($month)) $month = SpoonFilter::getValue($month, range(1, 12), 1);

		// month by name
		else
		{
			$month = array_keys($months, SpoonFilter::getValue($month, array_values($months), 'january'));
			$month = $month[0];
		}

		// fetch file
		require 'data/' . $language . '.php';

		// abbreviated?
		return ($abbreviated) ? $locale['date']['months']['abbreviated'][$month] : $locale['date']['months']['full'][$month];
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
		require 'data/' . $language . '.php';

		// abbreviated?
		return ($abbreviated) ? $locale['date']['months']['abbreviated'] : $locale['date']['months']['full'];
	}


	/**
	 * Fetch a specific day of the week for a specific language.
	 *
	 * @return	string
	 * @param	mixed $day						The name/number of the day.
	 * @param	string[optional] $language		The language to use, possible values can be found by calling SpoonLocale::getAvailableLanguages().
	 * @param	bool[optional] $abbreviated		Should the abbreviated value be used?
	 */
	public static function getWeekDay($day, $language = 'en', $abbreviated = false)
	{
		// init vars
		$dayIndexes = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
		$dayNames = array('sunday' => 'sun', 'monday' => 'mon', 'tuesday' => 'tue', 'wednesday' => 'wed', 'thursday' => 'thu', 'friday' => 'fri', 'saturday' => 'sat');
		$language = SpoonFilter::getValue($language, self::$languages, 'en');
		$locale = array();

		// which day?
		if(SpoonFilter::isInteger($day)) $day = $dayIndexes[SpoonFilter::getValue(strtolower($day), range(0, 6), 0)];
		else $day = $dayNames[SpoonFilter::getValue(strtolower($day), array_keys($dayNames), 'sunday')];

		// fetch file
		require 'data/' . $language . '.php';

		// abbreviated?
		return ($abbreviated) ? $locale['date']['days']['abbreviated'][$day] : $locale['date']['days']['full'][$day];
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
		require 'data/' . $language . '.php';

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
