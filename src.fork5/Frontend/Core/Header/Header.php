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
