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
        $data = $form->getData();

        if ($data->hasExampleData() === true) {
            $data->addModule('Blog');
        }

        $request->getSession()->set('installation_data', $data);

        return true;
    }
}
