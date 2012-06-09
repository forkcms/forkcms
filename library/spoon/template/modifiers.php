<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	template
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */


/**
 * This class implements modifier mapping for the template engine.
 *
 * @package		spoon
 * @subpackage	template
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */
class SpoonTemplateModifiers
{
	/**
	 * Default modifiers mapped to their functions
	 *
	 * @var	array
	 */
	private static $modifiers = array('addslashes' => 'addslashes',
										'createhtmllinks' => array('SpoonTemplateModifiers', 'createHTMLLinks'),
										'date' => array('SpoonTemplateModifiers', 'date'),
										'htmlentities' => array('SpoonFilter', 'htmlentities'),
										'lowercase' => array('SpoonTemplateModifiers', 'lowercase'),
										'ltrim' => 'ltrim',
										'nl2br' => 'nl2br',
										'repeat' => 'str_repeat',
										'rtrim' => 'rtrim',
										'shuffle' => 'str_shuffle',
										'sprintf' => 'sprintf',
										'stripslashes' => 'stripslashes',
										'substring' => 'substr',
										'trim' => 'trim',
										'ucfirst' => array('SpoonFilter', 'ucfirst'),
										'ucwords' => 'ucwords',
										'uppercase' => array('SpoonTemplateModifiers', 'uppercase'));


	/**
	 * Clears the entire modifiers list.
	 */
	public static function clearModifiers()
	{
		self::$modifiers = array();
	}


	/**
	 * Converts links to HTML links (only to be used with cleartext).
	 *
	 * @return	string			The text containing the parsed html links.
	 * @param	string $text	The cleartext that may contain urls that need to be transformed to html links.
	 */
	public static function createHTMLLinks($text)
	{
		return SpoonFilter::replaceURLsWithAnchors($text, false);
	}


	/**
	 * Formats a language specific date.
	 *
	 * @return	string						The formatted date according to the timestamp, format and provided language.
	 * @param	mixed $timestamp			The timestamp or date that you want to apply the format to.
	 * @param	string[optional] $format	The optional format that you want to apply on the provided timestamp.
	 * @param	string[optional] $language	The optional language that you want this format in (Check SpoonLocale for the possible languages).
	 */
	public static function date($timestamp, $format = 'Y-m-d H:i:s', $language = 'en')
	{
		if(is_string($timestamp) && !is_numeric($timestamp))
		{
			// use strptime if you want to restrict the input format
			$timestamp = strtotime($timestamp);
		}

		return SpoonDate::getDate($format, $timestamp, $language);
	}


	/**
	 * Retrieves the modifiers.
	 *
	 * @return	array	The list of modifiers and the function/method that they're mapped to.
	 */
	public static function getModifiers()
	{
		return self::$modifiers;
	}


	/**
	 * Makes this string lowercase.
	 *
	 * @return	string			The string, completely lowercased.
	 * @param	string $string	The string that you want to apply this method on.
	 */
	public static function lowercase($string)
	{
		return mb_convert_case($string, MB_CASE_LOWER, SPOON_CHARSET);
	}


	/**
	 * Maps a specific modifier to a function/method.
	 *
	 * @param	string $name		The name of the modifier that you want to map.
	 * @param	mixed $function		The function or method that you want to map to the provided name. To map a method provided this argument as an array containing class and method.
	 */
	public static function mapModifier($name, $function)
	{
		// validate modifier
		if(!SpoonFilter::isValidAgainstRegexp('/[a-zA-Z0-9\_\-]+/', (string) $name)) throw new SpoonTemplateException('Modifier names can only contain a-z, 0-9 and - and _');

		// class method
		if(is_array($function))
		{
			// not enough elements
			if(count($function) != 2) throw new SpoonTemplateException('The array should contain the class and static method.');

			// method doesn't exist
			if(!is_callable(array($function[0], $function[1]))) throw new SpoonTemplateException('The method "' . $function[1] . '" in the class ' . $function[0] . ' does not exist.');

			// all fine
			self::$modifiers[(string) $name] = $function;
		}

		// regular function
		else
		{
			// function doesn't exist
			if(!function_exists((string) $function)) throw new SpoonTemplateException('The function "' . (string) $function . '" does not exist.');

			// all fine
			self::$modifiers[(string) $name] = $function;
		}
	}


	/**
	 * Transform the string to uppercase.
	 *
	 * @return	string			The string, completly uppercased.
	 * @param	string $string	The string that you want to apply this method on.
	 */
	public static function uppercase($string)
	{
		return mb_convert_case($string, MB_CASE_UPPER, SPOON_CHARSET);
	}
}
