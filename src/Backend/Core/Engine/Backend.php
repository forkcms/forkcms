<?php

namespace Backend\Core\Engine;

use ForkCMS\App\ApplicationInterface;
use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpFoundation\Response;

/**
 * This class defines the backend, it is the core. Everything starts here.
 * We create all needed instances and execute the requested action
 */
class Backend extends KernelLoader implements ApplicationInterface
{
    /**
     * @var Action
     */
    private $action;

    /**
     * @return Response
     */
    public function display(): Response
    {
        return $this->action->execute();
    }

    /**
     * This method exists because the service container needs to be set before
     * the page's functionality gets loaded.
     */
    public function initialize(): void
    {
        new Url($this->getKernel());
        new TwigTemplate();
        new Navigation($this->getKernel());
        new Header($this->getKernel());

        $this->action = new Action($this->getKernel());
    }
}
