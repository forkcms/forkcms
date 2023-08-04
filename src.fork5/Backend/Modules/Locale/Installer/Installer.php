<?php

namespace Backend\Modules\Locale\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the locale module
 */
class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('Locale');
        $this->importSQL(__DIR__ . '/Data/install.sql');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->importLocale(dirname(__DIR__, 3) . '/Core/Installer/Data/locale.xml');
        $this->importLocale(dirname(__DIR__, 2) . '/Dashboard/Installer/Data/locale.xml');
        $this->configureBackendNavigation();
        $this->configureBackendRights();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings', null, null, 999);
        $this->setNavigation($navigationSettingsId, 'Translations', 'locale/index', [
            'locale/add',
            'locale/edit',
            'locale/import',
            'locale/analyse',
        ], 4);
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Analyse');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Export');
        $this->setActionRights(1, $this->getModule(), 'ExportAnalyse');
        $this->setActionRights(1, $this->getModule(), 'Import');
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'SaveTranslation'); // AJAX
    }
}
