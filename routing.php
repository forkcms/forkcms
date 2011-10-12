<?php

/**
 * Application routing
 *
 * @package			Frontend
 *
 * @author			Tijs Verkoyen <tijs@netlash.com>
 * @author			Davy Hellemans <davy@netlash.com>
 * @author			Dieter Vanden Eynde <dieter@netlash.com>
 * @since			2.0
 */
class ApplicationRouting
{
	// Default application
	const DEFAULT_APPLICATION = 'frontend';


	/**
	 * Virtual folders mappings
	 *
	 * @var	array
	 */
	private static $routes = array('' => self::DEFAULT_APPLICATION,
									'private' => 'backend',
									'backend' => 'backend',
									'api' => 'api');


	/**
	 * Class constructor.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// spoof querystring on lighttpd
		$this->spoofQueryString();

		// process querystring
		$this->processQueryString();

		// require correct app
		require_once APPLICATION . '/index.php';
	}


	/**
	 * Get the possible routes
	 *
	 * @return	array
	 */
	public static function getRoutes()
	{
		return self::$routes;
	}


	/**
	 * Process the querystring to define the application
	 *
	 * @return	void
	 */
	private function processQueryString()
	{
		// get querystring
		$queryString = trim($_SERVER['REQUEST_URI'], '/');

		// split into chunks
		$chunks = explode('/', $queryString);

		// is there a application specified
		if(isset($chunks[0]))
		{
			// cleanup
			$proposedApplication = (string) $chunks[0];

			// set real application
			$application = (isset(self::$routes[$proposedApplication])) ? self::$routes[$proposedApplication] : self::DEFAULT_APPLICATION;
		}

		// no application
		else $application = self::DEFAULT_APPLICATION;

		// define APP
		if(!defined('APPLICATION')) define('APPLICATION', $application);
		if(!defined('NAMED_APPLICATION')) define('NAMED_APPLICATION', $proposedApplication);
	}


	/**
	 * Spoof QUERY_STRING when on a lighttp webserver
	 *
	 * Lighttp does not fill up the QUERY_STRING var when using rewrites or the error handler.
	 * This function fakes the QUERY_STRING.
	 *
	 * PHP uses QUERY_STRING to fill up the GET array. Without this fix GET would be empty
	 *
	 * @return	void
	 */
	public static function spoofQueryString()
	{
		// its a lighttp server
		if(strpos($_SERVER['SERVER_SOFTWARE'], 'lighttpd') !== false)
		{
			// build current url (we need a valid url to use parse_url)
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

			// get querystring
			$queryString = @parse_url($url, PHP_URL_QUERY);

			// successfuly parsed
			if($queryString !== false)
			{
				// spoof the query string
				$_SERVER['QUERY_STRING'] = (string) $queryString;

				// parse querystring to array
				parse_str($queryString, $get);

				// spoof get
				foreach($get as $key => $val) $_GET[$key] = $val;
			}
		}
	}
}

?>