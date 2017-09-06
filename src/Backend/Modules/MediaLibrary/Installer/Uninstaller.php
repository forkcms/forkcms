<?php

namespace Backend\Modules\MediaLibrary\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the MediaLibrary module
 */
class Uninstaller extends ModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('MediaLibrary');

        $this->deleteBackendNavigation();

        $this->dropDatabase(['MediaItem', 'MediaGroupMediaItem', 'MediaGroup', 'MediaFolder']);
        $this->dropModule();
    }

    protected function deleteBackendNavigation(): void
    {
        $this->deleteNavigation($this->getModule());
    }
}
