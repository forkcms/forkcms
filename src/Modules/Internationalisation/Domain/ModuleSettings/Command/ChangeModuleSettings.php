<?php

namespace ForkCMS\Modules\Internationalisation\Domain\ModuleSettings\Command;

use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocale;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleDataTransferObject;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use RuntimeException;

final class ChangeModuleSettings
{
    public Locale $defaultForWebsite;
    public Locale $defaultForUser;

    /** @var InstalledLocaleDataTransferObject[] */
    public array $installedLocales = [];

    public function __construct(InstalledLocale ...$installedLocales)
    {
        foreach ($installedLocales as $installedLocale) {
            if ($installedLocale->isDefaultForWebsite()) {
                $this->defaultForWebsite = $installedLocale->getLocale();
            }
            if ($installedLocale->isDefaultForUser()) {
                $this->defaultForUser = $installedLocale->getLocale();
            }
            $tabKey = md5($installedLocale->getLocale()->asTranslatable());
            $this->installedLocales[$tabKey] = new InstalledLocaleDataTransferObject($installedLocale);
        }
    }

    public function validateDefaults(): void
    {
        $defaultUserLocale = $this->defaultForUser;
        $defaultWebsiteLocale = $this->defaultForWebsite;
        foreach ($this->installedLocales as $installedLocale) {
            if ($installedLocale->locale === $this->defaultForWebsite && !$installedLocale->isEnabledForWebsite) {
                $defaultWebsiteLocale = null;
                foreach ($this->installedLocales as $userLocale) {
                    if ($userLocale->isEnabledForUser) {
                        $defaultUserLocale = $userLocale->locale;
                        break;
                    }
                }
            }
            if ($installedLocale->locale === $this->defaultForUser && !$installedLocale->isEnabledForUser) {
                $defaultUserLocale = null;
                foreach ($this->installedLocales as $userLocale) {
                    if ($userLocale->isEnabledForUser) {
                        $defaultUserLocale = $userLocale->locale;
                        break;
                    }
                }
            }
        }

        if ($defaultWebsiteLocale === null || $defaultUserLocale === null) {
            throw new RuntimeException('Invalid default locale');
        }
        $this->defaultForUser = $defaultUserLocale;
        $this->defaultForWebsite = $defaultWebsiteLocale;
    }
}
