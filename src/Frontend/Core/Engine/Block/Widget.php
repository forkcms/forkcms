<?php

namespace Frontend\Core\Engine\Block;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\KernelInterface;

use Frontend\Core\Engine\Base\Config;
use Frontend\Core\Engine\Base\Object as FrontendBaseObject;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Exception as FrontendException;

/**
 * This class will handle all stuff related to widgets
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Widget extends FrontendBaseObject
{
    /**
     * The current action
     *
     * @var string
     */
    private $action;

    /**
     * The config file
     *
     * @var    Config
     */
    private $config;

    /**
     * The data that was passed by the extra
     *
     * @var    mixed
     */
    private $data;

    /**
     * The current module
     *
     * @var    string
     */
    private $module;

    /**
     * The extra object
     *
     * @var    FrontendBaseWidget
     */
    private $object;

    /**
     * The block's output
     *
     * @var    string
     */
    private $output;

    /**
     * @param KernelInterface $kernel
     * @param string          $module The module to load.
     * @param string          $action The action to load.
     * @param mixed           $data   The data that was passed from the database.
     */
    public function __construct(KernelInterface $kernel, $module, $action, $data = null)
    {
        parent::__construct($kernel);

        // set properties
        $this->setModule($module);
        $this->setAction($action);
        if ($data !== null) {
            $this->setData($data);
        }

        // load the config file for the required module
        $this->loadConfig();
    }

    /**
     * Execute the action
     * We will build the class name, require the class and call the execute method.
     */
    public function execute()
    {
        // build action-class-name
        $actionClass = 'Frontend\\Modules\\' . $this->getModule() . '\\Widgets\\' . $this->getAction();
        if ($this->getModule() == 'Core') {
            $actionClass = 'Frontend\\Core\\Widgets\\' . $this->getAction();
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($actionClass)) {
            throw new FrontendException(
                'The action file is present, but the class name should be: ' .
                $actionClass . '.'
            );
        }
        // create action-object
        $this->object = new $actionClass($this->getKernel(), $this->getModule(), $this->getAction(), $this->getData());

        // validate if the execute-method is callable
        if (!is_callable(array($this->object, 'execute'))) {
            throw new FrontendException(
                'The action file should contain a callable method "execute".'
            );
        }

        // call the execute method of the real action (defined in the module)
        $this->output = $this->object->execute();
    }

    /**
     * Get the current action
     * REMARK: You should not use this method from your code, but it has to be
     * public so we can access it later on in the core-code
     *
     * @return string
     */
    public function getAction()
    {
        // no action specified?
        if ($this->action === null) {
            $this->setAction($this->config->getDefaultAction());
        }

        // return action
        return $this->action;
    }

    /**
     * Get the block content
     *
     * @return string
     */
    public function getContent()
    {
        // set path to template if the widget didn't return any data
        if ($this->output === null) {
            return trim($this->object->getContent());
        }

        // return possible output
        return trim($this->output);
    }

    /**
     * Get the data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the current module
     * REMARK: You should not use this method from your code, but it has to be
     * public so we can access it later on in the core-code
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Get the assigned template.
     *
     * @return array
     */
    public function getTemplate()
    {
        return $this->object->getTemplate();
    }

    /**
     * Load the config file for the requested block.
     * In the config file we have to find disabled actions,
     * the constructor will read the folder and set possible actions
     * Other configurations will be stored in it also.
     */
    public function loadConfig()
    {
        $configClass = 'Frontend\\Modules\\' . $this->getModule() . '\\Config';
        if ($this->getModule() == 'Core') {
            $configClass = 'Frontend\\Core\\Config';
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($configClass)) {
            throw new FrontendException(
                'The config file is present, but the class name should be: ' . $configClass . '.'
            );
        }

        // create config-object, the constructor will do some magic
        $this->config = new $configClass($this->getKernel(), $this->getModule());
    }

    /**
     * Set the action
     *
     * @param string $action The action to load.
     */
    private function setAction($action = null)
    {
        if ($action !== null) {
            $this->action = (string) $action;
        }
    }

    /**
     * Set the data
     *
     * @param mixed $data The data that should be set.
     */
    private function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Set the module
     *
     * @param string $module The module to load.
     */
    private function setModule($module)
    {
        $this->module = (string) $module;
    }
}
