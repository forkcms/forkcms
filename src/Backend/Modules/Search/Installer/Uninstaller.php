<?php

namespace Backend\Modules\Search\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the search module
 */
class Uninstaller extends ModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('Search');

        $this->deleteFrontendPages();
        $this->deleteBackendNavigation();

        $this->dropDatabase(['search_synonyms', 'search_statistics', 'search_modules', 'search_index']);
        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Modules.' . $this->getModule() . '.Statistics');
        $this->deleteNavigation('Modules.' . $this->getModule() . '.Synonyms');
        $this->deleteNavigation('Modules.' . $this->getModule());
        $this->deleteNavigation('Settings.Modules.' . $this->getModule());
    }

    private function deleteFrontendPages(): void
    {
        foreach ($this->getLanguages() as $language) {
            $this->deletePages([
                \SpoonFilter::ucfirst($this->getLocale('Search', 'Core', $language, 'lbl', 'Frontend')),
            ]);
        }
    }
}
