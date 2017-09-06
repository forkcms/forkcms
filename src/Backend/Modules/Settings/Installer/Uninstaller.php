<?php

namespace Backend\Modules\Settings\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the settings module
 */
class Uninstaller extends ModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('Settings');

        $this->deleteBackendNavigation();

        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Settings.General');
        $this->deleteNavigation('Settings.Advanced.Email');
        $this->deleteNavigation('Settings.Advanced.SEO');
        $this->deleteNavigation('Settings.Advanced.Tools');
        $this->deleteNavigation('Settings.Advanced');
        $this->deleteNavigation('Settings.Modules');
        $this->deleteNavigation('Settings.Themes');
        $this->deleteNavigation('Settings');
    }
}
