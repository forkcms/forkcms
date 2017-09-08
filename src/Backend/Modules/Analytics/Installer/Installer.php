<?php

namespace Backend\Modules\Analytics\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\AbstractModuleInstaller;
use Backend\Core\Installer\InstallerInterface;

/**
 * Installer for the analytics module
 */
class Installer extends AbstractModuleInstaller implements InstallerInterface
{
    public function install(): void
    {
        $this->addModule('Analytics');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureBackendWidgets();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Modules"
        $navigationMarketingId = $this->setNavigation(null, 'Marketing', 'analytics/index', null, 4);
        $this->setNavigation($navigationMarketingId, 'Analytics', 'analytics/index');

        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'analytics/settings');
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'Settings');
        $this->setActionRights(1, $this->getModule(), 'Reset');
    }

    private function configureBackendWidgets(): void
    {
        $this->insertDashboardWidget('Analytics', 'RecentVisits');
        $this->insertDashboardWidget('Analytics', 'TraficSources');
    }
}
