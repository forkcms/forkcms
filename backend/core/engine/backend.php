<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class defines the backend, it is the core. Everything starts here.
 * We create all needed instances and execute the requested action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */
class backend extends KernelLoader implements ApplicationInterface
{
    /**
     * @var BackendAction
     */
    private $action;

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function display()
    {
        return $this->action->execute();
    }

    /**
     * This method exists because the service container needs to be set before
     * the page's functionality gets loaded.
     */
    public function initialize()
    {
        $URL = new BackendURL($this->getKernel());
        new BackendTemplate();
        new BackendNavigation($this->getKernel());
        new BackendHeader($this->getKernel());

        $this->action = new BackendAction($this->getKernel());
        $this->action->setModule($URL->getModule());
        $this->action->setAction($URL->getAction());
    }
}
