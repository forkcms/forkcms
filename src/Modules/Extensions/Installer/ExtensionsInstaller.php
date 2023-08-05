<?php

namespace ForkCMS\Modules\Extensions\Installer;

use ForkCMS\Modules\Extensions\Backend\Actions\ModuleDetail;
use ForkCMS\Modules\Extensions\Backend\Actions\ModuleIndex;
use ForkCMS\Modules\Extensions\Backend\Actions\ModuleInstall;
use ForkCMS\Modules\Extensions\Backend\Actions\ModuleUpload;
use ForkCMS\Modules\Extensions\Backend\Actions\ThemeActivate;
use ForkCMS\Modules\Extensions\Backend\Actions\ThemeDetail;
use ForkCMS\Modules\Extensions\Backend\Actions\ThemeIndex;
use ForkCMS\Modules\Extensions\Backend\Actions\ThemeInstall;
use ForkCMS\Modules\Extensions\Backend\Actions\ThemeTemplateAdd;
use ForkCMS\Modules\Extensions\Backend\Actions\ThemeTemplateDelete;
use ForkCMS\Modules\Extensions\Backend\Actions\ThemeTemplateEdit;
use ForkCMS\Modules\Extensions\Backend\Actions\ThemeTemplateExport;
use ForkCMS\Modules\Extensions\Backend\Actions\ThemeTemplateIndex;
use ForkCMS\Modules\Extensions\Backend\Actions\ThemeUpload;
use ForkCMS\Modules\Extensions\Domain\Module\Module;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstaller;
use ForkCMS\Modules\Extensions\Domain\Theme\Command\ActivateTheme;
use ForkCMS\Modules\Extensions\Domain\Theme\Command\InstallTheme;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use ForkCMS\Modules\Extensions\Domain\Theme\ThemeRepository;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

final class ExtensionsInstaller extends ModuleInstaller
{
    public const IS_REQUIRED = true;

    public function preInstall(): void
    {
        $this->createTableForEntities(
            Module::class,
            Theme::class,
            ThemeTemplate::class
        );
    }

    public function install(): void
    {
        $this->importTranslations(__DIR__ . '/../assets/installer/translations.xml');
        $this->createBackendPages();
        $this->installDefaultTheme();
    }

    private function createBackendPages(): void
    {
        $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('Modules'),
            ModuleIndex::getActionSlug(),
            $this->getModuleSettingsNavigationItem(),
            [
                ModuleDetail::getActionSlug(),
                ModuleInstall::getActionSlug(),
                ModuleUpload::getActionSlug(),
            ],
            0
        );
        $themeSettings = $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('Themes'),
            ThemeIndex::getActionSlug(),
            $this->getSettingsNavigationItem(),
        );
        $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('ThemeSelection'),
            ThemeIndex::getActionSlug(),
            $themeSettings,
            [
                ThemeDetail::getActionSlug(),
                ThemeInstall::getActionSlug(),
                ThemeActivate::getActionSlug(),
                ThemeUpload::getActionSlug(),
            ]
        );
        $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('ThemeTemplates'),
            ThemeTemplateIndex::getActionSlug(),
            $themeSettings,
            [
                ThemeTemplateAdd::getActionSlug(),
                ThemeTemplateEdit::getActionSlug(),
                ThemeTemplateDelete::getActionSlug(),
                ThemeTemplateExport::getActionSlug(),
            ]
        );
    }

    private function installDefaultTheme(): void
    {
        /** @var ThemeRepository $themeRepository */
        $themeRepository = $this->getRepository(Theme::class);
        $installTheme = new InstallTheme($themeRepository->findInstallable()[$_ENV['FORK_INSTALLER_THEME']]);
        $this->dispatchCommand($installTheme);
        $this->dispatchCommand(new ActivateTheme($installTheme->theme->getEntity()));
    }
}
