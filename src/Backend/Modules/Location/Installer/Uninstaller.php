<?php

namespace Backend\Modules\Location\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\AbstractModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the location module
 */
class Uninstaller extends AbstractModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('Location');

        $this->deleteBackendNavigation();

        $this->dropDatabaseTables(['location_settings', 'location']);
        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('/Modules/' . $this->getModule());
        $this->deleteNavigation('/Settings/Modules/' . $this->getModule());
    }
}
