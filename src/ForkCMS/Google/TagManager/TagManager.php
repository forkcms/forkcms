<?php

namespace ForkCMS\Google\TagManager;

use Common\ModulesSettings;

class TagManager
{
    /**
     * @var ModulesSettings
     */
    private $modulesSettings;

    public function __construct(ModulesSettings $modulesSettings)
    {
        $this->modulesSettings = $modulesSettings;
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
        $code = [
            '<!-- Google Tag Manager -->',
            '<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':',
            'new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],',
            'j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=',
            '\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);',
            '})(window,document,\'script\',\'dataLayer\',\'%1$s\');</script>',
            '<!-- End Google Tag Manager -->',
        ];

        return $this->generateCode($code);
    }

    public function generateStartOfBodyCode(): string
    {
        $code = [
            '<!-- Google Tag Manager (noscript) -->',
            '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%1$s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>',
            '<!-- End Google Tag Manager (noscript) -->'
        ];

        return $this->generateCode($code);
    }

    private function generateCode(array $code): string
    {
        if (!$this->shouldAddCode()) {
            return '';
        }

        return sprintf(
            implode("\n", $code) . "\n",
            $this->modulesSettings->get('Core', 'google_tracking_google_tag_manager_container_id', null)
        );
    }
}
