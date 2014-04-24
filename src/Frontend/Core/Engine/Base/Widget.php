<?php

namespace Frontend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\KernelInterface;

use Frontend\Core\Engine\Header;
use Frontend\Core\Engine\Template as FrontendTemplate;
use Frontend\Core\Engine\Url;

/**
 * This class implements a lot of functionality that can be extended by a specific widget
 * @later  Check which methods are the same in FrontendBaseBlock, maybe we should extend from a general class
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Widget extends Object
{
    /**
     * The current action
     *
     * @var    string
     */
    protected $action;

    /**
     * The data
     *
     * @var    mixed
     */
    protected $data;

    /**
     * The header object
     *
     * @var    Header
     */
    protected $header;

    /**
     * The current module
     *
     * @var    string
     */
    protected $module;

    /**
     * Path to the template
     *
     * @var    string
     */
    protected $templatePath;

    /**
     * A reference to the current template
     *
     * @var    FrontendTemplate
     */
    public $tpl;

    /**
     * A reference to the URL-instance
     *
     * @var    Url
     */
    public $URL;

    /**
     * @param KernelInterface $kernel
     * @param string          $module The module to use.
     * @param string          $action The action to use.
     * @param string          $data   The data that should be available.
     */
    public function __construct(KernelInterface $kernel, $module, $action, $data = null)
    {
        parent::__construct($kernel);

        // get objects from the reference so they are accessible
        $this->tpl = new FrontendTemplate(false);
        $this->header = $this->getContainer()->get('header');
        $this->URL = $this->getContainer()->get('url');

        // set properties
        $this->setModule($module);
        $this->setAction($action);
        $this->setData($data);
    }

    /**
     * Add a CSS file into the array
     *
     * @param string $file          The path for the CSS-file that should be loaded.
     * @param bool   $overwritePath Whether or not to add the module to this path. Module path is added by default.
     * @param bool   $minify        Should the CSS be minified?
     * @param bool   $addTimestamp  May we add a timestamp for caching purposes?
     */
    public function addCSS($file, $overwritePath = false, $minify = true, $addTimestamp = null)
    {
        // redefine
        $file = (string) $file;
        $overwritePath = (bool) $overwritePath;

        // use module path
        if (!$overwritePath) {
            $file = '/src/Frontend/Modules/' . $this->getModule() . '/Layout/Css/' . $file;
        }

        // add css to the header
        $this->header->addCSS($file, $minify, $addTimestamp);
    }

    /**
     * Add a javascript file into the array
     *
     * @param string $file          The path to the javascript-file that should be loaded.
     * @param bool   $overwritePath Whether or not to add the module to this path. Module path is added by default.
     * @param bool   $minify        Should the file be minified?
     */
    public function addJS($file, $overwritePath = false, $minify = true)
    {
        $file = (string) $file;
        $overwritePath = (bool) $overwritePath;

        // use module path
        if (!$overwritePath) {
            $file = '/src/Frontend/Modules/' . $this->getModule() . '/Js/' . $file;
        }

        // add js to the header
        $this->header->addJS($file, $minify);
    }

    /**
     * Add data that should be available in JS
     *
     * @param string $key   The key whereunder the value will be stored.
     * @param mixed  $value The value to pass.
     */
    public function addJSData($key, $value)
    {
        $this->header->addJSData($this->getModule(), $key, $value);
    }

    /**
     * Execute the action
     * If a javascript file with the name of the module or action exists it will be loaded.
     */
    public function execute()
    {
        // build path to the module
        $frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

        // build URL to the module
        $frontendModuleURL = '/src/Frontend/Modules/' . $this->getModule() . '/Js';

        // add javascript file with same name as module (if the file exists)
        if (is_file($frontendModulePath . '/Js/' . $this->getModule() . '.js')) {
            $this->header->addJS($frontendModuleURL . '/' . $this->getModule() . '.js', false);
        }

        // add javascript file with same name as the action (if the file exists)
        if (is_file($frontendModulePath . '/Js/' . $this->getAction() . '.js')) {
            $this->header->addJS($frontendModuleURL . '/' . $this->getAction() . '.js', false);
        }
    }

    /**
     * Get the action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get parsed template content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->tpl->getContent($this->templatePath, false, true);
    }

    /**
     * Get the module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->tpl;
    }

    /**
     * Load the template
     *
     * @param string $path The path for the template to use.
     */
    protected function loadTemplate($path = null)
    {
        // no template given, so we should build the path
        if ($path === null) {
            // build path to the module
            $frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

            // build template path
            $path = $frontendModulePath . '/Layout/Widgets/' . $this->getAction() . '.tpl';
        } else {
            // redefine
            $path = (string) $path;
        }

        // set template
        $this->setTemplatePath($path);
    }

    /**
     * Set the action, for later use
     *
     * @param string $action The action to use.
     */
    private function setAction($action)
    {
        $this->action = (string) $action;
    }

    /**
     * Set the data, for later use
     *
     * @param string $data The data that should available.
     */
    private function setData($data = null)
    {
        // data given?
        if ($data !== null) {
            // unserialize data
            $data = unserialize($data);

            // store
            $this->data = $data;
        }
    }

    /**
     * Set the module, for later use
     *
     * @param string $module The module to use.
     */
    private function setModule($module)
    {
        $this->module = (string) $module;
    }

    /**
     * Set the path for the template to include or to replace the current one
     *
     * @param string $path The path to the template that should be loaded.
     */
    protected function setTemplatePath($path)
    {
        $this->templatePath = (string) $path;
    }
}
