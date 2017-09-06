<?php

namespace Backend\Modules\Mailmotor\Installer;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the Mailmotor module
 */
class Uninstaller extends ModuleUninstaller implements UninstallerInterface
{

    public function uninstall(): void
    {
        $this->setModule('Mailmotor');

        $this->deleteFrontendPages();
        $this->deleteBackendNavigation();

        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Settings.Modules.' . $this->getModule());
    }

    private function deleteFrontendPages(): void
    {
        $this->deletePages(['Newsletters', 'Subscribe', 'Unsubscribe']);
    }
}
