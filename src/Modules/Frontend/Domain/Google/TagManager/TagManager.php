<?php

namespace ForkCMS\Modules\Frontend\Domain\Google\TagManager;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use ForkCMS\Modules\Frontend\Domain\Privacy\ConsentDialog;

class TagManager
{
    public function __construct(
        private readonly ModuleSettings $moduleSettings,
        private readonly DataLayer $dataLayer,
        private readonly ConsentDialog $consentDialog
    ) {
        $this->addDefaultDataLayerVariables();
    }

    private function addDefaultDataLayerVariables(): void
    {
        $this->dataLayer->set('anonymizeIp', $this->shouldAnonymizeIp());

        // only if the consent dialog is enabled we should extra variables
        if ($this->moduleSettings->get(ModuleName::fromString('Frontend'), 'consent_dialog_enabled', false)) {
            foreach ($this->consentDialog->getVisitorChoices() as $level => $choice) {
                $this->dataLayer->set('privacyConsentLevel' . ucfirst($level) . 'Agreed', $choice);
            }
        }
    }

    private function shouldAnonymizeIp(): bool
    {
        // if the consent dialog is disabled we will anonymize by default
        if (!$this->moduleSettings->get(ModuleName::fromString('Frontend'), 'consent_dialog_enabled', false)) {
            return true;
        }

        return $this->consentDialog->hasAgreedTo(ConsentDialog::CONSENT_DIALOG_ANALYTICS_TECHNICAL_NAME);
    }

    private function isEnabled(): bool
    {
        return $this->moduleSettings->get(
            ModuleName::fromString('Frontend'),
            'google_tag_manager_enabled',
            false
        );
    }

    public function generateHeadCode(): string
    {
        if (!$this->isEnabled()) {
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
            $this->moduleSettings->get(
                ModuleName::fromString('Frontend'),
                'google_tracking_google_tag_manager_container_id'
            )
        );

        if (!empty($this->dataLayer->all())) {
            $code = $this->dataLayer->generateHeadCode() . "\n" . $code;
        }

        return $code;
    }

    public function generateStartOfBodyCode(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        $codeLines = [
            '<!-- Google Tag Manager (noscript) -->',
            // @codingStandardsIgnoreLine
            '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%1$s%2$s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>',
            '<!-- End Google Tag Manager (noscript) -->'
        ];

        return sprintf(
            implode("\n", $codeLines) . "\n",
            $this->moduleSettings->get(
                ModuleName::fromString('Frontend'),
                'google_tracking_google_tag_manager_container_id'
            ),
            $this->dataLayer->generateNoScriptParameters()
        );
    }
}
