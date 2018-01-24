<?php

namespace Backend\Modules\Sitemap\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the settings module
 */
class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('Sitemap');
        $this->configureBackendNavigation();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'sitemap/settings');
    }
}
