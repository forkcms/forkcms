<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\KernelInterface;

use Frontend\Core\Engine\Base\AjaxAction as FrontendBaseAJAXAction;
use Frontend\Core\Engine\Exception;

/**
 * FrontendAJAXAction
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */
class AjaxAction extends FrontendBaseAJAXAction
{
    /**
     * The current action
     *
     * @var    string
     */
    protected $action;

    /**
     * The config file
     *
     * @var    Base\Config
     */
    protected $config;

    /**
     * The current module
     *
     * @var    string
     */
    protected $module;

    /**
     * @param KernelInterface $kernel
     * @param string          $action The action that should be executed.
     * @param string          $module The module that wherein the action is available.
     */
    public function __construct(KernelInterface $kernel, $action, $module)
    {
        parent::__construct($kernel, $action, $module);

        // set properties
        $this->setModule($module);
        $this->setAction($action);

        // load the config file for the required module
        $this->loadConfig();
    }

    /**
     * Execute the action.
     * We will build the class name, require the class and call the execute method
     */
    public function execute()
    {
        // build action-class-name
        $actionClass = 'Frontend\\Modules\\' . $this->getModule() . '\\Ajax\\' . $this->getAction();
        if($this->getModule() == 'Core') $actionClass = 'Frontend\\Core\\Ajax\\' . $this->getAction();

        // build the path (core is a special case)
        if ($this->getModule() == 'Core') {
            $path = FRONTEND_PATH . '/Core/Ajax/' . $this->getAction() . '.php';
        } else {
            $path = FRONTEND_PATH . '/Modules/' . $this->getModule() . '/Ajax/' . $this->getAction() . '.php';
        }

        // check if the config is present? If it isn't present there is a huge
        // problem, so we will stop our code by throwing an error
        if (!is_file($path)) {
            throw new Exception('The action file (' . $path . ') can\'t be found.');
        }

        // validate if class exists
        if (!class_exists($actionClass)) {
            throw new Exception(
                'The action file is present, but the class name should be: ' . $actionClass . '.'
            );
        }

        // create action-object
        $object = new $actionClass($this->getKernel(), $this->getAction(), $this->getModule());

        // validate if the execute-method is callable
        if (!is_callable(
            array($object, 'execute')
        )
        ) {
            throw new Exception('The action file should contain a callable method "execute".');
        }

        // call the execute method of the real action (defined in the module)
        $object->execute();

        return $object->getContent();
    }

    /**
     * Get the current action.
     * REMARK: You should not use this method from your code, but it has to be
     * public so we can access it later on in the core-code.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get the current module.
     * REMARK: You should not use this method from your code, but it has to be
     * public so we can access it later on in the core-code.
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Load the config file for the requested module.
     * In the config file we have to find disabled actions, the constructor
     * will read the folder and set possible actions.
     * Other configurations will also be stored in it.
     */
    public function loadConfig()
    {
        $configClass = 'Frontend\\Modules\\' . $this->getModule() . '\\Config';
        if($this->getModule() == 'Core') $configClass = 'Frontend\\Core\\Config';

        // validate if class exists (aka has correct name)
        if (!class_exists($configClass)) {
            throw new Exception(
                'The config file is present, but the class name should be: ' . $configClass . '.'
            );
        }

        // create config-object, the constructor will do some magic
        $this->config = new $configClass($this->getKernel(), $this->getModule());
    }

    /**
     * Set the action
     *
     * @param string $action The action that should be executed.
     */
    protected function setAction($action)
    {
        $this->action = (string) $action;
    }

    /**
     * Set the module
     *
     * @param string $module The module wherein the action is available.
     */
    protected function setModule($module)
    {
        $this->module = (string) $module;
    }
}
