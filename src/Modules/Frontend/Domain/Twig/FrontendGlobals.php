<?php

namespace ForkCMS\Modules\Frontend\Domain\Twig;

use ForkCMS\Core\Domain\Header\Header;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use ForkCMS\Modules\Frontend\Domain\Google\Analytics\GoogleAnalytics;
use ForkCMS\Modules\Frontend\Domain\Google\TagManager\TagManager;
use ForkCMS\Modules\Frontend\Domain\Privacy\ConsentDialog;
use Twig\Environment;

final class FrontendGlobals
{
    public function __construct(
        private readonly Environment $twig,
        private readonly ConsentDialog $consentDialog,
        private readonly Header $header,
        private readonly ModuleSettings $moduleSettings,
        private readonly TagManager $tagManager,
        private readonly GoogleAnalytics $googleAnalytics,
    ) {
    }

    public function addGlobals(): void
    {
        $this->header->parse($this->twig);
        $this->addPrivacyConsent();
        $this->addExtraHTML();
    }

    private function addPrivacyConsent(): void
    {
        $this->twig->addGlobal('privacyConsentEnabled', $this->consentDialog->isDialogEnabled());
        $this->twig->addGlobal('privacyConsentDialogHide', !$this->consentDialog->shouldDialogBeShown());
        $this->twig->addGlobal('privacyConsentDialogLevels', $this->consentDialog->getLevels());
    }

    private function addExtraHTML(): void
    {
        $frontendModuleName = ModuleName::fromString('Frontend');
        $head = [
            $this->moduleSettings->get($frontendModuleName, 'site_html_head'),
            $this->tagManager->generateHeadCode(),
            $this->googleAnalytics->generateHeadCode(),
        ];

        $startOfBody = [
            $this->moduleSettings->get($frontendModuleName, 'site_html_start_of_body'),
            $this->tagManager->generateStartOfBodyCode(),
        ];

        $this->twig->addGlobal('site_html_head', trim(implode(PHP_EOL, $head)));
        $this->twig->addGlobal('site_html_start_of_body', trim(implode(PHP_EOL, $startOfBody)));
        $this->twig->addGlobal(
            'site_html_end_of_body',
            trim($this->moduleSettings->get($frontendModuleName, 'site_html_end_of_body'))
        );
    }
}
