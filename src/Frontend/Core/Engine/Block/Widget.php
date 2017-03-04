<?php

namespace Frontend\Core\Engine\Block;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\TwigTemplate;
use Symfony\Component\HttpKernel\KernelInterface;
use Frontend\Core\Engine\Base\Config;
use Frontend\Core\Engine\Base\Object as FrontendBaseObject;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Exception as FrontendException;

/**
 * This class will handle all stuff related to widgets
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
     * @var Config
     */
    private $config;

    /**
     * The data that was passed by the extra
     *
     * @var mixed
     */
    private $data;

    /**
     * The current module
     *
     * @var string
     */
    private $module;

    /**
     * The extra object
     *
     * @var FrontendBaseWidget
     */
    private $object;

    /**
     * The block's output
     *
     * @var string
     */
    private $output;

    /**
     * @param KernelInterface $kernel
     * @param string          $module The module to load.
     * @param string          $action The action to load.
     * @param mixed           $data   The data that was passed from the database.
     */
    public function __construct(KernelInterface $kernel, string $module, string $action, $data = null)
    {
        parent::__construct($kernel);

        // set properties
        $this->setModule($module);
        $this->setAction($action);
        $this->setData($data);

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
        if ($this->getModule() === 'Core') {
            $actionClass = 'Frontend\\Core\\Widgets\\' . $this->getAction();
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($actionClass)) {
            throw new FrontendException('The action file ' . $actionClass . ' could not be found.');
        }
        // create action-object
        $this->object = new $actionClass($this->getKernel(), $this->getModule(), $this->getAction(), $this->getData());

        // validate if the execute-method is callable
        if (!is_callable(array($this->object, 'execute'))) {
            throw new FrontendException('The action file should contain a callable method "execute".');
        }

        // call the execute method of the real action (defined in the module)
        $this->object->execute();
        $this->output = $this->render($this->getCustomTemplate());
    }

    /**
     * Get the current action
     * REMARK: You should not use this method from your code, but it has to be
     * public so we can access it later on in the core-code
     *
     * @return string
     */
    public function getAction(): string
    {
        // no action specified?
        if ($this->action === null) {
            $this->setAction($this->config->getDefaultAction());
        }

        // return action
        return $this->action;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->output;
    }

    /**
     * Get the block content
     *
     * @param string $template
     *
     * @return string
     */
    public function render(string $template = null): string
    {
        // set path to template if the widget didn't return any data
        if ($this->output === null) {
            return trim($this->object->getContent($template));
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
     * @return string|null
     */
    public function getCustomTemplate()
    {
        $data = @unserialize($this->data);
        if (is_array($data) && array_key_exists('custom_template', $data)) {
            return $this->module . '/Layout/Widgets/' . $data['custom_template'];
        }
    }

    /**
     * Get the current module
     * REMARK: You should not use this method from your code, but it has to be
     * public so we can access it later on in the core-code
     *
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * Get the assigned template.
     *
     * @return TwigTemplate
     */
    public function getTemplate(): TwigTemplate
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
        if ($this->getModule() === 'Core') {
            $configClass = 'Frontend\\Core\\Config';
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($configClass)) {
            throw new FrontendException('The config file ' . $configClass . ' could not be found.');
        }

        // create config-object, the constructor will do some magic
        $this->config = new $configClass($this->getKernel(), $this->getModule());
    }

    /**
     * Set the action
     *
     * @param string $action The action to load.
     */
    private function setAction(string $action = null)
    {
        $this->action = $action;
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
    private function setModule(string $module)
    {
        $this->module = $module;
    }

    /**
     * @param KernelInterface $kernel
     * @param string $module The module to load.
     * @param string $action The action to load.
     * @param int|null $id This is not the modules_extra id but the id of the item itself
     *
     * @return string|null if we have data it is still serialised since it will be unserialized in the constructor
     */
    public static function getForId(KernelInterface $kernel, string $module, string $action, int $id = null)
    {
        $query = 'SELECT data FROM modules_extras WHERE type = :widget AND module = :module AND action = :action';
        $parameters = [
            'widget' => 'widget',
            'module' => $module,
            'action' => $action,
        ];
        if (is_numeric($id)) {
            $query .= ' AND data LIKE :data';
            $parameters['data'] = '%s:2:"id";i:' . $id . ';%';
        }

        return new self(
            $kernel,
            $module,
            $action,
            $kernel->getContainer()->get('database')->getVar($query, $parameters)
        );
    }
}
