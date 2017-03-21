<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\KernelInterface;
use Frontend\Core\Engine\Base\Object as FrontendBaseObject;
use Frontend\Core\Engine\Navigation as FrontendNavigation;

/**
 * This class will be used to alter the footer-part of the HTML-document that will be created by the frontend.
 */
class Footer extends FrontendBaseObject
{
    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->getContainer()->set('footer', $this);
    }

    /**
     * Parse the footer into the template
     */
    public function parse()
    {
        $footerLinks = (array) Navigation::getFooterLinks();
        $this->tpl->addGlobal('footerLinks', $footerLinks);

        $siteHTMLFooter = (string) $this->get('fork.settings')->get('Core', 'site_html_footer', null);

        $facebookAdminIds = $this->get('fork.settings')->get('Core', 'facebook_admin_ids', null);
        $facebookAppId = $this->get('fork.settings')->get('Core', 'facebook_app_id', null);

        // facebook admins given?
        if ($facebookAdminIds !== null || $facebookAppId !== null) {
            // add Facebook container
            $siteHTMLFooter .= $this->getFacebookHtml($facebookAppId);
        }

        // add Google sitelinks search box code if wanted.
        if ($this->get('fork.settings')->get('Search', 'use_sitelinks_search_box', true)) {
            $searchUrl = FrontendNavigation::getURLForBlock('Search');
            $url404 = FrontendNavigation::getURL(404);
            if ($searchUrl !== $url404) {
                $siteHTMLFooter .= $this->getSiteLinksCode($searchUrl);
            }
        }

        // assign site wide html
        $this->tpl->addGlobal('siteHTMLFooter', $siteHTMLFooter);
    }

    /**
     * Builds the HTML needed for Facebook to be initialized
     *
     * @param  string $facebookAppId The application id used to interact with FB
     *
     * @return string                HTML and JS needed to initialize FB JavaScript
     */
    protected function getFacebookHtml($facebookAppId)
    {
        // build correct locale
        $locale = mb_strtolower(LANGUAGE) . '_' . mb_strtoupper(LANGUAGE);

        // reform some locale
        switch (LANGUAGE) {
            case 'en':
                $locale = 'en_US';
                break;

            case 'zh':
                $locale = 'zh_CN';
                break;

            case 'cs':
                $locale = 'cs_CZ';
                break;

            case 'el':
                $locale = 'el_GR';
                break;

            case 'ja':
                $locale = 'ja_JP';
                break;

            case 'sv':
                $locale = 'sv_SE';
                break;

            case 'uk':
                $locale = 'uk_UA';
                break;
        }

        // add the fb-root div
        $facebookHtml = "\n" . '<div id="fb-root"></div>' . "\n";

        // add facebook JavaScript
        $facebookHtml .= '<script>' . "\n";
        if ($facebookAppId !== null) {
            $facebookHtml .= '    window.fbAsyncInit = function() {' . "\n";
            $facebookHtml .= '        FB.init({' . "\n";
            $facebookHtml .= '            appId: "' . $facebookAppId . '",' . "\n";
            $facebookHtml .= '            status: true,' . "\n";
            $facebookHtml .= '            cookie: true,' . "\n";
            $facebookHtml .= '            xfbml: true,' . "\n";
            $facebookHtml .= '            oauth: true' . "\n";
            $facebookHtml .= '        });' . "\n";
            $facebookHtml .= '        jsFrontend.facebook.afterInit();' . "\n";
            $facebookHtml .= '    };' . "\n";
        }

        $facebookHtml .= '    (function(d, s, id){' . "\n";
        $facebookHtml .= '        var js, fjs = d.getElementsByTagName(s)[0];' . "\n";
        $facebookHtml .= '        if (d.getElementById(id)) {return;}' . "\n";
        $facebookHtml .= '        js = d.createElement(s); js.id = id;' . "\n";
        $facebookHtml .= '        js.src = "//connect.facebook.net/' . $locale . '/all.js";' . "\n";
        $facebookHtml .= '        fjs.parentNode.insertBefore(js, fjs);' . "\n";
        $facebookHtml .= '    }(document, \'script\', \'facebook-jssdk\'));' . "\n";
        $facebookHtml .= '</script>';

        return $facebookHtml;
    }

    /**
     * Returns the code needed to get a site links search box in Google.
     * More information can be found on the offical Google documentation:
     * https://developers.google.com/webmasters/richsnippets/sitelinkssearch
     *
     * @param  string $searchUrl The url to the search page
     *
     * @return string            The script needed for google
     */
    protected function getSiteLinksCode($searchUrl)
    {
        $siteLinksCode = '<script type="application/ld+json">' . "\n";
        $siteLinksCode .= '{' . "\n";
        $siteLinksCode .= '    "@context": "https://schema.org",' . "\n";
        $siteLinksCode .= '    "@type": "WebSite",' . "\n";
        $siteLinksCode .= '    "url": "' . SITE_URL . '",' . "\n";
        $siteLinksCode .= '    "potentialAction": {' . "\n";
        $siteLinksCode .= '        "@type": "SearchAction",' . "\n";
        $siteLinksCode .= '        "target": "' . SITE_URL . $searchUrl . '?form=search&q_widget={q_widget}",' . "\n";
        $siteLinksCode .= '        "query-input": "name=q_widget"' . "\n";
        $siteLinksCode .= '    }' . "\n";
        $siteLinksCode .= '}' . "\n";
        $siteLinksCode .= '</script>';

        return $siteLinksCode;
    }
}
