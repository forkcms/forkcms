<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			template
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonTemplateException class */
require_once 'spoon/template/exception.php';

/** SpoonTemplate class */
require_once 'spoon/template/template.php';

/** SpoonDate class */
require_once 'spoon/date/date.php';


/**
 * This class implements modifier mapping for the templat engine
 *
 * @package			template
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			1.0.0
 */
class SpoonTemplateModifiers
{
	/**
	 * List of entities from a to z containing the lowercase & uppercase variant.
	 *
	 * @var array
	 */
	private static $entities = array(	// a
										'&aacute;' => '&Aacute;',
										'&#225;' => '&#193',
										'&acirc;' => '&Acirc;',
										'&#226;' => '&#194',
										'&agrave;' => '&Agrave;',
										'&#224;' => '&#192;',
										'&atilde;' => '&Atilde;',
										'&#227;' => '&#195;',
										'&auml;' => '&Auml;',
										'&#228;' => '&#196',
										'&aring;' => '&Aring;',
										'&#229;' => '&#197;',
										'&#7683;' => '&#7682;',
										// c
										'&ccedil;' => '&Ccedil;',
										'&#231;' => '&#199;',
										'&#269;' => '&#268;',
										'&#267;' => '&#266;',
										'&#265;' => '&#264;',
										'&#263;' => '&#262;',
										'&#7697;' => '&7696;',
										'&#7691;' => '&#7690;',
										'&#273;' => '&#272;',
										'&#271;' => '&#270;',
										// e
										'&egrave;' => '&Egrave;',
										'&#232;' => '&#200;',
										'&eacute;' => '&Eacute;',
										'&#233;' => '&#201;',
										'&ecirc;' => '&#Ecirc;',
										'&#234;' => '&#202;',
										'&euml;' => '&Euml;',
										'&#235;' => '&#203;',
										'&epsilon;' => '&Epsilon;',
										'&#949;' => '&#917;',
										'&#283;' => '&#282;',
										'&#281;' => '&#280;',
										'&#279;' => '&#278;',
										'&#277;' => '&#276;',
										'&#275;' => '&#274;',
										'&#7711;' => '&#7710;',
										'&#501;' => '&#500;',
										'&#487;' => '&#486;',
										'&#485;' => '&#484;',
										'&#291;' => '&#290;',
										'&#289;' => '&#288;',
										'&#287;' => '&#286;',
										'&#285;' => '&#284;',
										'&#295;' => '&#294;',
										'&#293;' => '&#292;',
										// i
										'&igrave;' => '&Igrave;',
										'&#236;' => '&#204;',
										'&iacute;' => '&Iacute;',
										'&#237;' => '&#205;',
										'&icirc;' => '&Icirc;',
										'&#238;' => '&#206;',
										'&iuml;' => '&Iuml;',
										'&#239;' => '&#207;',
										'&iota;' => '&Iota;',
										'&#953;' => '&#921;',
										'&#308;' => '&#309;',
										// k
										'&kappa;' => '&Kappa;',
										'&#954;' => '&#922;',
										'&#7729;' => '&#7728;',
										'&#489;' => '&#488;',
										'&#311;' => '&#310;',
										'&#7745;' => '&#7744;',
										// n
										'&ntilde;' => '&Ntilde;',
										'&#241;' => '&#209;',
										'&#331;' => '&#330;',
										'&#328;' => '&#327;',
										'&#326;' => '&#325;',
										'&#324;' => '&#323;',
										// o
										'&ograve;' => '&Ograve;',
										'&#242;' => '&#210;',
										'&oacute;' => '&Oacute;',
										'&#243;' => '&#211;',
										'&ocirc;' => '&Ocirc;',
										'&#244;' => '&#212;',
										'&otilde;' => '&Otilde;',
										'&#245;' => '&#213;',
										'&ouml;' => '&Ouml;',
										'&#246;' => '&#214;',
										'&oslash;' => '&Oslash;',
										'&#248;' => '&#216;',
										'&omicron;' => '&Omicron;',
										'&#959;' => '&#927;',
										'&#511;' => '&#510;',
										'&#337;' => '&#336;',
										'&#335;' => '&#334;',
										'&#333;' => '&#332;',
										// t
										'&thorn;' => '&THORN;',
										'&#254;' => '&#222;',
										'&rho;' => '&Rho;',
										'&#961;' => '&#929;',
										'&#7767;' => '&#7766;',
										'&#345;' => '&#344;',
										'&#343;' => '&#342;',
										'&#341;' => '&#340;',
										// s
										'&scaron;' => '&Scaron;',
										'&#353;' => '&#352;',
										'&#7777;' => '&#7776;',
										'&#351;' => '&#350;',
										'&#349;' => '&#348;',
										'&#347;' => '&#346;',
										// t
										'&tau;' => '&Tau;',
										'&#964;' => '&#932;',
										'&#7787;' => '&#7786;',
										'&#359;' => '&#358;',
										'&#357;' => '&#356;',
										'&#355;' => '&#354;',
										// u
										'&ugrave;' => '&Ugrave;',
										'&#249;' => '&#217;',
										'&uacute;' => '&Uacute;',
										'&#250;' => '&#218;',
										'&ucirc;' => '&Ucirc;',
										'&uuml;' => '&Uuml;',
										'&#252;' => '&#220;',
										'&#371;' => '&#370;',
										'&#369;' => '&#368;',
										'&#367;' => '&#366;',
										'&#365;' => '&#364;',
										'&#363;' => '&#362;',
										'&#361;' => '&#360;',
										'&#7813;' => '&#7812;',
										'&#7811;' => '&#7810;',
										'&#7809;' => '&#7808;',
										'&#373;' => '&#372;',
										'&chi;' => '&Chi;',
										'&#967;' => '&#935;',
										// y
										'&yacute;' => '&Yacute;',
										'&#253;' => '&#221;',
										'&yuml;' => '&Yuml;',
										'&#255;' => '&#376;',
										'&#7923;' => '&#7922;',
										'&#375;' => '&#374;',
										// z
										'&zeta;' => '&Zeta;',
										'&#950;' => '&#918;',
										'&#382;' => '&#381;',
										'&#380;' => '&#379;',
										'&#378;' => '&#377;');


	/**
	 * Default modifiers mapped to their functions
	 *
	 * @var	array
	 */
	private static $modifiers = array(	'addslashes' => 'addslashes',
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
										'truncate' => array('SpoonTemplateModifiers', 'truncate'),
										'truncatehtml' => array('SpoonTemplateModifiers', 'truncateHTML'),
										'ucfirst' => 'ucfirst',
										'ucwords' => 'ucwords',
										'uppercase' => array('SpoonTemplateModifiers', 'uppercase'));


	/**
	 * Clears the entire modifiers list
	 *
	 * @return	void
	 */
	public static function clearModifiers()
	{
		self::$modifiers = array();
	}


	/**
	 * Converts links to HTML links (only to be used with cleartext)
	 *
	 * @return	string
	 * @param	string $text
	 */
	public static function createHTMLLinks($text)
	{
		// init vars
		$pattern = '/((http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:\/~\+#]*[\w\-\@?^=%&amp;\/~\+#])?)/i';
		$replace = '<a href="$1">$1</a>';

		// replace links
		return preg_replace($pattern, $replace, (string) $text);
	}


	/**
	 * Formats a language specific date
	 *
	 * @return	string
	 * @param	int $timestamp
	 * @param	string[optional] $format
	 */
	public function date($timestamp, $format = 'Y-m-d H:i:s', $language = 'en')
	{
		return SpoonDate::getDate($format, $timestamp, $language);
	}


	/**
	 * Retrieves the modifiers
	 *
	 * @return	array
	 */
	public static function getModifiers()
	{
		return self::$modifiers;
	}


	/**
	 * Makes the string lowercase and takes entities into account
	 *
	 * @return	string
	 * @param	string $string
	 */
	public static function lowercase($string)
	{
		// replace the entities
		$string = str_replace(array_values(self::$entities), array_keys(self::$entities), $string);

		// convert to entities, apply uppercase, reconvert to html
		$string = SpoonFilter::htmlentities(strtolower(SpoonFilter::htmlentitiesDecode($string)));
		return $string;
	}


	/**
	 * Maps a specific modifier to a function/method
	 *
	 * @return	void
	 * @param	string $name
	 * @param	mixed $function
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
			if(!method_exists($function[0], $function[1])) throw new SpoonTemplateException('The method "'. $function[1] .'" in the class '. $function[0] .' does not exist.');

			// all fine
			self::$modifiers[(string) $name] = $function;
		}

		// regular function
		else
		{
			// function doesn't exist
			if(!function_exists((string) $function)) throw new SpoonTemplateException('The function "'. (string) $function .'" does not exist.');

			// all fine
			self::$modifiers[(string) $name] = $function;
		}
	}


	/**
	 * Truncates a string and adds ...
	 *
	 * @return	string
	 * @param	string $string
	 * @param	int $length
	 * @param	string[optional] $cut
	 */
	public static function truncate($string, $length, $cut = 'no')
	{
		// don't cut
		if($cut == 'no')
		{
			// string is too big
			if(strlen($string) > $length)
			{
				// chop
				$string = substr($string, 0, $length);

				// last space position
				$iPos = strrpos($string, ' ');

				// space found
				if($iPos !== false) $string = substr($string, 0, $iPos);
			}
		}

		// allow cut
		elseif(strlen($string) > $length) $string = substr($string, 0, (int) $length) .'...';

		// final string
		return $string;
	}


	/**
	 * Truncates an html string and adds ...
	 *
	 * @return	string
	 * @param	string $string
	 * @param	int $length
	 * @param	string[optional] $cut
	 */
	public static function truncateHTML($string, $length, $cut = 'no')
	{
		// strip html tags
		$string = trim(strip_tags($string));
		$string = SpoonFilter::htmlentitiesDecode($string);

		// actually shortened?
		$shortened = false;

		// don't cut
		if($cut == 'no')
		{
			// string is too big
			if(strlen($string) > $length)
			{
				// chop
				$string = substr($string, 0, $length);

				// shortened!
				$shortened = true;

				// last space position
				$iPos = strrpos($string, ' ');

				// space found
				if($iPos !== false) $string = substr($string, 0, $iPos);
			}
		}

		// allow cut
		elseif(strlen($string) > $length)
		{
			// cut
			$string = substr($string, 0, (int) $length);

			// shortened
			$shortened = true;
		}

		// re-parse html
		$string = SpoonFilter::htmlentities($string);

		// add ... only if the string was shortened
		if($shortened) $string .= '&hellip;';

		// final string
		return $string;
	}


	/**
	 * Makes the string uppercase and takes entities into account
	 *
	 * @return	string
	 * @param	string $string
	 */
	public static function uppercase($string)
	{
		// replace the entities
		$string = str_replace(array_keys(self::$entities), array_values(self::$entities), $string);

		// convert to entities, apply uppercase, reconvert to html
		$string = SpoonFilter::htmlentities(strtoupper(SpoonFilter::htmlentitiesDecode($string)));
		return $string;
	}
}

?>