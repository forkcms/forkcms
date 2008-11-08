<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			filter
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonFilterException class */
require_once 'spoon/filter/exception.php';


/**
 * This base class provides all the methods used to filter input of any kind.
 *
 * @package			filter
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */
final class SpoonFilter
{
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

		// elements found
		foreach($array as $value)
		{
			// counter
			if(!isset($i)) $i = 0;

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
	public static function getGetValue($field, $values = null, $defaultValue, $returnType = 'string')
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
	public static function getPostValue($field, $values = null, $defaultValue, $returnType = 'string')
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
	 * @param	mixed $variable
	 * @param	array[optional] $values
	 * @param	mixed $defaultValue
	 * @param	string[optional] $returnType
	 */
	public static function getValue($variable, $values = null, $defaultValue, $returnType = 'string')
	{
		// redefine arguments
		$variable = (string) $variable;
		$values = (array) $values;
		$defaultValue = (string) $defaultValue;
		$returnType = (string) $returnType;

		// redefine values array
		if(count($values) == 0) $values[0] = '';

		// default value
		$value = $defaultValue;

		// forced array with default parameter being empty
		if($values[0] == '' || in_array($variable, $values)) $value = $variable;

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
	 * Apply the htmlentities function with iso-88515 encoding by default
	 *
	 * @return	string
	 * @param	string $value
	 * @param	string[optional] $charset
	 */
	public static function htmlentities($value, $charset = 'iso-8859-15')
	{
		// define charset
		$charset = self::getValue($charset, array('utf-8', 'iso-8859-1', 'iso-8859-15'), 'iso-8859-15');

		// apply method
		$return = htmlentities($value, ENT_QUOTES, $charset);

		/**
		 * PHP doesn't replace a backslash to its html entity since this is something
		 * that's mostly used to escape characters when inserting in a database. Since
		 * we're using a decent database layer, we don't need this shit and we're replacing
		 * the double backslashes by it's html entity equivalent.
		 */
//	@todo smartquotes and mdashes? see maguza		return str_replace(array('\\', chr(145), chr(146), chr(147), chr(148), chr(151)), array('&#92;', '&#8216;', '&#8217;', '&#8220;', '&#8221;', '&#8212;'), $return);
		return str_replace(array('\\'), array('&#92;'), $return);
	}


	/**
	 * Apply the html_entity_decode function with iso-8859-15 encoding by default
	 *
	 * @return	string
	 * @param	string $value
	 * @param	string[optional] $charset
	 */
	public static function htmlentitiesDecode($value, $charset = 'iso-8859-15')
	{
		// define charset
		$charset = self::getValue($charset, array('utf-8', 'iso-8859-1', 'iso-8859-15'), 'iso-8859-15');

		// apply method
		return html_entity_decode($value, ENT_QUOTES, $charset);
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
		return (bool) preg_match("/^[a-z0-9_\.-]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i", (string) $value);
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
		return (bool) preg_match("/^-?([0-9]*\.?,?[0-9]+)$/", (string) $value);
	}


	/**
	 * Checks if the value is greather than a give minimum
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
	public static function isInternalReferrer($domains = null)
	{
		// no referrer found
		if(!isset($_SERVER['HTTP_REFERER'])) return true;

		// redefine hostname & domains
		$hostname = str_replace('www.', '', $_SERVER['HTTP_HOST']);
		$domains = ($domains === null) ? (array) $hostname : (array) $domains;

		// redefine referer
		$referrer = str_replace(array('http://', 'https://', 'www.'), '', $_SERVER['HTTP_REFERER']);
		$slashPosition = strpos($referrer, '/');
		if($slashPosition !== false) $referrer = substr($referrer, 0, $slashPosition);

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
	 */
	public static function isMaximumCharacters($maximum, $value)
	{
		return (bool) (strlen((string) $value) <= (int) $maximum);
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
	 */
	public static function isMinimumCharacters($minimum, $value)
	{
		return (bool) (strlen((string) $value) >= (int) $minimum);
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
		// @todo Nieuwe regex (flipt op tildes, asp shit server shit zo, protocollen checkene, der zijn der meer!)
		$regexp = '/^((http|ftp|https):\/{2})?(([0-9a-zA-Z_-]+\.)+[a-zA-Z]+)((:[0-9]+)?)((\/([0-9a-zA-Z\#%\.\/_-]+)?(\?[0-9a-zA-Z%\/&=_-]+)?)?)$/';
		return (bool) preg_match($regexp, (string) $value);
	}


	/**
	 * Validates a value against a regular expression
	 *
	 * @return	bool
	 * @param	string $regexp
	 * @param	mixed $value
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
	 * Convert a string to camelcasing
	 *
	 * @return	string
	 * @param	string $value
	 * @param	string[optional] $separator
	 */
	public static function toCamelCase($value, $separator = '_')
	{
		return str_replace(' ', '', ucwords(str_replace((string) $separator, ' ', (string) $value)));
	}


	/**
	 * Prepares a string so that it can be used in urls. Special characters are stripped/replaced
	 *
	 * @return	string
	 * @param	string $value
	 * @param	string[optional] $charset
	 */
	public static function urlise($value, $charset = 'iso-8859-15')
	{
		// redefine value
		$value = self::htmlentities($value, $charset);

		// allowed characters
		$aCharacters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_', ' ');

		// letter "a"
		$aSearchA = array('&Agrave;', '&#192;', '&Aacute;', '&#193;', '&Acirc;', '&#194;', '&Atilde;', '&#195;', '&Auml;', '&#196;', '&Aring;', '&#197;', '&agrave;', '&#224;', '&aacute;', '&#225;', '&acirc;', '&#226;', '&atilde;', '&#227;', '&auml;', '&#228;', '&aring;', '&#229;');
		$aReplaceA = 'a';

		// letter "c"
		$aSearchC = array('&Ccedil;', '&#199;', '&ccedil;', '&#231;');
		$aReplaceC = 'c';

		// letter "e"
		$aSearchE = array('&Egrave;', '&#200;', '&Eacute;', '&#201;', '&Ecirc;', '&#202;', '&Euml;', '&#203;', '&egrave;', '&#232;', '&eacute;', '&#233;', '&ecirc;', '&#234;', '&euml;', '&#235;');
		$aReplaceE = 'e';

		// letter "i"
		$aSearchI = array('&Igrave;', '&#204;', '&Iacute;', '&#205;', '&Icirc;', '&#206;', '&Iuml;', '&#207;', '&igrave;', '&#236;', '&iacute;', '&#237;', '&icirc;', '&#238;', '&iuml;', '&#239;');
		$aReplaceI = 'i';

		// letter "l"
		$aSearchL = array('&lgrave;', '&#204;', '&lacute;', '&#205;', '&lcirc;', '&#206;', '&luml;', '&#207;');
		$aReplaceL = 'l';

		// letter "n"
		$aSearchN = array('&Ntilde;', '&#209;', '&ntilde;', '&#241;');
		$aReplaceN = 'n';

		// letter "o"
		$aSearchO = array('&Ograve;', '&#210;', '&Oacute;', '&#211;', '&Ocirc;', '&#212;', '&Otilde;', '&#213;', '&Ouml;', '&#214;', '&ograve;', '&#242;', '&oacute;', '&#243;', '&ocirc;', '&#244;', '&otilde;', '&#245;', '&ouml;', '&#246;');
		$aReplaceO = 'o';

		// letter "u"
		$aSearchU = array('&micro;', '&#181;', '&Ugrave;', '&#217;', '&Uacute;', '&#218;', '&Ucirc;', '&#219;', '&Uuml;', '&#220;', '&ugrave;', '&#249;', '&uacute;', '&#250;', '&ucirc;', '&#251;', '&uuml;', '&#252;', '&mu;', '&#956;');
		$aReplaceU = 'u';

		// letter "y"
		$aSearchY = array('&Yacute;', '&#221;', '&yacute;', '&#253;', '&yuml;', '&#255;', '&Yuml;', '&#376;');
		$aReplaceY = 'y';

		// specials
		$aSearchMisc = array('&trade;', '&euro;', '&copy', '@');
		$aReplaceMisc = array(' tm ', ' euro ', ' copyright ', ' at ');

		// execute replacements
		$value = str_replace($aSearchA, $aReplaceA, $value);
		$value = str_replace($aSearchC, $aReplaceC, $value);
		$value = str_replace($aSearchE, $aReplaceE, $value);
		$value = str_replace($aSearchI, $aReplaceI, $value);
		$value = str_replace($aSearchL, $aReplaceL, $value);
		$value = str_replace($aSearchN, $aReplaceN, $value);
		$value = str_replace($aSearchO, $aReplaceO, $value);
		$value = str_replace($aSearchU, $aReplaceU, $value);
		$value = str_replace($aSearchY, $aReplaceY, $value);
		$value = str_replace($aSearchMisc, $aReplaceMisc, $value);

		// replace html entities
		$value = preg_replace("/&[a-z0-9\#]{2,8};/i", '', $value);

		/**
		 * To lower case, which makes special characters (eg copyright simbol),
		 * that haven't already been replaced, unreadable. They will therefor
		 * be deleted from the output.
		 */
		$value = strtolower($value);

		// replace dots with spaces
		$value = str_replace('.', ' ', $value);

		// remove spaces at the beginning and end
		$value = trim($value);

		// default endvalue
		$newValue = '';

		// loop charachtesr
		for ($i = 0; $i < strlen($value); $i++)
		{
			// valid character (so add to new string)
			if(in_array(substr($value, $i, 1), $aCharacters)) $newValue .= substr($value, $i, 1);
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