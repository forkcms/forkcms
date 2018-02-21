<?php

namespace App\Component\Installer\Handler;

use App\Component\Installer\InstallationData;

/**
 * Validates and saves the data from the modules form
 */
final class ModulesHandler extends InstallerHandler
{
    public function processInstallationData(InstallationData $installationData): InstallationData
    {
        if ($installationData->hasExampleData() === true) {
            $installationData->addModule('Blog');
        }

        return $installationData;
    }
}
