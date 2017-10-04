<?php

namespace Backend\Modules\FormBuilder\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\AbstractModuleUninstaller;
use Backend\Core\Installer\UninstallerInterface;

/**
 * Uninstaller for the form_builder module
 */
class Uninstaller extends AbstractModuleUninstaller implements UninstallerInterface
{
    public function uninstall(): void
    {
        $this->setModule('FormBuilder');

        $this->deleteBackendNavigation();

        $this->dropDatabaseTables([
            'forms_fields_validation',
            'forms_fields',
            'forms_data_fields',
            'forms_data',
            'forms',
        ]);

        $this->dropModule();
    }

    private function deleteBackendNavigation(): void
    {
        $this->deleteNavigation('/Modules/' . $this->getModule());
    }
}
