<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates and saves the data from the databases form
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class DatabaseHandler
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
        var_dump('ok', $form->getData());exit;
        /*$session = $request->getSession();
        $data = $form->getData();

        $session->set('modules', $data['modules']);
        $session->set('example_data', $data['example_data']);
        $session->set('example_data', $data['different_debug_email']);
        $session->set('debug_email', $data['debug_email']);*/

        return true;
    }
}
