<?php

namespace Frontend\Core\Engine\Block;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\KernelInterface;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Base\Config;
use Frontend\Core\Engine\Base\Object as FrontendBaseObject;
use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Theme as FrontendTheme;

/**
 * This class will handle all stuff related to blocks
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class Extra extends FrontendBaseObject
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
     * @var    FrontendBaseBlock
     */
    private $object;

    /**
     * The block's output
     *
     * @var    string
     */
    private $output;

    /**
     * Should the template overwrite the current one
     *
     * @var    bool
     */
    protected $overwrite = false;

    /**
     * The path for the template
     *
     * @var    string
     */
    protected $templatePath = '';

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

        // is the requested action possible? If not we throw an exception.
        // We don't redirect because that could trigger a redirect loop
        if (!in_array($this->getAction(), $this->config->getPossibleActions())) {
            $this->setAction(
                $this->config->getDefaultAction()
            );
        }
    }

    /**
     * Execute the action
     * We will build the class name, require the class and call the execute method.
     */
    public function execute()
    {
        // build action-class-name
        $actionClass = 'Frontend\\Modules\\' . $this->getModule() . '\\Actions\\' . $this->getAction();
        if ($this->getModule() == 'Core') {
            $actionClass = 'Frontend\\Core\\Actions\\' . $this->getAction();
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($actionClass)) {
            throw new FrontendException(
                'The action file is present, but the class name should be: ' . $actionClassName . '.'
            );
        }

        // create action-object
        $this->object = new $actionClass($this->getKernel(), $this->getModule(), $this->getAction(), $this->getData());

        // validate if the execute-method is callable
        if (!is_callable(
            array($this->object, 'execute')
        )
        ) {
            throw new FrontendException('The action file should contain a callable method "execute".');
        }

        // call the execute method of the real action (defined in the module)
        $this->object->execute();

        // set some properties
        $this->setOverwrite($this->object->getOverwrite());
        if ($this->object->getTemplatePath() !== null) {
            $this->setTemplatePath($this->object->getTemplatePath());
        }
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
            // get first parameter
            $actionParameter = $this->URL->getParameter(0);

            // unknown action and not provided in URL
            if ($actionParameter === null) {
                $this->setAction($this->config->getDefaultAction());
            } else {
                // action provided in the URL
                // loop possible actions
                $actionParameter = \SpoonFilter::toCamelCase($actionParameter);
                foreach ($this->config->getPossibleActions() as $actionName) {
                    // get action that should be passed as parameter
                    $actionURL = \SpoonFilter::toCamelCase(urlencode(FL::act(\SpoonFilter::toCamelCase($actionName))));

                    // the action is the requested one
                    if ($actionURL == $actionParameter) {
                        // set action
                        $this->setAction($actionName);

                        // stop the loop
                        break;
                    }
                }
            }
        }

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
     * Get overwrite mode
     *
     * @return bool
     */
    public function getOverwrite()
    {
        return $this->overwrite;
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
     * Get path for the template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Get the assigned variables for this block.
     *
     * @return array
     */
    public function getVariables()
    {
        return (array) $this->tpl->getAssignedVariables();
    }

    /**
     * Load the config file for the requested block.
     * In the config file we have to find disabled actions, the constructor
     * will read the folder and set possible actions
     * Other configurations will also be stored in it.
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

    /**
     * Set overwrite mode
     *
     * @param bool $overwrite Should the template overwrite the already loaded template.
     */
    private function setOverwrite($overwrite)
    {
        $this->overwrite = (bool) $overwrite;
    }

    /**
     * Set the path for the template
     *
     * @param string $path The path to set.
     */
    private function setTemplatePath($path)
    {
        $this->templatePath = FrontendTheme::getPath($path);
    }
}
