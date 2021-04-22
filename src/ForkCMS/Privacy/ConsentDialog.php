<?php

namespace ForkCMS\Privacy;

use Common\Core\Cookie;
use Common\ModulesSettings;

class ConsentDialog
{
    /**
     * @var ModulesSettings
     */
    private $settings;

    /**
     * @var Cookie
     */
    private $cookie;

    public function __construct(ModulesSettings $settings, Cookie $cookie)
    {
        $this->settings = $settings;
        $this->cookie = $cookie;
    }

    public function shouldDialogBeShown(): bool
    {
        // the cookiebar is hidden within the settings, so don't show it
        if (!$this->settings->get('Core', 'show_consent_dialog', false)) {
            return false;
        }

        // no levels mean there should not be any consent
        if (empty($this->getLevels())) {
            return false;
        }

        // if the hash in the cookie is the same as the current has it means the user
        // has already stored their preferences
        if ($this->cookie->get('privacy_consent_hash', '') === $this->getLevelsHash()) {
            return false;
        }

        return true;
    }

    public function getLevels(bool $includeFunctional = false): array
    {
        $levels = [];
        if ($includeFunctional) {
            $levels = ['functional'];
        }

        $levels = array_filter(array_merge(
            $levels,
            $this->settings->get('Core', 'privacy_consent_levels', [])
        ));

        return $levels;
    }

    public function getLevelsHash(): string
    {
        $levels = $this->getLevels(true);
        sort($levels);

        return md5(implode('|', $levels));
    }

    public function getVisitorChoices(): array
    {
        $choices = [
            'functional' => true,
        ];
        $levels = $this->getLevels(false);
        foreach ($levels as $level) {
            $choices[$level] = $this->cookie->get('privacy_consent_level_' . $level . '_agreed', '0') === '1';
        }

        return $choices;
    }

    public function getJsData(): array
    {
        return [
            'possibleLevels' => $this->getLevels(true),
            'levelsHash' => $this->getLevelsHash(),
            'visitorChoices' => $this->getVisitorChoices(),
        ];
    }

    public function hasAgreedTo(string $level): bool
    {
        $choices = $this->getVisitorChoices();
        if (!array_key_exists($level, $choices)) {
            return false;
        }

        return $choices[$level];
    }
}
