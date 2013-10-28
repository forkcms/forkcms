<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is our extended version of SpoonCookie
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class CommonCookie extends SpoonCookie
{
	/**
	 * Stores a value in a cookie, by default the cookie will expire in one day.
	 *
	 * @param string $key	A name for the cookie.
	 * @param mixed $value	The value to be stored. Keep in mind that they will be serialized.
	 * @param int[optional] $time	The number of seconds that this cookie will be available, 30 days is the default.
	 * @param string[optional] $path	The path on the server in which the cookie will be available. Use / for the entire domain, /foo if you just want it to be available in /foo.
	 * @param string[optional] $domain	The domain that the cookie is available on. Use .example.com to make it available on all subdomains of example.com.
	 * @param bool[optional] $secure	Should the cookie be transmitted over a HTTPS-connection? If true, make sure you use a secure connection, otherwise the cookie won't be set.
	 * @param bool[optional] $httpOnly	Should the cookie only be available through HTTP-protocol? If true, the cookie can't be accessed by Javascript, ...
	 * @return bool	If set with success, returns true otherwise false.
	 */
	public static function set($key, $value, $time = 2592000, $path = '/', $domain = null, $secure = null, $httpOnly = true)
	{
		// redefine
		$key = (string) $key;
		$value = serialize($value);
		$time = time() + (int) $time;
		$path = (string) $path;
		$httpOnly = (bool) $httpOnly;

		// when the domain isn't passed and the url-object is available we can set the cookies for all subdomains
		if($domain === null && Spoon::exists('url')) $domain = '.' . Spoon::get('url')->getDomain();

		// when the secure-parameter isn't set
		if($secure === null)
		{
			/*
			 detect if we are using HTTPS, this wil only work in Apache, if you are using nginx you should add the
			 code below into your config:
			 	ssl on;
				fastcgi_param HTTPS on;

			 for lighttpd you should add:
			 	setenv.add-environment = ("HTTPS" => "on")
			 */
			$secure = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on');
		}

		// set cookie
		$cookie = setcookie($key, $value, $time, $path, $domain, $secure, $httpOnly);

		// problem occurred
		return ($cookie === false) ? false : true;
	}
}
