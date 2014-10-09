<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates and saves the data from the languages form
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class LanguagesHandler
{
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

    public function processValidForm(Form $form, $request)
    {
        $session = $request->getSession();
        $data = $form->getData();

        $session->set('default_language', $data['default_language']);
        $session->set('default_interface_language', $data['interface_language']);
        $session->set('multiple_languages', $data['language_type'] === 'multiple');
        $session->set('languages', $data['languages']);
        $session->set('interface_languages', $data['interface_languages']);

        return true;
    }
}
