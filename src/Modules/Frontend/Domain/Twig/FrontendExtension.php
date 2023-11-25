<?php

namespace ForkCMS\Modules\Frontend\Domain\Twig;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use ForkCMS\Modules\Frontend\Domain\Google\Analytics\GoogleAnalytics;
use ForkCMS\Modules\Frontend\Domain\Google\TagManager\TagManager;
use ForkCMS\Modules\Frontend\Domain\Privacy\ConsentDialog;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class FrontendExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly ModuleSettings $moduleSettings,
        private readonly RequestStack $requestStack,
        private readonly ConsentDialog $consentDialog,
        private readonly TagManager $tagManager,
        private readonly GoogleAnalytics $googleAnalytics,
    ) {
    }

    public function getGlobals(): array
    {
        $mainRequest = $this->requestStack->getMainRequest();
        if ($mainRequest === null) {
            return [];
        }

        $globals = [
            'SITE_TITLE' => $this->moduleSettings->get(
                ModuleName::frontend(),
                'site_title_' . Locale::current()->value,
                $_ENV['SITE_DEFAULT_TITLE']
            ),
        ];

        if (str_starts_with($mainRequest->attributes->get('_route'), 'backend_')) {
            return $globals;
        }

        $globals['PRIVACY_CONSENT_ENABLED'] = $this->consentDialog->isDialogEnabled();
        $globals['PRIVACY_CONSENT_DIALOG_HIDE'] = !$this->consentDialog->shouldDialogBeShown();
        $globals['PRIVACY_CONSENT_DIALOG_LEVELS'] = $this->consentDialog->getLevels();
        $globals['SITE_HTML_HEAD'] = $this->getSiteHtmlHead();
        $globals['SITE_HTML_START_OF_BODY'] = $this->getSiteHtmlStartOfBody();
        $globals['SITE_HTML_END_OF_BODY'] = trim(
            $this->moduleSettings->get(ModuleName::frontend(), 'site_html_end_of_body')
        );

        return $globals;
    }

    private function getSiteHtmlHead(): string
    {
        return trim(
            implode(
                PHP_EOL,
                [
                    $this->moduleSettings->get(ModuleName::frontend(), 'site_html_head'),
                    $this->tagManager->generateHeadCode(),
                    $this->googleAnalytics->generateHeadCode(),
                ]
            )
        );
    }

    private function getSiteHtmlStartOfBody(): string
    {
        return trim(
            implode(
                PHP_EOL,
                [
                    $this->moduleSettings->get(ModuleName::frontend(), 'site_html_start_of_body'),
                    $this->tagManager->generateStartOfBodyCode(),
                ]
            )
        );
    }
}
