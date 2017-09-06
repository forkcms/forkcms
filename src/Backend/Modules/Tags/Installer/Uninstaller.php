<?php

namespace Backend\Modules\Tags\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the tags module
 */
class Uninstaller extends ModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('Tags');

        $this->deleteFrontendPages();
        $this->deleteBackendNavigation();

        $this->dropDatabase('tags');
        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Modules.' . $this->getModule());
    }

    private function deleteFrontendPages(): void
    {
        $this->deletePages([$this->getModule()]);
    }
}
