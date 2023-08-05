<?php

namespace ForkCMS\Modules\Frontend\Installer;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstaller;
use ForkCMS\Modules\Frontend\Backend\Actions\ModuleSettings;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Meta\Meta;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

final class FrontendInstaller extends ModuleInstaller
{
    public const IS_REQUIRED = true;

    public function preInstall(): void
    {
        $this->createTableForEntities(Meta::class, Block::class);
    }

    public function install(): void
    {
        $this->importTranslations(__DIR__ . '/../assets/installer/translations.xml');
        $this->createBackendPages();
    }

    private function createBackendPages(): void
    {
        $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('Frontend'),
            ModuleSettings::getActionSlug(),
            $this->getModuleSettingsNavigationItem()
        );
    }
}
