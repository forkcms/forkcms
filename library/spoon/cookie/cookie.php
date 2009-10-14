<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		cookie
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		0.1.1
 */


/**
 * This exception is used to handle cookie related exceptions.
 *
 * @package		cookie
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.be>
 * @since		0.1.1
 */
class SpoonCookieException extends SpoonException {}


/**
 * This base class provides some methods for setting, retrieving and
 * modifying cookies.
 *
 * @package		cookie
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonCookie
{
	/**
	 * Deletes one or more cookies
	 *
	 * @return	void
	 * @param	mixed $keys
	 */
	public static function delete($keys)
	{
		// redefine
		$keys = (array) $keys;

		// loop the keys
		foreach($keys as $key)
		{
			// remove from array
			unset($_COOKIE[$key]);

			// unset cookie
			setcookie($key);
		}
	}


	/**
	 * Checks if the given key exists
	 *
	 * @return	bool
	 * @param	string $key
	 */
	public static function exists($key)
	{
		return (isset($_COOKIE[(string) $key]));
	}


	/**
	 * Gets a variable that was stored in a cookie
	 *
	 * @return	mixed
	 * @param	string $key
	 */
	public static function get($key)
	{
		// redefine key
		$key = (string) $key;

		// cookie doesn't exist
		if(!self::exists($key)) return false;

		// fetch base value
		$value = (get_magic_quotes_gpc()) ? stripslashes($_COOKIE[$key]) : $_COOKIE[$key];

		// unserialize failed
		if(@unserialize($value) === false && serialize(false) != $value)
		{
			throw new SpoonCookieException('The value of the cookie "'. $key .'" could not be retrieved. This might indicate that it has been tampered with OR the cookie was not initially set using SpoonCookie.');
		}

		// everything is fine
		return @unserialize($value);
	}


	/**
	 * Stores a variable in a cookie, by default the cookie will expire in one day.
	 *
	 * @return	bool
	 * @param	string $key
	 * @param	mixed $value
	 * @param 	int[optional] $time
	 * @param	string[optional] $path
	 * @param	string[optional] $domain
	 * @param	bool[optional] $secure
	 */
	public static function set($key, $value, $time = 86400, $path = '/', $domain = null, $secure = false)
	{
		// redefine
		$key = (string) $key;
		$value = serialize($value);
		$time = time() + (int) $time;
		$path = (string) $path;
		$domain = ($domain !== null) ? (string) $domain : null;
		$secure = (bool) $secure;

		// set cookie
		$cookie = setcookie($key, $value, $time, $path, $domain, $secure);

		// problem occured
		return ($cookie === false) ? false : true;
	}
}

?>