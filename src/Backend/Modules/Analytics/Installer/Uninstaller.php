<?php

namespace Backend\Modules\Analytics\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */


use Backend\Core\Installer\AbstractModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the analytics module
 */
class Uninstaller extends AbstractModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('Analytics');

        $this->deleteBackendNavigation();
        $this->deleteBackendWidgets();

        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Settings.Modules.' . $this->getModule());
        $this->deleteNavigation('Marketing.Analytics');
        $this->deleteNavigation('Marketing');
    }

    private function deleteBackendWidgets(): void
    {
        $this->deleteDashboardWidgets($this->getModule(), ['RecentVisits', 'TraficSources']);
    }
}
