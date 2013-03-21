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
 * @author Dave Lens <dave.lens@wijs.be>
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
		'api' => 'api',
		'install' => 'install'
	);

	/**
	 * @var Kernel
	 */
	private $kernel;

	/**
	 * The actual request, formatted as a Symfony object.
	 *
	 * @var Request
	 */
	private $request;

	/**
	 * @param Request $request
	 * @param Kernel $kernel
	 */
	public function __construct(Request $request, Kernel $kernel)
	{
		// this class is used in most Fork applications to bubble down the Kernel object
		require_once __DIR__ . '/ApplicationInterface.php';
		require_once __DIR__ . '/KernelLoader.php';

		$this->request = $request;
		$this->kernel = $kernel;

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
	 * Handle the actual request and delegate it to other parts of Fork.
	 *
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function handleRequest()
	{
		$applicationName = APPLICATION;

		/**
		 * Our ajax and cronjobs don't go trough the index.php file at the
		 * moment. Because of this we need to add some extra validation.
		 */
		if(strpos($this->request->getRequestUri(), 'ajax.php') !== false)
		{
			$applicationName .= '_ajax';
		}
		elseif(strpos($this->request->getRequestUri(), 'cronjob.php') !== false)
		{
			$applicationName .= '_cronjob';
		}

		// Pave the way for the application we'll need to load.
		// This initializes basic functionality and retrieves the correct class to instantiate.
		switch($applicationName)
		{
			case 'frontend':
			case 'frontend_ajax':
				$applicationClass = $this->initializeFrontend($applicationName);
				break;
			case 'backend':
			case 'backend_ajax':
			case 'backend_cronjob':
				$applicationClass = $this->initializeBackend($applicationName);
				break;
			case 'api':
				$applicationClass = $this->initializeAPI($applicationName);
				break;
			case 'install':
				// install directory might be deleted after install, handle it as a normal frontend request
				if(file_exists(__DIR__ . '/../install'))
				{
					$applicationClass = $this->initializeInstaller();
				}
				else $applicationClass = 'frontend';
				break;
		}

		/**
		 * Load the page and pass along the application kernel
		 * This step is needed to bubble our container all the way to the action.
		 *
		 * Once we switch to bundles, the kernel will boot those bundles and pass the container.
		 * The kernel object itself will then be stored as a singleton in said container, same
		 * as in Symfony.
		 */
		$application = new $applicationClass($this->kernel);
		$application->passContainerToModels();
		$application->initialize();
		return $application->display();
	}

	/**
	 * @param string $app The name of the application to load (ex. backend_ajax)
	 * @return string The name of the application class we need to instantiate.
	 */
	protected function initializeAPI($app)
	{
		$queryString = $this->getQueryString();
		$chunks = explode('/', $queryString);
		$apiVersion = (array_key_exists(1, $chunks)) ? $chunks[1] : '1.0';

		require_once __DIR__ . '/../api/' . $apiVersion . '/init.php';
		$init = new APIInit($this->kernel);
		$init->initialize($app);

		// The client was requested
		if(array_key_exists(2, $chunks) && $chunks[2] === 'client')
		{
			require_once __DIR__ . '/../api/' . $apiVersion . '/engine/client.php';
			$applicationClass = 'APIClient';
		}
		// The regular API was requested
		else
		{
			$applicationClass = 'API';
		}

		return $applicationClass;
	}

	/**
	 * @return string The name of the application class we need to instantiate.
	 */
	protected function initializeInstaller()
	{
		session_start();

		// set a default timezone if no one was set by PHP.ini
		if(ini_get('date.timezone') == '') date_default_timezone_set('Europe/Brussels');

		// require the installer class
		require_once __DIR__ . '/../install/engine/installer.php';

		// we'll be using utf-8
		header('Content-type: text/html;charset=utf8');

		return 'Installer';
	}

	/**
	 * @param string $app The name of the application to load (ex. backend_ajax)
	 * @return string The name of the application class we need to instantiate.
	 */
	protected function initializeBackend($app)
	{
		require_once __DIR__ . '/../backend/init.php';
		$init = new BackendInit($this->kernel);
		$init->initialize($app);

		switch($app)
		{
			case 'backend_ajax':
				$applicationClass = 'BackendAJAX';
				break;
			case 'backend_cronjob':
				$applicationClass = 'BackendCronjob';
				break;
			default:
				$applicationClass = 'Backend';
		}

		return $applicationClass;
	}

	/**
	 * @param string $app The name of the application to load (ex. frontend_ajax)
	 * @return string The name of the application class we need to instantiate.
	 */
	protected function initializeFrontend($app)
	{
		require_once __DIR__ . '/../frontend/init.php';
		$init = new FrontendInit($this->kernel);
		$init->initialize($app);

		return ($app === 'frontend_ajax') ? 'FrontendAJAX' : 'Frontend';
	}

	/**
	 * Retrieves the request URI from the request object
	 *
	 * @return string
	 */
	private function getQueryString()
	{
		return trim($this->request->getRequestUri(), '/');
	}

	/**
	 * Process the querystring to define the application
	 */
	private function processQueryString()
	{
		$queryString = $this->getQueryString();

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
