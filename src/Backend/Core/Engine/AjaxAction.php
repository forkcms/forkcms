<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;

use Backend\Core\Engine\Model as BackendModel;

/**
 * This class is the real code, it creates an action, loads the config file, ...
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class AjaxAction extends Base\Object
{
    /**
     * The config file
     *
     * @var    Base\Config
     */
    private $config;

    /**
     * Execute the action
     * We will build the classname, require the class and call the execute method.
     *
     * @return Response
     */
    public function execute()
    {
        $this->loadConfig();

        // build action-class-name
        $actionClass = 'Backend\\Modules\\' . $this->getModule() . '\\Ajax\\' . $this->getAction();
        if ($this->getModule() == 'Core') {
            $actionClass = 'Backend\\Core\\Ajax\\' . $this->getAction();
        }

        // validate if class exists (aka has correct name)
        if (!class_exists(
            $actionClass
        )
        ) {
            throw new Exception('The actionfile is present, but the classname should be: ' . $actionClass . '.');
        }

        // create action-object
        $object = new $actionClass($this->getKernel(), $this->getAction(), $this->getModule());
        $object->setAction($this->getAction(), $this->getModule());
        $object->execute();

        return $object->getContent();
    }

    /**
     * Load the config file for the requested module.
     * In the config file we have to find disabled actions, the constructor
     * will read the folder and set possible actions
     * Other configurations will be stored in it also.
     */
    public function loadConfig()
    {
        // check if module path is not yet defined
        if (!defined('BACKEND_MODULE_PATH')) {
            // build path for core
            if ($this->getModule() == 'Core') {
                define('BACKEND_MODULE_PATH', BACKEND_PATH . '/' . $this->getModule());
            } else {
                // build path to the module and define it. This is a constant because we can use this in templates.
                define('BACKEND_MODULE_PATH', BACKEND_MODULES_PATH . '/' . $this->getModule());
            }
        }

        // check if we can load the config file
        $configClass = 'Backend\\Modules\\' . $this->getModule() . '\\Config';
        if ($this->getModule() == 'Core') {
            $configClass = 'Backend\\Core\\Config';
        }

        // validate if class exists (aka has correct name)
        if (!class_exists(
            $configClass
        )
        ) {
            throw new Exception('The config file is present, but the classname should be: ' . $configClassName . '.');
        }

        // create config-object, the constructor will do some magic
        $this->config = new $configClass($this->getKernel(), $this->getModule());

        // set action
        $action = ($this->config->getDefaultAction() !== null) ? $this->config->getDefaultAction() : 'Index';
    }
}
