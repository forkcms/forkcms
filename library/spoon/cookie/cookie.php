<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			cookie
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonCookieException class */
require_once 'spoon/cookie/exception.php';


/**
 * This base class provides all the methods used by cookies.
 *
 * @package			cookie
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */
class SpoonCookie
{
	/**
	 * Strict setting
	 *
	 * @var	bool
	 */
	private static $strict = SPOON_STRICT;


	/**
	 * Deletes a key-value-pair from the cookie
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
			// validate
			if(!self::exists($key) && self::$strict) throw new SpoonCookieException('This key doesn\'t exists. Key: '.$key);

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
		if(!self::exists($key))
		{
			// strict?
			if(self::$strict) throw new SpoonCookieException('The key "'. $key .'" doesn\'t exist.');
			return false;
		}

		// fetch base value
		$value = (get_magic_quotes_gpc()) ? stripslashes($_COOKIE[$key]) : $_COOKIE[$key];

		// unserialize failed
		if(@unserialize($value) === false && serialize(false) != $value)
		{
			// strict?
			if(self::$strict) throw new SpoonCookieException('The value of the cookie "'. $key .'" could not be retrieve. This might indicate that it has been tampered with OR the cookie was not initially set using SpoonCookie.');
			return false;
		}

		// everything is fine
		return @unserialize($value);
	}


	/**
	 * Retrieve the strict option
	 *
	 * @return	bool
	 */
	public static function getStrict()
	{
		return self::$strict;
	}


	/**
	 * Stores a variable in a cookie, by default the cookie will expire in one day.
	 *
	 * @return	mixed
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
		if($cookie === false)
		{
			// strict?
			if(self::$strict) throw new SpoonCookieException('The cookie "'. $key .'" could not be set.');
			return false;
		}
	}


	/**
	 * Sets the strict option
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public static function setStrict($on = true)
	{
		self::$strict = (bool) $on;
	}
 }

?>