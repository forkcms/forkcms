<?php

namespace Backend\Modules\MediaGalleries\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\AbstractModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the media module
 */
class Uninstaller extends AbstractModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('MediaGalleries');

        $this->deleteBackendNavigation();

        $this->dropDatabaseTables('MediaGallery');
        $this->dropModule();
    }

    protected function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Modules.' . $this->getModule());
    }
}
