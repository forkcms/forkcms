<?php

namespace ForkCMS\Modules\OAuth\Installer;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstaller;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use ForkCMS\Modules\OAuth\Backend\Actions\ModuleSettings;

final class OAuthInstaller extends ModuleInstaller
{
    public function install(): void
    {
        $this->importTranslations(__DIR__ . '/../assets/installer/translations.xml');
        $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('OAuth'),
            ModuleSettings::getActionSlug(),
            $this->getSettingsNavigationItem(),
        );

        $this->setSetting('client_id', null);
        $this->setSetting('client_secret', null);
        $this->setSetting('tenant', null);
        $this->setSetting('enabled', false);
    }
}
