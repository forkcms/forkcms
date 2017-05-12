<?php

namespace ForkCMS\App;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Ajax as BackendAjax;
use Backend\Core\Engine\Backend;
use Frontend\Core\Engine\Ajax as FrontendAjax;
use Frontend\Core\Engine\Frontend;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Backend\Init as BackendInit;
use Frontend\Init as FrontendInit;
use Common\Exception\RedirectException;
use Symfony\Component\HttpFoundation\Response;
use Twig_Error;

/**
 * Application routing
 */
class ForkController extends Controller
{
    const DEFAULT_APPLICATION = 'Frontend';

    /**
     * Virtual folders mappings
     *
     * @var array
     */
    private static $routes = [
        '' => self::DEFAULT_APPLICATION,
        'private' => 'Backend',
        'Backend' => 'Backend',
        'backend' => 'Backend',
    ];

    /**
     * Get the possible routes
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * Runs the backend
     */
    public function backendController(): Response
    {
        defined('APPLICATION') || define('APPLICATION', 'Backend');
        defined('NAMED_APPLICATION') || define('NAMED_APPLICATION', 'private');

        $applicationClass = $this->initializeBackend('Backend');
        $application = new $applicationClass($this->container->get('kernel'));

        return $this->handleApplication($application);
    }

    /**
     * Runs the backend ajax requests
     */
    public function backendAjaxController(): Response
    {
        defined('APPLICATION') || define('APPLICATION', 'Backend');

        $applicationClass = $this->initializeBackend('BackendAjax');
        $application = new $applicationClass($this->container->get('kernel'));

        return $this->handleApplication($application);
    }

    /**
     * Runs the frontend requests
     */
    public function frontendController(): Response
    {
        defined('APPLICATION') || define('APPLICATION', 'Frontend');

        $applicationClass = $this->initializeFrontend('Frontend');
        $application = new $applicationClass($this->container->get('kernel'));

        return $this->handleApplication($application);
    }

    /**
     * Runs the frontend ajax requests
     */
    public function frontendAjaxController(): Response
    {
        defined('APPLICATION') || define('APPLICATION', 'Frontend');

        $applicationClass = $this->initializeFrontend('FrontendAjax');
        $application = new $applicationClass($this->container->get('kernel'));

        return $this->handleApplication($application);
    }

    /**
     * Runs an application and returns the Response
     */
    protected function handleApplication(ApplicationInterface $application): Response
    {
        $application->passContainerToModels();

        try {
            $application->initialize();

            return $application->display();
        } catch (RedirectException $ex) {
            return $ex->getResponse();
        } catch (Twig_Error $twigError) {
            if ($twigError->getPrevious() instanceof RedirectException) {
                return $twigError->getPrevious()->getResponse();
            }

            throw $twigError;
        }
    }

    /**
     * @param string $app The name of the application to load (ex. BackendAjax)
     *
     * @return string The name of the application class we need to instantiate.
     */
    protected function initializeBackend(string $app): string
    {
        $init = new BackendInit($this->container->get('kernel'));
        $init->initialize($app);

        return $app === 'BackendAjax' ? BackendAjax::class : Backend::class;
    }

    /**
     * @param string $app The name of the application to load (ex. frontend_ajax)
     *
     * @return string The name of the application class we need to instantiate.
     */
    protected function initializeFrontend(string $app): string
    {
        $init = new FrontendInit($this->container->get('kernel'));
        $init->initialize($app);

        return $app === 'FrontendAjax' ? FrontendAjax::class : Frontend::class;
    }
}
