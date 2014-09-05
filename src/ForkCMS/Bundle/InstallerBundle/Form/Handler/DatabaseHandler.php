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
        $session = $request->getSession();
        $data = $form->getData();

        $session->set('db_hostname', $data['hostname']);
        $session->set('db_username', $data['username']);
        $session->set('db_database', $data['database']);
        $session->set('db_port', $data['port']);
        $session->set('db_password', $data['password']);

        return true;
    }
}
