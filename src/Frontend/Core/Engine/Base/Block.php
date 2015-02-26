<?php

namespace Frontend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelInterface;

use Frontend\Core\Engine\Breadcrumb;
use Frontend\Core\Engine\Exception;
use Frontend\Core\Engine\Header;
use Frontend\Core\Engine\Url;
use Frontend\Core\Engine\Template as FrontendTemplate;
use InvalidArgumentException;

/**
 * This class implements a lot of functionality that can be extended by a specific block
 * @later  Check which methods are the same in FrontendBaseWidget, maybe we should extend from a general class
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class Block extends Object
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
     * @var    Breadcrumb
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
     * @var    Url
     */
    public $URL;

    /**
     * @param KernelInterface $kernel
     * @param string          $module The name of the module.
     * @param string          $action The name of the action.
     * @param string          $data   The data that should be available in this block.
     */
    public function __construct(KernelInterface $kernel, $module, $action, $data = null)
    {
        parent::__construct($kernel);

        // get objects from the reference so they are accessible
        $this->tpl = new FrontendTemplate(false);
        $this->header = $this->getContainer()->get('header');
        $this->URL = $this->getContainer()->get('url');
        $this->breadcrumb = $this->getContainer()->get('breadcrumb');

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
     * @param bool   $addTimestamp  May we add a timestamp for caching purposes?
     */
    public function addJS($file, $overwritePath = false, $minify = true, $addTimestamp = null)
    {
        $file = (string) $file;
        $overwritePath = (bool) $overwritePath;

        // use module path
        if (!$overwritePath) {
            $file = '/src/Frontend/Modules/' . $this->getModule() . '/Js/' . $file;
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
     * @param string $path      The path for the template to use.
     * @param bool   $overwrite Should the template overwrite the default?
     */
    protected function loadTemplate($path = null, $overwrite = false)
    {
        $overwrite = (bool) $overwrite;

        // no template given, so we should build the path
        if ($path === null) {
            // build path to the module
            $frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

            // build template path
            $path = $frontendModulePath . '/Layout/Templates/' . $this->getAction() . '.tpl';
        } else {
            // redefine
            $path = (string) $path;
        }

        // set properties
        $this->setOverwrite($overwrite);
        $this->setTemplatePath($path);
    }

    /**
     * Parse pagination
     *
     * @param callable $getNbResults A function that returns the total number of results
     * @param callable $getSlice     A function that returns a slice of the collection
     * @param callable $getUrl       A function to get the correct url for this item
     * @param int      $currentPage  The current page number
     * @param int      $itemsPerPage The number of items we want per page
     *
     * @return array The items for the current page
     */
    protected function parsePagination($getNbResults, $getSlice, $getUrl, $currentPage = 1, $itemsPerPage = 10)
    {
        if (!is_callable($getUrl)) {
            throw new InvalidArgumentException('The getUrl parameter should be a callable function.');
        }

        // Create a new pagerfanta object using the given callbacks and pagerfanta's callback adapter.
        $adapter = new \Pagerfanta\Adapter\CallbackAdapter($getNbResults, $getSlice);
        $pagerFanta = new \Pagerfanta\Pagerfanta($adapter);
        $pagerFanta->setMaxPerPage($itemsPerPage);

        // Set the current page to the given value.
        // If it's less than one, set the current page to the first page.
        // If it's more than the number of pages we have in total, set it to the last page.
        try {
            $pagerFanta->setCurrentPage($currentPage);
        } catch (\Pagerfanta\Exception\LessThan1CurrentPageException $e) {
            $currentPage = 1;
            $pagerFanta->setCurrentPage($currentPage);
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
            $currentPage = $pagerFanta->getNbPages();
            $pagerFanta->setCurrentPage($currentPage);
        }

        // Create a list of all page numbers, with a label, an url and a boolean flag when they're the current page.
        $pages = range(1, $pagerFanta->getNbPages());
        $pages = array_map(
            function($pageNumber) use ($getUrl, $currentPage) {
                return array(
                    'url' => $getUrl($pageNumber),
                    'label' => $pageNumber,
                    'current' => ((int) $pageNumber === (int) $currentPage),
                );
            },
            $pages
        );

        // Should we show navigation buttons for the previous and next pages?
        $showNext = $pagerFanta->hasNextPage();
        $nextUrl = ($showNext) ? $pages[$currentPage]['url'] : null;
        $showPrevious = $pagerFanta->hasPreviousPage();
        $previousUrl = ($showPrevious) ? $pages[$currentPage - 2]['url'] : null;

        // Calculate for which page numbers we should show navigation buttons
        $first = null;
        $last = null;
        if ($pagerFanta->getNbPages() > 7) {
            if ($currentPage < 6) {
                $last = array_slice($pages, -1);
                $pages = array_slice($pages, 0, 6);
            } elseif ($currentPage > $pagerFanta->getNbPages() - 5) {
                $first = array_slice($pages, 0, 1);
                $pages = array_slice($pages, -6);
            } else {
                $first = array_slice($pages, 0, 1);
                $last = array_slice($pages, -1);
                $pages = array_slice($pages, $currentPage - 3, 5);
            }
        }

        // Create pagination data object for the template, and assign it
        $pagination = array(
            'multiple_pages' => $pagerFanta->haveToPaginate(),
            'first' => $first,
            'last' => $last,
            'pages' => $pages,
            'show_next' => $showNext,
            'next_url' => $nextUrl,
            'show_previous' => $showPrevious,
            'previous_url' => $previousUrl,
        );

        $this->tpl->assign('pagination', $pagination);

        return $pagerFanta->getCurrentPageResults();
    }

    /**
     * Redirect to a given URL
     *
     * @param string $URL  The URL whereto will be redirected.
     * @param int    $code The redirect code, default is 302 which means this is a temporary redirect.
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
     * @param string $data The data that should be available.
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
