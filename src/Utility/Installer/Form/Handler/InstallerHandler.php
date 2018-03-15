<?php

namespace ForkCMS\Utility\Installer\Form\Handler;

use ForkCMS\Utility\Installer\InstallationData;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base class to validate and save data from the installer form
 */
abstract class InstallerHandler
{
    final public function process(Form $form, Request $request): bool
    {
        if (!$request->isMethod('POST')) {
            return false;
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $request->getSession()->set('installation_data', $this->processInstallationData($form->getData()));

            return true;
        }

        return false;
    }

    abstract public function processInstallationData(InstallationData $installationData): InstallationData;
}
