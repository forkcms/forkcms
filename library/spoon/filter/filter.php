<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		filter
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		0.1.1
 */


/**
 * This exception is used to handle filter related exceptions.
 *
 * @package		filter
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonFilterException extends SpoonException {}


/**
 * This base class provides methods used to filter input of any kind.
 *
 * @package		filter
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author		Tijs Verkoyen <tijs@spoon-library.be>
 * @since		0.1.1
 */
class SpoonFilter
{
	/**
	 * List of top level domains
	 *
	 * @var	array
	 */
	private static $tlds = array(	'aero', 'asia', 'biz', 'cat', 'com', 'coop', 'edu', 'gov', 'info', 'int', 'jobs', 'mil', 'mobi',
									'museum', 'name', 'net', 'org', 'pro', 'tel', 'travel', 'ac', 'ad', 'ae', 'af', 'ag', 'ai', 'al',
									'am', 'an', 'ao', 'aq', 'ar', 'as', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb', 'bd' ,'be', 'bf', 'bg',
									'bh', 'bi', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by' ,'bz', 'ca', 'cc' ,'cd', 'cf',
									'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'cr', 'cu', 'cv', 'cx', 'cy', 'cz', 'cz', 'de', 'dj', 'dk',
									'dm', 'do', 'dz', 'ec', 'ee', 'eg', 'er', 'es', 'et', 'eu', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gb',
									'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk',
									'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo',
									'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr',
									'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mk', 'ml', 'mn', 'mn', 'mo', 'mp', 'mr',
									'ms', 'mt', 'mu', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'nc', 'ne', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr',
									'nu', 'nz', 'nom', 'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'ps', 'pt', 'pw', 'py', 'qa',
									're', 'ra', 'rs', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sj', 'sk', 'sl', 'sm',
									'sn', 'so', 'sr', 'st', 'su', 'sv', 'sy', 'sz', 'tc', 'td', 'tf', 'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn',
									'to', 'tp', 'tr', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi',
									'vn', 'vu', 'wf', 'ws', 'ye', 'yt', 'yu', 'za', 'zm', 'zw', 'arpa');



	/**
	 * This method will sort an array by its keys and reindex
	 *
	 * @return	array
	 * @param	array $array
	 */
	public static function arraySortKeys(array $array)
	{
		// has no elements
		if(count($array) == 0) throw new SpoonFilterException('The array needs to contain at least one element.');

		// elements found (reindex)
		ksort($array);

		// init var
		$i = 0;

		// elements found
		foreach($array as $value)
		{
			// key & value
			$newArray[$i] = $value;

			// update counter
			$i++;
		}

		return $newArray;
	}


	/**
	 * Disable php's magic quotes (yuck!)
	 *
	 * @return	void
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
	 * Retrieve the desired $_GET value from an array of allowed values
	 *
	 * @return	mixed
	 * @param	string $field
	 * @param	array[optional] $values
	 * @param	mixed $defaultValue
	 * @param	string[optional] $returnType
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
	 * Retrieve the desired $_POST value from an array of allowed values
	 *
	 * @return	mixed
	 * @param	string $field
	 * @param	array[optional] $values
	 * @param	mixed $defaultValue
	 * @param	string[optional] $returnType
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
	 * Retrieve the desired value from an array of allowed values
	 *
	 * @return	mixed
	 * @param	string $variable
	 * @param	array[optional] $values
	 * @param	mixed $defaultValue
	 * @param	string[optional] $returnType
	 */
	public static function getValue($variable, array $values = null, $defaultValue, $returnType = 'string')
	{
		// redefine arguments
		$variable = (string) $variable;
		$defaultValue = (string) $defaultValue;
		$returnType = (string) $returnType;

		// default value
		$value = $defaultValue;

		// provided values
		if($values !== null && in_array($variable, $values)) $value = $variable;

		// no values
		elseif($values === null && $variable != '') $value = $variable;

		/**
		 * We have to define the return type. Too bad we cant force it within
		 * a certain list of types, since that's what this method does xD
		 */
		switch($returnType)
		{
			// bool
			case 'bool':
				$value = (bool) $value;
			break;

			// double
			case 'double':
				$value = (double) $value;
			break;

			// float
			case 'float':
				$value = (float) $value;
			break;

			// int
			case 'int':
				$value = (int) $value;
			break;

			// string
			default:
				$value = (string) $value;
		}

		return $value;
	}


	/**
	 * Apply the htmlentities function with a specific charset
	 *
	 * @return	string
	 * @param	string $value
	 * @param	string[optional] $charset
	 */
	public static function htmlentities($value, $charset = null)
	{
		// define charset
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// apply method
		$return = htmlentities($value, ENT_QUOTES, $charset);

		/**
		 * PHP doesn't replace a backslash to its html entity since this is something
		 * that's mostly used to escape characters when inserting in a database. Since
		 * we're using a decent database layer, we don't need this shit and we're replacing
		 * the double backslashes by it's html entity equivalent.
		 */
		return str_replace(array('\\'), array('&#92;'), $return);
	}


	/**
	 * Apply the htmlspecialchars function with a specific charset
	 *
	 * @return	string
	 * @param	string $value
	 * @param	string[optional] $charset
	 */
	public static function htmlspecialchars($value, $charset = null)
	{
		// define charset
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// apply method
		return htmlspecialchars($value, ENT_QUOTES, $charset);
	}


	/**
	 * Apply the html_entity_decode function with a specific charset
	 *
	 * @return	string
	 * @param	string $value
	 * @param	string[optional] $charset
	 */
	public static function htmlentitiesDecode($value, $charset = null)
	{
		// define charset
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// apply method
		return html_entity_decode($value, ENT_NOQUOTES, $charset);
	}


	/**
	 * Checks the value for a-z & A-Z
	 *
	 * @return	bool
	 * @param	string $value
	 */
	public static function isAlphabetical($value)
	{
		return (bool) preg_match("/^[a-z]+$/i", (string) $value);
	}


	/**
	 * Checks the value for letters & numbers without spaces
	 *
	 * @return	bool
	 * @param	string $value
	 */
	public static function isAlphaNumeric($value)
	{
		return (bool) preg_match("/^[a-z0-9]+$/i", (string) $value);
	}


	/**
	 * Checks if the integer value is between the minimum and maximum (min & max included)
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	int $maximum
	 * @param	string $value
	 */
	public static function isBetween($minimum, $maximum, $value)
	{
		return ((int) $value >= (int) $minimum && (int) $value <= (int) $maximum);
	}


	/**
	 * Checks the string value for a boolean (true/false | 0/1)
	 *
	 * @return	bool
	 * @param	string $value
	 */
	public static function isBool($value)
	{
		return (bool) preg_match("/^true$|^1|^false|^0$/i", (string) $value);
	}


	/**
	 * Checks the value for numbers 0-9
	 *
	 * @return	bool
	 * @param	string $value
	 */
	public static function isDigital($value)
	{
		return (bool) preg_match("/^[0-9]+$/", (string) $value);
	}


	/**
	 * Checks the value for a valid e-mail address
	 *
	 * @return	bool
	 * @param	string $value
	 */
	public static function isEmail($value)
	{
		return (bool) preg_match("/^[a-z0-9!#\$%&'*+-\/=?^_`{|}\.~]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i", (string) $value);
	}


	/**
	 * Checks the value for an even number
	 *
	 * @return	bool
	 * @param	int $value
	 */
	public static function isEven($value)
	{
		// even number
		if(((int) $value % 2) == 0) return true;

		// odd number
		return false;
	}


	/**
	 * Checks the value for a filename (including dots, but not slashes and forbidden characters)
	 *
	 * @return	bool
	 * @param	string $value
	 */
	public static function isFilename($value)
	{
		return (bool) preg_match("{^[^\\/\*\?\:\,]+$}", (string) $value);
	}


	/**
	 * Checks the value for numbers 0-9 with a dot or komma and an optional minus sign (in the beginning only)
	 *
	 * @return	bool
	 * @param	string $value
	 */
	public static function isFloat($value)
	{
		return (bool) preg_match("/^-?([0-9]*[\.|,]?[0-9]+)$/", (string) $value);
	}


	/**
	 * Checks if the value is greather than a given minimum
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	int $value
	 */
	public static function isGreaterThan($minimum, $value)
	{
		return (bool) ((int) $value > (int) $minimum);
	}


	/**
	 * Checks the value for numbers 0-9 and an optional minus sign (in the beginning only)
	 *
	 * @return	bool
	 * @param	string $value
	 */
	public static function isInteger($value)
	{
		return (bool) preg_match("/^-?[0-9]+$/", (string) $value);
	}


	/**
	 * Checks if the user is coming from this site or not
	 *
	 * @return	bool
	 * @param	array[optional] $domains
	 */
	public static function isInternalReferrer(array $domains = null)
	{
		// no referrer or host found
		if(!isset($_SERVER['HTTP_REFERER'])) return true;
		if(!isset($_SERVER['HTTP_HOST'])) return true;

		// redefine hostname & domains
		$hostname = str_replace('www.', '', $_SERVER['HTTP_HOST']);
		$domains = ($domains === null) ? (array) $hostname : (array) $domains;

		// redefine referer
		$referrer = str_replace(array('http://', 'https://', 'www.'), '', $_SERVER['HTTP_REFERER']);
		$slashPosition = strpos($referrer, '/');
		if($slashPosition !== false) $referrer = mb_substr($referrer, 0, $slashPosition, SPOON_CHARSET);

		// internal?
		return in_array($referrer, $domains);
	}


	/**
	 * Checks the value for a proper ip address
	 *
	 * @return	bool
	 * @param	string $value
	 */
	public static function isIp($value)
	{
		return (bool) preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}.\d{1,3}:?\d*$/', (string) $value);
	}


	/**
	 * Checks if the value is not greater than or equal a given maximum
	 *
	 * @return	bool
	 * @param	int $maximum
	 * @param	int $value
	 */
	public static function isMaximum($maximum, $value)
	{
		return (bool) ((int) $value <= (int) $maximum);
	}


	/**
	 * Checks if the value's length is not greater than or equal a given maximum of characters
	 *
	 * @return	bool
	 * @param	int $maximum
	 * @param	string $value
	 * @param	string[optional] $charset
	 */
	public static function isMaximumCharacters($maximum, $value, $charset = null)
	{
		// define charset
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// execute & return
		return (bool) (mb_strlen((string) $value, $charset) <= (int) $maximum);
	}


	/**
	 * Checks if the value is greater than or equal to a given minimum
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	int $value
	 */
	public static function isMinimum($minimum, $value)
	{
		return (bool) ((int) $value >= (int) $minimum);
	}


	/**
	 * Checks if the value's length is greater than or equal to a given minimum of characters
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	string $value
	 * @param	string[optional] $charset
	 */
	public static function isMinimumCharacters($minimum, $value, $charset = null)
	{
		// define charset
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// execute & return
		return (bool) (mb_strlen((string) $value, $charset) >= (int) $minimum);
	}


	/**
	 * Alias for isDigital.
	 *
	 * @return	bool
	 * @param	string $value
	 */
	public static function isNumeric($value)
	{
		return self::isDigital((string) $value);
	}


	/**
	 * Checks the value for an odd number
	 *
	 * @return	bool
	 * @param	int $value
	 */
	public static function isOdd($value)
	{
		return !self::isEven((int) $value);
	}


	/**
	 * Checks if the value is smaller than a given maximum
	 *
	 * @return	bool
	 * @param	int $maximum
	 * @param 	int $value
	 */
	public static function isSmallerThan($maximum, $value)
	{
		return (bool) ((int) $value < (int) $maximum);
	}


	/**
	 * Checks the value for a string wihout control characters (ASCII 0 - 31), spaces are allowed
	 *
	 * @return	bool
	 * @param	string $value
	 */
	public static function isString($value)
	{
		return (bool) preg_match("/^[^\x-\x1F]+$/", (string) $value);
	}


	/**
	 * Checks the value for a valid url
	 *
	 * @return	bool
	 * @param	string $value
	 */
	public static function isURL($value)
	{
		$regexp = '/^((http|ftp|https):\/{2})?(([0-9a-zA-Z_-]+\.)+[0-9a-zA-Z]+)((:[0-9]+)?)((\/([~0-9a-zA-Z\#%@\.\/_-]+)?(\?[0-9a-zA-Z%@\/&=_-]+)?)?)$/';
		return (bool) preg_match($regexp, (string) $value);
	}


	/**
	 * Validates a value against a regular expression
	 *
	 * @return	bool
	 * @param	string $regexp
	 * @param	string $value
	 */
	public static function isValidAgainstRegexp($regexp, $value)
	{
		// redefine vars
		$regexp = (string) $regexp;

		// invalid regexp
		if(!self::isValidRegexp($regexp)) throw new SpoonFilterException('The provided regex pattern "'. $regexp .'" is not valid');

		// validate
		if(@preg_match($regexp, (string) $value)) return true;
		return false;
	}


	/**
	 * Checks if the given regex statement is valid
	 *
	 * @return	bool
	 * @param	string $regexp
	 */
	public static function isValidRegexp($regexp)
	{
		// regexp & dummy string
		$regexp = (string) $regexp;
		$dummy = 'spoon is growing every day';

		// validate
		return (@preg_match($regexp, $dummy) === false) ? false : true;
	}


	/**
	 * Function that will be used by replaceURLsWithAnchors
	 *
	 * @return	string
	 * @param	array $match
	 */
	private static function replaceURLsCallback($match)
	{
		// init var
		$link = $match[1];
		$label = $match[1];

		// no protocol provided
		if($match[3] == '') $link = 'http://'. $link;

		// return the replace-value
		return '<a href="'. $link .'">'. $label .'</a>';
	}


	/**
	 * Replace URLs with an anchor
	 *
	 * @return	string
	 * @param	string $value
	 * @param	bool[optional] $noFollow
	 */
	public static function replaceURLsWithAnchors($value, $noFollow = true)
	{
		// redefine
		$value = (string) $value;

		// init vars
		$matches = array();

		// build regexp
		$pattern = '/(((http|ftp|https):\/{2})?(([0-9a-z_-]+\.)+('. implode('|', self::$tlds) .')(:[0-9]+)?((\/([~0-9a-zA-Z\#\+\%@\.\/_-]+))?(\?[0-9a-zA-Z\+\%@\/&=_-]+)?)?))\b/imu';

		// get matches
		$value = preg_replace_callback($pattern, 'SpoonFilter::replaceURLsCallback', $value);

		// add noFollow-attribute
		if($noFollow) $value = str_replace('<a href=', '<a rel="nofollow" href=', $value);

		// return
		return $value;
	}


	/**
	 * Convert a string to camelcasing
	 *
	 * @return	string
	 * @param	string $value
	 * @param	string[optional] $separator
	 * @param	bool[optional] $lcfirst
	 * @param	string[optional] $charset
	 */
	public static function toCamelCase($value, $separator = '_', $lcfirst = false, $charset = null)
	{
		// define charset
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// start all words with a capital letter
		if(!$lcfirst) return str_replace(' ', '', mb_convert_case(str_replace((string) $separator, ' ', (string) $value), MB_CASE_TITLE, $charset));

		// start all words, except the first one, with a capital letter
		else
		{
			// init var
			$string = '';
			$skipped = false;

			// fetch words
			$aWords = explode($separator, $value);

			// create new string
			foreach($aWords as $word)
			{
				// first word
				if(!$skipped)
				{
					// add to value
					$string .= $word;

					// update skippy
					$skipped = true;
				}

				// second, third, ...
				else $string .= ucfirst($word);
			}

			return $string;
		}
	}


	/**
	 * Prepares a string so that it can be used in urls. Special characters are stripped/replaced
	 *
	 * @return	string
	 * @param	string $value
	 * @param	string[optional] $charset
	 */
	public static function urlise($value, $charset = null)
	{
		// define charset
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// allowed characters
		$aCharacters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_', ' ');

		// redefine value
		$value = mb_strtolower($value, $charset);

		// replace special characters
		$aReplace['.'] = ' ';
		$aReplace['@'] = ' at ';
		$aReplace['©'] = ' copyright ';
		$aReplace['€'] = ' euro ';
		$aReplace['™'] = ' tm ';

		// replace special characters
		$value = str_replace(array_keys($aReplace), array_values($aReplace), $value);

		// reform non ascii characters
		$value = iconv($charset, 'ASCII//TRANSLIT//IGNORE', $value);

		// remove spaces at the beginning and the end
		$value = trim($value);

		// default endvalue
		$newValue = '';

		// loop charachtesr
		for ($i = 0; $i < mb_strlen($value, $charset); $i++)
		{
			// valid character (so add to new string)
			if(in_array(mb_substr($value, $i, 1, $charset), $aCharacters)) $newValue .= mb_substr($value, $i, 1, $charset);
		}

		// replace spaces by dashes
		$newValue = str_replace(' ', '-', $newValue);

		// there IS a value
		if(strlen($newValue) != 0)
		{
			// convert "--" to "-"
			$newValue = preg_replace('/\-+/', '-', $newValue);
		}

		// trim - signs
		return trim($newValue, '-');
	}
}

?>