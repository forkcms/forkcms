<?php

namespace App\Component\Installer\Handler;

use App\Component\Installer\InstallationData;

/**
 * Validates and saves the data from the login form
 */
final class LoginHandler extends InstallerHandler
{
    public function processInstallationData(InstallationData $installationData): InstallationData
    {
        return $installationData;
    }
}
