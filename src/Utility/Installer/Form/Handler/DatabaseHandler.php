<?php

namespace ForkCMS\Utility\Installer\Form\Handler;

use ForkCMS\Utility\Installer\InstallationData;

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
