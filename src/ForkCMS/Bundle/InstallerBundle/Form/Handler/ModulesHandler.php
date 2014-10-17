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

        if ($data['example_data'] === true && !in_array('Blog', $data['modules'])) {
            $data['modules'][] = 'Blog';
        }

        $session->set('modules', $data['modules']);
        $session->set('example_data', $data['example_data']);
        $session->set('different_debug_email', $data['different_debug_email']);
        $session->set('debug_email', $data['debug_email']);

        return true;
    }
}
