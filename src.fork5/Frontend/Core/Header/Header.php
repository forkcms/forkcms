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
}
