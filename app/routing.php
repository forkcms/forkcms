<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

use Backend\Init as BackendInit;

use Frontend\Init as FrontendInit;

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
    const DEFAULT_APPLICATION = 'Frontend';

    /**
     * Virtual folders mappings
     *
     * @var    array
     */
    private static $routes = array(
        '' => self::DEFAULT_APPLICATION,
        'private' => 'Backend',
        'Backend' => 'Backend',
        'api' => 'Api',
        'install' => 'Install'
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
     * @param Kernel  $kernel
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
        if (strpos($this->request->getRequestUri(), 'Ajax.php') !== false) {
            $applicationName .= 'Ajax';
        } elseif (strpos($this->request->getRequestUri(), 'Cronjob.php') !== false) {
            $applicationName .= 'Cronjob';
        }

        // Pave the way for the application we'll need to load.
        // This initializes basic functionality and retrieves the correct class to instantiate.
        switch ($applicationName) {
            case 'Frontend':
            case 'FrontendAjax':
                $applicationClass = $this->initializeFrontend($applicationName);
                break;
            case 'Backend':
            case 'BackendAjax':
            case 'BackendCronjob':
                $applicationClass = $this->initializeBackend($applicationName);
                break;
            case 'Api':
                $applicationClass = $this->initializeAPI($applicationName);
                break;
            case 'Install':
                // install directory might be deleted after install, handle it as a normal frontend request
                if (file_exists(__DIR__ . '/../src/Install')) {
                    $applicationClass = $this->initializeInstaller();
                } else {
                    $applicationClass = 'Frontend';
                }
                break;
            default:
                throw new Exception('Unknown application. (' . $applicationName . ')');
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
     * @param string $app The name of the application to load (ex. BackendAjax)
     * @return string The name of the application class we need to instantiate.
     */
    protected function initializeAPI($app)
    {
        $queryString = $this->getQueryString();
        $chunks = explode('/', $queryString);
        $apiVersion = (array_key_exists(1, $chunks)) ? $chunks[1] : 'v1';
        $apiVersion = strtok($apiVersion, '?');
        $apiClass = 'Api\\' . SpoonFilter::ucfirst($apiVersion) . '\\Init';

        // validate
        if (!class_exists($apiClass)) {
            throw new Exception('This version of the API does not exists.');
        }

        $init = new $apiClass($this->kernel);
        $init->initialize($app);

        // The client was requested
        if (array_key_exists(2, $chunks) && $chunks[2] === 'client') {
            $applicationClass = 'Api\\' . SpoonFilter::ucfirst($apiVersion) . '\\Engine\\Client';
        } else {
            // The regular API was requested
            $applicationClass = 'Api\\' . SpoonFilter::ucfirst($apiVersion) . '\\Engine\\Api';
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
        if (ini_get('date.timezone') == '') {
            date_default_timezone_set('Europe/Brussels');
        }

        // we'll be using utf-8
        header('Content-type: text/html;charset=utf8');

        return 'Install\Engine\Installer';
    }

    /**
     * @param string $app The name of the application to load (ex. BackendAjax)
     * @return string The name of the application class we need to instantiate.
     */
    protected function initializeBackend($app)
    {
        $init = new BackendInit($this->kernel);
        $init->initialize($app);

        switch ($app) {
            case 'BackendAjax':
                $applicationClass = 'Backend\Core\Engine\Ajax';
                break;
            case 'BackendCronjob':
                $applicationClass = 'Backend\Core\Engine\Cronjob';
                break;
            default:
                $applicationClass = 'Backend\Core\Engine\Backend';
        }

        return $applicationClass;
    }

    /**
     * @param string $app The name of the application to load (ex. frontend_ajax)
     * @return string The name of the application class we need to instantiate.
     */
    protected function initializeFrontend($app)
    {
        $init = new FrontendInit($this->kernel);
        $init->initialize($app);

        return ($app === 'FrontendAjax') ? 'Frontend\Core\Engine\Ajax' : 'Frontend\Core\Engine\Frontend';
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
     * Process the query string to define the application
     */
    private function processQueryString()
    {
        $queryString = $this->getQueryString();

        // split into chunks
        $chunks = explode('/', $queryString);

        // remove the src part if necessary. This is needed for backend ajax/cronjobs
        if(isset($chunks[0]) && $chunks[0] == 'src')
        {
            unset($chunks[0]);
            $chunks = array_values($chunks);
        }

        // is there a application specified
        if (isset($chunks[0])) {
            // cleanup
            $proposedApplication = (string) $chunks[0];
            $proposedApplication = strtok($proposedApplication, '?');

            // set real application
            if (isset(self::$routes[$proposedApplication])) {
                $application = self::$routes[$proposedApplication];
            } else {
                $application = self::DEFAULT_APPLICATION;
            }
        } else {
            // no application
            $application = self::DEFAULT_APPLICATION;
            $proposedApplication = $application;
        }

        // define APP
        if (!defined('APPLICATION')) {
            define('APPLICATION', $application);
        }
        if (!defined('NAMED_APPLICATION')) {
            define('NAMED_APPLICATION', $proposedApplication);
        }
    }
}
