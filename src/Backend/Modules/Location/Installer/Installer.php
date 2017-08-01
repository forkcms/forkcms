<?php

namespace Backend\Modules\Location\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;
use Common\ModuleExtraType;

/**
 * Installer for the location module
 */
class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('Location');
        $this->importSQL(__DIR__ . '/Data/install.sql');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureSettings();
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureFrontendExtras();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Modules"
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation($navigationModulesId, 'Location', 'location/index', ['location/add', 'location/edit']);

        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Location', 'location/settings');
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'SaveLiveLocation'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'UpdateMarker'); // AJAX
    }

    private function configureFrontendExtras(): void
    {
        $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::block(),
            'Location',
            null,
            ['url' => '/private/location/index?token=true'],
            false
        );
    }

    private function configureSettings(): void
    {
        $this->setSetting($this->getModule(), 'height', 300);
        $this->setSetting($this->getModule(), 'height_widget', 300);
        $this->setSetting($this->getModule(), 'map_type', 'ROADMAP');
        $this->setSetting($this->getModule(), 'map_type_widget', 'ROADMAP');
        $this->setSetting($this->getModule(), 'requires_google_maps', true);
        $this->setSetting($this->getModule(), 'width', 400);
        $this->setSetting($this->getModule(), 'width_widget', 400);
        $this->setSetting($this->getModule(), 'zoom_level', 'auto');
        $this->setSetting($this->getModule(), 'zoom_level_widget', 13);
    }
}
