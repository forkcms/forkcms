<?php

namespace Backend\Modules\MediaGalleries\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */


use Backend\Core\Installer\ModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the media module
 */
class Uninstaller extends ModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('MediaGalleries');

        $this->deleteBackendNavigation();

        $this->dropDatabase('MediaGallery');
        $this->dropModule();
    }

    protected function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('Modules.' . $this->getModule());
    }
}
