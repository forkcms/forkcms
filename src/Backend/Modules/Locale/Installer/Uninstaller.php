<?php

namespace Backend\Modules\Locale\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the locale module
 */
class Uninstaller extends ModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('Locale');

        $this->deleteBackendNavigation();

        $this->dropDatabase('locale');
        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Settings.Translations');
    }
}
