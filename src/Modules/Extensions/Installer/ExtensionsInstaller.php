<?php

namespace ForkCMS\Modules\Extensions\Installer;

use ForkCMS\Modules\Extensions\Domain\Module\Module;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstaller;
use ForkCMS\Modules\Extensions\Domain\Theme\Command\ActivateTheme;
use ForkCMS\Modules\Extensions\Domain\Theme\Command\InstallTheme;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use ForkCMS\Modules\Extensions\Domain\Theme\ThemeRepository;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;

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
        $this->installDefaultTheme();
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
