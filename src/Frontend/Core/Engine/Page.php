<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Exception\RedirectException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Common\Cookie as CommonCookie;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Base\Object as FrontendBaseObject;
use Frontend\Core\Engine\Block\Extra as FrontendBlockExtra;
use Frontend\Core\Engine\Block\Widget as FrontendBlockWidget;

/**
 * Frontend page class, this class will handle everything on a page
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class Page extends FrontendBaseObject
{
    /**
     * Breadcrumb instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * Array of extras linked to this page
     *
     * @var array
     */
    protected $extras = array();

    /**
     * Footer instance
     *
     * @var    Footer
     */
    protected $footer;

    /**
     * Header instance
     *
     * @var    Header
     */
    protected $header;

    /**
     * The current pageId
     *
     * @var    int
     */
    protected $pageId;

    /**
     * Content of the page
     *
     * @var    array
     */
    protected $record = array();

    /**
     * The path of the template to show
     *
     * @var    string
     */
    protected $templatePath;

    /**
     * The statuscode
     *
     * @var    int
     */
    protected $statusCode = 200;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->getContainer()->set('page', $this);
    }

    /**
     * Loads the actual components on the page
     */
    public function load()
    {
        // set tracking cookie
        Model::getVisitorId();

        // get page content from pageId of the requested URL
        $this->record = $this->getPageContent(
            Navigation::getPageId(implode('/', $this->URL->getPages()))
        );

        // we need to set the correct id
        $this->pageId = (int) $this->record['id'];

        // set headers if this is a 404 page
        if ($this->pageId == 404) {
            $this->statusCode = 404;

            if (extension_loaded('newrelic')) {
                newrelic_name_transaction('404');
            }
        }

        // create breadcrumb instance
        $this->breadcrumb = new Breadcrumb($this->getKernel());

        // create header instance
        $this->header = new Header($this->getKernel());

        // new footer instance
        $this->footer = new Footer($this->getKernel());

        // process page
        $this->processPage();

        // execute all extras linked to the page
        $this->processExtras();

        // store statistics
        $this->storeStatistics();

        // trigger event
        Model::triggerEvent(
            'Core',
            'after_page_processed',
            array(
                 'id' => $this->getId(),
                 'record' => $this->getRecord(),
                 'statusCode' => $this->getStatusCode(),
                 'sessionId' => \SpoonSession::getSessionId(),
                 'visitorId' => Model::getVisitorId(),
                 'SESSION' => $_SESSION,
                 'COOKIE' => $_COOKIE,
                 'GET' => $_GET,
                 'POST' => $_POST,
                 'SERVER' => $_SERVER
            )
        );
    }

    /**
     * Display the page
     */
    public function display()
    {
        // parse header
        $this->header->parse();

        // parse breadcrumb
        $this->breadcrumb->parse();

        // parse languages
        $this->parseLanguages();

        // parse footer
        $this->footer->parse();

        // assign the id so we can use it as an option
        $this->tpl->assign('isPage' . $this->pageId, true);
        $this->tpl->assign('isChildOfPage' . $this->record['parent_id'], true);

        // hide the cookiebar from within the code to prevent flickering
        $this->tpl->assign(
            'cookieBarHide',
            (!$this->get('fork.settings')->get('Core', 'show_cookie_bar', false) || CommonCookie::hasHiddenCookieBar())
        );

        // the the positions to the template
        $this->parsePositions();

        // assign empty positions
        $unusedPositions = array_diff($this->record['template_data']['names'], array_keys($this->record['positions']));
        foreach ($unusedPositions as $position) {
            $this->tpl->assign(
                'position' . \SpoonFilter::ucfirst($position),
                array()
            );
        }

        // output
        return new Response(
            $this->tpl->getContent($this->templatePath, false, true),
            $this->statusCode
        );
    }

    /**
     * Get the extras linked to this page
     *
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Get the current page id
     *
     * @return int
     */
    public function getId()
    {
        return $this->pageId;
    }

    /**
     * Get page content
     *
     * @return array
     */
    protected function getPageContent($pageId)
    {
        // load revision
        if ($this->URL->getParameter('page_revision', 'int') != 0) {
            // get data
            $record = Model::getPageRevision($this->URL->getParameter('page_revision', 'int'));

            // add no-index to meta-custom, so the draft won't get accidentally indexed
            $this->header->addMetaData(array('name' => 'robots', 'content' => 'noindex, nofollow'), true);
        } else {
            // get page record
            $record = (array) Model::getPage($pageId);
        }

        // init var
        $redirect = true;

        // loop blocks, if all are empty we should redirect to the first child
        foreach ($record['positions'] as $blocks) {
            // loop blocks in position
            foreach ($blocks as $block) {
                // HTML provided?
                if ($block['html'] != '') {
                    $redirect = false;
                }

                // an decent extra provided?
                if ($block['extra_type'] == 'block') {
                    $redirect = false;
                }

                // a widget provided
                if ($block['extra_type'] == 'widget') {
                    $redirect = false;
                }
            }
        }

        // should we redirect?
        if ($redirect) {
            // get first child
            $firstChildId = Navigation::getFirstChildId($record['id']);

            // validate the child
            if ($firstChildId !== false) {
                // build URL
                $URL = Navigation::getURL($firstChildId);

                // redirect
                throw new RedirectException(
                    'Redirect',
                    new RedirectResponse(
                        $URL,
                        301
                    )
                );
            }
        }

        return $record;
    }

    /**
     * Get the content of the page
     *
     * @return    array
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Fetch the status code for the current page.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Parse the languages
     */
    protected function parseLanguages()
    {
        // just execute if the site is multi-language
        if ($this->getContainer()->getParameter('site.multilanguage')) {
            // get languages
            $activeLanguages = Language::getActiveLanguages();

            // init var
            $languages = array();

            // loop active languages
            foreach ($activeLanguages as $language) {
                // build temp array
                $temp = array();
                $temp['url'] = '/' . $language;
                $temp['label'] = $language;
                $temp['name'] = Language::msg(strtoupper($language));
                $temp['current'] = (bool) ($language == FRONTEND_LANGUAGE);

                // add
                $languages[] = $temp;
            }

            // assign
            if (count($languages) > 1) {
                $this->tpl->assign('languages', $languages);
            }
        }
    }

    /**
     * Parse the positions to the template
     */
    protected function parsePositions()
    {
        // init array to store parsed positions data
        $positions = array();

        do {
            $oldPositions = $positions;

            // fetch variables from main template
            $mainVariables = $this->tpl->getAssignedVariables();

            // loop all positions
            foreach ($this->record['positions'] as $position => $blocks) {
                // loop all blocks in this position
                foreach ($blocks as $i => $block) {
                    // check for extras that need to be reparsed
                    if (isset($block['extra'])) {
                        // fetch extra-specific variables
                        if (isset($positions[$position][$i]['variables'])) {
                            $extraVariables = $positions[$position][$i]['variables'];
                        } else {
                            $extraVariables = $block['extra']->getTemplate()->getAssignedVariables();
                        }

                        // assign all main variables
                        $block['extra']->getTemplate()->assignArray($mainVariables);

                        // overwrite with all specific variables
                        $block['extra']->getTemplate()->assignArray($extraVariables);

                        // parse extra
                        $positions[$position][$i] = array(
                            'variables' => $block['extra']->getTemplate()->getAssignedVariables(),
                            'blockIsEditor' => false,
                            'blockContent' => $block['extra']->getContent()
                        );

                        // Maintain backwards compatibility
                        $positions[$position][$i]['blockIsHTML'] = $positions[$position][$i]['blockIsEditor'];

                        if (empty($positions[$position][$i]['blockContent'])) {
                            unset($positions[$position][$i]);
                        }
                    } else {
                        $positions[$position][$i] = $block;
                    }
                }

                // assign position to template
                $this->tpl->assign('position' . \SpoonFilter::ucfirst($position), $positions[$position]);
            }
        } while ($oldPositions != $positions);
    }

    /**
     * Processes the extras linked to the page
     */
    protected function processExtras()
    {
        // loop all extras
        foreach ($this->extras as $extra) {
            $this->getContainer()->get('logger')->info(
                "Executing " . get_class($extra)
                . " '{$extra->getAction()}' for module '{$extra->getModule()}'."
            );

            // all extras extend FrontendBaseObject, which extends KernelLoader
            $extra->setKernel($this->getKernel());
            $extra->execute();

            // overwrite the template
            if (is_callable(array($extra, 'getOverwrite')) && $extra->getOverwrite()
            ) {
                $this->templatePath = $extra->getTemplatePath();
            }

            // assign the variables from this extra to the main template
            $this->tpl->assignArray((array) $extra->getTemplate()->getAssignedVariables());
        }
    }

    /**
     * Processes the page
     */
    protected function processPage()
    {
        // set pageTitle
        $this->header->setPageTitle($this->record['meta_title'], (bool) ($this->record['meta_title_overwrite'] == 'Y'));

        // set meta-data
        $this->header->addMetaDescription(
            $this->record['meta_description'],
            (bool) ($this->record['meta_description_overwrite'] == 'Y')
        );
        $this->header->addMetaKeywords(
            $this->record['meta_keywords'],
            ($this->record['meta_keywords_overwrite'] == 'Y')
        );
        $this->header->setMetaCustom($this->record['meta_custom']);

        // advanced SEO-attributes
        if (isset($this->record['meta_data']['seo_index'])) {
            $this->header->addMetaData(
                array('name' => 'robots', 'content' => $this->record['meta_data']['seo_index'])
            );
        }
        if (isset($this->record['meta_data']['seo_follow'])) {
            $this->header->addMetaData(
                array('name' => 'robots', 'content' => $this->record['meta_data']['seo_follow'])
            );
        }

        // create navigation instance
        new Navigation($this->getKernel());

        // assign content
        $pageInfo = Navigation::getPageInfo($this->record['id']);
        $this->record['has_children'] = $pageInfo['has_children'];
        $this->tpl->assign('page', $this->record);

        // set template path
        $this->templatePath = FRONTEND_PATH . '/' . $this->record['template_path'];

        // loop blocks
        foreach ($this->record['positions'] as $position => &$blocks) {
            // position not known in template = skip it
            if (!in_array($position, $this->record['template_data']['names'])) {
                continue;
            }

            // loop blocks in position
            foreach ($blocks as $index => &$block) {
                // an extra
                if ($block['extra_id'] !== null) {
                    // block
                    if ($block['extra_type'] == 'block') {
                        // create new instance
                        $extra = new FrontendBlockExtra(
                            $this->getKernel(),
                            $block['extra_module'],
                            $block['extra_action'],
                            $block['extra_data']
                        );

                        if (extension_loaded('newrelic')) {
                            newrelic_name_transaction($block['extra_module'] . '::' . $block['extra_action']);
                        }
                    } else {
                        // widget
                        $extra = new FrontendBlockWidget(
                            $this->getKernel(),
                            $block['extra_module'],
                            $block['extra_action'],
                            $block['extra_data']
                        );
                    }

                    // add to list of extras
                    $block = array('extra' => $extra);

                    // add to list of extras to parse
                    $this->extras[] = $extra;
                } else {
                    // the block only contains HTML
                    $block = array(
                        'blockIsEditor' => true,
                        'blockContent' => $block['html']
                    );

                    // Maintain backwards compatibility
                    $block['blockIsHTML'] = $block['blockIsEditor'];
                }
            }
        }
    }

    /**
     * Store the data for statistics
     */
    protected function storeStatistics()
    {
        // @later save temp statistics data here.
    }
}
