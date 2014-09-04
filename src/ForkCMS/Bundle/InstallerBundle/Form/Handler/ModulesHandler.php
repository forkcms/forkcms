<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates and saves the data from the modules form
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class ModulesHandler
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
var_dump($session->all(), $data);exit;
        $session->set('default_language', $data['default_language']);
        $session->set('default_interface_language', $data['interface_language']);
        $session->set('multiple_languages', $data['language_type'] === 'multiple');
        $session->set('languages', $data['languages']);
        $session->set('interface_languages', $data['interface_languages']);

        return true;
    }
}
