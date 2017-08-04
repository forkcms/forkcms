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
        $siteHTMLHeader = (string) $this->modulesSettings->get('Core', 'site_html_header', '');
        $siteHTMLFooter = (string) $this->modulesSettings->get('Core', 'site_html_footer', '');
        $webPropertyId = (string) $this->modulesSettings->get('Analytics', 'web_property_id', null);

        return $webPropertyId !== ''
               && mb_strpos($siteHTMLHeader, $webPropertyId) === false
               && mb_strpos($siteHTMLFooter, $webPropertyId) === false;
    }

    private function shouldAnonymize(): bool
    {
        return $this->modulesSettings->get('Core', 'show_cookie_bar', false) && !Cookie::hasAllowedCookies();
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
