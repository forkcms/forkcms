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
		$charset = ($charset !== null) ? SpoonFilter::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET) : SPOON_CHARSET;

		// decode htmlspecial characters
		$value = SpoonFilter::htmlspecialcharsDecode($value);

		// decode as url
		$value = urldecode($value);

		/*
		 * reserved characters in URI, according to RFC 3986
		 * @see http://www.ietf.org/rfc/rfc3986.txt
		 */
		$reservedCharacters = array(
			'/', '?', ':', '@', '#', '[', ']',
			'!', '$', '&', '\'', '(', ')', '*',
			'+', ',', ';', '='
		);

		/*
		 * in addition to RFC 3986, make sure the local filename is valid
		 * @see http://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
		 */
		$reservedCharacters = array_merge(array('.', '<', '>', '%', '"', '|'), $reservedCharacters);

		// remove reserved characters
		$value = str_replace($reservedCharacters, ' ', $value);

		// replace spaces by dashes
		$value = str_replace(' ', '-', $value);

		// to lowercase
		$value = mb_strtolower($value, $charset);

		// replace special characters by their normal character
		$value = SpoonFilter::htmlentities($value, $charset);
		$value = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $value);

		/**
		 * we need to clean leftovers by using urlencode
		 * so we can remove special characters like ‘ | ’ | “ |  ”
		 */
		$value = urlencode($value);
		$value = preg_replace('/%.+%3B/', '-', $value);

		// this cleans remaining letter accents for mozilla, f.e.: %CC%81, %CC%A7, %CC%80
		$value = preg_replace('/%CC%[a-zA-Z0-9]./', '', $value);

		// convert "--" to "-"
		$value = preg_replace('/\-+/', '-', $value);

		// convert "-." to "." when using a file extension
		$value = preg_replace('/\-\./', '.', $value);

		// trim '.' and '-' signs
		return trim($value, '.-');
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
		// get clean filename
		$value = self::getFilename($value, $charset);

		// define reserved characters
		$reservedCharacters = array(
			'.', '_'
		);

		// remove reserved characters
		$value = str_replace($reservedCharacters, '-', $value);

		return $value;
	}
}
