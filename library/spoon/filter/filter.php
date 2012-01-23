<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	filter
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */


/**
 * This base class provides methods used to filter input of any kind.
 *
 * @package		spoon
 * @subpackage	filter
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
 * @since		0.1.1
 */
class SpoonFilter
{
	/**
	 * List of top level domains
	 *
	 * @var	array
	 */
	private static $tlds = array(
		'ac', 'ad', 'ae', 'aero', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'arpa', 'as', 'asia', 'at', 'au', 'aw', 'ax', 'az',
		'ba', 'bb', 'bd' ,'be', 'bf', 'bg', 'bh', 'bi', 'biz', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by' ,'bz',
		'ca', 'cat', 'cc' ,'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'com', 'coop', 'cr', 'cu', 'cv', 'cx', 'cy', 'cz',
		'de', 'dj', 'dk', 'dm', 'do', 'dz',
		'ec', 'edu', 'ee', 'eg', 'er', 'es', 'et', 'eu',
		'fi', 'fj', 'fk', 'fm', 'fo', 'fr',
		'ga', 'gb', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gov', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy',
		'hk', 'hm', 'hn', 'hr', 'ht', 'hu',
		'id', 'ie', 'il', 'im', 'in', 'info', 'int', 'io', 'iq', 'ir', 'is', 'it',
		'je', 'jm', 'jo', 'jobs', 'jp',
		'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz',
		'la', 'lb', 'lc', 'li', 'lk', 'local', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly',
		'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mil', 'mk', 'ml', 'mm', 'mn', 'mo', 'mobi', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'museum', 'mv', 'mw', 'mx', 'my', 'mz',
		'na', 'name', 'nc', 'ne', 'net', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz',
		'om', 'org',
		'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'pro', 'ps', 'pt', 'pw', 'py',
		'qa',
		're', 'ro', 'rs', 'ru', 'rw',
		'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr', 'st', 'su', 'sv', 'sy', 'sz',
		'tc', 'td', 'tel', 'tf', 'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tp', 'tr', 'travel','tt', 'tv', 'tw', 'tz',
		'ua', 'ug', 'uk', 'us', 'uy', 'uz',
		'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu',
		'wf', 'ws',
		'ye', 'yt', 'yu',
		'za', 'zm', 'zw'
	);


	/**
	 * This method will map functions to an array with one or more dimensions
	 *
	 * @return	array
	 * @param	mixed $callback						The callback function(s) you wish to map.
	 * @param	array $array						The array you wish to map callback functions on.
	 * @param	mixed[optional] $allowedKeys		The list of keys you want to map the callback to. All other keys will be ignored.
	 */
	public static function arrayMapRecursive($callback, array $array, $allowedKeys = null)
	{
		// has no elements
		if(empty($array)) return array();

		// check if there is a key restriction
		if(!empty($allowedKeys))
		{
			// convert to array
			if(!is_array($allowedKeys)) $allowedKeys = (array) $allowedKeys;
		}

		// declare our result array
		$results = array();

		// loop the array
		foreach($array as $key => $value)
		{
			// if $value is an array, we make this stuff recursive
			if(is_array($value)) $results[$key] = self::arrayMapRecursive($callback, $value, $allowedKeys);

			// $value is no array
			else
			{
				// the current active key is not in the list of allowed keys
				if(!empty($allowedKeys) && !in_array($key, $allowedKeys))
				{
					// just store the exact value in the new result set
					$results[$key] = $value;

					// proceed to next key in the foreach
					continue;
				}

				// more than 1 function given, so apply them all
				if(is_array($callback)) $results[$key] = call_user_func_array($callback, array($value));

				// just 1 function given
				else $results[$key] = $callback($value);
			}
		}

		// return the results
		return $results;
	}


	/**
	 * This method will sort an array by its keys and reindex.
	 *
	 * @return	array					The sorted array.
	 * @param	array $array			The array that will be sorted.
	 */
	public static function arraySortKeys(array $array)
	{
		// has no elements
		if(empty($array)) throw new SpoonFilterException('The array needs to contain at least one element.');

		// elements found (reindex)
		ksort($array);

		// new array
		return array_values($array);
	}


	/**
	 * Disable php's magic quotes (yuck!)
	 */
	public static function disableMagicQuotes()
	{
		// magic dust!
		if(get_magic_quotes_gpc())
		{
			// function doesn't exist yet
			if(!function_exists('fixMagicQuotes'))
			{
				/**
				 * Applies the fixMagicQuotes method to every element of the arrays below
				 *
				 * @return	array
				 * @param	mixed $value
				 */
				function fixMagicQuotes($value)
				{
					$value = is_array($value) ? array_map('fixMagicQuotes', $value) : stripslashes($value);
					return $value;
				}
			}

			// fix the thing with magic dust!
			$_POST = array_map('fixMagicQuotes', $_POST);
			$_GET = array_map('fixMagicQuotes', $_GET);
			$_COOKIE = array_map('fixMagicQuotes', $_COOKIE);
			$_REQUEST = array_map('fixMagicQuotes', $_REQUEST);
		}
	}


	/**
	 * Retrieve the desired $_GET value from an array of allowed values.
	 *
	 * @return	mixed							The value that was stored in $_GET or the default when the field wasn't found.
	 * @param	string $field					The field to retrieve.
	 * @param	array[optional] $values			The possible values. If the value isn't present the default will be returned.
	 * @param	mixed $defaultValue				The default-value.
	 * @param	string[optional] $returnType	The type that should be returned.
	 */
	public static function getGetValue($field, array $values = null, $defaultValue, $returnType = 'string')
	{
		// redefine field
		$field = (string) $field;

		// define var
		$var = (isset($_GET[$field])) ? $_GET[$field] : '';

		// parent method
		return self::getValue($var, $values, $defaultValue, $returnType);
	}


	/**
	 * Retrieve the desired $_POST value from an array of allowed values.
	 *
	 * @return	mixed							The value that was stored in $_POST or the default when the field wasn't found.
	 * @param	string $field					The field to retrieve.
	 * @param	array[optional] $values			The possible values. If the value isn't present the default will be returned.
	 * @param	mixed $defaultValue				The default-value.
	 * @param	string[optional] $returnType	The type that should be returned.
	 */
	public static function getPostValue($field, array $values = null, $defaultValue, $returnType = 'string')
	{
		// redefine field
		$field = (string) $field;

		// define var
		$var = (isset($_POST[$field])) ? $_POST[$field] : '';

		// parent method
		return self::getValue($var, $values, $defaultValue, $returnType);
	}


	/**
	 * Retrieve the desired value from an array of allowed values.
	 *
	 * @return	mixed							The validated value or the default when the value wasn't found.
	 * @param	string $variable				The variable that should be validated.
	 * @param	array[optional] $values			The possible values. If the value isn't present the default will be returned.
	 * @param	mixed $defaultValue				The default-value.
	 * @param	string[optional] $returnType	The type that should be returned.
	 */
	public static function getValue($variable, array $values = null, $defaultValue, $returnType = 'string')
	{
		// redefine arguments
		$variable = !is_array($variable) ? (string) $variable : $variable;
		$defaultValue = !is_array($defaultValue) ? (string) $defaultValue : $defaultValue;
		$returnType = (string) $returnType;

		// default value
		$value = $defaultValue;

		// variable is an array
		if(is_array($variable) && !empty($variable))
		{
			// no values
			if($values === null) $values = array();

			// fetch difference between the 2 arrays
			$differences = array_diff($variable, $values);

			// set value
			if(count($variable) != count($differences)) $value = array_intersect($variable, $values);

			// values was empty
			elseif(empty($values)) $value = $variable;
		}

		// provided values
		elseif($values !== null && in_array($variable, $values)) $value = $variable;

		// no values
		elseif($values === null && $variable != '') $value = $variable;

		/**
		 * We have to define the return type. Too bad we cant force it within
		 * a certain list of types, since that's what this method actually does.
		 */
		switch($returnType)
		{
			// array
			case 'array':
				$value = ($value == '') ? array() : (array) $value;
			break;

			// bool
			case 'bool':
				$value = (bool) $value;
			break;

			// double/float
			case 'double':
			case 'float':
				$value = (float) $value;
			break;

			// int
			case 'int':
				$value = (int) $value;
			break;

			// string
			case 'string':
				$value = (string) $value;
			break;
		}

		return $value;
	}


	/**
	 * Apply the htmlentities function with a specific charset.
	 *
	 * @return	string						The string with HTML-entities.
	 * @param	string $value				The value that should HTML-entityfied.
	 * @param	string[optional] $charset	The charset to use, default wil be based on SPOON_CHARSET.
	 * @param	int[optional] $quoteStyle	Which quotes should be decoded, default ENT_NOQUOTES.
	 */
	public static function htmlentities($value, $charset = null, $quoteStyle = ENT_NOQUOTES)
	{
		// init vars
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;
		$quoteStyle = self::getValue($quoteStyle, array(ENT_COMPAT, ENT_QUOTES, ENT_NOQUOTES), ENT_NOQUOTES);

		// apply htmlentities
		$return = htmlentities((string) $value, $quoteStyle, $charset);

		/**
		 * PHP doesn't replace a backslash to its html entity since this is something
		 * that's mostly used to escape characters when inserting in a database. Since
		 * we're using a decent database layer, we don't need this shit and we're replacing
		 * the double backslashes by its' html entity equivalent.
		 */
		return str_replace(array('\\'), array('&#92;'), $return);
	}


	/**
	 * Apply the html_entity_decode function with a specific charset.
	 *
	 * @return	string						The string with no HTML-entities.
	 * @param	string $value				The value that should be decoded.
	 * @param	string[optional] $charset	The charset to use, default will be based on SPOON_CHARSET.
	 * @param	int[optional] $quoteStyle	Which quotes should be decoded, default ENT_NOQUOTES.
	 */
	public static function htmlentitiesDecode($value, $charset = null, $quoteStyle = ENT_NOQUOTES)
	{
		// init vars
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;
		$quoteStyle = self::getValue($quoteStyle, array(ENT_COMPAT, ENT_QUOTES, ENT_NOQUOTES), ENT_NOQUOTES);

		// apply method
		return html_entity_decode((string) $value, $quoteStyle, $charset);
	}


	/**
	 * Apply the htmlspecialchars function with a specific charset.
	 *
	 * @return	string						The string with HTML-special chars applied to it.
	 * @param	string $value				The value that should be used.
	 * @param	string[optional] $charset	The charset to use, default wil be based on SPOON_CHARSET.
	 */
	public static function htmlspecialchars($value, $charset = null)
	{
		// define charset
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// apply method
		return htmlspecialchars((string) $value, ENT_QUOTES, $charset);
	}


	/**
	 * Apply the htmlspecialchars_decode function with ENT_QUOTES by default.
	 *
	 * @return	string			The string with HTML-specialchars decoded.
	 * @param	string $value	The value that should be used.
	 */
	public static function htmlspecialcharsDecode($value)
	{
		return htmlspecialchars_decode((string) $value, ENT_QUOTES);
	}


	/**
	 * Checks the value for a-z & A-Z.
	 *
	 * @return	bool			true if the string is alphabetical, false if not.
	 * @param	string $value	The string to validate.
	 */
	public static function isAlphabetical($value)
	{
		return ctype_alpha((string) $value);
	}


	/**
	 * Checks the value for letters & numbers without spaces.
	 *
	 * @return	bool			true if the string is alphanumeric, false if not.
	 * @param	string $value	The string to validate.
	 */
	public static function isAlphaNumeric($value)
	{
		return ctype_alnum((string) $value);
	}


	/**
	 * Checks if the value is between the minimum and maximum (min & max included).
	 *
	 * @return	bool				true if the integer is between the given values, false if not.
	 * @param	float $minimum		The minimum.
	 * @param	float $maximum		The maximum.
	 * @param	float $value		The value to validate.
	 */
	public static function isBetween($minimum, $maximum, $value)
	{
		return ((float) $value >= (float) $minimum && (float) $value <= (float) $maximum);
	}


	/**
	 * Checks the string value for a boolean (true/false | 0/1).
	 *
	 * @return	bool			true if the value is a boolean, false if not.
	 * @param	string $value	The value to validate.
	 */
	public static function isBool($value)
	{
		return (filter_var((string) $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null);
	}


	/**
	 * Checks the value for numbers 0-9.
	 *
	 * @return	bool			true if the value is digital, false if not.
	 * @param	string $value	The value to validate.
	 */
	public static function isDigital($value)
	{
		return ctype_digit((string) $value);
	}


	/**
	 * Checks the value for a valid e-mail address.
	 *
	 * @return	bool			true if the value is a valid e-mail address, false if not.
	 * @param	string $value	The value to validate.
	 */
	public static function isEmail($value)
	{
		return (filter_var((string) $value, FILTER_VALIDATE_EMAIL) === false) ? false : true;
	}


	/**
	 * Checks the value for an even number.
	 *
	 * @return	bool			true if the value is an even number, false if not.
	 * @param	int $value		The value to validate.
	 */
	public static function isEven($value)
	{
		return (((int) $value % 2) == 0);
	}


	/**
	 * Checks the value for a filename (including dots, but not slashes and forbidden characters).
	 *
	 * @return	bool			true if the value is a valid filename, false if not.
	 * @param	string $value	The value to validate.
	 */
	public static function isFilename($value)
	{
		return (bool) preg_match('/^[^\\/\*\?\:\,]+$/', (string) $value);
	}


	/**
	 * Checks if the value is a valid floating point number.
	 *
	 * @return	bool							true if the value is a valid float, false if not.
	 * @param	string $value					The value to validate.
	 * @param	bool[optional] $allowCommas		Do you want to use commas as a decimal separator?
	 */
	public static function isFloat($value, $allowCommas = false)
	{
		// replace commas if needed
		if($allowCommas) $value = str_replace(',', '.', (string) $value);

		// trim zero characters after the decimal separator
		if(mb_strpos($value, '.') !== false) rtrim($value, '0');

		// validate
		return ((string) (float) $value === (string) $value);
	}


	/**
	 * Checks if the value is greather than a given minimum.
	 *
	 * @return	bool				true if the value is greather then, false if not.
	 * @param	float $minimum		The minimum as a float.
	 * @param	float $value		The value to validate.
	 */
	public static function isGreaterThan($minimum, $value)
	{
		return ((float) $value > (float) $minimum);
	}


	/**
	 * Checks the value for numbers 0-9 and an optional minus sign (in the beginning only).
	 *
	 * @return	bool			true if the value is an integer, false if not.
	 * @param	string $value	The value to validate.
	 */
	public static function isInteger($value)
	{
		return ((string) (int) $value == (string) $value);
	}


	/**
	 * Checks if the request is coming from this site or not.
	 *
	 * @return	bool						True if the request is coming from this site, false if not.
	 * @param	array[optional] $domains	An array containing all known domains.
	 */
	public static function isInternalReferrer(array $domains = null)
	{
		// no referrer or host found
		if(!isset($_SERVER['HTTP_REFERER'])) return true;
		if(!isset($_SERVER['HTTP_HOST'])) return true;

		// get own & referrer host names
		$referrer = parse_url($_SERVER['HTTP_REFERER']);
		$referrer = $referrer['host'];
		$hostname = $_SERVER['HTTP_HOST'];

		// redefine hostname & domains
		if(strpos($referrer, 'www.') === 0) $referrer = substr($referrer, 4);
		if(strpos($hostname, 'www.') === 0) $hostname = substr($hostname, 4);
		$domains = ($domains === null) ? (array) $hostname : (array) $domains;

		// internal?
		return in_array($referrer, $domains);
	}


	/**
	 * Checks the value for a proper ip address.
	 *
	 * @return	bool			True if the value is a valid IPv4-address, false if not.
	 * @param	string $value	The value to validate.
	 */
	public static function isIp($value)
	{
		return (filter_var((string) $value, FILTER_VALIDATE_IP) !== false) ? true : false;
	}


	/**
	 * Checks if the value is not greater than or equal a given maximum.
	 *
	 * @return	bool			True on success, false if not.
	 * @param	int $maximum	The maximum.
	 * @param	int $value		The value to validate.
	 */
	public static function isMaximum($maximum, $value)
	{
		return ((int) $value <= (int) $maximum);
	}


	/**
	 * Checks if the value's length is not greater than or equal a given maximum of characters.
	 *
	 * @return	bool						True if the length isn't greather then the given maximum, false if not.
	 * @param	int $maximum				The maximum allowed characters.
	 * @param	string $value				The value to validate.
	 * @param	string[optional] $charset	The charset to use, default is based on SPOON_CHARSET.
	 */
	public static function isMaximumCharacters($maximum, $value, $charset = null)
	{
		// define charset
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// execute & return
		return (mb_strlen((string) $value, $charset) <= (int) $maximum);
	}


	/**
	 * Checks if the value is greater than or equal to a given minimum.
	 *
	 * @return	bool			True if the given value is greater then the given minimum.
	 * @param	int $minimum	The minimum.
	 * @param	int $value		The value to validate.
	 */
	public static function isMinimum($minimum, $value)
	{
		return ((int) $value >= (int) $minimum);
	}


	/**
	 * Checks if the value's length is greater than or equal to a given minimum of characters.
	 *
	 * @return	bool						True if the length is greater then the given minimum.
	 * @param	int $minimum				The minimum allowed charachters.
	 * @param	string $value				The value to validate.
	 * @param	string[optional] $charset	The charset to use, default is based on SPOON_CHARSET.
	 */
	public static function isMinimumCharacters($minimum, $value, $charset = null)
	{
		// define charset
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// execute & return
		return (mb_strlen((string) $value, $charset) >= (int) $minimum);
	}


	/**
	 * Alias for isDigital.
	 *
	 * @return	bool			True if the value is numeric, false if not.
	 * @param	string $value	The value to validate.
	 */
	public static function isNumeric($value)
	{
		return self::isDigital((string) $value);
	}


	/**
	 * Checks the value for an odd number.
	 *
	 * @return	bool			True if the value is odd, false if not.
	 * @param	int $value		The value to validate.
	 */
	public static function isOdd($value)
	{
		return !self::isEven((int) $value);
	}


	/**
	 * Checks this field for numbers 0-9 and an optional - (minus) sign (in the beginning only).
	 *
	 * @return	bool
	 * @param 	string $value					The value to validate.
	 * @param	string[optional] $error			The error message to set.
	 * @param	int[optional] $precision		The allowed number of digits after the decimal separator. Defaults to 2.
	 * @param	bool[optional] $allowNegative	Do you want to allow negative prices? Defaults to false.
	 * @param	bool[optional] $allowCommas		Do you want to use commas as a decimal separator? Defaults to true.
	 */
	public static function isPrice($value, $precision = 2, $allowNegative = false, $allowCommas = true)
	{
		// replace commas if needed
		if($allowCommas) $value = str_replace(',', '.', (string) $value);

		// trim zero characters after the decimal separator
		if(mb_strpos($value, '.') !== false) rtrim($value, '0');

		// no negatives allowed
		if(!$allowNegative) return ((float) $value >= 0);

		// no commas allowed
		return ((string) (float) $value === (string) $value);
	}


	/**
	 * Checks if the value is smaller than a given maximum.
	 *
	 * @return	bool			True if the value is smaller, false if not.
	 * @param	int $maximum	The maximum.
	 * @param 	int $value		The value to validate.
	 */
	public static function isSmallerThan($maximum, $value)
	{
		return ((int) $value < (int) $maximum);
	}


	/**
	 * Checks the value for a string wihout control characters (ASCII 0 - 31), spaces are allowed.
	 *
	 * @return	bool			True if the value is a string, false if not.
	 * @param	string $value	The value to validate.
	 */
	public static function isString($value)
	{
		return (bool) preg_match('/^[^\x-\x1F]+$/', (string) $value);
	}


	/**
	 * Checks the value for a valid URL.
	 *
	 * @return	bool			True if the value is a valid URL, false if not.
	 * @param	string $value	The value to validate.
	 */
	public static function isURL($value)
	{
		return (bool) preg_match('_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS', (string) $value);
	}


	/**
	 * Validates a value against a regular expression.
	 *
	 * @return	bool				True if the given string is valid against the regular expression, false if not.
	 * @param	string $regexp		The regular expression to use.
	 * @param	string $value		The value to validate.
	 */
	public static function isValidAgainstRegexp($regexp, $value)
	{
		// redefine vars
		$regexp = (string) $regexp;

		// invalid regexp
		if(!self::isValidRegexp($regexp)) throw new SpoonFilterException('The provided regex pattern "' . $regexp . '" is not valid');

		// validate
		return (bool) (@preg_match($regexp, (string) $value));
	}


	/**
	 * Checks if the given regex statement is valid.
	 *
	 * @return	bool				True if the given string is a valid regular expression, false if not.
	 * @param	string $regexp		The value to validate.
	 */
	public static function isValidRegexp($regexp)
	{
		// dummy string
		$dummy = 'spoon is growing every day';

		// validate
		return (@preg_match((string) $regexp, $dummy) !== false);
	}


	/**
	 * Function that will be used by replaceURLsWithAnchors.
	 *
	 * @return	string			A string with HTML-markup for a link.
	 * @param	array $match	The link that should be enclosed with HTML-markup for a link.
	 */
	private static function replaceURLsCallback($match)
	{
		// init var
		$link = $match[1];
		$label = $match[1];

		// no protocol provided
		if($match[3] == '') $link = 'http://' . $link;

		// return the replace-value
		return '<a href="' . $link . '">' . $label . '</a>';
	}


	/**
	 * Replace URLs with an anchor.
	 *
	 * @return	string						A string with each link replaced with valid HTML-markup for a link.
	 * @param	string $value				The string without links.
	 * @param	bool[optional] $noFollow	Should a rel="nofollow" be added into the link-attribute.
	 */
	public static function replaceURLsWithAnchors($value, $noFollow = true)
	{
		// redefine
		$value = (string) $value;

		// build regexp
		$pattern = '/(((http|ftp|https):\/{2})?(([0-9a-z_-]+\.)+(' . implode('|', self::$tlds) . ')(:[0-9]+)?((\/([~0-9a-zA-Z\#\+\%@\.\/_-]+))?(\?[0-9a-zA-Z\+\%@\/&\[\];=_-]+)?)?))\b/imu';

		// get matches
		$value = preg_replace_callback($pattern, array('SpoonFilter', 'replaceURLsCallback'), $value);

		// add noFollow-attribute
		if($noFollow) $value = str_replace('<a href=', '<a rel="nofollow" href=', $value);

		// return
		return $value;
	}


	/**
	 * Strips HTML from a string
	 *
	 * @return	string										A string with all HTML elements stripped.
	 * @param	string $string								The string with HTML in it.
	 * @param	mixed[optional] $exceptions					The HTML elements you want to exclude from stripping. Notation example: '<table><tr><td>'.
	 * @param	bool[optional] $replaceAnchorsWithURL			If this is true it will replace all anchor elements with their href value.
	 * @param	bool[optional] $replaceImagesWithAltText		If this is true it will replace all img elements with their alt text.
	 * @param	bool[optional] $preserveParagraphLinebreaks	If this is true it will generate an additional EOL for paragraphs.
	 * @param	bool[optional] $stripTabs						If this is true it will strip all tabs from the string.
	 */
	public static function stripHTML($string, $exceptions = null, $replaceAnchorsWithURL = false, $replaceImagesWithAltText = false, $preserveParagraphLinebreaks = false, $stripTabs = true)
	{
		// redefine
		$string = (string) $string;

		// check input
		if(is_array($exceptions)) $exceptions = implode('', $exceptions);

		// remove ugly and mac endlines
		$string = preg_replace('/\r\n/', PHP_EOL, $string);
		$string = preg_replace('/\r/', PHP_EOL, $string);

		// remove tabs
		if($stripTabs) $string = preg_replace("/\t/", '', $string);

		// remove the style- and head-tags and all their contents
		$string = preg_replace('|\<style.*\>(.*\n*)\</style\>|is', '', $string);
		$string = preg_replace('|\<head.*\>(.*\n*)\</head\>|is', '', $string);

		// replace images with their alternative content
		// eg. <img src="path/to/the/image.jpg" alt="My image" /> => My image
		if($replaceImagesWithAltText) $string = preg_replace('|\<img[^>]*alt="(.*)".*/\>|isU', '$1', $string);

		// replace links with the inner html of the link with the url between ()
		// eg.: <a href="http://site.domain.com">My site</a> => My site (http://site.domain.com)
		if($replaceAnchorsWithURL) $string = preg_replace('|<a.*href="(.*)".*>(.*)</a>|isU', '$2 ($1)', $string);

		// check if we need to preserve paragraphs and/or breaks
		$exceptions = ($preserveParagraphLinebreaks) ? $exceptions . '<p>' : $exceptions;

		// strip HTML tags and preserve paragraphs
		$string = strip_tags($string, $exceptions);

		// remove multiple with a single one
		$string = preg_replace('/\n\s/', PHP_EOL, $string);
		$string = preg_replace('/\n{2,}/', PHP_EOL, $string);

		// for each linebreak, table row or- paragraph end we want an additional linebreak at the end
		if($preserveParagraphLinebreaks)
		{
			$string = preg_replace('|<p>|', '', $string);
			$string = preg_replace('|</p>|', PHP_EOL, $string);
		}

		// trim whitespace and strip HTML tags
		$string = trim($string);

		// replace html entities that aren't replaced by SpoonFilter::htmlentitiesDecode (should be solved when using a newer Spoon Library)
		$string = str_replace('&euro;', 'EUR', $string);
		$string = str_replace('&#8364;', 'EUR', $string);
		$string = str_replace('&#8211;', '-', $string);
		$string = str_replace('&#8230;', '...', $string);

		// decode html entities
		$string = SpoonFilter::htmlentitiesDecode($string);

		// return the plain text
		return $string;
	}


	/**
	 * Convert a string to camelcasing.
	 *
	 * @return	string							The camelcased string.
	 * @param	string $value					The string that should be camelcased.
	 * @param	mixed[optional] $separator		The word-separator.
	 * @param	bool[optional] $lcfirst			Should the first charachter be lowercase?
	 * @param	string[optional] $charset		The charset to use, default is based on SPOON_CHARSET.
	 */
	public static function toCamelCase($value, $separator = '_', $lcfirst = false, $charset = null)
	{
		$string = '';
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// fetch words
		$words = explode((string) $separator, (string) $value);

		// create new string
		foreach($words as $i => $word)
		{
			// skip empty words
			if($word == '') continue;

			// first word lowercase
			if($i == 0 && $lcfirst) $word[0] = mb_strtolower($word[0], $charset);

			// convert first letter to uppercase
			else $word[0] = mb_strtoupper($word[0], $charset);

			$string .= $word;
		}

		return $string;
	}


	/**
	 * Multibyte-safe ucfirst
	 *
	 * @return	string							The ucfirst'ed string.
	 * @param	string $string					The string to ucfirst
	 * @param	string[optional] $charset		The charset to use, default is based on SPOON_CHARSET.
	 */
	public static function ucfirst($string, $charset = null)
	{
		// init vars
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;
		$string = (string) $string;

		// uppercase first character
		$first = mb_strtoupper(mb_substr($string, 0, 1, $charset), $charset);

		// leave rest untouched
		$rest = mb_substr($string, 1, mb_strlen($string, $charset) - 1, $charset);

		return $first . $rest;
	}


	/**
	 * Prepares a string so that it can be used in urls.
	 *
	 * @return	string						The urlised string.
	 * @param	string $value				The value that should be urlised.
	 * @param	string[optional] $charset	The charset to use, default is based on SPOON_CHARSET.
	 */
	public static function urlise($value, $charset = null)
	{
		// define charset
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// reserved characters (RFC 3986)
		$reservedCharacters = array(
			'/', '?', ':', '@', '#', '[', ']',
			'!', '$', '&', '\'', '(', ')', '*',
			'+', ',', ';', '='
		);

		// remove reserved characters
		$value = str_replace($reservedCharacters, ' ', $value);

		// replace double quote, since this one might cause problems in html (e.g. <a href="double"quote">)
		$value = str_replace('"', ' ', $value);

		// replace spaces by dashes
		$value = str_replace(' ', '-', $value);

		// only urlencode if not yet urlencoded
		if(urldecode($value) == $value)
		{
			// to lowercase
			$value = mb_strtolower($value, $charset);

			// urlencode
			$value = urlencode($value);
		}

		// convert "--" to "-"
		$value = preg_replace('/\-+/', '-', $value);

		// trim - signs
		return trim($value, '-');
	}
}


/**
 * This exception is used to handle filter related exceptions.
 *
 * @package		spoon
 * @subpackage	filter
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFilterException extends SpoonException {}
