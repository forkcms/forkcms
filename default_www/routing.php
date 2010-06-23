<?php

/**
 * ApplicationRouting
 *
 * @package			Frontend
 *
 * @author 			Tijs Verkoyen <tijs@netlash.com>
 * @author			Davy Hellemans <davy@netlash.com>
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
		// process querystring
		$this->processQueryString();

		// require correct app
		require_once APPLICATION .'/index.php';
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
}

?>