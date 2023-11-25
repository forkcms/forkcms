<?php

namespace Frontend\Core\Header;

use Common\Core\Header\Asset;
use Common\Core\Header\AssetCollection;
use Common\Core\Header\JsData;
use Common\Core\Header\Minifier;
use Common\Core\Header\Priority;
use ForkCMS\App\KernelLoader;
use ForkCMS\Google\TagManager\TagManager;
use ForkCMS\Privacy\ConsentDialog;
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
        if (!$this->getContainer()->get('fork.validator.url')->isExternalUrl($image)) {
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

    /**
     * Parse the header into the template
     */
    public function parse(): void
    {
        // @deprecated remove this in Fork 6, check if this still should be used.
        $facebook = new Facebook($this->get('fork.settings'));
        $facebook->addOpenGraphMeta($this);
        $this->parseSeo();

        // in debug mode we don't want our pages to be indexed.
        if ($this->getContainer()->getParameter('kernel.debug')) {
            $this->meta->addMetaData(MetaData::forName('robots', 'noindex, nofollow'), true);
        }

        $siteHTMLHead = '';
        $siteHTMLStartOfBody = '';

        // Add Google Tag Manager code if needed
        $googleTagManagerContainerId = $this->get('fork.settings')->get('Core', 'google_tracking_google_tag_manager_container_id', '');
        if ($googleTagManagerContainerId !== '') {
            $googleTagManager = $this->get(TagManager::class);
            $siteHTMLHead .= $googleTagManager->generateHeadCode() . "\n";

            $siteHTMLStartOfBody .= $googleTagManager->generateStartOfBodyCode() . "\n";
        }

        // Add Google Analytics code if needed
        $googleAnalyticsTrackingId = $this->get('fork.settings')->get('Core', 'google_tracking_google_analytics_tracking_id', '');
        if ($googleAnalyticsTrackingId !== '') {
            $siteHTMLHead .= new GoogleAnalytics(
                $this->get('fork.settings'),
                $this->get(ConsentDialog::class),
                $this->get('fork.cookie')
            ) . "\n";
        }
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

    /**
     * @deprecated
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

        if ($this->pageTitle === null || $this->pageTitle === '') {
            $this->pageTitle = $this->get('fork.settings')->get('Core', 'site_title_' . LANGUAGE, SITE_DEFAULT_TITLE);
            $this->pageTitle = $value . ' -  ' . $this->pageTitle;

            return;
        }

        $this->pageTitle = $value . ' - ' . $this->pageTitle;
    }
}
