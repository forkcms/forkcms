<?php

namespace ForkCMS\Bundle\InstallerBundle\Controller;

use Common\Exception\ExitException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use ForkCMS\Bundle\InstallerBundle\Form\Type\LanguagesType;
use ForkCMS\Bundle\InstallerBundle\Form\Type\ModulesType;
use ForkCMS\Bundle\InstallerBundle\Form\Type\DatabaseType;
use ForkCMS\Bundle\InstallerBundle\Form\Type\LoginType;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\LanguagesHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\ModulesHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\DatabaseHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\LoginHandler;
use ForkCMS\Bundle\InstallerBundle\Entity\InstallationData;
use Symfony\Component\HttpFoundation\Response;

class InstallerController extends Controller
{
    /** @var InstallationData */
    public static $installationData;

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function step1Action()
    {
        $this->checkInstall();

        // if all our requirements are met, go to the next step
        $requirementsChecker = $this->get('forkcms.requirements.checker');
        if ($requirementsChecker->passes()) {
            return $this->redirect($this->generateUrl('install_step2'));
        }

        return $this->render(
            'ForkCMSInstallerBundle:Installer:step1.html.twig',
            [
                'checker' => $requirementsChecker,
                'rootDir' => realpath($this->container->getParameter('site.path_www')),
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function step2Action(Request $request)
    {
        $this->checkInstall();

        // check if can start the next step
        $requirementsChecker = $this->get('forkcms.requirements.checker');
        if ($requirementsChecker->hasErrors()) {
            return $this->redirect($this->generateUrl('install_step1'));
        }

        // show language information form.
        $form = $this->createForm(new LanguagesType(), $this->getInstallationData($request));
        $handler = new LanguagesHandler();
        if ($handler->process($form, $request)) {
            return $this->redirect($this->generateUrl('install_step3'));
        }

        return $this->render(
            'ForkCMSInstallerBundle:Installer:step2.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function step3Action(Request $request)
    {
        $this->checkInstall();

        // @todo: check if all data from step 2 is available

        // show modules form
        $form = $this->createForm(new ModulesType(), $this->getInstallationData($request));
        $handler = new ModulesHandler();
        if ($handler->process($form, $request)) {
            return $this->redirect($this->generateUrl('install_step4'));
        }

        return $this->render(
            'ForkCMSInstallerBundle:Installer:step3.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function step4Action(Request $request)
    {
        $this->checkInstall();

        // show database form
        $form = $this->createForm(new DatabaseType(), $this->getInstallationData($request));
        $handler = new DatabaseHandler();
        if ($handler->process($form, $request)) {
            return $this->redirect($this->generateUrl('install_step5'));
        }

        return $this->render(
            'ForkCMSInstallerBundle:Installer:step4.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function step5Action(Request $request)
    {
        $this->checkInstall();

        // show database form
        $form = $this->createForm(new LoginType(), $this->getInstallationData($request));
        $handler = new LoginHandler();
        if ($handler->process($form, $request)) {
            return $this->redirect($this->generateUrl('install_step6'));
        }

        return $this->render(
            'ForkCMSInstallerBundle:Installer:step5.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function step6Action(Request $request)
    {
        $this->checkInstall();

        $forkInstaller = $this->get('forkcms.installer');
        $status = $forkInstaller->install($this->getInstallationData($request));

        return $this->render(
            'ForkCMSInstallerBundle:Installer:step6.html.twig',
            [
                'installStatus' => $status,
                'installer' => $forkInstaller,
                'data' => $this->getInstallationData($request),
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    protected function getInstallationData(Request $request)
    {
        if (!$request->getSession()->has('installation_data')) {
            $request->getSession()->set('installation_data', new InstallationData());
        }
        // static cache
        self::$installationData = $request->getSession()->get('installation_data');

        return $request->getSession()->get('installation_data');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function noStepAction()
    {
        $this->checkInstall();

        return $this->redirect($this->generateUrl('install_step1'));
    }

    protected function checkInstall()
    {
        $filesystem = new Filesystem();
        $kernelDir = $this->container->getParameter('kernel.root_dir');
        $parameterFile = $kernelDir . 'config/parameters.yml';
        if ($filesystem->exists($parameterFile)) {
            throw new ExitException(
                'This Fork has already been installed. To reinstall, delete
                 parameters.yml from the ' . $kernelDir . 'config/ directory.',
                'This Fork has already been installed. To reinstall, delete
                 parameters.yml from the ' . $kernelDir . 'config/ directory. To log in,
                 <a href="/private">click here</a>.',
                Response::HTTP_FORBIDDEN
            );
        }
    }
}
