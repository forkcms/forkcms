<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Exception\RedirectException;
use ForkCMS\App\KernelLoader;
use Frontend\Core\Engine\Block\ModuleExtraInterface;
use Frontend\Core\Header\Header;
use Frontend\Core\Language\Language;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Frontend\Core\Engine\Block\ExtraInterface as FrontendBlockExtra;
use Frontend\Core\Engine\Block\Widget as FrontendBlockWidget;
use Backend\Core\Engine\Model as BackendModel;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendAuthenticationModel;

/**
 * Frontend page class, this class will handle everything on a page
 */
class Page extends KernelLoader
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
    protected $extras = [];

    /**
     * Footer instance
     *
     * @var Footer
     */
    protected $footer;

    /**
     * Header instance
     *
     * @var Header
     */
    protected $header;

    /**
     * The current pageId
     *
     * @var int
     */
    protected $pageId;

    /**
     * Content of the page
     *
     * @var array
     */
    protected $record = [];

    /**
     * The path of the template to show
     *
     * @var string
     */
    protected $templatePath;

    /**
     * The statuscode
     *
     * @var int
     */
    protected $statusCode = Response::HTTP_OK;

    /**
     * TwigTemplate instance
     *
     * @var TwigTemplate
     */
    protected $template;

    /**
     * URL instance
     *
     * @var Url
     */
    protected $url;

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->getContainer()->set('page', $this);
        $this->template = $this->getContainer()->get('templating');
        $this->url = $this->getContainer()->get('url');
    }

    /**
     * Loads the actual components on the page
     */
    public function load(): void
    {
        // set tracking cookie
        Model::getVisitorId();

        // create header instance
        $this->header = new Header($this->getKernel());

        // get page content from pageId of the requested URL
        $this->record = $this->getPageContent(Navigation::getPageId(implode('/', $this->url->getPages())));

        if (empty($this->record)) {
            $this->record = Model::getPage(Response::HTTP_NOT_FOUND);
        }

        $this->checkAuthentication();

        // we need to set the correct id
        $this->pageId = (int) $this->record['id'];

        if ($this->pageId === Response::HTTP_NOT_FOUND) {
            $this->statusCode = Response::HTTP_NOT_FOUND;

            if (extension_loaded('newrelic')) {
                newrelic_name_transaction('404');
            }
        }

        $this->breadcrumb = new Breadcrumb($this->getKernel());
        $this->footer = new Footer($this->getKernel());

        $this->processPage();

        // execute all extras linked to the page
        array_map([$this, 'processExtra'], $this->extras);
    }

    private function checkAuthentication(): void
    {
        // no authentication needed
        if (!isset($this->record['data']['auth_required'])
            || !$this->record['data']['auth_required']
            || !BackendModel::isModuleInstalled('Profiles')
        ) {
            return;
        }

        if (!FrontendAuthenticationModel::isLoggedIn()) {
            $this->redirect(
                Navigation::getUrlForBlock('Profiles', 'Login') . '?queryString=' . $this->url->getQueryString()
            );
        }

        // specific groups for auth?
        if (empty($this->record['data']['auth_groups'])) {
            // no further checks needed
            return;
        }

        foreach ($this->record['data']['auth_groups'] as $group) {
            if (FrontendAuthenticationModel::getProfile()->isInGroup($group)) {
                // profile is in a group that is allowed to see the page
                return;
            }
        }

        // turns out the logged in profile isn't in a group that is allowed to see the page
        $this->record = Model::getPage(Response::HTTP_NOT_FOUND);
    }

    public function display(): Response
    {
        // assign the id so we can use it as an option
        $this->template->assignGlobal('isPage' . $this->pageId, true);
        $this->template->assignGlobal('isChildOfPage' . $this->record['parent_id'], true);

        // hide the cookiebar from within the code to prevent flickering
        $this->template->assignGlobal(
            'cookieBarHide',
            !$this->get('fork.settings')->get('Core', 'show_cookie_bar', false)
            || $this->getContainer()->get('fork.cookie')->hasHiddenCookieBar()
        );

        $this->parsePositions();

        // assign empty positions
        $unusedPositions = array_diff($this->record['template_data']['names'], array_keys($this->record['positions']));
        foreach ($unusedPositions as $position) {
            $this->template->assign('position' . \SpoonFilter::ucfirst($position), []);
        }

        $this->header->parse();
        $this->breadcrumb->parse();
        $this->parseLanguages();
        $this->footer->parse();

        return new Response(
            $this->template->getContent($this->templatePath),
            $this->statusCode
        );
    }

    public function getExtras(): array
    {
        return $this->extras;
    }

    public function getId(): int
    {
        return $this->pageId;
    }

    private function getPageRecord(int $pageId): array
    {
        if ($this->url->getParameter('page_revision', 'int') === null) {
            return Model::getPage($pageId);
        }

        // add no-index to meta-custom, so the draft won't get accidentally indexed
        $this->header->addMetaData(['name' => 'robots', 'content' => 'noindex, nofollow'], true);

        return Model::getPageRevision($this->url->getParameter('page_revision', 'int'));
    }

    protected function getPageContent(int $pageId): array
    {
        $record = $this->getPageRecord($pageId);

        if (empty($record)) {
            return [];
        }

        // redirect to the first child if the page is empty
        if ($this->allPositionsAreEmpty($record['positions'])) {
            $firstChildId = Navigation::getFirstChildId($record['id']);

            // check if we actually have a first child
            if (Navigation::getFirstChildId($record['id']) !== false) {
                $this->redirect(Navigation::getUrl($firstChildId), RedirectResponse::HTTP_MOVED_PERMANENTLY);
            }
        }

        return $record;
    }

    private function allPositionsAreEmpty(array $positions): bool
    {
        // loop positions to check if they are empty
        foreach ($positions as $blocks) {
            // loop blocks in position
            foreach ($blocks as $block) {
                // It isn't empty if HTML is provided, a decent extra is provided or a widget is provided
                if ($block['extra_type'] === 'block'
                    || $block['extra_type'] === 'widget'
                    || trim($block['html']) !== ''
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getRecord(): array
    {
        return $this->record;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    protected function parseLanguages(): void
    {
        // just execute if the site is multi-language
        if (!$this->getContainer()->getParameter('site.multilanguage') || count(Language::getActiveLanguages()) === 1) {
            return;
        }

        $this->template->assignGlobal(
            'languages',
            array_map(
                function (string $language) {
                    return [
                        'url' => '/' . $language,
                        'label' => $language,
                        'name' => Language::msg(mb_strtoupper($language)),
                        'current' => $language === LANGUAGE,
                    ];
                },
                Language::getActiveLanguages()
            )
        );
    }

    protected function parsePositions(): void
    {
        // init array to store parsed positions data
        $positions = [];

        // fetch variables from main template
        $mainVariables = $this->template->getAssignedVariables();

        // loop all positions
        foreach ($this->record['positions'] as $position => $blocks) {
            // loop all blocks in this position
            foreach ($blocks as $i => $block) {
                $positions[$position][$i] = $this->parseBlock($block, $mainVariables);
            }

            // assign position to template
            $this->template->assign('position' . \SpoonFilter::ucfirst($position), $positions[$position]);
        }

        $this->template->assign('positions', $positions);
    }

    private function parseBlock(array $block, array $mainVariables): array
    {
        if (!isset($block['extra'])) {
            $parsedBlock = $block;
            if (array_key_exists('blockContent', $block)) {
                $parsedBlock['html'] = $block['blockContent'];
            }

            return $parsedBlock;
        }

        $block['extra']->execute();
        $extraVariables = $block['extra']->getTemplate()->getAssignedVariables();
        $block['extra']->getTemplate()->assignArray($mainVariables);
        $block['extra']->getTemplate()->assignArray($extraVariables);

        return [
            'variables' => $block['extra']->getTemplate()->getAssignedVariables(),
            'blockIsEditor' => false,
            'html' => $block['extra']->getContent(),
        ];
    }

    protected function processExtra(ModuleExtraInterface $extra): void
    {
        $this->getContainer()->get('logger')->info(
            'Executing ' . get_class($extra) . " '{$extra->getAction()}' for module '{$extra->getModule()}'."
        );

        // overwrite the template
        if (is_callable([$extra, 'getOverwrite']) && $extra->getOverwrite()) {
            $this->templatePath = $extra->getTemplatePath();
        }
    }

    private function addAlternateLinks(): void
    {
        // no need for alternate links if there is only one language
        if (!$this->getContainer()->getParameter('site.multilanguage')) {
            return;
        }

        array_map([$this, 'addAlternateLinkForLanguage'], Language::getActiveLanguages());
    }

    private function addAlternateLinkForLanguage(string $language): void
    {
        if ($language === LANGUAGE) {
            return;
        }

        $url = Navigation::getUrl($this->pageId, $language);

        // Ignore 404 links
        if ($this->pageId !== Response::HTTP_NOT_FOUND
            && $url === Navigation::getUrl(Response::HTTP_NOT_FOUND, $language)) {
            return;
        }

        // Convert relative to absolute url
        if (strpos($url, '/') === 0) {
            $url = SITE_URL . $url;
        }

        $this->header->addLink(['rel' => 'alternate', 'hreflang' => $language, 'href' => $url]);
    }

    private function assignPageMeta(): void
    {
        // set pageTitle
        $this->header->setPageTitle(
            $this->record['meta_title'],
            $this->record['meta_title_overwrite']
        );

        // set meta-data
        $this->header->addMetaDescription(
            $this->record['meta_description'],
            $this->record['meta_description_overwrite']
        );
        $this->header->addMetaKeywords(
            $this->record['meta_keywords'],
            $this->record['meta_keywords_overwrite']
        );
        $this->header->setMetaCustom($this->record['meta_custom']);

        // advanced SEO-attributes
        if (isset($this->record['meta_seo_index'])) {
            $this->header->addMetaData(
                ['name' => 'robots', 'content' => $this->record['meta_seo_index']]
            );
        }
        if (isset($this->record['meta_seo_follow'])) {
            $this->header->addMetaData(
                ['name' => 'robots', 'content' => $this->record['meta_seo_follow']]
            );
        }
    }

    protected function processPage(): void
    {
        $this->assignPageMeta();
        new Navigation($this->getKernel());
        $this->addAlternateLinks();

        // assign content
        $pageInfo = Navigation::getPageInfo($this->record['id']);
        $this->record['has_children'] = $pageInfo['has_children'];
        $this->template->assignGlobal('page', $this->record);

        // set template path
        $this->templatePath = $this->record['template_path'];

        // loop blocks
        foreach ($this->record['positions'] as $position => &$blocks) {
            // position not known in template = skip it
            if (!in_array($position, $this->record['template_data']['names'])) {
                continue;
            }

            $blocks = array_map(
                function (array $block) {
                    if ($block['extra_id'] === null) {
                        return [
                            'blockIsEditor' => true,
                            'blockContent' => $block['html'],
                        ];
                    }

                    $block = ['extra' => $this->getExtraForBlock($block)];

                    // add to list of extras to parse
                    $this->extras[] = $block['extra'];

                    return $block;
                },
                $blocks
            );
        }
    }

    private function getExtraForBlock(array $block): ModuleExtraInterface
    {
        // block
        if ($block['extra_type'] === 'block') {
            if (extension_loaded('newrelic')) {
                newrelic_name_transaction($block['extra_module'] . '::' . $block['extra_action']);
            }

            return new FrontendBlockExtra(
                $this->getKernel(),
                $block['extra_module'],
                $block['extra_action'],
                $block['extra_data']
            );
        }

        return new FrontendBlockWidget(
            $this->getKernel(),
            $block['extra_module'],
            $block['extra_action'],
            $block['extra_data']
        );
    }

    private function redirect(string $url, int $code = RedirectResponse::HTTP_FOUND): void
    {
        throw new RedirectException('Redirect', new RedirectResponse($url, $code));
    }
}
