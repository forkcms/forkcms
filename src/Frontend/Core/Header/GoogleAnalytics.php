<?php

namespace Frontend\Core\Header;

use Common\Core\Cookie;
use Common\ModulesSettings;

final class GoogleAnalytics
{
    /** @var ModulesSettings */
    private $modulesSettings;

    /** @var Cookie */
    private $cookie;

    /**
     * @var string
     * @deprecated this isn't used anymore
     */
    private $httpHost;

    public function __construct(ModulesSettings $modulesSettings, string $httpHost, Cookie $cookie)
    {
        // @deprecated $httpHost is deprecated
        $this->httpHost = $httpHost;
        $this->modulesSettings = $modulesSettings;
        $this->cookie = $cookie;
    }

    private function shouldAddGoogleAnalyticsHtml(): bool
    {
        $googleAnalyticsTrackingId = $this->modulesSettings->get(
            'Core',
            'google_tracking_google_analytics_tracking_id',
            ''
        );

        return ($googleAnalyticsTrackingId !== '');
    }

    private function shouldAnonymize(): bool
    {
        return $this->modulesSettings->get('Core', 'show_cookie_bar', false) && !$this->cookie->hasAllowedCookies();
    }

    public function __toString(): string
    {
        if (!$this->shouldAddGoogleAnalyticsHtml()) {
            return '';
        }

        $code = [
            '<!-- Global site tag (gtag.js) - Google Analytics -->',
            '<script async src="https://www.googletagmanager.com/gtag/js?id=%1$s"></script>',
            '<script>',
            '  window.dataLayer = window.dataLayer || [];',
            '  function gtag(){dataLayer.push(arguments);}',
            '  gtag(\'js\', new Date());',
        ];

        if ($this->shouldAnonymize()) {
            $code[] = '  gtag(\'config\', \'%1$s\', { \'anonymize_ip\': true });';
        } else {
            $code[] = '  gtag(\'config\', \'%1$s\');';
        }

        $code[] = '</script>';

        return sprintf(
            implode("\n", $code) . "\n",
            $this->modulesSettings->get('Core', 'google_tracking_google_analytics_tracking_id', null)
        );
    }
}
