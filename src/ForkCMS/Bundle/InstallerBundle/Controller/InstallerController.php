<?php

namespace ForkCMS\Bundle\InstallerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use ForkCMS\Bundle\InstallerBundle\Form\Type\LanguagesType;
use ForkCMS\Bundle\InstallerBundle\Form\Type\ModulesType;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\LanguagesHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\ModulesHandler;

class InstallerController extends Controller
{
    public function step1Action()
    {
        $this->checkInstall();

        // if all our requirements are met, go to the next step
        $requirementsChecker = $this->get('forkcms.requirements.checker');
        if ($requirementsChecker->passes()) {
            return $this->redirect($this->generateUrl('install_step2'));
        }

        // not all requirements are met, render the errors in the template
        $errors = $requirementsChecker->getErrors();
        return $this->render(
            'ForkCMSInstallerBundle:Installer:step1.html.twig',
            array(
                'checker' => $requirementsChecker,
                'rootDir' => $this->container->getParameter('kernel.root_dir'),
            )
        );
    }

    public function step2Action(Request $request)
    {
        $this->checkInstall();

        // check if can start the next step
        $requirementsChecker = $this->get('forkcms.requirements.checker');
        if ($requirementsChecker->hasErrors()) {
            return $this->redirect($this->generateUrl('install_step1'));
        }

        // show language information form.
        $form = $this->createForm(new LanguagesType());
        $handler = new LanguagesHandler();
        if ($handler->process($form, $request)) {
            return $this->redirect($this->generateUrl('install_step3'));
        }

        return $this->render(
            'ForkCMSInstallerBundle:Installer:step2.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function step3Action(Request $request)
    {
        $this->checkInstall();

        // @todo: check if all data from step 2 is available

        // show modules form
        $form = $this->createForm(new ModulesType());
        $handler = new ModulesHandler();
        if ($handler->process($form, $request)) {
            return $this->redirect($this->generateUrl('install_step4'));
        }

        return $this->render(
            'ForkCMSInstallerBundle:Installer:step3.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function step4Action(Request $request)
    {
        $this->checkInstall();
        var_dump($request->getSession()->all());exit;
    }

    public function step5Action()
    {
        $this->checkInstall();
        var_dump('5');exit;
    }

    public function step6Action()
    {
        $this->checkInstall();

        $libraryFolder = $this->container->getParameter('kernel.root_dir')
            . '/../library'
        ;

        var_dump('6');exit;
    }

    public function noStepAction()
    {
        $this->checkInstall();

        return $this->redirect($this->generateUrl('install_step1'));
    }

    protected function checkInstall()
    {
        $fs = new FileSystem();
        $installCacheDir = $this->container->getParameter('kernel.cache_dir');
        if ($fs->exists($installCacheDir . 'installed.txt')) {
            exit('This Fork has already been installed. To reinstall, delete
                 installed.txt from the ' . $installCacheDir . ' directory. To log in,
                 <a href="/private">click here</a>.');
        }
    }
}
