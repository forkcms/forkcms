<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Config;
use Backend\Core\Engine\Base\AjaxAction as BaseAjaxAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class is the real code, it creates an action, loads the config file, ...
 */
class AjaxAction extends Base\Object
{
    /**
     * The config file
     *
     * @var Base\Config
     */
    private $config;

    /**
     * @var BaseAjaxAction
     */
    private $ajaxAction;

    /**
     * @return Response
     */
    public function display(): Response
    {
        $this->execute();

        return $this->ajaxAction->getContent();
    }

    /**
     * @param KernelInterface $kernel
     * @param string $action The action to use.
     * @param string $module The module to use.
     */
    public function __construct(KernelInterface $kernel, string $action, string $module)
    {
        parent::__construct($kernel);

        // store the current module and action (we grab them from the URL)
        $this->setModule($module);
        $this->setAction($action);
    }

    /**
     * Execute the action
     * We will build the classname, require the class and call the execute method.
     */
    private function execute()
    {
        $this->loadConfig();

        $actionClass = 'Backend\\Modules\\' . $this->getModule() . '\\Ajax\\' . $this->getAction();
        if ($this->getModule() === 'Core') {
            $actionClass = 'Backend\\Core\\Ajax\\' . $this->getAction();
        }

        if (!class_exists($actionClass)) {
            throw new Exception('The class ' . $actionClass . ' could not be found.');
        }

        // create action-object
        $this->ajaxAction = new $actionClass($this->getKernel());
        $this->ajaxAction->setAction($this->getAction(), $this->getModule());
        $this->ajaxAction->execute();
    }

    /**
     * Load the config file for the requested module.
     * In the config file we have to find disabled actions, the constructor
     * will read the folder and set possible actions
     * Other configurations will be stored in it also.
     */
    private function loadConfig()
    {
        // check if we can load the config file
        $configClass = 'Backend\\Modules\\' . $this->getModule() . '\\Config';
        if ($this->getModule() === 'Core') {
            $configClass = Config::class;
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($configClass)) {
            throw new Exception('The config file ' . $configClass . ' could not be found.');
        }

        // create config-object, the constructor will do some magic
        $this->config = new $configClass($this->getKernel(), $this->getModule());
    }
}
