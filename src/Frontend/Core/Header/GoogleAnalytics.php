<?php

namespace Frontend\Core\Header;

use Common\Core\Cookie;
use Common\ModulesSettings;
use ForkCMS\Privacy\ConsentDialog;

final class GoogleAnalytics
{
    /** @var ModulesSettings */
    private $modulesSettings;

    /** @var Cookie */
    private $cookie;

    /** @var ConsentDialog */
    private $consentDialog;

    public function __construct(ModulesSettings $modulesSettings, ConsentDialog $consentDialog, Cookie $cookie)
    {
        $this->modulesSettings = $modulesSettings;
        $this->consentDialog = $consentDialog;
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
        // @deprecated remove this in Fork 6, the privacy consent dialog should be used
        if ($this->modulesSettings->get('Core', 'show_cookie_bar', false) && !$this->cookie->hasAllowedCookies()) {
            return true;
        }

        // if the consent dialog is disabled we will anonymize by default
        if (!$this->modulesSettings->get('Core', 'show_consent_dialog', false)) {
            return true;
        }

        // the visitor has agreed to be tracked
        if ($this->consentDialog->hasAgreedTo('statistics')) {
            return false;
        }

        // fallback
        return true;
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
