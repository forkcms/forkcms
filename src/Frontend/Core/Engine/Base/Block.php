<?php

namespace Frontend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Doctrine\Entity\Meta;
use Common\Exception\RedirectException;
use Frontend\Core\Engine\Breadcrumb;
use Frontend\Core\Engine\Exception;
use Frontend\Core\Engine\Header;
use Frontend\Core\Engine\TwigTemplate;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class implements a lot of functionality that can be extended by a specific block
 *
 * @later  Check which methods are the same in FrontendBaseWidget, maybe we should extend from a general class
 */
class Block extends Object
{
    /**
     * The current action
     *
     * @var string
     */
    protected $action;

    /**
     * The breadcrumb object
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

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
     * Should the current template be replaced with the blocks one?
     *
     * @var bool
     */
    private $overwrite;

    /**
     * Pagination array
     *
     * @var array
     */
    protected $pagination;

    /**
     * The path of the template to include, or that replaced the current one
     *
     * @var string
     */
    private $templatePath;

    /**
     * @param KernelInterface $kernel
     * @param string $module The name of the module.
     * @param string $action The name of the action.
     * @param string $data The data that should be available in this block.
     */
    public function __construct(KernelInterface $kernel, $module, $action, $data = null)
    {
        parent::__construct($kernel);

        // get objects from the reference so they are accessible
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
     * @param string $file The path for the CSS-file that should be loaded.
     * @param bool $overwritePath Whether or not to add the module to this path. Module path is added by default.
     * @param bool $minify Should the CSS be minified?
     * @param bool $addTimestamp May we add a timestamp for caching purposes?
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
     * @param string $file The path to the javascript-file that should be loaded.
     * @param bool $overwritePath Whether or not to add the module to this path. Module path is added by default.
     * @param bool $minify Should the file be minified?
     * @param bool $addTimestamp May we add a timestamp for caching purposes?
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
     * @param string $key The key whereunder the value will be stored.
     * @param mixed $value The value to pass.
     */
    public function addJSData($key, $value)
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
                Header::PRIORITY_GROUP_MODULE
            );
        }

        // add javascript file with same name as the action (if the file exists)
        if (is_file($frontendModulePath . '/Js/' . $this->getAction() . '.js')) {
            $this->header->addJS(
                $frontendModuleURL . '/' . $this->getAction() . '.js',
                true,
                true,
                Header::PRIORITY_GROUP_MODULE
            );
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
        return $this->tpl->getContent($this->templatePath);
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
     * @return TwigTemplate
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
     * @param string $path The path for the template to use.
     * @param bool $overwrite Should the template overwrite the default?
     */
    protected function loadTemplate($path = null, $overwrite = false)
    {
        $overwrite = (bool) $overwrite;

        // no template given, so we should build the path
        if ($path === null) {
            $path = $this->getModule() . '/Layout/Templates/' . $this->getAction() . '.html.twig';
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
     * @param string $query_parameter
     *
     * @throws Exception
     */
    protected function parsePagination($query_parameter = 'page')
    {
        $pagination = null;
        $showFirstPages = false;
        $showLastPages = false;
        $useQuestionMark = true;

        // validate pagination array
        if (!isset($this->pagination['limit'])) {
            throw new Exception('no limit in the pagination-property.');
        }
        if (!isset($this->pagination['offset'])) {
            throw new Exception('no offset in the pagination-property.');
        }
        if (!isset($this->pagination['requested_page'])) {
            throw new Exception('no requested_page available in the pagination-property.');
        }
        if (!isset($this->pagination['num_items'])) {
            throw new Exception('no num_items available in the pagination-property.');
        }
        if (!isset($this->pagination['num_pages'])) {
            throw new Exception('no num_pages available in the pagination-property.');
        }
        if (!isset($this->pagination['url'])) {
            throw new Exception('no URL available in the pagination-property.');
        }

        // should we use a questionmark or an ampersand
        if (mb_strpos($this->pagination['url'], '?') !== false) {
            $useQuestionMark = false;
        }

        // no pagination needed
        if ($this->pagination['num_pages'] < 1) {
            return;
        }

        // populate count fields
        $pagination['num_pages'] = $this->pagination['num_pages'];
        $pagination['current_page'] = $this->pagination['requested_page'];

        // define anchor
        $anchor = (isset($this->pagination['anchor'])) ? '#' . $this->pagination['anchor'] : '';

        // as long as we have more then 5 pages and are 5 pages from the end we should show all pages till the end
        if ($this->pagination['requested_page'] > 5 && $this->pagination['requested_page'] >= ($this->pagination['num_pages'] - 4)) {
            // init vars
            $pagesStart = ($this->pagination['num_pages'] > 7) ? $this->pagination['num_pages'] - 5 : $this->pagination['num_pages'] - 6;
            $pagesEnd = $this->pagination['num_pages'];

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
            // init vars
            $pagesStart = 1;
            $pagesEnd = 6;

            // when we have 7 pages, show 7 as end
            if ($this->pagination['num_pages'] == 7) {
                $pagesEnd = 7;
            } elseif ($this->pagination['num_pages'] <= 6) {
                // when we have less then 6 pages, show the maximum page
                $pagesEnd = $this->pagination['num_pages'];
            }

            // show last pages
            if ($this->pagination['num_pages'] > 7) {
                $showLastPages = true;
            }
        } else {
            // page 6
            // init vars
            $pagesStart = $this->pagination['requested_page'] - 2;
            $pagesEnd = $this->pagination['requested_page'] + 2;
            $showFirstPages = true;
            $showLastPages = true;
        }

        // show previous
        if ($this->pagination['requested_page'] > 1) {
            // build URL
            if ($useQuestionMark) {
                $url = $this->pagination['url'] . '?' . $query_parameter . '=' . ($this->pagination['requested_page'] - 1);
            } else {
                $url = $this->pagination['url'] . '&' . $query_parameter . '=' . ($this->pagination['requested_page'] - 1);
            }

            // set
            $pagination['show_previous'] = true;
            $pagination['previous_url'] = $url . $anchor;

            // flip ahead
            $this->header->addLink(
                array(
                    'rel' => 'prev',
                    'href' => SITE_URL . $url . $anchor,
                )
            );
        }

        // show first pages?
        if ($showFirstPages) {
            // init var
            $pagesFirstStart = 1;
            $pagesFirstEnd = 1;

            // loop pages
            for ($i = $pagesFirstStart; $i <= $pagesFirstEnd; ++$i) {
                // build URL
                if ($useQuestionMark) {
                    $url = $this->pagination['url'] . '?' . $query_parameter . '=' . $i;
                } else {
                    $url = $this->pagination['url'] . '&' . $query_parameter . '=' . $i;
                }

                // add
                $pagination['first'][] = array('url' => $url . $anchor, 'label' => $i);
            }
        }

        // build array
        for ($i = $pagesStart; $i <= $pagesEnd; ++$i) {
            // init var
            $current = ($i == $this->pagination['requested_page']);

            // build URL
            if ($useQuestionMark) {
                $url = $this->pagination['url'] . '?' . $query_parameter . '=' . $i;
            } else {
                $url = $this->pagination['url'] . '&' . $query_parameter . '=' . $i;
            }

            // add
            $pagination['pages'][] = array('url' => $url . $anchor, 'label' => $i, 'current' => $current);
        }

        // show last pages?
        if ($showLastPages) {
            // init var
            $pagesLastStart = $this->pagination['num_pages'];
            $pagesLastEnd = $this->pagination['num_pages'];

            // loop pages
            for ($i = $pagesLastStart; $i <= $pagesLastEnd; ++$i) {
                // build URL
                if ($useQuestionMark) {
                    $url = $this->pagination['url'] . '?' . $query_parameter . '=' . $i;
                } else {
                    $url = $this->pagination['url'] . '&' . $query_parameter . '=' . $i;
                }

                // add
                $pagination['last'][] = array('url' => $url . $anchor, 'label' => $i);
            }
        }

        // show next
        if ($this->pagination['requested_page'] < $this->pagination['num_pages']) {
            // build URL
            if ($useQuestionMark) {
                $url = $this->pagination['url'] . '?' . $query_parameter . '=' . ($this->pagination['requested_page'] + 1);
            } else {
                $url = $this->pagination['url'] . '&' . $query_parameter . '=' . ($this->pagination['requested_page'] + 1);
            }

            // set
            $pagination['show_next'] = true;
            $pagination['next_url'] = $url . $anchor;

            // flip ahead
            $this->header->addLink(
                array(
                    'rel' => 'next',
                    'href' => SITE_URL . $url . $anchor,
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
     * @param string $url The URL whereto will be redirected.
     * @param int $code The redirect code, default is 302 which means this is a temporary redirect.
     *
     * @throws RedirectException
     */
    public function redirect($url, $code = 302)
    {
        $response = new RedirectResponse($url, $code);

        throw new RedirectException('Redirect', $response);
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

    /**
     * @param Meta $meta
     */
    protected function setMeta(Meta $meta)
    {
        $this->header->setPageTitle($meta->getTitle(), $meta->isTitleOverwrite());
        $this->header->addMetaDescription($meta->getDescription(), $meta->isDescriptionOverwrite());
        $this->header->addMetaKeywords($meta->getKeywords(), $meta->isKeywordsOverwrite());
        $SEO = [];
        if ($meta->hasSEOFollow()) {
            $SEO[] = $meta->getSEOFollow();
        }
        if ($meta->hasSEOIndex()) {
            $SEO[] = $meta->getSEOIndex();
        }
        if (!empty($SEO)) {
            $this->header->addMetaData(
                ['name' => 'robots', 'content' => implode(', ', $SEO)],
                true
            );
        }
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string|FormTypeInterface $type The built type of the form
     * @param mixed $data The initial data for the form
     * @param array $options Options for the form
     *
     * @return Form
     */
    public function createForm($type, $data = null, array $options = array())
    {
        return $this->get('form.factory')->create($type, $data, $options);
    }
}
