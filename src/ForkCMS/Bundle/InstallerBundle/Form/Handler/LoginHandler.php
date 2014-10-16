<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates and saves the data from the login form
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class LoginHandler
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

        $session->set('email', $data['email']);
        $session->set('password', $data['password']);

        return true;
    }
}
