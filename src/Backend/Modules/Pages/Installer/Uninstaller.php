<?php

namespace Backend\Modules\Pages\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the pages module
 */
class Uninstaller extends ModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('Pages');

        $this->deleteFrontendPages();
        $this->deleteBackendNavigation();

        $this->dropDatabase(['pages_blocks', 'pages']);
        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation($this->getModule());
        $this->deleteNavigation('Settings.Modules.' . $this->getModule());
    }

    private function deleteFrontendPages(): void
    {
        $this->deletePages([
            '404',
        ]);

        foreach ($this->getLanguages() as $language) {
            $this->deletePages([
                \SpoonFilter::ucfirst($this->getLocale('Home', 'Core', $language, 'lbl', 'Backend')),
                \SpoonFilter::ucfirst($this->getLocale('Sitemap', 'Core', $language, 'lbl', 'Frontend')),
                \SpoonFilter::ucfirst($this->getLocale('Disclaimer', 'Core', $language, 'lbl', 'Frontend')),
            ]);
        }
    }
}
