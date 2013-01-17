<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is our Uri generating class
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class CommonUri
{
	/**
	 * Prepares a string for a filename so that it can be used in urls.
	 *
	 * @return	string						The urlised string.
	 * @param	string $value				The value (without extension) that should be urlised.
	 * @param	string[optional] $charset	The charset to use, default is based on SPOON_CHARSET.
	 */
	public static function getFilename($value, $charset = null)
	{
		// define charset
		$charset = ($charset !== null) ? self::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// decode value
		$value = urldecode($value);

		// reserved characters (RFC 3986)
		$reservedCharacters = array(
			'.', '/', '?', ':', '@', '#', '[', ']',
			'!', '$', '&', '\'', '(', ')', '*',
			'<', '>', '+', ',', ';', '=', '%', '"'
		);

		// remove reserved characters
		$value = str_replace($reservedCharacters, ' ', $value);

		// replace spaces by dashes
		$value = str_replace(' ', '-', $value);

		// to lowercase
		$value = mb_strtolower($value, $charset);

		// remove special characters
		$value = htmlentities($value, ENT_COMPAT, $charset);
		$value = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $value);

		// convert "--" to "-"
		$value = preg_replace('/\-+/', '-', $value);

		// trim - signs
		return trim($value, '-');
	}

	/**
	 * Prepares a string so that it can be used in urls.
	 *
	 * @return	string						The urlised string.
	 * @param	string $value				The value that should be urlised.
	 * @param	string[optional] $charset	The charset to use, default is based on SPOON_CHARSET.
	 */
	public static function getUrl($value, $charset = null)
	{
		SpoonFilter::urlise($value, $charset);
	}
}
