<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Handler;

use ForkCMS\Bundle\InstallerBundle\Entity\InstallationData;

/**
 * Validates and saves the data from the languages form
 */
final class LanguagesHandler extends InstallerHandler
{
    public function processInstallationData(InstallationData $installationData): InstallationData
    {
        // different fields for single and multiple language
        $installationData->setLanguages(
            ($installationData->getLanguageType() === 'multiple')
                ? $installationData->getLanguages()
                : [$installationData->getDefaultLanguage()]
        );

        // take same_interface_language field into account
        $installationData->setInterfaceLanguages(
            ($installationData->getSameInterfaceLanguage() === true)
                ? $installationData->getLanguages()
                : $installationData->getInterfaceLanguages()
        );

        return $installationData;
    }
}
