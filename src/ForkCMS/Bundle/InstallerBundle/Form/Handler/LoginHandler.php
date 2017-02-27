<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Handler;

use ForkCMS\Bundle\InstallerBundle\Entity\InstallationData;

/**
 * Validates and saves the data from the login form
 */
final class LoginHandler extends InstallerHandler
{
    /**
     * @param InstallationData $installationData
     *
     * @return InstallationData
     */
    public function processInstallationData(InstallationData $installationData): InstallationData
    {
        return $installationData;
    }
}
