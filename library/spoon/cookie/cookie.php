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
	 * Deletes one or more cookies.
	 *
	 * @return	void
	 * @param	mixed $keys		The key(s) that should be deleted.
	 */
	public static function delete($keys)
	{
		// loop all arguments
		foreach(func_get_args() as $argument)
		{
			// array element
			if(is_array($argument))
			{
				// loop the keys
				foreach($argument as $key)
				{
					// remove from array
					unset($_COOKIE[(string) $key]);

					// unset cookie
					setcookie((string) $key, null, time() - 3600);
				}
			}

			// other type(s)
			else
			{
				// remove from array
				unset($_COOKIE[(string) $argument]);

				// unset cookie
				setcookie((string) $argument, null, time() - 3600);
			}
		}
	}


	/**
	 * Checks if the given key exists.
	 *
	 * @return	bool			If the key(s) exists, returns true otherwise false.
	 * @param	string $key		The key(s) that should be checked.
	 */
	public static function exists($key)
	{
		// loop all arguments
		foreach(func_get_args() as $argument)
		{
			// array element
			if(is_array($argument))
			{
				// loop the keys
				foreach($argument as $key)
				{
					// does NOT exist
					if(!isset($_COOKIE[(string) $key])) return false;
				}
			}

			// other type(s)
			else
			{
				// does NOT exist
				if(!isset($_COOKIE[(string) $argument])) return false;
			}
		}

		return true;
	}


	/**
	 * Gets a variable that was stored in a cookie.
	 *
	 * @return	mixed			The value that was stored in the cookie.
	 * @param	string $key		The key for the cookie that should be retrieved.
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
	 * @return	bool						If set with succes, returns true otherwise false.
	 * @param	string $key					A name for the cookie.
	 * @param	mixed $value				The value to be stored, will be serialized, so be carefull with objects.
	 * @param 	int[optional] $time			The number of seconds that will be added to the timestamp for the expiration-timestamp.
	 * @param	string[optional] $path		The path on the server in which the cookie will be availabe. Use / for the entire domain, /foo if you just want it to be available in /foo.
	 * @param	string[optional] $domain	The domain that the cookie is available. Use .example.com to make it available on all subdomains of example.com.
	 * @param	bool[optional] $secure		Should the cookie be transmitted over a HTTPS-connection? If true, make sure you use a secure connection, otherwise the cookie won't be set.
	 * @param	bool[optional] $httpOnly	Should the cookie only be available through http-protocol? If true, the cookie can't be accessed by Javascript, ...
	 */
	public static function set($key, $value, $time = 86400, $path = '/', $domain = null, $secure = false, $httpOnly = false)
	{
		// redefine
		$key = (string) $key;
		$value = serialize($value);
		$time = time() + (int) $time;
		$path = (string) $path;
		$domain = ($domain !== null) ? (string) $domain : null;
		$secure = (bool) $secure;
		$httpOnly = (bool) $httpOnly;

		// set cookie
		$cookie = setcookie($key, $value, $time, $path, $domain, $secure, $httpOnly);

		// problem occured
		return ($cookie === false) ? false : true;
	}
}

?>