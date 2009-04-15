<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			http
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonHTTPExecption class */
require_once 'spoon/http/exception.php';

/** SpoonFilterExecption class */
require_once 'spoon/filter/filter.php';


/**
 * This class is used to manipulate raw http headers
 *
 * @package			http
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
final class SpoonHTTP
{
	/**
	 * Redirect the browser with an optional delay (in seconds) and stop script execution
	 *
	 * @return	void
	 * @param	string $url
	 * @param	int[optional] $delay
	 */
	public static function redirect($url, $code = 302, $delay = null)
	{
		// redefine url
		$url = (string) $url;
		$code = SpoonFilter::getValue($code, array(301, 302), 302, 'int');

		// redirect headers
		self::setHeadersByCode($code);

		// delay execution
		if($delay !== null)
		{
			// sleep
			sleep((int) $delay);
		}

		// redirect
		self::setHeaders("Location: $url");

		// stop execution
		exit;
	}


	/**
	 * Retrieve the list with headers that are sent or to be sent
	 *
	 * @return	array
	 */
	public static function getHeadersList()
	{
		return headers_list();
	}


	/**
	 * Retrieve the ip address
	 *
	 * @return	string
	 */
	public static function getIp()
	{
		return (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
	}


	/**
	 * Checks if any headers were already sent
	 *
	 * @return	bool
	 */
	private static function isSent()
	{
		// headers were already sent
		if(headers_sent()) throw new SpoonHTTPException('Headers were already sent.');

		// no headers were sent
		return false;
	}


	/**
	 * Set one or multiple headers
	 *
	 * @return	void
	 * @param	mixed $headers
	 */
	public static function setHeaders($headers)
	{
		// redefine headers
		$headers = (array) $headers;

		// headers not sent
		if(!self::isSent())
		{
			// loop elements
			foreach($headers as $header)
			{
				// set header
				header((string) $header);
			}
		}
	}


	/**
	 * Parse headers for a given status code
	 *
	 * @return	void
	 * @param	int[optional] $code
	 */
	public static function setHeadersByCode($code = 200)
	{
		// allowed status codes
		$aCodes[200] = '200 OK';
		$aCodes[301] = '301 Moved Permanently';
		$aCodes[302] = '302 Found';
		$aCodes[304] = '304 Not Modified';
		$aCodes[307] = '307 Temporary Redirect';
		$aCodes[400] = '400 Bad Request';
		$aCodes[401] = '401 Unauthorized';
		$aCodes[403] = '403 Forbidden';
		$aCodes[404] = '404 Not Found';
		$aCodes[410] = '410 Gone';
		$aCodes[500] = '500 Internal Server Error';
		$aCodes[501] = '501 Not Implemented';

		// code
		$code = (int) $code;
		if(!isset($aCodes[$code])) $code = 200;

		// set header
		self::setHeaders('HTTP/1.1 '. $aCodes[$code]);
	}
}

?>