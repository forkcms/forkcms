<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class ApplicationRouting extends Controller
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
        'backend' => 'Backend',
        'api' => 'Api',
        'install' => 'Install'
    );

    public function __construct()
    {
        // this class is used in most Fork applications to bubble down the Kernel object
        require_once __DIR__ . '/ApplicationInterface.php';
        require_once __DIR__ . '/KernelLoader.php';
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
     * Runs the backend
     *
     * @param Request $request
     * @param string  $module
     * @param string  $action
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function backendController(Request $request, $module, $action)
    {
        define('APPLICATION', 'Backend');
        define('NAMED_APPLICATION', 'private');

        $applicationClass = $this->initializeBackend('Backend');
        $application = new $applicationClass($this->container->get('kernel'));
        return $this->handleApplication($application);
    }

    /**
     * Runs the backend ajax requests
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function backendAjaxController(Request $request)
    {
        define('APPLICATION', 'Backend');

        $applicationClass = $this->initializeBackend('BackendAjax');
        $application = new $applicationClass($this->container->get('kernel'));
        return $this->handleApplication($application);
    }

    /**
     * Runs the cronjobs
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function backendCronjobController(Request $request)
    {
        define('APPLICATION', 'Backend');

        $applicationClass = $this->initializeBackend('BackendCronjob');
        $application = new $applicationClass($this->container->get('kernel'));
        return $this->handleApplication($application);
    }

    /**
     * Runs the frontend requests
     *
     * @param Request $request
     * @param string  $route
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function frontendController(Request $request, $route)
    {
        define('APPLICATION', 'Frontend');

        $applicationClass = $this->initializeFrontend('Frontend');
        $application = new $applicationClass($this->container->get('kernel'));
        return $this->handleApplication($application);
    }

    /**
     * Runs the frontend ajax requests
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function frontendAjaxController(Request $request)
    {
        define('APPLICATION', 'Frontend');

        $applicationClass = $this->initializeFrontend('FrontendAjax');
        $application = new $applicationClass($this->container->get('kernel'));
        return $this->handleApplication($application);
    }

    /**
     * Runs the install requests
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function installController(Request $request)
    {
        // if we're able to run the installer, install it
        if (file_exists(__DIR__ . '/../src/Install')) {
            define('APPLICATION', 'Install');

            $applicationClass = $this->initializeInstaller('Install');
            $application = new $applicationClass($this->container->get('kernel'));
            return $this->handleApplication($application);
        }

        // fallback to default frontend request
        return $this->frontendController($request);
    }

    /**
     * Runs the api requests
     *
     * @param Request $request
     * @param string  $version
     * @param string  $client
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function apiController(Request $request, $version, $client)
    {
        define('APPLICATION', 'Api');

        $applicationClass = $this->initializeAPI('Api', $request);
        $application = new $applicationClass($this->container->get('kernel'));
        return $this->handleApplication($application);
    }

    /**
     * Runs an application and returns the Response
     *
     * @param \ApplicationInterface $application
     * @return Symfony\Component\HttpFoundation\Response
     */
    protected function handleApplication(\ApplicationInterface $application)
    {
        $application->passContainerToModels();
        $application->initialize();

        return $application->display();
    }

    /**
     * @param string $app The name of the application to load (ex. BackendAjax)
     * @return string The name of the application class we need to instantiate.
     */
    protected function initializeAPI($app, $request)
    {
        $queryString = trim($request->getRequestUri(), '/');
        $chunks = explode('/', $queryString);
        $apiVersion = (array_key_exists(1, $chunks)) ? $chunks[1] : 'v1';
        $apiVersion = strtok($apiVersion, '?');
        $apiClass = 'Api\\' . SpoonFilter::ucfirst($apiVersion) . '\\Init';

        // validate
        if (!class_exists($apiClass)) {
            throw new Exception('This version of the API does not exists.');
        }

        $init = new $apiClass($this->container->get('kernel'));
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

        return 'Install\Engine\Installer';
    }

    /**
     * @param string $app The name of the application to load (ex. BackendAjax)
     * @return string The name of the application class we need to instantiate.
     */
    protected function initializeBackend($app)
    {
        $init = new BackendInit($this->container->get('kernel'));
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
        $init = new FrontendInit($this->container->get('kernel'));
        $init->initialize($app);

        return ($app === 'FrontendAjax') ? 'Frontend\Core\Engine\Ajax' : 'Frontend\Core\Engine\Frontend';
    }
}
