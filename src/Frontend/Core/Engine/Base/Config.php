<?php

namespace Frontend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Route;

use Frontend\Core\Engine\Language as FL;

/**
 * This is the base-object for config-files.
 * The module-specific config-files can extend the functionality from this class.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Config extends \KernelLoader
{
    /**
     * The default action
     *
     * @var    string
     */
    protected $defaultAction = 'index';

    /**
     * The disabled actions
     *
     * @var    array
     */
    protected $disabledActions = array();

    /**
     * The disabled AJAX-actions
     *
     * @var    array
     */
    protected $disabledAJAXActions = array();

    /**
     * The current loaded module
     *
     * @var    string
     */
    protected $module;

    /**
     * Module router
     *
     * @var Router
     */
    protected $router;

    /**
     * All the possible actions
     *
     * @var    array
     */
    protected $possibleActions = array();

    /**
     * All the possible AJAX actions
     *
     * @var    array
     */
    protected $possibleAJAXActions = array();

    /**
     * @param KernelInterface $kernel
     * @param string          $module The module wherefore this is the configuration-file.
     */
    public function __construct(KernelInterface $kernel, $module)
    {
        parent::__construct($kernel);

        $this->module = (string) $module;

        // read the possible actions based on the files
        $this->setPossibleActions();

        // lets load specific module router
        $this->loadRouter();
    }

    /**
     * Get the default action
     *
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     * Get the current loaded module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Get the possible actions
     *
     * @return array
     */
    public function getPossibleActions()
    {
        return $this->possibleActions;
    }

    /**
     * Get the possible AJAX actions
     *
     * @return array
     */
    public function getPossibleAJAXActions()
    {
        return $this->possibleAJAXActions;
    }

    /**
     * Set the possible actions, based on files in folder.
     * You can disable action in the config file. (Populate $disabledActions)
     */
    protected function setPossibleActions()
    {
        // build path to the module
        $frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();
        $fs = new Filesystem();

        if ($fs->exists($frontendModulePath . '/Actions')) {
            // get regular actions
            $finder = new Finder();
            $finder->name('*.php');
            foreach ($finder->files()->in($frontendModulePath . '/Actions') as $file) {
                /** @var $file \SplFileInfo */
                $action = $file->getBasename('.php');
                if (!in_array($action, $this->disabledActions)) {
                    $this->possibleActions[$file->getBasename()] = $action;
                }
            }
        }

        if ($fs->exists($frontendModulePath . '/Ajax')) {
            // get ajax-actions
            $finder = new Finder();
            $finder->name('*.php');
            foreach ($finder->files()->in($frontendModulePath . '/Ajax') as $file) {
                /** @var $file \SplFileInfo */
                $action = $file->getBasename('.php');
                if (!in_array($action, $this->disabledAJAXActions)) {
                    $this->possibleAJAXActions[$file->getBasename()] = $action;
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function hasRouter()
    {
        return null !== $this->router;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Load module router
     */
    public function loadRouter()
    {
        $file = FRONTEND_MODULES_PATH . '/' . $this->module . '/Resources/config/routing.yml';
        $fs = new Filesystem();

        // if there is no routing file we have nothing to set
        if ($fs->exists($file)) {
            $actions = FL::getActions();
            $this->router = new Router(new YamlFileLoader(new FileLocator()), $file);

            /**
             * set a requirement for translated _action parameter
             *
             * @var Route $route
             */
            foreach ($this->router->getRouteCollection()->getIterator() as $routeName => $route) {
                $action = \SpoonFilter::toCamelCase($route->getDefault('_action'));
                $actionLocale = isset($actions[$action]) ? $actions[$action] : $action;
                $actionLocale = ltrim(strtolower(preg_replace('/[A-Z]/', '-$0', $actionLocale)), '-');
                $route->setRequirement('_action', $actionLocale);
            }
        }
    }
}
