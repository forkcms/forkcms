<?php

namespace ForkCMS\Modules\Frontend\Domain\Google\Analytics;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use ForkCMS\Modules\Frontend\Domain\Privacy\ConsentDialog;

final class GoogleAnalytics
{
    public function __construct(
        private readonly ConsentDialog $consentDialog,
        private readonly ModuleSettings $moduleSettings
    ) {
    }

    private function isEnabled(): bool
    {
        return $this->moduleSettings->get(
            ModuleName::frontend(),
            'google_analytics_enabled',
            false
        );
    }

    private function shouldAnonymize(): bool
    {
        // if the consent dialog is disabled we will anonymize by default
        if (!$this->moduleSettings->get(ModuleName::frontend(), 'consent_dialog_enabled', false)) {
            return true;
        }

        return $this->consentDialog->hasAgreedTo(ConsentDialog::CONSENT_DIALOG_ANALYTICS_TECHNICAL_NAME);
    }

    public function generateHeadCode(): string
    {
        if (!$this->isEnabled()) {
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
            $this->moduleSettings->get(
                ModuleName::frontend(),
                'google_tracking_google_analytics_tracking_id'
            )
        );
    }
}
