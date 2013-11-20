<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This class will be the base of the objects used in on-site
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class FrontendBaseObject extends KernelLoader
{
    /**
     * Template instance
     *
     * @var    FrontendTemplate
     */
    protected $tpl;

    /**
     * URL instance
     *
     * @var    FrontendURL
     */
    protected $URL;

    /**
     * It will grab stuff from the reference.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->tpl = $this->getContainer()->get('template');
        $this->URL = $this->getContainer()->get('url');
    }
}

/**
 * This is the base-object for config-files.
 * The module-specific config-files can extend the functionality from this class.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendBaseConfig extends KernelLoader
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

        // check if model exists
        if (is_file(FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/engine/model.php')) {
            // the model exists, so we require it
            require_once FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/engine/model.php';
        }

        // read the possible actions based on the files
        $this->setPossibleActions();
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
        $fs                 = new Filesystem();

        if ($fs->exists($frontendModulePath . '/actions')) {
            // get regular actions
            $finder = new Finder();
            $finder->name('*.php');
            foreach ($finder->files()->in($frontendModulePath . '/actions') as $file) {
                $action = $file->getBasename('.php');
                if (!in_array($action, $this->disabledActions)) {
                    $this->possibleActions[$file->getBasename()] = $action;
                }
            }
        }

        if ($fs->exists($frontendModulePath . '/ajax')) {
            // get ajax-actions
            $finder = new Finder();
            $finder->name('*.php');
            foreach ($finder->files()->in($frontendModulePath . '/ajax') as $file) {
                $action = $file->getBasename('.php');
                if (!in_array($action, $this->disabledAJAXActions)) {
                    $this->possibleAJAXActions[$file->getBasename()] = $action;
                }
            }
        }
    }
}

/**
 * This class implements a lot of functionality that can be extended by a specific block
 * @later  Check which methods are the same in FrontendBaseWidget, maybe we should extend from a general class
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class FrontendBaseBlock extends FrontendBaseObject
{
    /**
     * The current action
     *
     * @var    string
     */
    protected $action;

    /**
     * The breadcrumb object
     *
     * @var    FrontendBreadcrumb
     */
    protected $breadcrumb;

    /**
     * The data
     *
     * @var    mixed
     */
    protected $data;

    /**
     * The header object
     *
     * @var    FrontendHeader
     */
    protected $header;

    /**
     * The current module
     *
     * @var    string
     */
    protected $module;

    /**
     * Should the current template be replaced with the blocks one?
     *
     * @var    bool
     */
    private $overwrite;

    /**
     * Pagination array
     *
     * @var    array
     */
    protected $pagination;

    /**
     * The path of the template to include, or that replaced the current one
     *
     * @var    string
     */
    private $templatePath;

    /**
     * A reference to the current template
     *
     * @var    FrontendTemplate
     */
    public $tpl;

    /**
     * A reference to the URL-instance
     *
     * @var    FrontendURL
     */
    public $URL;

    /**
     * @param KernelInterface        $kernel
     * @param string                 $module The name of the module.
     * @param string                 $action The name of the action.
     * @param                 string $data   The data that should be available in this block.
     */
    public function __construct(KernelInterface $kernel, $module, $action, $data = null)
    {
        parent::__construct($kernel);

        // get objects from the reference so they are accessible
        $this->tpl        = new FrontendTemplate(false);
        $this->header     = $this->getContainer()->get('header');
        $this->URL        = $this->getContainer()->get('url');
        $this->breadcrumb = $this->getContainer()->get('breadcrumb');

        // set properties
        $this->setModule($module);
        $this->setAction($action);
        $this->setData($data);
    }

    /**
     * Add a CSS file into the array
     *
     * @param string      $file          The path for the CSS-file that should be loaded.
     * @param        bool $overwritePath Whether or not to add the module to this path. Module path is added by default.
     * @param        bool $minify        Should the CSS be minified?
     * @param        bool $addTimestamp  May we add a timestamp for caching purposes?
     */
    public function addCSS($file, $overwritePath = false, $minify = true, $addTimestamp = null)
    {
        // redefine
        $file          = (string) $file;
        $overwritePath = (bool) $overwritePath;

        // use module path
        if (!$overwritePath) {
            $file = '/frontend/modules/' . $this->getModule() . '/layout/css/' . $file;
        }

        // add css to the header
        $this->header->addCSS($file, $minify, $addTimestamp);
    }

    /**
     * Add a javascript file into the array
     *
     * @param string      $file          The path to the javascript-file that should be loaded.
     * @param        bool $overwritePath Whether or not to add the module to this path. Module path is added by default.
     * @param        bool $minify        Should the file be minified?
     * @param        bool $addTimestamp  May we add a timestamp for caching purposes?
     */
    public function addJS($file, $overwritePath = false, $minify = true, $addTimestamp = null)
    {
        $file          = (string) $file;
        $overwritePath = (bool) $overwritePath;

        // use module path
        if (!$overwritePath) {
            $file = '/frontend/modules/' . $this->getModule() . '/js/' . $file;
        }

        // add js to the header
        $this->header->addJS($file, $minify, $addTimestamp);
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
        $frontendModuleURL = '/frontend/modules/' . $this->getModule() . '/js';

        // add javascript file with same name as module (if the file exists)
        if (is_file($frontendModulePath . '/js/' . $this->getModule() . '.js')) {
            $this->header->addJS($frontendModuleURL . '/' . $this->getModule() . '.js', false);
        }

        // add javascript file with same name as the action (if the file exists)
        if (is_file($frontendModulePath . '/js/' . $this->getAction() . '.js')) {
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
     * Get parsed template content.
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
     * Get overwrite mode
     *
     * @return bool
     */
    public function getOverwrite()
    {
        return $this->overwrite;
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
     * Get template path
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Load the template
     *
     * @param      string     [optional] $path      The path for the template to use.
     * @param bool $overwrite Should the template overwrite the default?
     */
    protected function loadTemplate($path = null, $overwrite = false)
    {
        $overwrite = (bool) $overwrite;

        // no template given, so we should build the path
        if ($path === null) {
            // build path to the module
            $frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

            // build template path
            $path = $frontendModulePath . '/layout/templates/' . $this->getAction() . '.tpl';
        } else {
            $path = (string) $path;
        }

        // set properties
        $this->setOverwrite($overwrite);
        $this->setTemplatePath($path);
    }

    /**
     * Parse pagination
     */
    protected function parsePagination()
    {
        $pagination      = null;
        $showFirstPages  = false;
        $showLastPages   = false;
        $useQuestionMark = true;

        // validate pagination array
        if (!isset($this->pagination['limit'])) {
            throw new FrontendException('no limit in the pagination-property.');
        }
        if (!isset($this->pagination['offset'])) {
            throw new FrontendException('no offset in the pagination-property.');
        }
        if (!isset($this->pagination['requested_page'])) {
            throw new FrontendException('no requested_page available in the pagination-property.');
        }
        if (!isset($this->pagination['num_items'])) {
            throw new FrontendException('no num_items available in the pagination-property.');
        }
        if (!isset($this->pagination['num_pages'])) {
            throw new FrontendException('no num_pages available in the pagination-property.');
        }
        if (!isset($this->pagination['url'])) {
            throw new FrontendException('no URL available in the pagination-property.');
        }

        // should we use a questionmark or an ampersand
        if (mb_strpos($this->pagination['url'], '?') > 0) {
            $useQuestionMark = false;
        }

        // no pagination needed
        if ($this->pagination['num_pages'] < 1) {
            return;
        }

        // populate count fields
        $pagination['num_pages']    = $this->pagination['num_pages'];
        $pagination['current_page'] = $this->pagination['requested_page'];

        // define anchor
        $anchor = (isset($this->pagination['anchor'])) ? '#' . $this->pagination['anchor'] : '';

        // as long as we have more then 5 pages and are 5 pages from the end we should show all pages till the end
        if ($this->pagination['requested_page'] > 5 &&
            $this->pagination['requested_page'] >= ($this->pagination['num_pages'] - 4)
        ) {
            // init vars
            $pagesStart = ($this->pagination['num_pages'] > 7) ? $this->pagination['num_pages'] - 5 : $this->pagination['num_pages'] - 6;
            $pagesEnd   = $this->pagination['num_pages'];

            // fix for page 6
            if ($this->pagination['num_pages'] == 6) {
                $pagesStart = 1;
            }

            // show first pages
            if ($this->pagination['num_pages'] > 7) {
                $showFirstPages = true;
            }
        } elseif ($this->pagination['requested_page'] <= 5) {
            // as long as we are below page 5 and below 5 from the end we should show all pages starting from 1
            $pagesStart = 1;
            $pagesEnd   = 6;


            if ($this->pagination['num_pages'] == 7) {
                $pagesEnd = 7;
            } elseif ($this->pagination['num_pages'] <= 6) {
                $pagesEnd = $this->pagination['num_pages'];
            }


            if ($this->pagination['num_pages'] > 7) {
                $showLastPages = true;
            }
        } else {
            $pagesStart     = $this->pagination['requested_page'] - 2;
            $pagesEnd       = $this->pagination['requested_page'] + 2;
            $showFirstPages = true;
            $showLastPages  = true;
        }

        // show previous
        if ($this->pagination['requested_page'] > 1) {
            // build URL
            if ($useQuestionMark) {
                $URL = $this->pagination['url'] . '?page=' . ($this->pagination['requested_page'] - 1);
            } else {
                $URL = $this->pagination['url'] . '&amp;page=' . ($this->pagination['requested_page'] - 1);
            }

            // set
            $pagination['show_previous'] = true;
            $pagination['previous_url']  = $URL . $anchor;

            // flip ahead
            $this->header->addLink(
                array(
                    'rel'  => 'prev',
                    'href' => SITE_URL . $URL . $anchor,
                )
            );
        }

        // show first pages?
        if ($showFirstPages) {
            // init var
            $pagesFirstStart = 1;
            $pagesFirstEnd   = 1;

            // loop pages
            for ($i = $pagesFirstStart; $i <= $pagesFirstEnd; $i++) {
                // build URL
                if ($useQuestionMark) {
                    $URL = $this->pagination['url'] . '?page=' . $i;
                } else {
                    $URL = $this->pagination['url'] . '&amp;page=' . $i;
                }

                // add
                $pagination['first'][] = array('url' => $URL . $anchor, 'label' => $i);
            }
        }

        // build array
        for ($i = $pagesStart; $i <= $pagesEnd; $i++) {
            // init var
            $current = ($i == $this->pagination['requested_page']);

            // build URL
            if ($useQuestionMark) {
                $URL = $this->pagination['url'] . '?page=' . $i;
            } else {
                $URL = $this->pagination['url'] . '&amp;page=' . $i;
            }

            // add
            $pagination['pages'][] = array('url' => $URL . $anchor, 'label' => $i, 'current' => $current);
        }

        // show last pages?
        if ($showLastPages) {
            // init var
            $pagesLastStart = $this->pagination['num_pages'];
            $pagesLastEnd   = $this->pagination['num_pages'];

            // loop pages
            for ($i = $pagesLastStart; $i <= $pagesLastEnd; $i++) {
                // build URL
                if ($useQuestionMark) {
                    $URL = $this->pagination['url'] . '?page=' . $i;
                } else {
                    $URL = $this->pagination['url'] . '&amp;page=' . $i;
                }

                // add
                $pagination['last'][] = array('url' => $URL . $anchor, 'label' => $i);
            }
        }

        // show next
        if ($this->pagination['requested_page'] < $this->pagination['num_pages']) {
            // build URL
            if ($useQuestionMark) {
                $URL = $this->pagination['url'] . '?page=' . ($this->pagination['requested_page'] + 1);
            } else {
                $URL = $this->pagination['url'] . '&amp;page=' . ($this->pagination['requested_page'] + 1);
            }

            // set
            $pagination['show_next'] = true;
            $pagination['next_url']  = $URL . $anchor;

            // flip ahead
            $this->header->addLink(
                array(
                    'rel'  => 'next',
                    'href' => SITE_URL . $URL . $anchor,
                )
            );
        }

        // multiple pages
        $pagination['multiple_pages'] = ($pagination['num_pages'] == 1) ? false : true;

        // assign pagination
        $this->tpl->assign('pagination', $pagination);
    }

    /**
     * Redirect to a given URL
     *
     * @param string     $URL  The URL whereto will be redirected.
     * @param        int $code The redirect code, default is 302 which means this is a temporary redirect.
     */
    public function redirect($URL, $code = 302)
    {
        $response = new RedirectResponse($URL, $code);

        /*
         * Since we've got some nested action structure, we'll send this
         * response directly after creating.
         */
        $response->send();

        /*
         * Stop code executing here
         * I know this is ugly as hell, but if we don't do this the code after
         * this call is executed and possibly will trigger errors.
         */
        exit;
    }

    /**
     * Set the action, for later use
     *
     * @param string $action The action to set.
     */
    private function setAction($action)
    {
        $this->action = (string) $action;
    }

    /**
     * Set the data, for later use
     *
     * @param string [optional] $data The data that should be available.
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
     * @param string $module The module that should be used.
     */
    private function setModule($module)
    {
        $this->module = (string) $module;
    }

    /**
     * Set overwrite mode
     *
     * @param bool $overwrite true if the template should overwrite the current template, false if not.
     */
    protected function setOverwrite($overwrite)
    {
        $this->overwrite = (bool) $overwrite;
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

/**
 * This class implements a lot of functionality that can be extended by a specific widget
 * @later  Check which methods are the same in FrontendBaseBlock, maybe we should extend from a general class
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class FrontendBaseWidget extends FrontendBaseObject
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
     * @var    FrontendHeader
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
     * @var    FrontendURL
     */
    public $URL;

    /**
     * @param KernelInterface        $kernel
     * @param string                 $module The module to use.
     * @param string                 $action The action to use.
     * @param                 string $data   The data that should be available.
     */
    public function __construct(KernelInterface $kernel, $module, $action, $data = null)
    {
        parent::__construct($kernel);

        // get objects from the reference so they are accessible
        $this->tpl    = new FrontendTemplate(false);
        $this->header = $this->getContainer()->get('header');
        $this->URL    = $this->getContainer()->get('url');

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
        $file          = (string) $file;
        $overwritePath = (bool) $overwritePath;

        // use module path
        if (!$overwritePath) {
            $file = '/frontend/modules/' . $this->getModule() . '/layout/css/' . $file;
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
        $file          = (string) $file;
        $overwritePath = (bool) $overwritePath;

        // use module path
        if (!$overwritePath) {
            $file = '/frontend/modules/' . $this->getModule() . '/js/' . $file;
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
        $frontendModuleURL = '/frontend/modules/' . $this->getModule() . '/js';

        // add javascript file with same name as module (if the file exists)
        if (is_file($frontendModulePath . '/js/' . $this->getModule() . '.js')) {
            $this->header->addJS($frontendModuleURL . '/' . $this->getModule() . '.js', false);
        }

        // add javascript file with same name as the action (if the file exists)
        if (is_file($frontendModulePath . '/js/' . $this->getAction() . '.js')) {
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
     * @param string [optional] $path The path for the template to use.
     */
    protected function loadTemplate($path = null)
    {
        // no template given, so we should build the path
        if ($path === null) {
            // build path to the module
            $frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

            // build template path
            $path = $frontendModulePath . '/layout/widgets/' . $this->getAction() . '.tpl';
        } else {
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
     * @param string [optional] $data The data that should available.
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

/**
 * This class implements a lot of functionality that can be extended by a specific AJAX action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendBaseAJAXAction extends KernelLoader
{
    const OK          = 200;
    const BAD_REQUEST = 400;
    const FORBIDDEN   = 403;
    const ERROR       = 500;

    /**
     * The current action
     *
     * @var    string
     */
    protected $action;

    /**
     * @var array
     */
    protected $content;

    /**
     * The current module
     *
     * @var    string
     */
    protected $module;

    /**
     * @param KernelInterface $kernel
     * @param string          $action The action to use.
     * @param string          $module The module to use.
     */
    public function __construct(KernelInterface $kernel, $action, $module)
    {
        parent::__construct($kernel);

        // store the current module and action (we grab them from the URL)
        $this->setModule($module);
        $this->setAction($action);
    }

    /**
     * Execute the action
     */
    public function execute()
    {
        return $this->getContent();
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
     * Since the display action in the backend is rather complicated and we
     * want to make this work with our Kernel, I've added this getContent
     * method to extract the output from the actual displaying.
     *
     * With this function we'll be able to get the content and return it as a
     * Symfony output object.
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getContent()
    {
        $statusCode = (isset($this->content['code']) ? $this->content['code'] : 200);

        return new Response(
            json_encode($this->content),
            $statusCode,
            array('content-type' => 'application/json')
        );
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
     * Outputs an answer to the browser
     *
     * @param int    $statusCode The status code to use, use one of the available constants
     *                           (self::OK, self::BAD_REQUEST, self::FORBIDDEN, self::ERROR).
     * @param mixed  $data       The data to be returned (will be encoded as JSON).
     * @param string $message    A text-message.
     */
    public function output($statusCode, $data = null, $message = null)
    {
        $statusCode = (int) $statusCode;
        if ($message !== null) {
            $message = (string) $message;
        }

        $response = array('code' => $statusCode, 'data' => $data, 'message' => $message);

        $this->content = $response;
    }

    /**
     * Set the action, for later use
     *
     * @param string $action The action to use.
     */
    protected function setAction($action)
    {
        $this->action = (string) $action;
    }

    /**
     * Set the module, for later use
     *
     * @param string $module The module to use.
     */
    protected function setModule($module)
    {
        $this->module = (string) $module;
    }
}
