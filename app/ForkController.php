<?php

namespace ForkCMS\App;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Backend\Init as BackendInit;
use Frontend\Init as FrontendInit;
use Common\Exception\RedirectException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Application routing
 */
class ForkController extends Controller
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
    );

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
     * @return Response
     */
    public function backendController()
    {
        defined('APPLICATION') || define('APPLICATION', 'Backend');
        defined('NAMED_APPLICATION') || define('NAMED_APPLICATION', 'private');

        $applicationClass = $this->initializeBackend('Backend');
        $application = new $applicationClass($this->container->get('kernel'));

        return $this->handleApplication($application);
    }

    /**
     * Runs the backend ajax requests
     *
     * @return Response
     */
    public function backendAjaxController()
    {
        defined('APPLICATION') || define('APPLICATION', 'Backend');

        $applicationClass = $this->initializeBackend('BackendAjax');
        $application = new $applicationClass($this->container->get('kernel'));

        return $this->handleApplication($application);
    }

    /**
     * Runs the cronjobs
     *
     * @return Response
     */
    public function backendCronjobController()
    {
        defined('APPLICATION') || define('APPLICATION', 'Backend');

        $applicationClass = $this->initializeBackend('BackendCronjob');
        $application = new $applicationClass($this->container->get('kernel'));

        return $this->handleApplication($application);
    }

    /**
     * Runs the frontend requests
     *
     * @return Response
     */
    public function frontendController()
    {
        defined('APPLICATION') || define('APPLICATION', 'Frontend');

        $applicationClass = $this->initializeFrontend('Frontend');
        $application = new $applicationClass($this->container->get('kernel'));

        return $this->handleApplication($application);
    }

    /**
     * Runs the frontend ajax requests
     *
     * @return Response
     */
    public function frontendAjaxController()
    {
        defined('APPLICATION') || define('APPLICATION', 'Frontend');

        $applicationClass = $this->initializeFrontend('FrontendAjax');
        $application = new $applicationClass($this->container->get('kernel'));

        return $this->handleApplication($application);
    }

    /**
     * Runs an application and returns the Response
     *
     * @param ApplicationInterface $application
     *
     * @return Response
     */
    protected function handleApplication(ApplicationInterface $application)
    {
        $application->passContainerToModels();

        try {
            $application->initialize();

            return $application->display();
        } catch (RedirectException $ex) {
            return $ex->getResponse();
        }
    }

    /**
     * @param string $app The name of the application to load (ex. BackendAjax)
     *
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
     *
     * @return string The name of the application class we need to instantiate.
     */
    protected function initializeFrontend($app)
    {
        $init = new FrontendInit($this->container->get('kernel'));
        $init->initialize($app);

        return ($app === 'FrontendAjax') ? 'Frontend\Core\Engine\Ajax' : 'Frontend\Core\Engine\Frontend';
    }
}
