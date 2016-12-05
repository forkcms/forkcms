<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates and saves the data from the languages form
 */
class LanguagesHandler
{
    /**
     * @param Form    $form
     * @param Request $request
     *
     * @return bool
     */
    public function process(Form $form, Request $request)
    {
        if (!$request->isMethod('POST')) {
            return false;
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            return $this->processValidForm($form, $request);
        }

        return false;
    }

    /**
     * @param Form    $form
     * @param Request $request
     *
     * @return bool
     */
    public function processValidForm(Form $form, Request $request)
    {
        $data = $form->getData();

        // different fields for single and multiple language
        $data->setLanguages(
            ($data->getLanguageType() === 'multiple')
                ? $data->getLanguages()
                : array($data->getDefaultLanguage())
        );

        // take same_interface_language field into account
        $data->setInterfaceLanguages(
            ($data->getSameInterfaceLanguage() === true)
                ? $data->getLanguages()
                : $data->getInterfaceLanguages()
        );

        $request->getSession()->set('installation_data', $data);

        return true;
    }
}
