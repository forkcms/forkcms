<?php

namespace Frontend\Core\Header;

use Common\Core\Cookie;
use Common\ModulesSettings;

final class GoogleAnalytics
{
    /** @var ModulesSettings */
    private $modulesSettings;

    /** @var string */
    private $httpHost;

    /** @var Cookie */
    private $cookie;

    public function __construct(ModulesSettings $modulesSettings, string $httpHost, Cookie $cookie)
    {
        $this->modulesSettings = $modulesSettings;
        $this->httpHost = $httpHost;
        $this->cookie = $cookie;
    }

    private function shouldAddGoogleAnalyticsHtml(): bool
    {
        // @deprecated fallback to site_html_header as this was used in the past
        $siteHTMLHead = (string) $this->modulesSettings->get('Core', 'site_html_head', $this->modulesSettings->get('Core', 'site_html_header', ''));
        $siteHTMLStartOfBody = (string) $this->modulesSettings->get('Core', 'site_html_start_of_body', $this->modulesSettings->get('Core', 'site_start_of_body_scripts', ''));
        $siteHTMLEndOfBody = (string) $this->modulesSettings->get('Core', 'site_html_end_of_body', $this->modulesSettings->get('Core', 'site_html_footer', ''));

        $webPropertyId = (string) $this->modulesSettings->get('Analytics', 'web_property_id', '');

        // check if GTM is present, if so we expect Google Analytics to be added in GTM
        $searchFor = 'GTM-';
        if (mb_stripos($siteHTMLHead, $searchFor) !== false && mb_stripos($siteHTMLStartOfBody, $searchFor) !== false) {
            return false;
        }

        // no web property, so we can't build an Analytics code.
        if ($webPropertyId === '') {
            return false;
        }

        // if the web property is not present in the site wide HTML we should parse add Analytics code.
        return mb_strpos($siteHTMLHead, $webPropertyId) === false
               && mb_strpos($siteHTMLEndOfBody, $webPropertyId) === false
               && mb_strpos($siteHTMLStartOfBody, $webPropertyId) === false;
    }

    private function shouldAnonymize(): bool
    {
        return $this->modulesSettings->get('Core', 'show_cookie_bar', false) && !$this->cookie->hasAllowedCookies();
    }

    private function getGoogleAnalyticsEvent(): string
    {
        if ($this->shouldAnonymize()) {
            return 'ga(\'send\', \'pageview\', {\'anonymizeIp\': true});';
        }

        return 'ga(\'send\', \'pageview\');';
    }

    public function __toString(): string
    {
        if (!$this->shouldAddGoogleAnalyticsHtml()) {
            return '';
        }

        $webPropertyId = $this->modulesSettings->get('Analytics', 'web_property_id', null);

        $trackingCode = '<script>
                          (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
                          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                          })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');
                          ga(\'create\', \'' . $webPropertyId . '\', \'' . $this->httpHost
                        /**string */. '\');
                        ';
        $trackingCode .= $this->getGoogleAnalyticsEvent();
        $trackingCode .= '</script>';

        return $trackingCode;
    }
}
