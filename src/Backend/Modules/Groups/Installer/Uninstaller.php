<?php

namespace Backend\Modules\Groups\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the groups module
 */
class Uninstaller extends ModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('Groups');

        $this->deleteBackendWidgets();
        $this->deleteBackendNavigation();

        $this->dropDatabase('groups_settings');
        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Settings.' . $this->getModule());
    }

    private function deleteBackendWidgets(): void
    {
        $this->deleteDashboardWidgets('Settings', ['Analyse']);
        $this->deleteDashboardWidgets('Users', ['Statistics']);
    }
}
