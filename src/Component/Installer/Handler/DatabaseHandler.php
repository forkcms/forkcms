<?php

namespace App\Component\Installer\Handler;

use App\Component\Installer\InstallationData;

/**
 * Validates and saves the data from the databases form
 */
final class DatabaseHandler extends InstallerHandler
{
    public function processInstallationData(InstallationData $installationData): InstallationData
    {
        return $installationData;
    }
}
