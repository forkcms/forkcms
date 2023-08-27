<?php

namespace ForkCMS\Modules\Frontend\Domain\Privacy;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use JsonSerializable;
use Symfony\Component\HttpFoundation\RequestStack;

class ConsentDialog implements JsonSerializable
{
    public const CONSENT_DIALOG_ANALYTICS_TECHNICAL_NAME = 'analytics';

    public function __construct(private readonly ModuleSettings $settings, private readonly RequestStack $requestStack)
    {
    }

    public function isDialogEnabled(): bool
    {
        return $this->settings->get(ModuleName::fromString('Frontend'), 'consent_dialog_enabled', false);
    }

    public function shouldDialogBeShown(): bool
    {
        // the consent dialog is hidden within the settings, so don't show it
        if (!$this->settings->get(ModuleName::fromString('Frontend'), 'consent_dialog_enabled', false)) {
            return false;
        }

        // no levels mean there should not be any consent
        if (count($this->getLevels()) === 0) {
            return false;
        }

        // if the hash in the cookie is the same as the current hash
        // it means the user has already stored their preferences
        $privacyConsentHash = $this->requestStack->getCurrentRequest()->cookies->get('privacy_consent_hash', '');

        return $privacyConsentHash !== $this->getLevelsHash();
    }

    /** @return string[] */
    public function getLevels(bool $includeFunctional = false): array
    {
        $levels = [];
        if ($includeFunctional) {
            $levels[] = 'functional';
        }

        $frontendModuleName = ModuleName::fromString('Frontend');
        $customLevels = $this->settings->get($frontendModuleName, 'consent_dialog_levels', []);
        if (
            $this->settings->get($frontendModuleName, 'google_analytics_enabled', false)
            || $this->settings->get($frontendModuleName, 'google_tag_manager_enabled', false)
        ) {
            if (!in_array(self::CONSENT_DIALOG_ANALYTICS_TECHNICAL_NAME, $customLevels, true)) {
                $levels[] = self::CONSENT_DIALOG_ANALYTICS_TECHNICAL_NAME;
            }
        }
        $customLevels = $this->settings->get($frontendModuleName, 'consent_dialog_levels', []);

        return array_filter(array_merge($levels, $customLevels));
    }

    public function getLevelsHash(): string
    {
        $levels = $this->getLevels(true);
        sort($levels);

        return md5(implode('|', $levels));
    }

    /** @return array<string, bool> */
    public function getVisitorChoices(): array
    {
        $choices = [
            'functional' => true,
        ];
        $levels = $this->getLevels();
        foreach ($levels as $level) {
            $enabled = $this->requestStack->getCurrentRequest()->cookies->get(
                'privacy_consent_level_' . $level . '_agreed',
                '0'
            );
            $choices[$level] = $enabled === '1';
        }

        return $choices;
    }

    public function hasAgreedTo(string $level): bool
    {
        $choices = $this->getVisitorChoices();
        if (!array_key_exists($level, $choices)) {
            return false;
        }

        return $choices[$level];
    }

    /** @return array{possibleLevels: string[], levelsHash: string, visitorChoices: array<string, bool>} */
    public function jsonSerialize(): array
    {
        return [
            'possibleLevels' => $this->getLevels(true),
            'levelsHash' => $this->getLevelsHash(),
            'visitorChoices' => $this->getVisitorChoices(),
        ];
    }
}
