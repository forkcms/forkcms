<?php

namespace Frontend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Core\Header\Priority;
use Common\Exception\RedirectException;
use Frontend\Core\Header\Header;
use Frontend\Core\Engine\TwigTemplate;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class implements a lot of functionality that can be extended by a specific widget
 *
 * @later  Check which methods are the same in FrontendBaseBlock, maybe we should extend from a general class
 */
class Widget extends Object
{
    /**
     * The current action
     *
     * @var string
     */
    protected $action;

    /**
     * The data
     *
     * @var mixed
     */
    protected $data;

    /**
     * The header object
     *
     * @var Header
     */
    protected $header;

    /**
     * The current module
     *
     * @var string
     */
    protected $module;

    /**
     * Path to the template
     *
     * @var string
     */
    public $templatePath;

    /**
     * @param KernelInterface $kernel
     * @param string $module The module to use.
     * @param string $action The action to use.
     * @param string $data The data that should be available.
     */
    public function __construct(KernelInterface $kernel, string $module, string $action, string $data = null)
    {
        parent::__construct($kernel);

        // get objects from the reference so they are accessible
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
     * @param string $file The path for the CSS-file that should be loaded.
     * @param bool $overwritePath Whether or not to add the module to this path. Module path is added by default.
     * @param bool $minify Should the CSS be minified?
     * @param bool $addTimestamp May we add a timestamp for caching purposes?
     */
    public function addCSS(string $file, bool $overwritePath = false, bool $minify = true, bool $addTimestamp = false)
    {
        if (!$overwritePath) {
            $file = '/src/Frontend/Modules/' . $this->getModule() . '/Layout/Css/' . $file;
        }

        $this->header->addCSS($file, $minify, $addTimestamp, Priority::widget());
    }

    /**
     * Add a javascript file into the array
     *
     * @param string $file The path to the javascript-file that should be loaded.
     * @param bool $overwritePath Whether or not to add the module to this path. Module path is added by default.
     * @param bool $minify Should the file be minified?
     * @param bool $addTimestamp May we add a timestamp for caching purposes?
     */
    public function addJS(string $file, bool $overwritePath = false, bool $minify = true, bool $addTimestamp = false)
    {
        if (!$overwritePath) {
            $file = '/src/Frontend/Modules/' . $this->getModule() . '/Js/' . $file;
        }

        $this->header->addJS($file, $minify, $addTimestamp, Priority::widget());
    }

    /**
     * Add data that should be available in JS
     *
     * @param string $key The key whereunder the value will be stored.
     * @param mixed $value The value to pass.
     */
    public function addJSData(string $key, $value)
    {
        $this->header->addJsData($this->getModule(), $key, $value);
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
            $this->header->addJS(
                $frontendModuleURL . '/' . $this->getModule() . '.js',
                true,
                true,
                Priority::widget()
            );
        }

        // add javascript file with same name as the action (if the file exists)
        if (is_file($frontendModulePath . '/Js/' . $this->getAction() . '.js')) {
            $this->header->addJS(
                $frontendModuleURL . '/' . $this->getAction() . '.js',
                true,
                true,
                Priority::widget()
            );
        }
    }

    /**
     * Get the action
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Get parsed template content
     *
     * @param string $template
     *
     * @return string
     */
    public function getContent(string $template = null): string
    {
        if ($template !== null) {
            return $this->tpl->getContent($template);
        }

        return $this->tpl->getContent($this->templatePath);
    }

    /**
     * Get the module
     *
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * Get template
     *
     * @return TwigTemplate
     */
    public function getTemplate(): TwigTemplate
    {
        return $this->tpl;
    }

    /**
     * Load the template
     *
     * @param string $path The path for the template to use.
     */
    protected function loadTemplate(string $path = null)
    {
        // no template given, so we should build the path
        if ($path === null) {
            // build path to the module
            $frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

            // build template path
            $path = $frontendModulePath . '/Layout/Widgets/' . $this->getAction() . '.html.twig';
        }

        // set template
        $this->setTemplatePath($path);
    }

    /**
     * Set the action, for later use
     *
     * @param string $action The action to use.
     */
    private function setAction(string $action)
    {
        $this->action = $action;
    }

    /**
     * Set the data, for later use
     *
     * @param string $data The data that should available.
     */
    private function setData(string $data = null)
    {
        // data given?
        if ($data === null) {
            return;
        }

        // unserialize data
        $data = unserialize($data);

        // store
        $this->data = $data;
    }

    /**
     * Set the module, for later use
     *
     * @param string $module The module to use.
     */
    private function setModule(string $module)
    {
        $this->module = $module;
    }

    /**
     * Set the path for the template to include or to replace the current one
     *
     * @param string $path The path to the template that should be loaded.
     */
    protected function setTemplatePath(string $path)
    {
        $this->templatePath = $path;
    }

    /**
     * Redirect to a given URL
     *
     * @param string $url The URL whereto will be redirected.
     * @param int $code The redirect code, default is 302 which means this is a temporary redirect.
     *
     * @throws RedirectException
     */
    public function redirect(string $url, int $code = 302)
    {
        $response = new RedirectResponse($url, $code);

        throw new RedirectException('Redirect', $response);
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string $type FQCN of the form type class i.e: MyClass::class
     * @param mixed $data The initial data for the form
     * @param array $options Options for the form
     *
     * @return Form
     */
    public function createForm(string $type, $data = null, array $options = []): Form
    {
        return $this->get('form.factory')->create($type, $data, $options);
    }
}
