<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

/**
 * Application routing
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy@netlash.com>
 * @author Dieter Vanden Eynde <dieter@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class ApplicationRouting
{
	const DEFAULT_APPLICATION = 'frontend';

	/**
	 * Virtual folders mappings
	 *
	 * @var	array
	 */
	private static $routes = array(
		'' => self::DEFAULT_APPLICATION,
		'private' => 'backend',
		'backend' => 'backend',
		'api' => 'api'
	);

	/**
	 * The actual request, formatted as a Symfony object.
	 *
	 * @var Request
	 */
	private $request;


	public function __construct(Request $request)
	{
		$this->request = $request;

		// process querystring
		$this->processQueryString();
	}

	/**
	 * Get the possible routes
	 *
	 * @return array
	 */
	public static function getRoutes()
	{
		return self::$routes;
	}

	/**
	 * Handle the actual request and deligate it to other parts of Fork.
	 *
	 * @return Response
	 */
	public function handleRequest()
	{
		switch(APPLICATION)
		{
			case 'frontend':
				require_once __DIR__ . '/../frontend/init.php';

				new FrontendInit(APPLICATION);
				$application = new Frontend();
				break;
			case 'backend':
				require_once __DIR__ . '/../backend/init.php';

				new BackendInit(APPLICATION);
				$application = new Backend();
				break;
			case 'api':
				require_once __DIR__ . '/../api/1.0/init.php';

				new APIInit(APPLICATION);
				new API();
				break;
		}

		return $application->display();
	}

	/**
	 * Process the querystring to define the application
	 */
	private function processQueryString()
	{
		// get querystring
		$queryString = trim($this->request->getRequestUri(), '/');

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
