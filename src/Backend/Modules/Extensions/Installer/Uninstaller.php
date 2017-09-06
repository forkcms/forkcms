<?php

namespace Backend\Modules\Extensions\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */


use Backend\Core\Installer\ModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the extensions module.
 */
class Uninstaller extends ModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('Extensions');

        $this->deleteFrontendForkTheme();
        $this->deleteBackendNavigation();
        $this->deleteFrontendExtras();

        $this->dropDatabase('themes_templates');
        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Settings.Modules.Overview');
        $this->deleteNavigation('Settings.Themes.ThemesSelection');
        $this->deleteNavigation('Settings.Themes.Templates');
    }

    private function deleteFrontendExtras(): void
    {
        $this->deleteModuleExtra('Search', ['SearchForm']);
    }

    private function deleteFrontendForkTheme(): void
    {
        $this->deleteSettings('Core', 'theme');
        $this->deleteSettings('Pages', 'default_template');
        $this->deleteSettings('Pages', 'meta_navigation');
    }
}
