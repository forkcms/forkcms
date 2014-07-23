<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

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
        $routes = new RouteCollection();
        $routes->add('backend', new Route(
            '/private/{_locale}/{module}/{action}',
            array(
                '_controller' => 'ApplicationRouting::backendController',
                '_locale'     => null,
                'module'      => null,
                'action'      => null,
            )
        ));
        $routes->add('backend_ajax', new Route(
            '/src/Backend/Ajax.php',
            array(
                '_controller' => 'ApplicationRouting::backendAjaxController',
            )
        ));
        $routes->add('backend_cronjob', new Route(
            '/src/Backend/Cronjob.php',
            array(
                '_controller' => 'ApplicationRouting::backendCronjobController',
            )
        ));
        $routes->add('frontend_ajax', new Route(
            '/src/Frontend/Ajax.php',
            array(
                '_controller' => 'ApplicationRouting::frontendAjaxController',
            )
        ));
        $routes->add('install', new Route(
            '/install',
            array(
                '_controller' => 'ApplicationRouting::installController',
            )
        ));
        $routes->add('api', new Route(
            '/api',
            array(
                '_controller' => 'ApplicationRouting::apiController',
            )
        ));
        $routes->add('frontend', new Route(
            '/{route}',
            array(
                '_controller' => 'ApplicationRouting::frontendController',
                'route'       => null,
            ),
            array(
                'route' => '(.*)'
            )
        ));

        $context = new RequestContext();
        $context->fromRequest($this->request);
        $matcher = new UrlMatcher($routes, $context);

        // call the given controller when it matches
        $attributes = $matcher->match($this->request->getPathInfo());
        return call_user_func($attributes['_controller'], $attributes);
    }

    /**
     * Runs the backend
     *
     * @param array $attributes
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function backendController($attributes)
    {
        define('APPLICATION', 'Backend');
        define('NAMED_APPLICATION', 'private');

        $applicationClass = $this->initializeBackend('Backend');
        $application = new $applicationClass($this->kernel);
        return $this->handleApplication($application);
    }

    /**
     * Runs the backend ajax requests
     *
     * @param array $attributes
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function backendAjaxController($attributes)
    {
        define('APPLICATION', 'Backend');

        $applicationClass = $this->initializeBackend('BackendAjax');
        $application = new $applicationClass($this->kernel);
        return $this->handleApplication($application);
    }

    /**
     * Runs the cronjobs
     *
     * @param array $attributes
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function backendCronjobController($attributes)
    {
        define('APPLICATION', 'Backend');

        $applicationClass = $this->initializeBackend('BackendCronjob');
        $application = new $applicationClass($this->kernel);
        return $this->handleApplication($application);
    }

    /**
     * Runs the frontend requests
     *
     * @param array $attributes
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function frontendController($attributes)
    {
        define('APPLICATION', 'Frontend');

        $applicationClass = $this->initializeFrontend('Frontend');
        $application = new $applicationClass($this->kernel);
        return $this->handleApplication($application);
    }

    /**
     * Runs the frontend ajax requests
     *
     * @param array $attributes
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function frontendAjaxController($attributes)
    {
        define('APPLICATION', 'Frontend');

        $applicationClass = $this->initializeFrontend('FrontendAjax');
        $application = new $applicationClass($this->kernel);
        return $this->handleApplication($application);
    }

    /**
     * Runs the install requests
     *
     * @param array $attributes
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function installController($attributes)
    {
        // if we're able to run the installer, install it
        if (file_exists(__DIR__ . '/../src/Install')) {
            define('APPLICATION', 'Install');

            $applicationClass = $this->initializeInstaller('Install');
            $application = new $applicationClass($this->kernel);
            return $this->handleApplication($application);
        }

        // fallback to default frontend request
        return $this->frontendController($attributes);
    }

    /**
     * Runs the api requests
     *
     * @param array $attributes
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function apiController($attributes)
    {
        define('APPLICATION', 'Api');

        $applicationClass = $this->initializeAPI('Api');
        $application = new $applicationClass($this->kernel);
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
}
