<?php

namespace ForkCMS\Google\TagManager;

use Common\Core\Cookie;
use Common\ModulesSettings;

class TagManager
{
    /**
     * @var ModulesSettings
     */
    private $modulesSettings;

    /**
     * @var DataLayer
     */
    private $dataLayer;

    /**
     * @var Cookie
     */
    private $cookie;

    public function __construct(ModulesSettings $modulesSettings, DataLayer $dataLayer, Cookie $cookie)
    {
        $this->modulesSettings = $modulesSettings;
        $this->dataLayer = $dataLayer;
        $this->cookie = $cookie;

        $this->addDefaultDataLayerVariables();
    }

    private function addDefaultDataLayerVariables(): void
    {
        $this->dataLayer->set('anonymizeIp', $this->shouldAnonymizeIp());
    }

    private function shouldAnonymizeIp(): bool
    {
        return $this->modulesSettings->get('Core', 'show_cookie_bar', false) && !$this->cookie->hasAllowedCookies();
    }

    private function shouldAddCode(): bool
    {
        $googleAnalyticsTrackingId = $this->modulesSettings->get(
            'Core',
            'google_tracking_google_tag_manager_container_id',
            ''
        );

        return ($googleAnalyticsTrackingId !== '');
    }

    public function generateHeadCode(): string
    {
        if (!$this->shouldAddCode()) {
            return '';
        }

        $codeLines = [
            '<!-- Google Tag Manager -->',
            '<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':',
            'new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],',
            'j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=',
            '\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);',
            '})(window,document,\'script\',\'dataLayer\',\'%1$s\');</script>',
            '<!-- End Google Tag Manager -->',
        ];

        $code = sprintf(
            implode("\n", $codeLines) . "\n",
            $this->modulesSettings->get('Core', 'google_tracking_google_tag_manager_container_id', null)
        );

        if (!empty($this->dataLayer->all())) {
            $code = $this->dataLayer->generateHeadCode() . "\n" . $code;
        }

        return $code;
    }

    public function generateStartOfBodyCode(): string
    {
        if (!$this->shouldAddCode()) {
            return '';
        }

        $codeLines = [
            '<!-- Google Tag Manager (noscript) -->',
            '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%1$s%2$s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>',
            '<!-- End Google Tag Manager (noscript) -->'
        ];

        return sprintf(
            implode("\n", $codeLines) . "\n",
            $this->modulesSettings->get('Core', 'google_tracking_google_tag_manager_container_id', null),
            $this->dataLayer->generateNoScriptParameters()
        );
    }
}
