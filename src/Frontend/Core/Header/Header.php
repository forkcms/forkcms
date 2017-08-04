<?php

namespace Frontend\Core\Header;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Core\Header\Asset;
use Common\Core\Header\AssetCollection;
use Common\Core\Header\JsData;
use Common\Core\Header\Minifier;
use Common\Core\Header\Priority;
use ForkCMS\App\KernelLoader;
use Frontend\Core\Engine\Model;
use Frontend\Core\Engine\Theme;
use Frontend\Core\Engine\TwigTemplate;
use Frontend\Core\Engine\Url;
use Frontend\Core\Language\Locale;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class will be used to alter the head-part of the HTML-document that will be created by the frontend
 * Therefore it will handle meta-stuff (title, including JS, including CSS, ...)
 */
class Header extends KernelLoader
{
    /**
     * The canonical URL
     *
     * @var string
     */
    private $canonical;

    /**
     * The added css-files
     *
     * @var AssetCollection
     */
    private $cssFiles;

    /**
     * Data that will be passed to js
     *
     * @var JsData
     */
    private $jsData;

    /**
     * The added js-files
     *
     * @var AssetCollection
     */
    private $jsFiles;

    /**
     * Meta data and links
     *
     * @var MetaCollection
     */
    private $meta;

    /**
     * The custom meta data
     *
     * @var string
     */
    private $metaCustom = '';

    /**
     * Page title
     *
     * @var string
     */
    private $pageTitle;

    /**
     * Content title
     *
     * @var string
     */
    private $contentTitle;

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

        $container = $this->getContainer();
        $container->set('header', $this);

        $this->template = $container->get('templating');
        $this->url = $container->get('url');

        $this->cssFiles = new AssetCollection(
            Minifier::css(
                $container->getParameter('site.path_www'),
                FRONTEND_CACHE_URL . '/MinifiedCss/',
                FRONTEND_CACHE_PATH . '/MinifiedCss/'
            )
        );
        $this->jsFiles = new AssetCollection(
            Minifier::js(
                $container->getParameter('site.path_www'),
                FRONTEND_CACHE_URL . '/MinifiedJs/',
                FRONTEND_CACHE_PATH . '/MinifiedJs/'
            )
        );
        $this->jsData = new JsData(['LANGUAGE' => Locale::frontendLanguage()]);
        $this->meta = new MetaCollection();

        // add some default CSS files
        $this->addCSS('/src/Frontend/Core/Layout/Css/screen.css');

        // debug stylesheet
        if ($container->getParameter('kernel.debug')) {
            $this->addCSS('/src/Frontend/Core/Layout/Css/debug.css', true, false, Priority::debug());
        }

        // add default javascript-files
        $this->addJS('/src/Frontend/Core/Js/jquery/jquery.js', false, false, Priority::core());
        $this->addJS('/src/Frontend/Core/Js/jquery/jquery.frontend.js', true, false, Priority::core());
        $this->addJS('/src/Frontend/Core/Js/utils.js', true, false, Priority::core());
        $this->addJS('/src/Frontend/Core/Js/frontend.js', true, false, Priority::core());
    }

    /**
     * Add a CSS file into the array
     *
     * @param string $file The path for the CSS-file that should be loaded.
     * @param bool $minify Should the CSS be minified?
     * @param bool $addTimestamp May we add a timestamp for caching purposes?
     * @param Priority|null $priority Provides a way to change the order that things are loaded
     */
    public function addCSS(
        string $file,
        bool $minify = true,
        bool $addTimestamp = false,
        Priority $priority = null
    ): void {
        if (mb_strpos($file, 'http') !== 0) {
            $file = Theme::getPath($file);
        }

        $this->cssFiles->add(new Asset($file, $addTimestamp, $priority), $minify);
    }

    /**
     * Add a javascript file into the array
     *
     * @param string $file The path to the javascript-file that should be loaded.
     * @param bool $minify Should the file be minified?
     * @param bool $addTimestamp May we add a timestamp for caching purposes?
     * @param Priority|null $priority Provides a way to change the order that things are loaded
     */
    public function addJS(
        string $file,
        bool $minify = true,
        bool $addTimestamp = false,
        Priority $priority = null
    ): void {
        if (mb_strpos($file, 'http') !== 0) {
            $file = Theme::getPath($file);
        }

        $this->jsFiles->add(new Asset($file, $addTimestamp, $priority), $minify);
    }

    /**
     * Add data into the jsData
     *
     * @param string $module The name of the module.
     * @param string $key The key whereunder the value will be stored.
     * @param mixed $value The value
     */
    public function addJsData(string $module, string $key, $value): void
    {
        $this->jsData->add($module, $key, $value);
    }

    /**
     * @param array $attributes The attributes to parse.
     * @param bool $overwrite Should we overwrite the current value?
     * @param string[] $uniqueAttributeKeys Which keys can we use to decide if an item is unique.
     */
    public function addLink(
        array $attributes,
        bool $overwrite = false,
        array $uniqueAttributeKeys = ['rel', 'hreflang', 'type', 'title']
    ): void {
        if (!isset($attributes['href']) || empty($attributes['href'])) {
            return;
        }
        $href = $attributes['href'];
        unset($attributes['href']);

        $this->meta->addMetaLink(new MetaLink($href, $attributes, $uniqueAttributeKeys), $overwrite);
    }

    /**
     * @param array $attributes The attributes to parse.
     * @param bool $overwrite Should we overwrite the current value?
     * @param array $uniqueAttributeKeys Which keys can we use to decide if an item is unique.
     * @param string $uniqueKeySuffix This additional key helps you to create your own custom unique keys if required.
     */
    public function addMetaData(
        array $attributes,
        bool $overwrite = false,
        array $uniqueAttributeKeys = ['name'],
        string $uniqueKeySuffix = null
    ): void {
        if (!isset($attributes['content']) || $attributes['content'] === '') {
            return;
        }

        $content = $attributes['content'];
        unset($attributes['content']);

        $this->meta->addMetaData(
            new MetaData($content, $attributes, $uniqueAttributeKeys, $uniqueKeySuffix),
            $overwrite
        );
    }

    /**
     * Add meta-description, somewhat a shortcut for the addMetaData-method
     *
     * @param string $metaDescription The description.
     * @param bool $overwrite Should we overwrite the previous value?
     */
    public function addMetaDescription(string $metaDescription, bool $overwrite = false): void
    {
        $this->meta->addMetaData(MetaData::forName('description', $metaDescription), $overwrite);
    }

    /**
     * Add meta-keywords, somewhat a shortcut for the addMetaData-method
     *
     * @param string $metaKeywords The keywords.
     * @param bool $overwrite Should we overwrite the previous value?
     */
    public function addMetaKeywords(string $metaKeywords, bool $overwrite = false): void
    {
        $this->meta->addMetaData(MetaData::forName('keywords', $metaKeywords), $overwrite);
    }

    /**
     * @param string $property The key (without og:).
     * @param string $openGraphData The value.
     * @param bool $overwrite Should we overwrite the previous value?
     */
    public function addOpenGraphData(string $property, string $openGraphData, bool $overwrite = false): void
    {
        $this->meta->addMetaData(MetaData::forProperty('og:' . $property, $openGraphData), $overwrite);
    }

    public function addOpenGraphImage($image, bool $overwrite = false, int $width = 0, int $height = 0): void
    {
        // remove site url from path
        $image = str_replace(SITE_URL, '', $image);

        // check if it no longer points to an absolute uri
        if (strpos($image, 'http://') !== 0 && strpos($image, 'https://') !== 0) {
            if (!is_file(PATH_WWW . strtok($image, '?'))) {
                return;
            }
            $image = SITE_URL . $image;
        }

        $this->meta->addMetaData(MetaData::forProperty('og:image', $image, ['property', 'content']), $overwrite);
        if (SITE_PROTOCOL === 'https') {
            $this->meta->addMetaData(
                MetaData::forProperty('og:image:secure_url', $image, ['property', 'content']),
                $overwrite
            );
        }

        if ($width !== 0) {
            $this->meta->addMetaData(
                MetaData::forProperty('og:image:width', $width, ['property', 'content']),
                $overwrite
            );
        }

        if ($height !== 0) {
            $this->meta->addMetaData(
                MetaData::forProperty('og:image:height', $height, ['property', 'content']),
                $overwrite
            );
        }
    }

    public function addRssLink(string $title, string $link): void
    {
        $this->meta->addMetaLink(MetaLink::rss($link, $title), true);
    }

    /**
     * Extract images from content that can be added add Open Graph image
     *
     * @param string $content The content (where from to extract the images).
     */
    public function extractOpenGraphImages(string $content): void
    {
        $images = [];

        // check if any img-tags are present in the content
        if (preg_match_all('/<img.*?src="(.*?)".*?\/>/i', $content, $images)) {
            // loop all found images and add to Open Graph metadata
            foreach ($images[1] as $image) {
                $this->addOpenGraphImage($image);
            }
        }
    }

    public function getMetaCustom(): string
    {
        return (string) $this->metaCustom;
    }

    public function getPageTitle(): string
    {
        return (string) $this->pageTitle;
    }

    /**
     * Parse the header into the template
     */
    public function parse(): void
    {
        $facebook = new Facebook($this->get('fork.settings'));
        $facebook->addOpenGraphMeta($this);
        $this->parseSeo();

        // in debug mode we don't want our pages to be indexed.
        if ($this->getContainer()->getParameter('kernel.debug')) {
            $this->meta->addMetaData(MetaData::forName('robots', 'noindex, nofollow'), true);
        }

        $this->template->assignGlobal('meta', $this->meta);
        $this->template->assignGlobal('metaCustom', $this->getMetaCustom());
        $this->cssFiles->parse($this->template, 'cssFiles');
        $this->jsFiles->parse($this->template, 'jsFiles');

        $siteHTMLHeader = (string) $this->get('fork.settings')->get('Core', 'site_html_header', '') . "\n";
        $siteHTMLHeader .= new GoogleAnalytics(
            $this->get('fork.settings'),
            Model::getRequest()->getHttpHost(),
            $this->get('fork.cookie')
        );
        $siteHTMLHeader .= "\n" . $this->jsData;
        $this->template->assignGlobal('siteHTMLHeader', trim($siteHTMLHeader));

        $this->template->assignGlobal('pageTitle', $this->getPageTitle());
        $this->template->assignGlobal('contentTitle', $this->getContentTitle());
        $this->template->assignGlobal(
            'siteTitle',
            (string) $this->get('fork.settings')->get('Core', 'site_title_' . LANGUAGE, SITE_DEFAULT_TITLE)
        );
    }

    private function getCanonical(): string
    {
        $queryString = trim($this->url->getQueryString(), '/');
        $language = $this->get('fork.settings')->get('Core', 'default_language', SITE_DEFAULT_LANGUAGE);
        if ($queryString === $language) {
            $this->canonical = rtrim(SITE_URL, '/');

            if ($this->getContainer()->getParameter('site.multilanguage')) {
                $this->canonical .= '/' . $language;
            }
        }

        if (!empty($this->canonical)) {
            return $this->canonical;
        }

        // get the chunks of the current url
        $urlChunks = parse_url($this->url->getQueryString());

        // a canonical url should contain the domain. So make sure you
        // redirect your website to a single url with .htaccess
        $url = rtrim(SITE_URL, '/');
        if (isset($urlChunks['port'])) {
            $url .= ':' . $urlChunks['port'];
        }
        if (isset($urlChunks['path'])) {
            $url .= $urlChunks['path'];
        }

        // any items provided through GET?
        if (!isset($urlChunks['query']) || !Model::getRequest()->query->has('page')) {
            return $url;
        }

        return $url . '?page=' . Model::getRequest()->query->get('page');
    }

    /**
     * Parse SEO specific data
     */
    private function parseSeo(): void
    {
        if ($this->get('fork.settings')->get('Core', 'seo_noodp', false)) {
            $this->meta->addMetaData(MetaData::forName('robots', 'noodp'));
        }

        if ($this->get('fork.settings')->get('Core', 'seo_noydir', false)) {
            $this->meta->addMetaData(MetaData::forName('robots', 'noydir'));
        }

        $charset = $this->getContainer()->getParameter('kernel.charset');
        if ($charset === 'utf-8') {
            $this->meta->addMetaLink(MetaLink::canonical(\SpoonFilter::htmlspecialchars($this->getCanonical())));

            return;
        }

        $this->meta->addMetaLink(MetaLink::canonical(\SpoonFilter::htmlentities($this->getCanonical())));
    }

    public function setCanonicalUrl(string $canonicalUrl): void
    {
        if (strpos($canonicalUrl, '/') === 0) {
            $canonicalUrl = SITE_URL . $canonicalUrl;
        }

        $this->canonical = $canonicalUrl;
    }

    public function setMetaCustom(string $meta = null): void
    {
        $this->metaCustom = $meta;
    }

    public function setContentTitle(string $contentTitle): void
    {
        $this->contentTitle = $contentTitle;
    }

    public function getContentTitle(): string
    {
        return $this->contentTitle;
    }

    /**
     * @param string $value The page title to be set or to be prepended.
     * @param bool $overwrite Should the existing page title be overwritten?
     */
    public function setPageTitle(string $value, bool $overwrite = false): void
    {
        $this->setContentTitle($value);

        $value = trim($value);

        if ($overwrite) {
            $this->pageTitle = $value;

            return;
        }

        if (empty($value)) {
            $this->pageTitle = $this->get('fork.settings')->get('Core', 'site_title_' . LANGUAGE, SITE_DEFAULT_TITLE);

            return;
        }

        if ($this->pageTitle === '') {
            $this->pageTitle = $this->get('fork.settings')->get('Core', 'site_title_' . LANGUAGE, SITE_DEFAULT_TITLE);
            $this->pageTitle = $value . ' -  ' . $this->pageTitle;

            return;
        }

        $this->pageTitle = $value . ' - ' . $this->pageTitle;
    }

    /**
     * @param string $title The title (maximum 70 characters)
     * @param string $description A brief description of the card (maximum 200 characters)
     * @param string $imageUrl The URL of the image (minimum 280x150 and <1MB)
     * @param string $cardType The cardtype, possible types: https://dev.twitter.com/cards/types
     * @param string $siteHandle (optional)  Twitter handle of the site
     * @param string $creatorHandle (optional) Twitter handle of the author
     */
    public function setTwitterCard(
        string $title,
        string $description,
        string $imageUrl,
        string $cardType = 'summary',
        string $siteHandle = null,
        string $creatorHandle = null
    ): void {
        $this->meta->addMetaData(MetaData::forName('twitter:card', $cardType));
        $this->meta->addMetaData(MetaData::forName('twitter:title', $title));
        $this->meta->addMetaData(MetaData::forName('twitter:description', $description));
        $this->meta->addMetaData(MetaData::forName('twitter:image', $imageUrl));

        if ($siteHandle !== null) {
            $this->meta->addMetaData(MetaData::forName('twitter:site', $siteHandle));
        }

        if ($creatorHandle !== null) {
            $this->meta->addMetaData(MetaData::forName('twitter:creator', $creatorHandle));
        }
    }
}
