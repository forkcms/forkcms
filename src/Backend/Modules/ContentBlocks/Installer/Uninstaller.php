<?php

namespace Backend\Modules\ContentBlocks\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the content blocks module
 */
class Uninstaller extends ModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('ContentBlocks');

        $this->deleteBackendNavigation();

        $this->dropDatabase('content_blocks');
        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Modules.' . $this->getModule());
    }
}
