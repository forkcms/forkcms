<?php

namespace Backend\Modules\Settings\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the settings module
 */
class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('Settings');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureBackendNavigation();
        $this->configureBackendRights();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation "Settings" (should be the last tab)
        $navigationSettingsId = $this->setNavigation(null, 'Settings', null, null, 999);

        // Set navigation for "Settings > General"
        $this->setNavigation($navigationSettingsId, 'General', 'settings/index', null, 1);

        // Set navigation for "Settings > Advanced"
        $navigationAdvancedId = $this->setNavigation($navigationSettingsId, 'Advanced', null, null, 2);
        $this->setNavigation($navigationAdvancedId, 'Email', 'settings/email');
        $this->setNavigation($navigationAdvancedId, 'SEO', 'settings/seo');
        $this->setNavigation($navigationAdvancedId, 'Tools', 'settings/tools');

        // Set navigation for "Settings > Modules"
        $this->setNavigation($navigationSettingsId, 'Modules', null, null, 6);

        // Set navigation for "Settings > Themes"
        $this->setNavigation($navigationSettingsId, 'Themes', null, null, 7);
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Email');
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'Seo');
        $this->setActionRights(1, $this->getModule(), 'TestEmailConnection');
    }
}
